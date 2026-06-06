<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package gorizont
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />

    <!-- Glide.js CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@glidejs/glide/dist/css/glide.core.min.css">

    <!-- Опционально тема -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@glidejs/glide/dist/css/glide.theme.min.css">

    <script src="https://cdn.jsdelivr.net/npm/@glidejs/glide"></script>

		<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<form id="login-form" class="modal" aria-labelledby="login-form-title">
    <div class="modal--inner">
        <div class="modal__heading">
            <h2 id="login-form-title" class="h3 modal__h3">Оставьте заявку</h2>
            <p class="p modal__p">И мы перезвоним вам в ближайшее время</p>
        </div>
        <div class="modal__inputs">
            <label class="visually-hidden" for="login-name">Ваше имя</label>
            <input id="login-name" name="name" type="text" class="input" placeholder="Ваше имя" autocomplete="name">
            <label class="visually-hidden" for="login-phone">Ваш номер телефона</label>
            <input id="login-phone" name="phone" type="tel" class="input" placeholder="Ваш номер телефона" autocomplete="tel">
            <button type="submit" class="button">Отправить</button>
        </div>
    </div>
</form>

<form id="develop-form" class="modal" aria-labelledby="develop-form-title">
    <div class="modal--inner">
        <div class="modal__heading">
            <h2 id="develop-form-title" class="h3 modal__h3">Раздел в разработке</h2>
            <p class="p modal__p">Вы можете оставить заявку и&nbsp;в&nbsp;ближайшее время мы&nbsp;перезвоним вам</p>
        </div>
        <div class="modal__inputs">
            <label class="visually-hidden" for="develop-name">Ваше имя</label>
            <input id="develop-name" name="name" type="text" class="input" placeholder="Ваше имя" autocomplete="name">
            <label class="visually-hidden" for="develop-phone">Ваш номер телефона</label>
            <input id="develop-phone" name="phone" type="tel" class="input" placeholder="Ваш номер телефона" autocomplete="tel">
            <button type="submit" class="button">Отправить</button>
        </div>
    </div>
</form>

<div class="lines" aria-hidden="true"><span></span><span></span><span></span></div>

<header class="header" data-js-header>

    <div class="header--inner container" >
        <div class="logo">
            <a href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php echo esc_attr(get_bloginfo('name')); ?>">
                <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/icons/logo.svg'); ?>" alt="" width="95" height="78">
            </a>
        </div>
        <nav class="header__nav" aria-label="Основная навигация">
            <ul>
                <li><a href="<?php echo esc_url(home_url('/#mountaineering')); ?>">Направления</a></li>
                <li><a href="#develop-form" rel="modal:open">Контакты</a></li>
            </ul>
        </nav>
        <div class="phone">
            <a href="tel:+79999999999" class="phone__tel">+7(999)999-99-99</a>
            <a href="#login-form" rel="modal:open" class="phone__callback">Связаться с нами</a>
        </div>

        <div class="header__overlay" id="mobile-menu" data-js-header-overlay>
            <nav class="header__menu" aria-label="Мобильная навигация">
                <ul class="header__menu-list">
                    <li class="header__menu-item">
                        <a class="header__menu-link<?php echo is_front_page() ? ' is-active' : ''; ?>" href="<?php echo esc_url(home_url('/#mountaineering')); ?>">Альпинизм и трекинг</a>
                    </li>
                    <li class="header__menu-item">
                        <a class="header__menu-link" href="<?php echo esc_url(home_url('/#excursion')); ?>">Экскурсионные туры</a>
                    </li>
                    <li class="header__menu-item">
                        <a class="header__menu-link" href="<?php echo esc_url(home_url('/#children')); ?>">Экскурсии и мероприятия для детей</a>
                    </li>
                    <li class="header__menu-item">
                        <a class="header__menu-link" href="<?php echo esc_url(home_url('/#products')); ?>">Авторская продукция</a>
                    </li>
                </ul>
            </nav>
        </div>

        <button type="button" class="burger nav-toggle visible-mobile" aria-label="Открыть меню" aria-controls="mobile-menu" aria-expanded="false" data-js-header-burger-button>
            <span class="bar-top"></span>
            <span class="bar-mid"></span>
            <span class="bar-bot"></span>
        </button>
    </div>

    <div class="lines" aria-hidden="true"><span></span><span></span><span></span></div>

</header><!-- #masthead -->
