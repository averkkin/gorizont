<?php
// Задаем значения по умолчанию, если в админке пусто
$title = !empty($args['title']) ? $args['title'] : 'Экспедиции <br>и выездные походы';
$desc  = !empty($args['desc']) ? $args['desc'] : 'Путешествия по России и миру — Кавказ, Алтай, Памир, Непал и другие маршруты для тех, кто хочет испытать себя';
$bg    = !empty($args['bg']) ? $args['bg'] : get_template_directory_uri() . '/assets/img/images/hero-2.jpg';
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
                    <a class="button" href="#login-form" rel="modal:open">выбрать маршрут</a>
                    <button type="button" class="button button--outline">написать в WhatsApp</button>
                </div>
                <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/icons/Arrow-Down-Circle.svg'); ?>" alt="" class="hero__arrow-down" aria-hidden="true">
            </div>
        </div>
    </div>
    <img src="<?php echo esc_url($bg); ?>" alt="" width="1920" height="847" class="background" aria-hidden="true">
</section>
