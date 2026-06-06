<?php
// Принимаем тип тура из аргументов (переданных через get_template_part)
$current_tour_type = !empty($args['tour_type']) ? $args['tour_type'] : 'all';

$query_args = array(
        'post_type'      => 'tours',
        'posts_per_page' => -1,
        'meta_query'     => array(),
);

// Фильтруем по типу (местные/выездные туры или экскурсии)
if ($current_tour_type !== 'all') {
    $query_args['meta_query'][] = array(
            'key'     => '_tour_type',
            'value'   => $current_tour_type,
            'compare' => '=',
    );
}

$tours_loop = new WP_Query($query_args);

if ($tours_loop->have_posts()) :
    while ($tours_loop->have_posts()) : $tours_loop->the_post();

        $tour_id = get_the_ID();

        // Извлекаем кастомные поля Carbon Fields для карточки
        $tour_date       = carbon_get_post_meta($tour_id, 'tour_start_date'); // Например: "28 июня 2026"
        $tour_difficulty = intval(carbon_get_post_meta($tour_id, 'tour_difficulty_stars')); // Число от 1 до 5
        $tour_cover      = get_the_post_thumbnail_url($tour_id, 'large');
        $tour_type       = carbon_get_post_meta($tour_id, 'tour_type');
        $tour_duration   = carbon_get_post_meta($tour_id, 'tour_duration');
        $tour_duration_days = carbon_get_post_meta($tour_id, 'tour_duration_days');
        $is_popular      = carbon_get_post_meta($tour_id, 'is_popular') ? '1' : '0';
        $duration_days_count = 0;

        if (preg_match('/\d+/', (string) $tour_duration_days, $duration_matches)) {
            $duration_days_count = (int) $duration_matches[0];
        } elseif (is_numeric($tour_duration)) {
            $duration_days_count = (int) $tour_duration;
        } elseif ($tour_duration === 'other') {
            $duration_days_count = 1;
        }

        $search_text     = trim(get_the_title() . ' ' . get_the_excerpt() . ' ' . $tour_date . ' ' . $tour_duration_days);
        $search_text     = function_exists('mb_strtolower') ? mb_strtolower($search_text, 'UTF-8') : strtolower($search_text);

        // Fallback на дефолтную картинку, если в посте нет миниатюры
        if (!$tour_cover) {
            $tour_cover = get_template_directory_uri() . '/assets/img/images/item-tour-1.jpg';
        }
        ?>

        <article
                class="tour-schedule__tour"
                data-tour-type="<?php echo esc_attr($tour_type); ?>"
                data-tour-duration="<?php echo esc_attr($tour_duration); ?>"
                data-tour-duration-days="<?php echo esc_attr($duration_days_count); ?>"
                data-tour-popular="<?php echo esc_attr($is_popular); ?>"
                data-tour-search="<?php echo esc_attr($search_text); ?>"
        >
            <div class="content">
                <div class="tour-schedule__head">
                    <span class="title">Дата</span>
                    <time class="date"><?php echo esc_html($tour_date); ?></time>
                </div>
                <div class="tour-schedule__info">

                    <div class="heading">
                        <div class="tour-header">
                            <div class="level" aria-label="<?php echo esc_attr(sprintf('Сложность: %d из 5', $tour_difficulty)); ?>">
                                <span>Сложность:</span>
                                <div class="levels" aria-hidden="true">
                                    <?php
                                    for ($i = 1; $i <= 5; $i++) {
                                        $class = ($i > $tour_difficulty) ? ' class="disable"' : '';
                                        echo '<img src="' . esc_url(get_template_directory_uri() . '/assets/img/icons/level.svg') . '" alt=""' . $class . '>';
                                    }
                                    ?>
                                </div>
                            </div>
                            <h3 class="title"><?php the_title(); ?></h3>
                        </div>
                        <p class="subtitle"><?php echo esc_html(get_the_excerpt()); ?></p>
                    </div>

                    <div class="actions">
                        <a class="button" href="<?php the_permalink(); ?>">узнать больше</a>
                        <button type="button" class="button button--outline">написать в WhatsApp</button>
                    </div>
                </div>
            </div>
            <img src="<?php echo esc_url($tour_cover); ?>" class="cover" alt="<?php the_title_attribute(); ?>">
        </article>

    <?php
    endwhile;
    wp_reset_postdata();
else :
    echo '<p>В этой категории пока нет доступных туров.</p>';
endif;
?>
