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
        'fade'  => (bool) carbon_get_post_meta($page_id, 'schedule_about_text'),
);

?>
<main id="primary" class="site-main has-texture">
<?php
get_template_part('template-parts/hero-block', null, $hero_args);
get_template_part('template-parts/schedule-about', null, array('page_id' => $page_id));

$tour_type = carbon_get_post_meta($page_id, 'tour_type_filter');

// Местные туры короткие — фильтруем по дням, школьные — по России и за рубеж, у остальных диапазоны
if ('local_tours' === $tour_type) {
    $schedule_filters = array('all' => 'Все', 'days-1' => '1 день', 'days-2' => '2 дня', 'days-3' => '3 дня');
} elseif ('kids_tours' === $tour_type) {
    $schedule_filters = array('all' => 'Все', 'region-russia' => 'По России', 'region-abroad' => 'За рубеж');
} elseif ('jeep_tours' === $tour_type) {
    $schedule_filters = array('all' => 'Все', 'region-russia' => 'Байкал', 'region-abroad' => 'Монголия');
} else {
    $schedule_filters = array('all' => 'Все', 'popular' => 'Популярные туры', 'duration-up-to-7' => 'до 7-ми дней', 'duration-up-to-15' => 'до 15-ти дней', 'duration-over-15' => 'более 15-ти дней');
}

$section_title = get_the_title();
$search_id = 'tour-schedule-search-' . $page_id;
?>

    <section class="tour-schedule" aria-labelledby="tour-schedule-title">
        <div class="container inner">
            <header class="tour-schedule__header">
                <h2 id="tour-schedule-title" class="h2"><?php echo esc_html($section_title); ?> <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/icons/line-decor.svg'); ?>" alt="" class="h2-decor" aria-hidden="true"></h2>

                <div class="tour-filter" data-js-tour-filter>
                    <ul aria-label="Фильтр туров по параметрам">
                        <?php foreach ($schedule_filters as $filter => $label) : ?>
                            <li<?php echo 'all' === $filter ? ' class="active"' : ''; ?> data-filter="<?php echo esc_attr($filter); ?>"><button type="button" aria-pressed="<?php echo 'all' === $filter ? 'true' : 'false'; ?>"><?php echo esc_html($label); ?></button></li>
                        <?php endforeach; ?>
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
