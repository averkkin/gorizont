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

        const isHeroSlider = sliderElement.classList.contains('hero-glide')
        const slider = new Glide(`#${sliderElement.id}`, {
            type: 'carousel',
            startAt: 0,
            perView: 1,
            gap: 0,
            focusAt: 'center',
            autoplay: isHeroSlider ? 3000 : false,
            animationDuration: 600,
        })

        const currentSlideElement = sliderElement.querySelector('#current')
        const totalSlidesElement = sliderElement.querySelector('#total')
        const updateCounter = () => {
            if (!currentSlideElement || !totalSlidesElement) {
                return
            }

            currentSlideElement.textContent = String(slider.index + 1).padStart(2, '0')
        }

        if (totalSlidesElement) {
            totalSlidesElement.textContent = String(sliderElement.querySelectorAll('.glide__slide').length).padStart(2, '0')
        }

        slider.on(['mount.after', 'run'], updateCounter)

        // Текст хиро анимируется только при загрузке, при листании — нет
        if (isHeroSlider) {
            slider.on('run', () => {
                sliderElement.dataset.motionDone = ''
            })
        }
        slider.mount()
        updateCounter()
        sliderElement.dataset.glideInitialized = 'true'

        sliderElement.querySelectorAll('.glide__arrows [data-glide-dir]').forEach((arrowElement) => {
            if (arrowElement.closest('[data-glide-el]')) {
                return
            }

            arrowElement.addEventListener('click', (event) => {
                event.preventDefault()
                slider.go(arrowElement.dataset.glideDir)
            })
        })
    })
}

const initReviewsSliders = () => {
    if (typeof Glide === 'undefined') {
        return
    }

    document.querySelectorAll('[data-js-reviews-slider]').forEach((sliderElement) => {
        if (!sliderElement.id || sliderElement.dataset.glideInitialized === 'true') {
            return
        }

        const slidesCount = sliderElement.querySelectorAll('.glide__slide').length
        const prevElement = sliderElement.querySelector('[data-js-reviews-prev]')
        const nextElement = sliderElement.querySelector('[data-js-reviews-next]')
        const progressElement = sliderElement.querySelector('[data-js-reviews-progress]')
        const controlsElement = sliderElement.querySelector('.reviews-slider__controls')

        if (slidesCount < 2) {
            if (controlsElement) {
                controlsElement.hidden = true
            }
            return
        }

        const slider = new Glide(`#${sliderElement.id}`, {
            type: 'carousel',
            startAt: 0,
            perView: 3,
            focusAt: 'center',
            gap: 0,
            animationDuration: 600,
            breakpoints: {
                1023: { perView: 2 },
                767: { perView: 1.35 },
            },
        })

        const updateProgress = () => {
            if (!progressElement) {
                return
            }

            progressElement.style.width = `${100 / slidesCount}%`
            progressElement.style.left = `${(slider.index / slidesCount) * 100}%`
        }

        slider.on(['mount.after', 'run'], updateProgress)
        slider.mount()
        sliderElement.dataset.glideInitialized = 'true'

        prevElement?.addEventListener('click', () => slider.go('<'))
        nextElement?.addEventListener('click', () => slider.go('>'))

        // Клик по боковому слайду сдвигает его в центр, а не открывает скрин
        sliderElement.addEventListener('click', (event) => {
            const slideElement = event.target.closest('.glide__slide')

            if (!slideElement || slideElement.classList.contains('glide__slide--active')) {
                return
            }

            event.preventDefault()
            event.stopPropagation()

            const sliderRect = sliderElement.getBoundingClientRect()
            const slideRect = slideElement.getBoundingClientRect()
            const isLeft = slideRect.left + slideRect.width / 2 < sliderRect.left + sliderRect.width / 2

            slider.go(isLeft ? '<' : '>')
        }, true)
    })
}

const initReviewLightbox = () => {
    const lightboxElement = document.querySelector('[data-js-review-lightbox]')

    if (!lightboxElement || typeof lightboxElement.showModal !== 'function') {
        return
    }

    const imageElement = lightboxElement.querySelector('[data-js-review-lightbox-img]')

    document.addEventListener('click', (event) => {
        const openElement = event.target.closest('[data-js-review-open]')

        if (!openElement) {
            return
        }

        const previewElement = openElement.querySelector('img')

        imageElement.src = openElement.dataset.full
        imageElement.alt = previewElement ? previewElement.alt : ''
        lightboxElement.showModal()
        lightboxElement.scrollTop = 0
    })

    lightboxElement.addEventListener('click', (event) => {
        if (event.target === lightboxElement || event.target.closest('[data-js-review-close]')) {
            lightboxElement.close()
        }
    })

    lightboxElement.addEventListener('close', () => {
        imageElement.removeAttribute('src')
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

                // Перезапускаем анимацию появления карточек
                const gridElement = scheduleElement.querySelector('.tour-schedule__grid')
                gridElement?.classList.remove('is-filtering')
                void gridElement?.offsetWidth
                gridElement?.classList.add('is-filtering')
            })
        })

        if (searchElement) {
            searchElement.addEventListener('input', applyFilters)
        }

        applyFilters()
    })
}

const cleanText = (element) => (element?.textContent || '').replace(/\s+/g, ' ').trim()

// Откуда открыли форму: текст кнопки и блок, в котором она стоит
const getLeadSource = (link) => {
    let place = ''

    if (link.closest('header')) {
        place = 'шапка сайта'
    } else if (link.closest('footer')) {
        place = 'подвал сайта'
    } else {
        for (let parent = link.parentElement; parent && parent !== document.body; parent = parent.parentElement) {
            const heading = parent.querySelector('h1:not(.visually-hidden), h2:not(.visually-hidden)')
            if (heading) {
                place = `блок «${cleanText(heading)}»`
                break
            }
        }
    }

    return `«${cleanText(link)}»` + (place ? `, ${place}` : '')
}

const initLeadForms = () => {
    document.addEventListener('click', (event) => {
        const link = event.target.closest('a[rel="modal:open"]')
        const form = link && document.querySelector(link.getAttribute('href'))

        if (form?.matches('[data-js-lead-form]')) {
            form.dataset.source = getLeadSource(link)
        }
    }, true)

    document.querySelectorAll('[data-js-lead-form]').forEach((form) => {
        const status = form.querySelector('[data-js-lead-status]')
        const button = form.querySelector('button[type="submit"]')

        form.addEventListener('submit', async (event) => {
            event.preventDefault()
            const data = new FormData(form)
            data.append('action', 'gorizont_lead')
            data.append('page', window.location.href)
            data.append('form', form.id)
            data.append('source', form.dataset.source || '')
            button.disabled = true
            status.classList.remove('is-error')
            status.textContent = 'Отправляем...'

            try {
                const response = await fetch(window.gorizontLead.url, {method: 'POST', body: data})
                const result = await response.json()
                if (!result.success) {
                    throw new Error(result.data?.message || 'Ошибка отправки')
                }
                status.textContent = 'Спасибо! Мы скоро свяжемся с вами'
                form.reset()
                setTimeout(() => {
                    if (form.classList.contains('modal') && window.jQuery?.modal) {
                        window.jQuery.modal.close()
                    }
                    status.textContent = ''
                }, 2000)
            } catch (error) {
                status.classList.add('is-error')
                status.textContent = error.message || 'Ошибка отправки'
            } finally {
                button.disabled = false
            }
        })
    })
}

// Блоки, которые мягко появляются при прокрутке. Хиро анимируется отдельно, в CSS
const revealSelectors = [
    '.direction__content > *',
    '.tour-directions__item',
    '.tour-schedule__header',
    '.tour-schedule__tour',
    '.tour-description__content > *',
    '.tour-timeline .h2',
    '.timeline__item',
    '.tour-conditions .h2',
    '.conditions-grid > *',
    '.price-block',
    '.tour-reviews__header',
    '.reviews-slider',
    '.reviews-wall__item',
    '.more-info__content > *',
    '.legal__header',
    '.legal-list__item',
    '.footer__head',
]

const initMotion = () => {
    if (window.jQuery?.modal) {
        Object.assign(window.jQuery.modal.defaults, {fadeDuration: 200, fadeDelay: 0.4})
    }

    if (!('IntersectionObserver' in window) || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return
    }

    const elements = document.querySelectorAll(revealSelectors.join(','))
    const images = document.querySelectorAll('.direction__image')

    elements.forEach((element) => {
        element.dataset.reveal = ''
    })
    images.forEach((element) => {
        element.dataset.reveal = 'image'
    })
    document.documentElement.classList.add('has-motion')

    const observer = new IntersectionObserver((entries) => {
        // Блоки, попавшие в экран одновременно, появляются по очереди
        let order = 0

        entries.forEach((entry) => {
            if (!entry.isIntersecting) {
                return
            }

            const element = entry.target
            const delay = Math.min(order, 4) * 0.09
            order += 1

            element.style.setProperty('--reveal-delay', `${delay}s`)
            element.classList.add('is-revealed')
            observer.unobserve(element)

            // После показа убираем служебные атрибуты, чтобы не мешать ховерам и фильтрам
            setTimeout(() => {
                delete element.dataset.reveal
                element.classList.remove('is-revealed')
                element.style.removeProperty('--reveal-delay')
            }, (delay + 1.5) * 1000)
        })
    }, {rootMargin: '0px 0px -8% 0px'})

    elements.forEach((element) => observer.observe(element))
    images.forEach((element) => observer.observe(element))
}

// Линия программы по дням дорисовывается по мере прокрутки: от первой точки до последней
const initTimelineLine = () => {
    const timeline = document.querySelector('.timeline')
    const items = timeline ? timeline.querySelectorAll('.timeline__item') : []

    if (items.length < 2 || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return
    }

    let lineTop = 0
    let lineHeight = 0
    let frame = null

    // Центр точки дня относительно списка. offsetTop не учитывает сдвиг от анимации появления
    const getDotCenter = (item) => {
        const dot = getComputedStyle(item, '::before')
        return item.offsetTop + parseFloat(dot.top) + parseFloat(dot.height) / 2
    }

    const measure = () => {
        lineTop = getDotCenter(items[0])
        lineHeight = getDotCenter(items[items.length - 1]) - lineTop
        timeline.style.setProperty('--timeline-line-top', `${lineTop}px`)
        timeline.style.setProperty('--timeline-line-height', `${lineHeight}px`)
    }

    // Кончик линии идёт за точкой на 65% высоты экрана
    const update = () => {
        frame = null
        const start = timeline.getBoundingClientRect().top + lineTop
        const progress = Math.min(Math.max((window.innerHeight * 0.65 - start) / lineHeight, 0), 1)
        timeline.style.setProperty('--timeline-line-progress', progress.toFixed(4))
    }

    const requestUpdate = () => {
        if (!frame) {
            frame = requestAnimationFrame(update)
        }
    }

    measure()
    update()
    timeline.classList.add('is-line-drawing')

    // Высота дней меняется при загрузке картинок и смене ширины экрана
    new ResizeObserver(() => {
        measure()
        requestUpdate()
    }).observe(timeline)

    window.addEventListener('scroll', requestUpdate, {passive: true})
    window.addEventListener('resize', requestUpdate)
}

const initPage = () => {
    initMotion()
    initTimelineLine()
    initLeadForms()
    new Header()
    initGlideSliders()
    initReviewsSliders()
    initReviewLightbox()
    initTourScheduleFilters()
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initPage)
} else {
    initPage()
}
