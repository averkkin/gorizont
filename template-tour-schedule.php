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

$tour_type = carbon_get_post_meta($page_id, 'tour_type_filter');

$section_title = get_the_title();
$search_id = 'tour-schedule-search-' . $page_id;
?>

    <section class="tour-schedule" aria-labelledby="tour-schedule-title">
        <div class="container inner">
            <header class="tour-schedule__header">
                <h2 id="tour-schedule-title" class="h2"><?php echo esc_html($section_title); ?> <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/icons/line-decor.svg'); ?>" alt="" class="h2-decor" aria-hidden="true"></h2>

                <div class="tour-filter" data-js-tour-filter>
                    <ul aria-label="Фильтр туров по параметрам">
                        <li class="active" data-filter="all"><button type="button" aria-pressed="true">Все</button></li>
                        <li data-filter="popular"><button type="button" aria-pressed="false">Популярные туры</button></li>
                        <li data-filter="duration-up-to-7"><button type="button" aria-pressed="false">до 7-ми дней</button></li>
                        <li data-filter="duration-up-to-15"><button type="button" aria-pressed="false">до 15-ти дней</button></li>
                        <li data-filter="duration-over-15"><button type="button" aria-pressed="false">более 15-ти дней</button></li>
                    </ul>
                    <label class="visually-hidden" for="<?php echo esc_attr($search_id); ?>">Поиск туров</label>
                    <input
                            class="search"
                            id="<?php echo esc_attr($search_id); ?>"
                            type="search"
                            name="tour-search"
                            placeholder="Поиск"
                            autocomplete="off"
                            data-js-tour-search
                    >
                </div>
            </header>

            <div class="tour-schedule__grid">
                <?php
                // Передаем базовый тип (например, intl_tours), чтобы внутри файла выполнился правильный SQL-запрос
                get_template_part('template-parts/tour-schedule', null, array('tour_type' => $tour_type));
                ?>
            </div>
            <p class="tour-schedule__empty" data-js-tour-empty hidden>По выбранным параметрам туры не найдены.</p>
        </div>
    </section>
</main>

<?php
get_footer();
