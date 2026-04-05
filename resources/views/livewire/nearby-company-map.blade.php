<div
    class="mb-8 rounded-3xl bg-white/80 shadow-[0_30px_90px_-60px_rgba(15,23,42,0.55)] ring-1 ring-slate-200/70 backdrop-blur-sm dark:bg-slate-900/70 dark:ring-slate-800/70 overflow-hidden"
    x-data="{
        companies: @js($companies),
        map: null,
        markers: [],
        userMarker: null,
        userLat: null,
        userLng: null,
        filterAvailability: 'all',
        maxDistanceKm: 50,
        hasLocation: false,
        locationError: null,
        get isSecure() { return window.isSecureContext || window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1'; },
        haversineKm(lat1, lng1, lat2, lng2) {
            const toRad = (x) => (x * Math.PI) / 180;
            const R = 6371;
            const dLat = toRad(lat2 - lat1);
            const dLng = toRad(lng2 - lng1);
            const a =
                Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(dLng / 2) * Math.sin(dLng / 2);
            const c = 2 * Math.asin(Math.min(1, Math.sqrt(a)));
            return R * c;
        },
        normalizedCompanies() {
            const list = (this.companies || []).map((c) => {
                const d =
                    this.hasLocation && c.lat != null && c.lng != null
                        ? this.haversineKm(this.userLat, this.userLng, c.lat, c.lng)
                        : null;
                return { ...c, distance_km: d };
            });
            list.sort((a, b) => {
                if (a.distance_km == null && b.distance_km == null) return 0;
                if (a.distance_km == null) return 1;
                if (b.distance_km == null) return -1;
                return a.distance_km - b.distance_km;
            });
            return list;
        },
        filteredCompanies() {
            return this.normalizedCompanies().filter((c) => {
                if (this.filterAvailability !== 'all' && c.availability !== this.filterAvailability) return false;
                if (this.hasLocation && c.distance_km != null && c.distance_km > this.maxDistanceKm) return false;
                return true;
            });
        },
        markerColor(c) {
            if (c.availability === 'available') return '#16a34a';
            if (c.availability === 'limited') return '#f59e0b';
            return '#ef4444';
        },
        buildPopupNode(c) {
            const container = document.createElement('div');
            container.style.minWidth = '220px';

            const title = document.createElement('div');
            title.textContent = c.name;
            title.style.fontWeight = '800';
            title.style.color = '#0f172a';
            title.style.marginBottom = '4px';
            container.appendChild(title);

            if (c.address) {
                const address = document.createElement('div');
                address.textContent = c.address;
                address.style.fontSize = '12px';
                address.style.color = 'rgba(15,23,42,0.7)';
                address.style.marginBottom = '6px';
                container.appendChild(address);
            }

            const meta = document.createElement('div');
            meta.style.fontSize = '12px';
            meta.style.color = 'rgba(15,23,42,0.7)';

            const statusLabel = c.is_open_now ? 'Open' : 'Closed';
            const availabilityLabel = c.availability === 'available' ? 'Available' : c.availability === 'limited' ? 'Limited availability' : 'Closed / Unavailable';
            const distanceLabel = c.distance_km != null ? `${c.distance_km.toFixed(2)} km` : '—';

            const statusRow = document.createElement('div');
            statusRow.textContent = `Status: ${statusLabel}`;
            meta.appendChild(statusRow);

            const availabilityRow = document.createElement('div');
            availabilityRow.textContent = `Availability: ${availabilityLabel}`;
            meta.appendChild(availabilityRow);

            const distanceRow = document.createElement('div');
            distanceRow.textContent = `Distance: ${distanceLabel}`;
            meta.appendChild(distanceRow);

            container.appendChild(meta);

            const actions = document.createElement('div');
            actions.style.display = 'flex';
            actions.style.gap = '8px';
            actions.style.marginTop = '10px';

            const bookBtn = document.createElement('button');
            bookBtn.type = 'button';
            bookBtn.textContent = 'Book Now';
            bookBtn.style.flex = '1';
            bookBtn.style.background = 'linear-gradient(90deg,#4f46e5,#7c3aed)';
            bookBtn.style.color = '#fff';
            bookBtn.style.fontWeight = '800';
            bookBtn.style.padding = '8px 10px';
            bookBtn.style.borderRadius = '12px';
            bookBtn.style.border = 'none';
            bookBtn.style.cursor = 'pointer';
            bookBtn.addEventListener('click', (e) => {
                e.preventDefault();
                if (window.Livewire?.dispatch) window.Livewire.dispatch('mapSelectCompany', { companyId: c.id });
                if (window.livewire?.emit) window.livewire.emit('mapSelectCompany', c.id);
            });
            actions.appendChild(bookBtn);

            if (c.lat != null && c.lng != null) {
                const destination = `${c.lat},${c.lng}`;
                const origin = this.hasLocation ? `${this.userLat},${this.userLng}` : '';
                const url = `https://www.google.com/maps/dir/?api=1${origin ? `&origin=${encodeURIComponent(origin)}` : ''}&destination=${encodeURIComponent(destination)}`;

                const directions = document.createElement('a');
                directions.href = url;
                directions.target = '_blank';
                directions.rel = 'noopener';
                directions.textContent = 'Directions';
                directions.style.flex = '1';
                directions.style.background = '#fff';
                directions.style.color = '#0f172a';
                directions.style.fontWeight = '800';
                directions.style.padding = '8px 10px';
                directions.style.borderRadius = '12px';
                directions.style.border = '1px solid rgba(15,23,42,0.15)';
                directions.style.textAlign = 'center';
                directions.style.textDecoration = 'none';
                actions.appendChild(directions);
            }

            container.appendChild(actions);
            return container;
        },
        initMap() {
            const el = this.$refs.map;
            if (!el) return;

            this.map = L.map(el, { zoomControl: true }).setView([14.5995, 120.9842], 11);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap',
            }).addTo(this.map);

            this.refreshMarkers();
        },
        refreshMarkers() {
            if (!this.map) return;
            this.markers.forEach((m) => m.remove());
            this.markers = [];

            const list = this.filteredCompanies().filter((c) => c.lat != null && c.lng != null);
            list.forEach((c) => {
                const color = this.markerColor(c);
                const marker = L.circleMarker([c.lat, c.lng], {
                    radius: 10,
                    color,
                    fillColor: color,
                    fillOpacity: 0.9,
                    weight: 2,
                }).addTo(this.map);
                marker.bindPopup(this.buildPopupNode(c));

                this.markers.push(marker);
            });

            if (list.length && this.hasLocation) {
                const nearest = list[0];
                this.map.setView([nearest.lat, nearest.lng], 13);
            }
        },
        setUserLocation(lat, lng) {
            this.userLat = lat;
            this.userLng = lng;
            this.hasLocation = lat != null && lng != null;
            if (this.map) {
                if (this.userMarker) this.userMarker.remove();
                this.userMarker = L.circleMarker([lat, lng], {
                    radius: 8,
                    color: '#0ea5e9',
                    fillColor: '#0ea5e9',
                    fillOpacity: 0.9,
                    weight: 2,
                }).addTo(this.map).bindPopup('You are here');
            }
            this.refreshMarkers();
        },
        requestLocation() {
            this.locationError = null;
            if (!navigator.geolocation) {
                this.locationError = 'This browser does not support location services.';
                return;
            }
            if (!this.isSecure) {
                this.locationError = 'Location is blocked on HTTP. Open this site using HTTPS (or localhost) to allow GPS.';
                return;
            }
            navigator.geolocation.getCurrentPosition(
                (pos) => this.setUserLocation(pos.coords.latitude, pos.coords.longitude),
                (err) => {
                    this.setUserLocation(null, null);
                    if (!err) {
                        this.locationError = 'Could not get your location.';
                        return;
                    }
                    if (err.code === 1) this.locationError = 'Location permission was denied. Enable it in your browser settings.';
                    else if (err.code === 2) this.locationError = 'Location is unavailable. Turn on GPS or try again.';
                    else if (err.code === 3) this.locationError = 'Location request timed out. Try again.';
                    else this.locationError = 'Could not get your location.';
                },
                { enableHighAccuracy: false, timeout: 8000, maximumAge: 60000 },
            );
        },
        init() {
            const loadLeaflet = () =>
                new Promise((resolve) => {
                    if (window.L) return resolve();
                    const css = document.createElement('link');
                    css.rel = 'stylesheet';
                    css.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                    css.integrity = 'sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=';
                    css.crossOrigin = '';
                    document.head.appendChild(css);

                    const js = document.createElement('script');
                    js.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                    js.integrity = 'sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=';
                    js.crossOrigin = '';
                    js.onload = () => resolve();
                    document.head.appendChild(js);
                });

            loadLeaflet().then(() => {
                this.initMap();
                this.requestLocation();
            });
        },
    }"
>
    <div class="px-6 py-6 sm:px-10">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white">Nearby Companies</h2>
                <p class="mt-1 text-sm font-medium text-slate-500 dark:text-slate-400">Find nearby companies and book faster.</p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <div class="flex items-center gap-2">
                    <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Availability</div>
                    <select x-model="filterAvailability" @change="refreshMarkers()" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-800 shadow-sm dark:border-slate-800 dark:bg-slate-900 dark:text-slate-100">
                        <option value="all">All</option>
                        <option value="available">Available</option>
                        <option value="limited">Limited</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Max distance</div>
                    <input type="range" min="1" max="100" step="1" x-model="maxDistanceKm" @input="refreshMarkers()" class="w-36">
                    <div class="text-sm font-extrabold text-slate-900 dark:text-white" x-text="maxDistanceKm + ' km'"></div>
                </div>

                <button type="button" @click="requestLocation()" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-extrabold text-white shadow-lg shadow-indigo-500/20 hover:bg-indigo-700 transition">
                    Use my location
                </button>
            </div>
        </div>
        <div class="mt-3 text-xs font-semibold text-rose-600 dark:text-rose-300" x-show="locationError" x-text="locationError"></div>
        <div class="mt-2 text-xs font-semibold text-slate-500 dark:text-slate-400" x-show="!isSecure">
            Location needs HTTPS on most devices. This site is currently running on HTTP, so the browser will block GPS here.
        </div>
    </div>

    <div class="grid grid-cols-1 gap-0 lg:grid-cols-5">
        <div class="lg:col-span-3">
            <div class="h-[360px] sm:h-[420px] lg:h-[520px] w-full" x-ref="map"></div>
            <div class="px-6 py-4 sm:px-10 border-t border-slate-200/70 dark:border-slate-800/70 bg-white/60 dark:bg-slate-900/40">
                <div class="flex flex-wrap items-center gap-3 text-xs font-semibold text-slate-500 dark:text-slate-400">
                    <div class="inline-flex items-center gap-2">
                        <span class="h-3 w-3 rounded-full" style="background:#16a34a"></span>
                        Available
                    </div>
                    <div class="inline-flex items-center gap-2">
                        <span class="h-3 w-3 rounded-full" style="background:#f59e0b"></span>
                        Limited
                    </div>
                    <div class="inline-flex items-center gap-2">
                        <span class="h-3 w-3 rounded-full" style="background:#ef4444"></span>
                        Closed / Unavailable
                    </div>
                    <div class="ml-auto text-[11px] font-medium text-slate-400" x-show="!isSecure">
                        Location requires HTTPS on most devices. Distance sorting may be unavailable.
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 border-t lg:border-t-0 lg:border-l border-slate-200/70 dark:border-slate-800/70">
            <div class="px-6 py-6 sm:px-10">
                <div class="text-sm font-extrabold text-slate-900 dark:text-white">Companies</div>
                <div class="mt-4 space-y-3">
                    <template x-for="c in filteredCompanies()" :key="c.id">
                        <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200/70 dark:bg-slate-900 dark:ring-slate-800/70">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="truncate text-sm font-extrabold text-slate-900 dark:text-white" x-text="c.name"></div>
                                    <div class="mt-1 text-xs font-semibold text-slate-500 dark:text-slate-400" x-text="c.address || '—'"></div>
                                </div>
                                <div class="shrink-0">
                                    <div class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold"
                                        :class="c.availability === 'available' ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200/70 dark:bg-emerald-900/20 dark:text-emerald-200 dark:ring-emerald-800/40' : (c.availability === 'limited' ? 'bg-amber-50 text-amber-700 ring-1 ring-amber-200/70 dark:bg-amber-900/20 dark:text-amber-200 dark:ring-amber-800/40' : 'bg-rose-50 text-rose-700 ring-1 ring-rose-200/70 dark:bg-rose-900/20 dark:text-rose-200 dark:ring-rose-800/40')">
                                        <span class="h-2 w-2 rounded-full" :style="'background:' + markerColor(c)"></span>
                                        <span x-text="c.is_open_now ? (c.availability === 'limited' ? 'Limited' : 'Open') : 'Closed'"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3 flex items-center justify-between gap-3">
                                <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">
                                    <span x-show="c.distance_km != null" x-text="c.distance_km != null ? (c.distance_km.toFixed(2) + ' km') : ''"></span>
                                    <span x-show="c.distance_km == null">Distance: —</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="button" class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-extrabold text-slate-700 hover:bg-slate-200 transition dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
                                        @click="
                                            if (c.lat != null && c.lng != null) {
                                                map.setView([c.lat, c.lng], 14);
                                            }
                                            if (window.Livewire?.dispatch) window.Livewire.dispatch('mapSelectCompany', { companyId: c.id });
                                            if (window.livewire?.emit) window.livewire.emit('mapSelectCompany', c.id);
                                        ">
                                        Book Now
                                    </button>
                                    <a class="rounded-xl bg-indigo-600 px-3 py-2 text-xs font-extrabold text-white shadow-sm hover:bg-indigo-700 transition"
                                        :href="(c.lat != null && c.lng != null) ? ('https://www.google.com/maps/dir/?api=1' + (hasLocation ? ('&origin=' + encodeURIComponent(userLat + ',' + userLng)) : '') + '&destination=' + encodeURIComponent(c.lat + ',' + c.lng)) : '#'"
                                        target="_blank" rel="noopener">
                                        Directions
                                    </a>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
 </div>
