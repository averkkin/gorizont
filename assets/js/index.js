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
        const isActive = this.burgerButtonElement.classList.toggle(this.stateClasses.isActive)

        this.overlayElement.classList.toggle(this.stateClasses.isActive, isActive)
        document.documentElement.classList.toggle(this.stateClasses.isLock, isActive)
        this.burgerButtonElement.setAttribute('aria-expanded', String(isActive))
        this.burgerButtonElement.setAttribute('aria-label', isActive ? 'Закрыть меню' : 'Открыть меню')
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

const initTourScheduleFilters = () => {
    document.querySelectorAll('.tour-schedule').forEach((scheduleElement) => {
        const filterElement = scheduleElement.querySelector('[data-js-tour-filter]')
        const searchElement = scheduleElement.querySelector('[data-js-tour-search]')
        const emptyElement = scheduleElement.querySelector('[data-js-tour-empty]')
        const tourElements = Array.from(scheduleElement.querySelectorAll('.tour-schedule__tour'))

        if (!filterElement || tourElements.length === 0) {
            return
        }

        let activeFilter = 'all'

        const normalizeText = (text) => text.trim().toLocaleLowerCase('ru-RU')

        const isMatchingDurationRange = (tourElement) => {
            const durationDays = Number(tourElement.dataset.tourDurationDays)

            if (!durationDays) {
                return false
            }

            if (activeFilter === 'duration-up-to-7') {
                return durationDays <= 7
            }

            if (activeFilter === 'duration-up-to-15') {
                return durationDays <= 15
            }

            if (activeFilter === 'duration-over-15') {
                return durationDays > 15
            }

            return false
        }

        const applyFilters = () => {
            const searchQuery = searchElement ? normalizeText(searchElement.value) : ''
            let visibleCount = 0

            tourElements.forEach((tourElement) => {
                const isMatchingFilter = activeFilter === 'all'
                    || (activeFilter === 'popular' && tourElement.dataset.tourPopular === '1')
                    || isMatchingDurationRange(tourElement)
                const isMatchingSearch = !searchQuery
                    || normalizeText(tourElement.dataset.tourSearch || '').includes(searchQuery)
                const isVisible = isMatchingFilter && isMatchingSearch

                tourElement.hidden = !isVisible

                if (isVisible) {
                    visibleCount += 1
                }
            })

            if (emptyElement) {
                emptyElement.hidden = visibleCount > 0
            }
        }

        filterElement.querySelectorAll('[data-filter]').forEach((filterItemElement) => {
            const buttonElement = filterItemElement.querySelector('button')

            if (!buttonElement) {
                return
            }

            buttonElement.addEventListener('click', () => {
                activeFilter = filterItemElement.dataset.filter || 'all'

                filterElement.querySelectorAll('[data-filter]').forEach((itemElement) => {
                    const itemButtonElement = itemElement.querySelector('button')
                    const isActive = itemElement === filterItemElement

                    itemElement.classList.toggle('active', isActive)

                    if (itemButtonElement) {
                        itemButtonElement.setAttribute('aria-pressed', String(isActive))
                    }
                })

                applyFilters()
            })
        })

        if (searchElement) {
            searchElement.addEventListener('input', applyFilters)
        }

        applyFilters()
    })
}

const initPage = () => {
    new Header()
    initGlideSliders()
    initTourScheduleFilters()
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initPage)
} else {
    initPage()
}
