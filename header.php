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

<form id="login-form" class="modal">
    <div class="modal--inner">
        <div class="modal__heading">
            <h3 class="h3 modal__h3">Оставьте заявку</h3>
            <p class="p modal__p">И мы перезвоним вам в ближайшее время</p>
        </div>
        <div class="modal__inputs">
            <input type="text" class="input" placeholder="Ваше имя">
            <input type="tel" class="input" placeholder="Ваш номер телефона">
            <button class="button">Отправить</button>
        </div>
    </div>
</form>

<form id="develop-form" class="modal">
    <div class="modal--inner">
        <div class="modal__heading">
            <h3 class="h3 modal__h3">Раздел в разработке</h3>
            <p class="p modal__p">Вы можете оставить заявку и&nbsp;в&nbsp;ближайшее время мы&nbsp;перезвоним вам</p>
        </div>
        <div class="modal__inputs">
            <input type="text" class="input" placeholder="Ваше имя">
            <input type="tel" class="input" placeholder="Ваш номер телефона">
            <button class="button">Отправить</button>
        </div>
    </div>
</form>

<div class="lines"><span></span><span></span><span></span></div>

<header class="header" data-js-header>

    <div class="header--inner container" >
        <div class="logo">
            <a href="/"><img src="<?php echo get_template_directory_uri(); ?>/assets/img/icons/logo.svg" alt="logo" width="95" height="78"></a>
        </div>
        <nav class="header__nav">
            <ul>
                <li><a href="#mountaineering">Направления</a></li>
                <li><a href="#develop-form" rel="modal:open">Контакты</a></li>
            </ul>
        </nav>
        <div class="phone">
            <a href="" class="phone__tel">+7(999)999-99-99</a>
            <a href="#login-form" rel="modal:open" class="phone__callback">Связаться с нами</a>
        </div>

        <div class="header__overlay" data-js-header-overlay>
            <nav class="header__menu">
                <ul class="header__menu-list">
                    <li class="header__menu-item">
                        <a class="header__menu-link is-active" href="">Альпинизм и трекинг</a>
                    </li>
                    <li class="header__menu-item">
                        <a class="header__menu-link" href="">Экскурсионные туры</a>
                    </li>
                    <li class="header__menu-item">
                        <a class="header__menu-link" href="">Экскурсии и мероприятия для детей</a>
                    </li>
                    <li class="header__menu-item">
                        <a class="header__menu-link" href="">Авторская продукция</a>
                    </li>
                </ul>
            </nav>
        </div>

        <button class="burger nav-toggle visible-mobile" data-js-header-burger-button>
            <span class="bar-top"></span>
            <span class="bar-mid"></span>
            <span class="bar-bot"></span>
        </button>
    </div>

    <div class="lines"><span></span><span></span><span></span></div>

</header><!-- #masthead -->
