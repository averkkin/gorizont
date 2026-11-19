<?php
// Задаем значения по умолчанию, если в админке пусто
$title = !empty($args['title']) ? $args['title'] : 'Экспедиции <br>и выездные походы';
$desc  = !empty($args['desc']) ? $args['desc'] : 'Путешествия по России и миру — Кавказ, Алтай, Памир, Непал и другие маршруты для тех, кто хочет испытать себя';
$bg    = !empty($args['bg']) ? $args['bg'] : get_template_directory_uri() . '/assets/img/images/hero-2.jpg';
// Плавный уход фото в фон, как на странице тура, — когда ниже идёт желтый блок описания
$fade  = !empty($args['fade']);
$position = !empty($args['position']) ? $args['position'] : '';
?>

<section class="hero" aria-labelledby="page-hero-title">
    <div class="container inner">
        <div class="hero__content">
            <div class="hero__heading">
                <h1 id="page-hero-title" class="h1"><?php echo wp_kses_post($title); ?></h1>
                <p class="p"><?php echo esc_html($desc); ?></p>
            </div>
            <div class="hero__buttons">
                <div class="actions">
                    <a class="button" href="#login-form" rel="modal:open">подобрать маршрут</a>
                    <a class="button button--outline" href="<?php echo esc_url(gorizont_whatsapp_url()); ?>" target="_blank" rel="noopener">написать в WhatsApp</a>
                </div>
                <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/icons/Arrow-Down-Circle.svg'); ?>" alt="" class="hero__arrow-down" aria-hidden="true">
            </div>
        </div>
    </div>
    <?php if ($fade) : ?>
        <div class="bg-blur-matrix"></div>
        <div class="bg-blur-gradient"></div>
    <?php endif; ?>
    <img src="<?php echo esc_url($bg); ?>" alt="" width="1920" height="847" class="background" aria-hidden="true"<?php echo gorizont_hero_position_style($position); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
</section>
