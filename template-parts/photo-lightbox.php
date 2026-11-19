<?php
/**
 * Окно для просмотра фото из программы тура: листание внутри дня, клавиши ← →, свайп.
 */
?>
<dialog class="photo-lightbox" data-js-photo-lightbox aria-label="Фото из программы">
    <img class="photo-lightbox__img" src="" alt="" data-js-photo-lightbox-img>

    <button type="button" class="photo-lightbox__close" data-js-photo-close aria-label="Закрыть">
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
    </button>

    <button type="button" class="photo-lightbox__nav photo-lightbox__nav--prev" data-js-photo-prev aria-label="Предыдущее фото">
        <svg width="18" height="14" viewBox="0 0 18 14" fill="none" aria-hidden="true"><path d="M17 7H1M1 7L7 1M1 7L7 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </button>
    <button type="button" class="photo-lightbox__nav photo-lightbox__nav--next" data-js-photo-next aria-label="Следующее фото">
        <svg width="18" height="14" viewBox="0 0 18 14" fill="none" aria-hidden="true"><path d="M1 7H17M17 7L11 1M17 7L11 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </button>

    <span class="photo-lightbox__counter" data-js-photo-counter aria-live="polite"></span>
</dialog>
