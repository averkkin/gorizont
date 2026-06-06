class Header {
    selectors = {
        root: '[data-js-header]',
        overlay: '[data-js-header-overlay]',
        burgerButton: '[data-js-header-burger-button]',
    }

    stateClasses = {
        isActive: 'is-active',
        isLock: 'is-lock',
    }

    constructor() {
        this.rootElement = document.querySelector(this.selectors.root)
        if (!this.rootElement) {
            return
        }

        this.overlayElement = this.rootElement.querySelector(this.selectors.overlay)
        this.burgerButtonElement = this.rootElement.querySelector(this.selectors.burgerButton)
        this.bindEvents()
    }

    onBurgerButtonClick = () => {
        this.burgerButtonElement.classList.toggle(this.stateClasses.isActive)
        this.overlayElement.classList.toggle(this.stateClasses.isActive)
        document.documentElement.classList.toggle(this.stateClasses.isLock)
    }

    bindEvents() {
        this.burgerButtonElement.addEventListener('click', this.onBurgerButtonClick)
    }
}

const initGlideSliders = () => {
    if (typeof Glide === 'undefined') {
        console.error('Glide is not loaded')
        return
    }

    document.querySelectorAll('[data-js-glide-slider]').forEach((sliderElement) => {
        if (!sliderElement.id || sliderElement.dataset.glideInitialized === 'true') {
            return
        }

        const slider = new Glide(`#${sliderElement.id}`, {
            type: 'carousel',
            startAt: 0,
            perView: 1,
            gap: 0,
            focusAt: 'center',
            autoplay: false,
            animationDuration: 600,
        })

        slider.mount()
        sliderElement.dataset.glideInitialized = 'true'

        sliderElement.querySelectorAll('.glide__arrows [data-glide-dir]').forEach((arrowElement) => {
            arrowElement.addEventListener('click', (event) => {
                event.preventDefault()
                slider.go(arrowElement.dataset.glideDir)
            })
        })
    })
}

const initPage = () => {
    new Header()
    initGlideSliders()
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initPage)
} else {
    initPage()
}
