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

?>
<main id="primary" class="site-main">
<?php
get_template_part('template-parts/hero-block', null, $hero_args);

// 2. Получаем тип туров для фильтрации WP_Query (выездные/местные туры или экскурсии)
$tour_type = carbon_get_post_meta($page_id, 'tour_type_filter');

// Используем заголовок страницы в качестве названия секции (например, "Расписание выездных туров")
$section_title = get_the_title();
?>

    <section class="tour-schedule" aria-labelledby="tour-schedule-title">
        <div class="container inner">
            <header class="tour-schedule__header">
                <h2 id="tour-schedule-title" class="h2"><?php echo esc_html($section_title); ?> <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/icons/line-decor.svg'); ?>" alt="" class="h2-decor" aria-hidden="true"></h2>

                <div class="tour-filter">
                    <ul>
                        <li class="active" data-filter="all"><button type="button" aria-pressed="true">Все</button></li>
                        <li data-filter="popular"><button type="button" aria-pressed="false">Популярные туры</button></li>
                        <li data-filter="3"><button type="button" aria-pressed="false">3-х дневные</button></li>
                        <li data-filter="5"><button type="button" aria-pressed="false">5-ти дневные</button></li>
                        <li data-filter="7"><button type="button" aria-pressed="false">7-ми дневные</button></li>
                    </ul>
                    <button type="button" class="search">Поиск</button>
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
</main>

<?php
get_footer();
