<?php
$local_url = gorizont_schedule_page_url('local_tours');
$intl_url  = gorizont_schedule_page_url('intl_tours', home_url('/tour-schedule/'));
?>
<section class="tour-directions" aria-label="Расписание туров">
    <div class="container tour-directions__items">

        <article class="tour-directions__item">
            <div class="tour-directions__inner">
                <span class="total-tour"><?php echo esc_html(gorizont_plural_tours(gorizont_count_schedule_tours('local_tours'))); ?></span>
                <div class="content">
                    <h2 class="h2">Расписание местных туров</h2>
                    <div class="action">
                        <?php if ($local_url) : ?>
                            <a href="<?php echo esc_url($local_url); ?>">Смотреть расписание</a>
                        <?php else : ?>
                            <a href="#develop-form" rel="modal:open">Смотреть расписание</a>
                        <?php endif; ?>
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/icons/arrow-right-link.svg'); ?>" alt="" aria-hidden="true">
                    </div>
                </div>
            </div>
            <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/images/tour-1.jpg'); ?>" alt="" class="background" width="690" height="585" aria-hidden="true">
        </article>

        <article class="tour-directions__item">
            <div class="tour-directions__inner">
                <span class="total-tour"><?php echo esc_html(gorizont_plural_tours(gorizont_count_schedule_tours('intl_tours'))); ?></span>
                <div class="content">
                    <h2 class="h2">Расписание выездных туров</h2>
                    <div class="action">
                        <a href="<?php echo esc_url($intl_url); ?>">Смотреть расписание</a>
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/icons/arrow-right-link.svg'); ?>" alt="" aria-hidden="true">
                    </div>
                </div>
            </div>
            <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/images/tour-2.jpg'); ?>" alt="" class="background" width="690" height="585" aria-hidden="true">
        </article>
    </div>
    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/images/tour-directions-bg.jpg'); ?>" alt="" width="1920" height="847" class="tour-directions__background" aria-hidden="true">
</section>
