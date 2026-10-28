<?php
/**
 * Карточка отзыва: скриншот из мессенджера или текстовый отзыв (если скрина нет).
 * Используется внутри цикла WP_Query по типу записи reviews.
 */

$review_id     = get_the_ID();
$review_author = get_the_title();
$screenshot_id = gorizont_review_screenshot_id($review_id);
$caption       = carbon_get_post_meta($review_id, 'review_caption');

if ($screenshot_id) :
    $full_src = wp_get_attachment_image_url($screenshot_id, 'full');
    ?>
    <article class="review-shot">
        <button
                type="button"
                class="review-shot__open"
                data-js-review-open
                data-full="<?php echo esc_url($full_src); ?>"
                aria-label="<?php echo esc_attr(sprintf('Открыть отзыв: %s', $review_author)); ?>"
        >
            <?php echo wp_get_attachment_image($screenshot_id, 'large', false, array(
                'class'   => 'review-shot__img',
                'alt'     => sprintf('Отзыв: %s', $review_author),
                'loading' => 'lazy',
            )); ?>
            <span class="review-shot__zoom" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none">
                    <path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
        </button>
        <div class="review-shot__meta">
            <h3 class="review-shot__name"><?php echo esc_html($review_author); ?></h3>
            <?php if (!empty($caption)) : ?>
                <p class="review-shot__caption"><?php echo esc_html($caption); ?></p>
            <?php endif; ?>
        </div>
    </article>
<?php else :
    $rating = (int) carbon_get_post_meta($review_id, 'review_rating');
    $rating = max(1, min(5, $rating ?: 5));
    $review_initial = function_exists('mb_substr') ? mb_substr($review_author, 0, 1) : substr($review_author, 0, 1);
    $review_text = carbon_get_post_meta($review_id, 'review_text');
    $avatar_id = (int) carbon_get_post_meta($review_id, 'review_avatar') ?: (int) get_post_thumbnail_id($review_id);
    ?>
    <article class="review-card">
        <div class="review-card__text">
            <?php if (!empty($review_text)) : ?>
                <?php echo wpautop(esc_html($review_text)); ?>
            <?php else : ?>
                <?php the_content(); ?>
            <?php endif; ?>
        </div>
        <div class="review-card__author">
            <div class="review-card__avatar">
                <?php if ($avatar_id) : ?>
                    <?php echo wp_get_attachment_image($avatar_id, 'thumbnail', false, array('alt' => $review_author)); ?>
                <?php else : ?>
                    <span class="review-card__placeholder"><?php echo esc_html($review_initial); ?></span>
                <?php endif; ?>
            </div>
            <div class="review-card__meta">
                <div class="review-card__stars" role="img" aria-label="<?php echo esc_attr($rating); ?> из 5">
                    <?php for ($star_index = 1; $star_index <= 5; $star_index++) : ?>
                        <svg class="review-card__star" viewBox="0 0 15 15" aria-hidden="true" focusable="false">
                            <path d="M7.5 0.5L9.35 5.34L14.5 5.62L10.48 8.86L11.84 13.5L7.5 10.7L3.16 13.5L4.52 8.86L0.5 5.62L5.65 5.34L7.5 0.5Z" fill="<?php echo esc_attr($star_index <= $rating ? 'currentColor' : '#14141420'); ?>"/>
                        </svg>
                    <?php endfor; ?>
                </div>
                <h3 class="review-card__name"><?php echo esc_html($review_author); ?></h3>
            </div>
        </div>
    </article>
<?php endif;
