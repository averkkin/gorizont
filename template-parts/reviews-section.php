<?php
// Блок «Мы ценим ваше доверие»: слайдер отзывов. review_ids — выбранные отзывы, иначе последние
$selected_review_ids = !empty($args['review_ids']) ? array_map('intval', $args['review_ids']) : array();
$slider_id           = !empty($args['slider_id']) ? $args['slider_id'] : 'reviews-slider';

$reviews_args = array(
    'post_type'      => 'reviews',
    'posts_per_page' => 12,
    'post_status'    => 'publish',
    'orderby'        => gorizont_reviews_orderby(),
);

if (!empty($selected_review_ids)) {
    $reviews_args['post__in'] = $selected_review_ids;
    $reviews_args['orderby'] = 'post__in';
}

$reviews_query = new WP_Query($reviews_args);
?>

<?php if ($reviews_query->have_posts()) : ?>
    <section class="tour-reviews" aria-labelledby="tour-reviews-title">
        <div class="container">
            <div class="tour-reviews__header">
                <h2 id="tour-reviews-title" class="h2">Мы ценим ваше доверие</h2>
            </div>

            <div class="glide reviews-slider" id="<?php echo esc_attr($slider_id); ?>" data-js-reviews-slider>
                <div class="glide__track" data-glide-el="track">
                    <ul class="glide__slides">
                        <?php while ($reviews_query->have_posts()) : $reviews_query->the_post(); ?>
                            <li class="glide__slide reviews-slider__slide">
                                <?php get_template_part('template-parts/review-card'); ?>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>

                <div class="reviews-slider__controls">
                    <button type="button" class="reviews-slider__arrow" data-js-reviews-prev aria-label="Предыдущий отзыв">
                        <svg width="18" height="14" viewBox="0 0 18 14" fill="none" aria-hidden="true">
                            <path d="M17 7H1M1 7L7 1M1 7L7 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <div class="reviews-slider__progress" aria-hidden="true"><span data-js-reviews-progress></span></div>
                    <button type="button" class="reviews-slider__arrow" data-js-reviews-next aria-label="Следующий отзыв">
                        <svg width="18" height="14" viewBox="0 0 18 14" fill="none" aria-hidden="true">
                            <path d="M1 7H17M17 7L11 1M17 7L11 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
            </div>
            <?php wp_reset_postdata(); ?>
            <?php get_template_part('template-parts/review-lightbox'); ?>
        </div>
    </section>
<?php endif; ?>
