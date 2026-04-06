(() => {
  const loadScript = (src, { defer = true } = {}) =>
    new Promise((resolve, reject) => {
      const el = document.createElement('script')
      el.src = src
      el.defer = defer
      el.onload = () => resolve()
      el.onerror = () => reject(new Error(`Failed to load ${src}`))
      document.head.appendChild(el)
    })

  const showBanner = (message) => {
    const existing = document.getElementById('rb-runtime-banner')
    if (existing) return

    const bar = document.createElement('div')
    bar.id = 'rb-runtime-banner'
    bar.style.position = 'fixed'
    bar.style.left = '12px'
    bar.style.right = '12px'
    bar.style.bottom = '12px'
    bar.style.zIndex = '2147483647'
    bar.style.padding = '12px 14px'
    bar.style.borderRadius = '16px'
    bar.style.background = 'rgba(15,23,42,0.95)'
    bar.style.color = '#fff'
    bar.style.fontFamily = 'ui-sans-serif,system-ui,-apple-system,Segoe UI,Roboto,Inter,Arial'
    bar.style.fontSize = '13px'
    bar.style.fontWeight = '700'
    bar.style.boxShadow = '0 20px 60px rgba(0,0,0,0.35)'
    bar.textContent = message

    const btn = document.createElement('button')
    btn.type = 'button'
    btn.textContent = 'Reload'
    btn.style.marginLeft = '10px'
    btn.style.padding = '8px 10px'
    btn.style.borderRadius = '12px'
    btn.style.border = '1px solid rgba(255,255,255,0.2)'
    btn.style.background = 'rgba(255,255,255,0.08)'
    btn.style.color = '#fff'
    btn.style.cursor = 'pointer'
    btn.addEventListener('click', () => window.location.reload())

    bar.appendChild(btn)
    document.body.appendChild(bar)
  }

  const start = async () => {
    const base = window.location.origin
    const alpineLocal = `${base}/js/alpine.min.js`
    const loaderLocal = `${base}/js/rb-loader.js`

    try {
      await loadScript(alpineLocal)
    } catch {
      try {
        await loadScript('https://unpkg.com/alpinejs@3.14.9/dist/cdn.min.js')
      } catch {
        showBanner('Alpine failed to load. Buttons may not work.')
        return
      }
    }

    try {
      await loadScript(loaderLocal)
    } catch {
      showBanner('App runtime failed to load. Buttons may not work.')
      return
    }

    window.setTimeout(() => {
      if (!window.Livewire) {
        showBanner('Livewire is not loaded. Booking actions may not work.')
      }
    }, 2000)
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', start, { once: true })
  } else {
    start()
  }
})()

