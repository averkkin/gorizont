<?php
/*
 * Template Name: Расписание туров
 */

get_header();

// 1. Получаем данные для Hero-блока текущей страницы
$page_id = get_the_ID();
$hero_args = array(
        'title' => carbon_get_post_meta($page_id, 'hero_title'),
        'desc'  => carbon_get_post_meta($page_id, 'hero_desc'),
        'bg'    => carbon_get_post_meta($page_id, 'hero_bg'),
);

get_template_part('template-parts/hero-block', null, $hero_args);

// 2. Получаем тип туров для фильтрации WP_Query (выездные/местные туры или экскурсии)
$tour_type = carbon_get_post_meta($page_id, 'tour_type_filter');

// Используем заголовок страницы в качестве названия секции (например, "Расписание выездных туров")
$section_title = get_the_title();
?>

    <section class="tour-schedule">
        <div class="container inner">
            <header class="tour-schedule__header">
                <h2 class="h2"><?php echo esc_html($section_title); ?> <img src="<?php echo get_template_directory_uri(); ?>/assets/img/icons/line-decor.svg" class="h2-decor"></h2>

                <div class="tour-filter">
                    <ul>
                        <li class="active" data-filter="all">Все</li>
                        <li data-filter="popular">Популярные туры</li>
                        <li data-filter="3">3-х дневные</li>
                        <li data-filter="5">5-ти дневные</li>
                        <li data-filter="7">7-ми дневные</li>
                    </ul>
                    <div class="search">Поиск</div>
                </div>
            </header>

            <div class="tour-schedule__grid">
                <?php
                // Передаем базовый тип (например, intl_tours), чтобы внутри файла выполнился правильный SQL-запрос
                get_template_part('template-parts/tour-schedule', null, array('tour_type' => $tour_type));
                ?>
            </div>
        </div>
    </section>

<?php
get_footer();