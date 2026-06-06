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

        // Fallback на дефолтную картинку, если в посте нет миниатюры
        if (!$tour_cover) {
            $tour_cover = get_template_directory_uri() . '/assets/img/images/item-tour-1.jpg';
        }
        ?>

        <div class="tour-schedule__tour">
            <div class="content">
                <div class="tour-schedule__head">
                    <span class="title">Дата</span>
                    <span class="date"><?php echo esc_html($tour_date); ?></span>
                </div>
                <div class="tour-schedule__info">

                    <div class="heading">
                        <div class="tour-header">
                            <div class="level">
                                <span>Сложность:</span>
                                <div class="levels">
                                    <?php
                                    for ($i = 1; $i <= 5; $i++) {
                                        $class = ($i > $tour_difficulty) ? ' class="disable"' : '';
                                        echo '<img src="' . get_template_directory_uri() . '/assets/img/icons/level.svg" alt="level"' . $class . '>';
                                    }
                                    ?>
                                </div>
                            </div>
                            <h3 class="title"><?php the_title(); ?></h3>
                        </div>
                        <p class="subtitle"><?php echo esc_html(get_the_excerpt()); ?></p>
                    </div>

                    <div class="actions">
                        <a href="<?php the_permalink(); ?>"><button class="button">узнать больше</button></a>
                        <button class="button button--outline">написать в WhatsApp</button>
                    </div>
                </div>
            </div>
            <img src="<?php echo esc_url($tour_cover); ?>" class="cover" alt="<?php the_title_attribute(); ?>">
        </div>

    <?php
    endwhile;
    wp_reset_postdata();
else :
    echo '<p>В этой категории пока нет доступных туров.</p>';
endif;
?>