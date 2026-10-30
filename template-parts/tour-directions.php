<?php
$count_tours = static function ($type) {
    $query = new WP_Query(array(
        'post_type'      => 'tours',
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'no_found_rows'  => false,
        'meta_query'     => array(array('key' => '_tour_type', 'value' => $type)),
    ));
    return (int) $query->found_posts;
};
$plural_tours = static function ($n) {
    $mod100 = $n % 100;
    $mod10  = $n % 10;
    if ($mod100 >= 11 && $mod100 <= 14) {
        $word = 'туров';
    } elseif ($mod10 === 1) {
        $word = 'тур';
    } elseif ($mod10 >= 2 && $mod10 <= 4) {
        $word = 'тура';
    } else {
        $word = 'туров';
    }
    return $n . ' ' . $word;
};
?>
<section class="tour-directions" aria-label="Расписание туров">
    <div class="container tour-directions__items">

        <article class="tour-directions__item">
            <div class="tour-directions__inner">
                <span class="total-tour"><?php echo esc_html($plural_tours($count_tours('local_tours'))); ?></span>
                <div class="content">
                    <h2 class="h2">Расписание местных туров</h2>
                    <div class="action">
                        <a href="#develop-form" rel="modal:open">Смотреть расписание</a>
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/icons/arrow-right-link.svg'); ?>" alt="" aria-hidden="true">
                    </div>
                </div>
            </div>
            <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/images/tour-1.jpg'); ?>" alt="" class="background" width="690" height="585" aria-hidden="true">
        </article>

        <article class="tour-directions__item">
            <div class="tour-directions__inner">
                <span class="total-tour"><?php echo esc_html($plural_tours($count_tours('intl_tours'))); ?></span>
                <div class="content">
                    <h2 class="h2">Расписание выездных туров</h2>
                    <div class="action">
                        <a href="/tour-schedule">Смотреть расписание</a>
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/icons/arrow-right-link.svg'); ?>" alt="" aria-hidden="true">
                    </div>
                </div>
            </div>
            <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/images/tour-2.jpg'); ?>" alt="" class="background" width="690" height="585" aria-hidden="true">
        </article>
    </div>
    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/images/tour-directions-bg.jpg'); ?>" alt="" width="1920" height="847" class="tour-directions__background" aria-hidden="true">
</section>
