<?php
// Задаем значения по умолчанию, если в админке пусто
$title = !empty($args['title']) ? $args['title'] : 'Экспедиции <br>и выездные походы';
$desc  = !empty($args['desc']) ? $args['desc'] : 'Путешествия по России и миру — Кавказ, Алтай, Памир, Непал и другие маршруты для тех, кто хочет испытать себя';
$bg    = !empty($args['bg']) ? $args['bg'] : get_template_directory_uri() . '/assets/img/images/hero-2.jpg';
?>

<section class="hero">
    <div class="container inner">
        <div class="hero__content">
            <div class="hero__heading">
                <h1 class="h1"><?php echo wp_kses_post($title); ?></h1>
                <p class="p"><?php echo esc_html($desc); ?></p>
            </div>
            <div class="hero__buttons">
                <div class="actions">
                    <a href="#login-form" rel="modal:open"><button class="button">выбрать маршрут</button></a>
                    <button class="button button--outline">написать в WhatsApp</button>
                </div>
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/icons/Arrow-Down-Circle.svg" alt="Arrow Down" class="hero__arrow-down">
            </div>
        </div>
    </div>
    <img src="<?php echo esc_url($bg); ?>" alt="Горы" width="1920" height="847" class="background">
</section>