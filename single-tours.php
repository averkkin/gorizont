<?php
get_header();

$tour_id = get_the_ID();

// Получаем тип, чтобы знать, выводить ли команду
$tour_type = carbon_get_post_meta($tour_id, 'tour_type');

// Собираем базовые мета-поля
$duration   = carbon_get_post_meta($tour_id, 'tour_duration_days');
$difficulty = carbon_get_post_meta($tour_id, 'tour_difficulty');
$format     = carbon_get_post_meta($tour_id, 'tour_format');
$price      = carbon_get_post_meta($tour_id, 'tour_price');
$hero_description = carbon_get_post_meta($tour_id, 'tour_hero_description');

$way = 'echo get_template_directory_uri();';
?>

    <section class="hero">
        <div class="container inner">
            <div class="hero__content">
                <div class="hero__heading">
                    <h1 class="h1"><?php the_title(); ?></h1>
                    <?php the_content(); ?>
                </div>
                <div class="hero__buttons">
                    <div class="actions">
                        <a href="#login-form" rel="modal:open"><button class="button">выбрать маршрут</button></a>
                        <button class="button button--outline">написать в WhatsApp</button>
                    </div>
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/icons/Arrow-Down-Circle.svg" alt="Arrow Down" class="hero__arrow-down">
                </div>
            </div>
        </div>
        <div class="bg-blur-matrix"></div>
        <div class="bg-blur-gradient"></div>
        <img src="<?php echo get_the_post_thumbnail_url($tour_id, 'full'); ?>" alt="Горы" width="1920" height="847" class="background">
    </section>

    <div class="single-tour__inner">
        <section class="tour-description container">
            <div class="inner">
                <div class="tour-description__content">
                    <h2 class="h2">Описание тура</h2>
                    <div class="tour-description__text">
                        <?php if (!empty($hero_description)) : ?>
                            <?php echo wp_kses_post(wpautop($hero_description)); ?>
                        <?php elseif (has_excerpt()) : ?>
                            <p><?php echo esc_html(get_the_excerpt()); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="tour-description__meta-grid">
                    <div class="group-meta">
                        <span class="line-meta"></span>
                        <div class="group">
                            <span class="key">Продолжительность:</span>
                            <span class="value"><?php echo esc_html($duration); ?></span>
                        </div>
                    </div>
                    <div class="group-meta">
                        <span class="line-meta"></span>
                        <div class="group">
                            <span class="key">Сложность:</span>
                            <span class="value"><?php echo esc_html($difficulty); ?></span>
                        </div>
                    </div>
                    <div class="group-meta">
                        <span class="line-meta"></span>
                        <div class="group">
                            <span class="key">Формат:</span>
                            <span class="value"><?php echo esc_html($format); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/icons/bg-pattern.svg" class="bg-pattern">
        </section>
        <div class="bg-white"></div>
    </div>

    <section class="tour-timeline">
        <div class="container">
            <h2 class="h2">Программа по дням</h2>
            <div class="timeline">
                <?php
                $program = carbon_get_post_meta($tour_id, 'tour_program');
                if (!empty($program)) :
                    foreach ($program as $day_index => $day) : ?>
                        <?php
                        $day_images = array();

                        if (!empty($day['day_images']) && is_array($day['day_images'])) {
                            $day_images = array_filter(array_map('absint', $day['day_images']));
                        }

                        if (empty($day_images) && !empty($day['day_img'])) {
                            $day_images[] = $day['day_img'];
                        }

                        $day_images_count = count($day_images);
                        $day_slider_id = 'timeline-gallery-' . $day_index;
                        ?>
                        <div class="timeline__item">
                            <div class="timeline__content">
                                <span class="day-num"><?php echo esc_html($day['day_number']); ?></span>
                                <h3 class="h3"><?php echo esc_html($day['day_title']); ?></h3>
                                <div class="day-text">
                                    <?php if (!empty($day['day_points'])) : ?>
                                        <ul>
                                            <?php foreach ($day['day_points'] as $point) : ?>
                                                <?php if (!empty($point['point_text'])) : ?>
                                                    <li><?php echo esc_html($point['point_text']); ?></li>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php if (!empty($day_images)) : ?>
                                <div class="timeline__image">
                                    <?php if ($day_images_count > 1) : ?>
                                        <div class="glide timeline-gallery" id="<?php echo esc_attr($day_slider_id); ?>" data-js-glide-slider>
                                            <div class="glide__track" data-glide-el="track">
                                                <div class="glide__slides">
                                                    <?php foreach ($day_images as $image_id) : ?>
                                                        <div class="glide__slide timeline-gallery__slide">
                                                            <?php echo wp_get_attachment_image($image_id, 'large', false, array('class' => 'timeline-gallery__img')); ?>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>

                                            <div class="glide__arrows">
                                                <button type="button" class="glide__arrow glide__arrow--left" data-glide-dir="<" aria-label="Предыдущий слайд">
                                                    <svg width="18" height="14" viewBox="0 0 18 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M17 7H1M1 7L7 1M1 7L7 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                </button>

                                                <button type="button" class="glide__arrow glide__arrow--right" data-glide-dir=">" aria-label="Следующий слайд">
                                                    <svg width="18" height="14" viewBox="0 0 18 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M1 7H17M17 7L11 1M17 7L11 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                </button>
                                            </div>

                                            <div class="glide__bullets" data-glide-el="controls[nav]">
                                                <?php foreach ($day_images as $image_index => $image_id) : ?>
                                                    <button class="glide__bullet" data-glide-dir="=<?php echo esc_attr($image_index); ?>" aria-label="Перейти к слайду <?php echo esc_attr($image_index + 1); ?>"></button>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php elseif (is_numeric($day_images[0])) : ?>
                                        <?php echo wp_get_attachment_image((int) $day_images[0], 'large', false, array('class' => 'timeline-gallery__img')); ?>
                                    <?php else : ?>
                                        <img src="<?php echo esc_url($day_images[0]); ?>" alt="<?php echo esc_attr($day['day_title']); ?>" class="timeline-gallery__img">
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach;
                endif; ?>
            </div>
        </div>
    </section>

    <section class="tour-conditions">
        <div class="tour-conditions__inner container">
            <h2 class="h2">Условия участия</h2>
            <div class="conditions-grid">
                <div class="included">
                    <h3 class="h3">Включено в стоимость</h3>
                    <ul>
                        <?php
                        $included = carbon_get_post_meta($tour_id, 'tour_included');
                        $included_lines = explode("\n", str_replace("\r", "", $included));
                        foreach ($included_lines as $line) {
                            if (!empty(trim($line))) echo '<li><span class="box"><img src="' . get_template_directory_uri() . '/assets/img/icons/check.svg"></span>' . esc_html($line) . '</li>';
                        }
                        ?>
                    </ul>
                </div>
                <div class="excluded">
                    <h3>Исключено</h3>
                    <ul>
                        <?php
                        $excluded = carbon_get_post_meta($tour_id, 'tour_excluded');
                        $excluded_lines = explode("\n", str_replace("\r", "", $excluded));
                        foreach ($excluded_lines as $line) {
                            if (!empty(trim($line))) echo '<li><span class="box"><img src="' . get_template_directory_uri() . '/assets/img/icons/close.svg"></span>' . esc_html($line) . '</li>';;
                        }
                        ?>
                    </ul>
                </div>
            </div>
            <div class="price-block">
                <div class="price-block__grid">
                    <span class="price-block__label">Стоимость</span>
                    <span class="price-block__value">от <span><?php echo esc_html($price); ?>₽</span> за участника</span>
                </div>
                <button class="button price-block__button">забронировать экскурсию</button>
            </div>
        </div>

        <img src="<?= get_template_directory_uri(); ?>/assets/img/icons/bg-pattern-lines.svg" alt="" class="tour-conditions__bg">

    </section>

<?php
$gallery = carbon_get_post_meta($tour_id, 'tour_gallery');
if (!empty($gallery)) : ?>
<!--    <section class="tour-gallery">-->
<!--        <div class="container">-->
<!--            <h2 class="h2">Галерея тура</h2>-->
<!---->
<!--            <div class="glide gallery-slider" id="gallery-slider" data-js-glide-slider>-->
<!---->
<!--                <div class="glide__track" data-glide-el="track">-->
<!--                    <div class="glide__slides">-->
<!--                        --><?php //foreach ($gallery as $img_id) : ?>
<!--                            <div class="glide__slide gallery-slide">-->
<!--                                --><?php //echo wp_get_attachment_image($img_id, 'large', false, array('class' => 'gallery-slide__img')); ?>
<!--                            </div>-->
<!--                        --><?php //endforeach; ?>
<!--                    </div>-->
<!--                </div>-->
<!---->
<!--                <div class="glide__arrows" data-glide-el="controls">-->
<!--                    <button class="glide__arrow glide__arrow--left" data-glide-dir="<" aria-label="Предыдущий слайд">-->
<!--                        <svg width="18" height="14" viewBox="0 0 18 14" fill="none" xmlns="http://www.w3.org/2000/svg">-->
<!--                            <path d="M17 7H1M1 7L7 1M1 7L7 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>-->
<!--                        </svg>-->
<!--                    </button>-->
<!--                    <button class="glide__arrow glide__arrow--right" data-glide-dir=">" aria-label="Следующий слайд">-->
<!--                        <svg width="18" height="14" viewBox="0 0 18 14" fill="none" xmlns="http://www.w3.org/2000/svg">-->
<!--                            <path d="M1 7H17M17 7L11 1M17 7L11 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>-->
<!--                        </svg>-->
<!--                    </button>-->
<!--                </div>-->
<!---->
<!--                <div class="glide__bullets" data-glide-el="controls[nav]">-->
<!--                    --><?php //foreach (array_values($gallery) as $index => $img_id) : ?>
<!--                        <button class="glide__bullet" data-glide-dir="=--><?php //echo $index; ?><!--" aria-label="Перейти к слайду --><?php //echo $index + 1; ?><!--"></button>-->
<!--                    --><?php //endforeach; ?>
<!--                </div>-->
<!---->
<!--            </div>-->
<!--        </div>-->
<!--    </section>-->

<?php endif; ?>

<?php //if ($tour_type !== 'local_excursions' && $tour_type !== 'intl_excursions') : ?>
<!--    <section class="tour-team">-->
<!--        <div class="container">-->
<!--            <h2 class="h2">Наша команда в этом туре</h2>-->
<!--            <div class="team-grid">-->
<!--                --><?php
//                $guides = carbon_get_post_meta($tour_id, 'tour_guides');
//                if (!empty($guides)) :
//                    foreach ($guides as $guide_item) :
//                        $guide_id = $guide_item['id'];
//                        ?>
<!--                        <div class="guide-card">-->
<!--                            --><?php //echo get_the_post_thumbnail($guide_id, 'thumbnail'); ?>
<!--                            <h3>--><?php //echo get_the_title($guide_id); ?><!--</h3>-->
<!--                            <p>--><?php //echo get_the_excerpt($guide_id); ?><!--</p>-->
<!--                        </div>-->
<!--                    --><?php //endforeach;
//                else: ?>
<!--                    <p>Инструкторы будут назначены позже.</p>-->
<!--                --><?php //endif; ?>
<!--            </div>-->
<!--        </div>-->
<!--    </section>-->
<?php //endif; ?>

<?php
$selected_reviews = carbon_get_post_meta($tour_id, 'tour_reviews');
$selected_review_ids = array();

if (!empty($selected_reviews)) {
    foreach ($selected_reviews as $review_item) {
        if (!empty($review_item['id'])) {
            $selected_review_ids[] = (int) $review_item['id'];
        }
    }
}

$reviews_args = array(
    'post_type'      => 'reviews',
    'posts_per_page' => 6,
    'post_status'    => 'publish',
);

if (!empty($selected_review_ids)) {
    $reviews_args['post__in'] = $selected_review_ids;
    $reviews_args['orderby'] = 'post__in';
}

$reviews_query = new WP_Query($reviews_args);
$reviews_archive_url = get_post_type_archive_link('reviews');
?>

<?php if ($reviews_query->have_posts()) : ?>
    <section class="tour-reviews">
        <div class="container">
            <div class="tour-reviews__header">
                <h2 class="h2">Мы ценим ваше доверие</h2>
                <?php if (!empty($reviews_archive_url)) : ?>
                    <a class="button tour-reviews__button" href="<?php echo esc_url($reviews_archive_url); ?>">смотреть все отзывы</a>
                <?php endif; ?>
            </div>

            <div class="tour-reviews__grid">
                <?php while ($reviews_query->have_posts()) : $reviews_query->the_post(); ?>
                    <?php
                    $review_id = get_the_ID();
                    $rating = (int) carbon_get_post_meta($review_id, 'review_rating');
                    $rating = max(1, min(5, $rating ?: 5));
                    $review_author = get_the_title();
                    $review_initial = function_exists('mb_substr') ? mb_substr($review_author, 0, 1) : substr($review_author, 0, 1);
                    ?>
                    <article class="review-card">
                        <div class="review-card__text">
                            <?php the_content(); ?>
                        </div>
                        <div class="review-card__author">
                            <div class="review-card__avatar">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php echo get_the_post_thumbnail($review_id, 'thumbnail'); ?>
                                <?php else : ?>
                                    <span class="review-card__placeholder"><?php echo esc_html($review_initial); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="review-card__meta">
                                <div class="review-card__stars" aria-label="<?php echo esc_attr($rating); ?> из 5">
                                    <?php for ($star_index = 1; $star_index <= 5; $star_index++) : ?>
                                        <svg class="review-card__star" viewBox="0 0 15 15" aria-hidden="true" focusable="false">
                                            <path d="M7.5 0.5L9.35 5.34L14.5 5.62L10.48 8.86L11.84 13.5L7.5 10.7L3.16 13.5L4.52 8.86L0.5 5.62L5.65 5.34L7.5 0.5Z" fill="<?php echo $star_index <= $rating ? 'currentColor' : '#14141420'; ?>"/>
                                        </svg>
                                    <?php endfor; ?>
                                </div>
                                <h3 class="review-card__name"><?php echo esc_html($review_author); ?></h3>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php
$more_info = carbon_get_post_meta($tour_id, 'tour_more_info');
$more_info_sections = array();

if (is_array($more_info)) {
    foreach ($more_info as $section) {
        $more_info_items = !empty($section['more_info_items']) && is_array($section['more_info_items'])
            ? array_filter($section['more_info_items'], function ($item) {
                return !empty($item['more_info_text']);
            })
            : array();

        if (!empty($section['more_info_title']) || !empty($more_info_items)) {
            $section['more_info_items'] = $more_info_items;
            $more_info_sections[] = $section;
        }
    }
}
?>

<?php if (!empty($more_info_sections)) : ?>
    <section class="more-info">
        <div class="more-info__inner container">
            <h2 class="h2">Дополнительная информация</h2>
            <div class="more-info__content">
                <ul>
                    <?php foreach ($more_info_sections as $section) : ?>
                        <li>
                            <?php if (!empty($section['more_info_title'])) : ?>
                                <h3 class="h3"><?php echo esc_html($section['more_info_title']); ?></h3>
                            <?php endif; ?>

                            <?php if (!empty($section['more_info_items'])) : ?>
                                <div class="more-info__text-items">
                                    <?php foreach ($section['more_info_items'] as $item) : ?>
                                        <span><?php echo esc_html($item['more_info_text']); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php wp_reset_postdata(); ?>

<?php
get_footer();
