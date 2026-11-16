<?php
// Описание направления и форматы для страницы «Расписание туров». Каждая часть выводится, только если заполнена в админке
$page_id       = !empty($args['page_id']) ? $args['page_id'] : get_the_ID();
$about_title   = carbon_get_post_meta($page_id, 'schedule_about_title');
$about_text    = carbon_get_post_meta($page_id, 'schedule_about_text');
$about_meta    = carbon_get_post_meta($page_id, 'schedule_about_meta');
$formats_title = carbon_get_post_meta($page_id, 'schedule_formats_title');
$formats       = carbon_get_post_meta($page_id, 'schedule_formats');

$split_lines = static function ($text) {
    return array_filter(array_map('trim', explode("\n", str_replace("\r", '', (string) $text))));
};
?>

<?php if (!empty($about_text)) : ?>
    <div class="single-tour__inner">
        <section class="tour-description container" aria-labelledby="schedule-about-title">
            <div class="inner">
                <div class="tour-description__content">
                    <h2 id="schedule-about-title" class="h2"><?php echo esc_html($about_title ?: get_the_title($page_id)); ?></h2>
                    <div class="tour-description__text">
                        <?php echo wp_kses_post(wpautop($about_text)); ?>
                    </div>
                </div>
                <?php if (!empty($about_meta)) : ?>
                    <dl class="tour-description__meta-grid">
                        <?php foreach ($about_meta as $meta) : ?>
                            <div class="group-meta">
                                <dt class="key"><?php echo esc_html($meta['label']); ?></dt>
                                <dd class="value"><?php echo esc_html($meta['text']); ?></dd>
                            </div>
                        <?php endforeach; ?>
                    </dl>
                <?php endif; ?>
            </div>
            <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/icons/bg-pattern.svg'); ?>" alt="" class="bg-pattern" aria-hidden="true">
        </section>
        <div class="bg-white"></div>
    </div>
<?php endif; ?>

<?php if (!empty($formats)) : ?>
    <section class="schedule-formats" aria-labelledby="schedule-formats-title" data-js-schedule-formats>
        <div class="container inner">
            <header class="schedule-formats__header">
                <h2 id="schedule-formats-title" class="h2"><?php echo esc_html($formats_title ?: 'Форматы походов'); ?></h2>
                <div class="schedule-formats__tabs" role="tablist" aria-label="<?php echo esc_attr($formats_title ?: 'Форматы походов'); ?>">
                    <?php foreach ($formats as $index => $format) : ?>
                        <button
                                type="button"
                                class="schedule-formats__tab<?php echo 0 === $index ? ' active' : ''; ?>"
                                role="tab"
                                id="schedule-format-tab-<?php echo esc_attr($index); ?>"
                                aria-controls="schedule-format-<?php echo esc_attr($index); ?>"
                                aria-selected="<?php echo 0 === $index ? 'true' : 'false'; ?>"
                                tabindex="<?php echo 0 === $index ? '0' : '-1'; ?>"
                        ><?php echo esc_html($format['tab']); ?></button>
                    <?php endforeach; ?>
                </div>
            </header>

            <?php foreach ($formats as $index => $format) :
                $included = $split_lines($format['included']);
                $routes   = $split_lines($format['routes']);
                ?>
                <div
                        class="schedule-formats__panel"
                        role="tabpanel"
                        id="schedule-format-<?php echo esc_attr($index); ?>"
                        aria-labelledby="schedule-format-tab-<?php echo esc_attr($index); ?>"
                        <?php echo 0 === $index ? '' : 'hidden'; ?>
                >
                    <div class="schedule-formats__about">
                        <?php if (!empty($format['title'])) : ?>
                            <h3 class="h3"><?php echo esc_html($format['title']); ?></h3>
                        <?php endif; ?>
                        <div class="schedule-formats__text">
                            <?php echo wp_kses_post(wpautop($format['text'])); ?>
                        </div>
                    </div>

                    <?php if ($included) : ?>
                        <section class="schedule-formats__included">
                            <h4 class="schedule-formats__label">Входит в стоимость</h4>
                            <ul>
                                <?php foreach ($included as $line) : ?>
                                    <li><span class="box" aria-hidden="true"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/icons/check.svg'); ?>" alt=""></span><?php echo esc_html(ltrim($line, '-– ')); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </section>
                    <?php endif; ?>

                    <?php if ($routes) : ?>
                        <section class="schedule-formats__routes">
                            <h4 class="schedule-formats__label">Маршруты</h4>
                            <ul>
                                <?php foreach ($routes as $route) : ?>
                                    <li><?php echo esc_html(ltrim($route, '-– ')); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </section>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>
