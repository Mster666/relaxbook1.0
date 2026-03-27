(() => {
  const SHOW_DELAY_NAV_MS = 500
  const SHOW_DELAY_FULL_SUBMIT_MS = 250
  const MIN_VISIBLE_MS = 180
  const FADE_MS = 180

  const state = {
    el: null,
    logoUrl: null,
    isActive: false,
    lastShowAt: 0,
    hideTimer: null,
    showTimer: null,
    pendingRequests: 0,
    pageLoadHold: true,
  }

  const normalizeStorageUrl = (raw) => {
    const value = (raw || '').trim()
    if (!value) return null
    if (value.startsWith('storage/')) return `/${value}`
    if (value.startsWith('/storage/')) return value

    let url
    try {
      url = new URL(value, window.location.href)
    } catch {
      return value
    }

    if (!url.pathname.includes('/storage/')) return value

    const host = (url.hostname || '').toLowerCase()
    const currentHost = (window.location.hostname || '').toLowerCase()
    const badHosts = new Set(['localhost', '127.0.0.1', 'relaxbook.test'])

    if (host && host !== currentHost && badHosts.has(host)) {
      const idx = url.pathname.indexOf('/storage/')
      const tail = idx >= 0 ? url.pathname.slice(idx) : url.pathname
      return `${window.location.origin}${tail}${url.search}${url.hash}`
    }

    return value
  }

  const rewriteStorageImages = (root = document) => {
    const imgs = root.querySelectorAll ? root.querySelectorAll('img[src]') : []
    imgs.forEach((img) => {
      const next = normalizeStorageUrl(img.getAttribute('src'))
      if (next && next !== img.getAttribute('src')) {
        img.setAttribute('src', next)
      }
    })
  }

  const startStorageImageObserver = () => {
    let scheduled = false
    const schedule = () => {
      if (scheduled) return
      scheduled = true
      window.requestAnimationFrame(() => {
        scheduled = false
        rewriteStorageImages(document)
      })
    }

    rewriteStorageImages(document)

    const observer = new MutationObserver((mutations) => {
      for (const m of mutations) {
        if (m.type !== 'childList') continue
        if (m.addedNodes && m.addedNodes.length) {
          schedule()
          return
        }
      }
    })

    observer.observe(document.documentElement, { childList: true, subtree: true })
  }

  const getLogoUrl = () => {
    if (state.logoUrl) return state.logoUrl
    const meta = document.querySelector("meta[name='rb-logo']")
    const metaUrl = meta?.getAttribute('content')?.trim()
    state.logoUrl = metaUrl || '/images/logo.png'
    return state.logoUrl
  }

  const build = () => {
    if (state.el) return state.el

    const style = document.createElement('style')
    style.textContent = `
      .rb-loader{--rb-bg-top:#f7faff;--rb-bg-mid:#ffffff;--rb-bg-bot:#f6f9ff;--rb-blob-a:rgba(59,130,246,.28);--rb-blob-b:rgba(99,102,241,.28);--rb-blob-c:rgba(56,189,248,.22);--rb-glow:rgba(59,130,246,.18);--rb-logo-shadow:rgba(15,23,42,.14);--rb-ring-track:rgba(148,163,184,.18);--rb-ring-soft:rgba(56,189,248,.18);--rb-ring-glow:rgba(59,130,246,.18);--rb-text:#64748b;--rb-dot:#93c5fd;position:fixed;inset:0;z-index:2147483000;display:flex;align-items:center;justify-content:center;opacity:0;pointer-events:none;transition:opacity ${FADE_MS}ms cubic-bezier(.2,.8,.2,1);background:radial-gradient(1200px 600px at 20% 10%, rgba(59,130,246,.16), transparent 55%),radial-gradient(900px 700px at 85% 80%, rgba(99,102,241,.16), transparent 55%),linear-gradient(180deg,var(--rb-bg-top) 0%,var(--rb-bg-mid) 65%,var(--rb-bg-bot) 100%);overflow:hidden}
      .rb-loader.is-active{opacity:1;pointer-events:auto}
      .dark .rb-loader{--rb-bg-top:#060b16;--rb-bg-mid:#0b1220;--rb-bg-bot:#070d1b;--rb-blob-a:rgba(59,130,246,.22);--rb-blob-b:rgba(99,102,241,.22);--rb-blob-c:rgba(56,189,248,.16);--rb-glow:rgba(56,189,248,.14);--rb-logo-shadow:rgba(0,0,0,.35);--rb-ring-track:rgba(148,163,184,.22);--rb-ring-soft:rgba(56,189,248,.14);--rb-ring-glow:rgba(56,189,248,.16);--rb-text:#cbd5e1;--rb-dot:#60a5fa}
      .rb-loader__bg{position:absolute;inset:0;pointer-events:none}
      .rb-loader__blob{position:absolute;border-radius:9999px;filter:blur(48px);opacity:.55}
      .rb-loader__blob--a{width:520px;height:520px;left:-220px;top:-220px;background:var(--rb-blob-a)}
      .rb-loader__blob--b{width:620px;height:620px;right:-280px;bottom:-300px;background:var(--rb-blob-b)}
      .rb-loader__blob--c{width:340px;height:220px;right:120px;top:46px;border-radius:64px;transform:rotate(-10deg);background:var(--rb-blob-c);filter:blur(44px);opacity:.45}
      .rb-loader__wrap{position:relative;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:14px}
      .rb-loader__stage{position:relative;width:188px;height:188px;display:grid;place-items:center}
      .rb-loader__glow{position:absolute;inset:-26px;border-radius:44px;background:radial-gradient(closest-side, var(--rb-glow), transparent 70%);filter:blur(8px);opacity:.0;transition:opacity 500ms cubic-bezier(.2,.8,.2,1)}
      .rb-loader.is-active .rb-loader__glow{opacity:1}
      .rb-loader__logo{position:absolute;inset:18px;background-image:var(--rb-logo);background-size:contain;background-repeat:no-repeat;background-position:center;filter:drop-shadow(0 14px 24px var(--rb-logo-shadow));transform:translateZ(0)}
      .rb-loader__ring{position:absolute;inset:-18px;display:grid;place-items:center;pointer-events:none;transform:translateZ(0)}
      .rb-loader__ring svg{width:100%;height:100%;transform-origin:50% 50%;animation:rbRingSpin 1.05s linear infinite}
      .rb-loader__ring .rb-ring-track{fill:none;stroke:var(--rb-ring-track);stroke-width:3.2}
      .rb-loader__ring .rb-ring-arc{fill:none;stroke:url(#rbRingGrad);stroke-width:3.6;stroke-linecap:round;stroke-dasharray:92 210;animation:rbRingDash 1.15s cubic-bezier(.35,.1,.3,.9) infinite;filter:drop-shadow(0 10px 18px rgba(59,130,246,.18))}
      .rb-loader__ring .rb-ring-soft{fill:none;stroke:var(--rb-ring-soft);stroke-width:8;opacity:.35;filter:blur(.2px);animation:rbRingGlow 1.3s ease-in-out infinite}
      .rb-loader__ring .rb-ring-arc{filter:drop-shadow(0 10px 18px var(--rb-ring-glow))}
      .rb-loader__text{font-family:ui-sans-serif,system-ui,-apple-system,Segoe UI,Roboto,Inter,Arial;letter-spacing:.2px;color:var(--rb-text);font-weight:600;font-size:14px}
      .rb-loader__dots{display:inline-flex;gap:3px;margin-left:6px;vertical-align:middle}
      .rb-loader__dot{width:5px;height:5px;border-radius:9999px;background:var(--rb-dot);opacity:.35;animation:rbDots 1s ease-in-out infinite}
      .rb-loader__dot:nth-child(2){animation-delay:.15s}
      .rb-loader__dot:nth-child(3){animation-delay:.3s}
      @keyframes rbRingSpin{to{transform:rotate(360deg)}}
      @keyframes rbRingDash{0%{stroke-dasharray:10 210;stroke-dashoffset:0}40%{stroke-dasharray:120 210;stroke-dashoffset:-24}100%{stroke-dasharray:10 210;stroke-dashoffset:-140}}
      @keyframes rbRingGlow{0%,100%{opacity:.28}50%{opacity:.46}}
      @keyframes rbDots{0%,100%{transform:translateY(0);opacity:.25}50%{transform:translateY(-2px);opacity:.75}}
      @media (prefers-reduced-motion: reduce){
        .rb-loader,.rb-loader *{animation:none!important;transition:none!important}
        .rb-loader{opacity:1}
        .rb-loader__dots{display:none}
      }

      .rb-btn-loading{position:relative;pointer-events:none}
      .rb-btn-loading[disabled]{opacity:.75}
      .rb-btn-spinner{width:14px;height:14px;border-radius:9999px;border:2px solid currentColor;border-right-color:transparent;display:inline-block;vertical-align:middle;animation:rbBtnSpin .8s linear infinite}
      @keyframes rbBtnSpin{to{transform:rotate(360deg)}}
    `
    document.head.appendChild(style)

    const overlay = document.createElement('div')
    overlay.className = 'rb-loader'
    overlay.setAttribute('role', 'status')
    overlay.setAttribute('aria-live', 'polite')

    const logoUrl = getLogoUrl()
    overlay.style.setProperty('--rb-logo', `url("${logoUrl}")`)

    overlay.innerHTML = `
      <div class="rb-loader__bg" aria-hidden="true">
        <div class="rb-loader__blob rb-loader__blob--a"></div>
        <div class="rb-loader__blob rb-loader__blob--b"></div>
        <div class="rb-loader__blob rb-loader__blob--c"></div>
      </div>
      <div class="rb-loader__wrap">
        <div class="rb-loader__stage" aria-hidden="true">
          <div class="rb-loader__glow"></div>
          <div class="rb-loader__logo"></div>
          <div class="rb-loader__ring">
            <svg viewBox="0 0 100 100">
              <defs>
                <linearGradient id="rbRingGrad" x1="0" y1="0" x2="1" y2="1">
                  <stop offset="0%" stop-color="#60a5fa" stop-opacity="1"></stop>
                  <stop offset="55%" stop-color="#38bdf8" stop-opacity="1"></stop>
                  <stop offset="100%" stop-color="#6366f1" stop-opacity="1"></stop>
                </linearGradient>
              </defs>
              <circle class="rb-ring-track" cx="50" cy="50" r="44"></circle>
              <circle class="rb-ring-soft" cx="50" cy="50" r="44"></circle>
              <circle class="rb-ring-arc" cx="50" cy="50" r="44"></circle>
            </svg>
          </div>
        </div>
        <div class="rb-loader__text">
          Loading<span class="rb-loader__dots" aria-hidden="true">
            <span class="rb-loader__dot"></span>
            <span class="rb-loader__dot"></span>
            <span class="rb-loader__dot"></span>
          </span>
        </div>
      </div>
    `

    document.body.appendChild(overlay)
    state.el = overlay
    return overlay
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', startStorageImageObserver, { once: true })
  } else {
    startStorageImageObserver()
  }

  const clearTimers = () => {
    if (state.hideTimer) window.clearTimeout(state.hideTimer)
    if (state.showTimer) window.clearTimeout(state.showTimer)
    state.hideTimer = null
    state.showTimer = null
  }

  const showNow = () => {
    const el = build()
    clearTimers()

    state.lastShowAt = Date.now()
    state.isActive = true

    el.classList.add('is-active')
  }

  const scheduleShow = (delayMs) => {
    const el = build()
    if (state.isActive) return
    if (state.showTimer) return

    state.showTimer = window.setTimeout(() => {
      state.showTimer = null
      showNow()
    }, delayMs)

    return el
  }

  const hide = () => {
    if (!state.el) return
    if (state.pageLoadHold) return
    if (state.pendingRequests > 0) return

    const elapsed = Date.now() - state.lastShowAt
    const remaining = Math.max(MIN_VISIBLE_MS - elapsed, 0)

    clearTimers()
    state.hideTimer = window.setTimeout(() => {
      state.el.classList.remove('is-active')
      state.isActive = false
    }, remaining)
  }

  const isInternalNavigation = (href) => {
    if (!href) return false
    if (href.startsWith('#')) return false
    try {
      const url = new URL(href, window.location.href)
      if (url.origin !== window.location.origin) return false
      if (url.pathname === window.location.pathname && url.search === window.location.search) return false
      return true
    } catch {
      return false
    }
  }

  const shouldSkipForLink = (a) => {
    if (!a) return true
    if (a.hasAttribute('data-rb-no-loader')) return true
    const mode = a.getAttribute('data-rb-loader')
    if (mode === 'none') return true
    if (a.target && a.target !== '_self') return true
    if (a.hasAttribute('download')) return true
    const href = a.getAttribute('href') || ''
    if (!href || href.startsWith('#')) return true
    if (href.startsWith('mailto:') || href.startsWith('tel:')) return true
    return false
  }

  const toLoadingText = (text) => {
    const t = (text || '').trim()
    if (!t) return 'Loading...'
    const lower = t.toLowerCase()
    if (lower.includes('log in') || lower === 'login') return 'Logging in...'
    if (lower.includes('sign in')) return 'Signing in...'
    if (lower.includes('create')) return 'Creating...'
    if (lower.includes('save')) return 'Saving...'
    if (lower.includes('update')) return 'Updating...'
    if (lower.includes('submit')) return 'Submitting...'
    if (lower.includes('generate')) return 'Generating...'
    if (t.endsWith('...')) return t
    return `${t}...`
  }

  const applyButtonLoading = (form, submitter) => {
    const btn = submitter instanceof HTMLButtonElement ? submitter : null
    const inputBtn = submitter instanceof HTMLInputElement ? submitter : null
    const target = btn || inputBtn
    if (!target) return
    if (target.hasAttribute('data-rb-no-btn-loading')) return
    if (target.classList.contains('rb-btn-loading')) return

    const originalText =
      btn?.innerText?.trim() ||
      inputBtn?.value?.trim() ||
      ''

    const loadingText = target.getAttribute('data-rb-loading-text') || toLoadingText(originalText)
    target.setAttribute('data-rb-original-text', originalText)

    if (btn) {
      btn.disabled = true
      btn.classList.add('rb-btn-loading')
      btn.setAttribute('aria-busy', 'true')
      btn.innerHTML = `<span class="rb-btn-spinner" aria-hidden="true"></span><span style="margin-left:8px">${escapeHtml(
        loadingText,
      )}</span>`
    } else if (inputBtn) {
      inputBtn.disabled = true
      inputBtn.classList.add('rb-btn-loading')
      inputBtn.setAttribute('aria-busy', 'true')
      inputBtn.value = loadingText
    }

    const allSubmit = form.querySelectorAll("button[type='submit'], input[type='submit']")
    allSubmit.forEach((el) => {
      if (el === target) return
      el.setAttribute('disabled', 'disabled')
    })
  }

  const escapeHtml = (value) => {
    return String(value)
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;')
      .replaceAll('"', '&quot;')
      .replaceAll("'", '&#39;')
  }

  const bind = () => {
    document.addEventListener(
      'click',
      (e) => {
        const a = e.target?.closest?.('a')
        if (shouldSkipForLink(a)) return
        const href = a.getAttribute('href') || ''
        const mode = a.getAttribute('data-rb-loader')
        const isFilament = document.documentElement.classList.contains('fi')
        if (mode === 'full') {
          scheduleShow(SHOW_DELAY_NAV_MS)
          return
        }
        if (isFilament) {
          if (a.hasAttribute('wire:navigate')) {
            scheduleShow(SHOW_DELAY_NAV_MS)
          }
          return
        }
        if (isInternalNavigation(href)) {
          scheduleShow(SHOW_DELAY_NAV_MS)
        }
      },
      true,
    )

    document.addEventListener(
      'submit',
      (e) => {
        const form = e.target
        if (!(form instanceof HTMLFormElement)) return
        if (form.hasAttribute('data-rb-no-loader')) return
        const isFilament = document.documentElement.classList.contains('fi')
        const isLivewireForm = form
          .getAttributeNames()
          .some((name) => name.startsWith('wire:submit'))

        if (
          (!isFilament && !isLivewireForm) ||
          form.hasAttribute('data-rb-btn-loading')
        ) {
          applyButtonLoading(form, e.submitter)
        }

        const mode = form.getAttribute('data-rb-loader')
        if (mode === 'full') {
          scheduleShow(SHOW_DELAY_FULL_SUBMIT_MS)
        }
      },
      true,
    )

    window.addEventListener('beforeunload', () => {
      state.pageLoadHold = false
    })

    document.addEventListener('livewire:navigating', () => {
      scheduleShow(SHOW_DELAY_NAV_MS)
    })
    document.addEventListener('livewire:navigated', () => {
      hide()
    })
  }

  const init = () => {
    build()

    const isAlreadyLoaded = document.readyState === 'complete'
    state.pageLoadHold = !isAlreadyLoaded

    if (!isAlreadyLoaded) {
      showNow()
      window.addEventListener(
        'load',
        () => {
          state.pageLoadHold = false
          hide()
        },
        { once: true },
      )
    } else {
      state.pageLoadHold = false
    }

    bind()
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init, { once: true })
  } else {
    init()
  }

  window.RBLoader = {
    show: () => showNow(),
    hide: () => hide(),
    schedule: (ms = SHOW_DELAY_NAV_MS) => scheduleShow(ms),
  }
})()
