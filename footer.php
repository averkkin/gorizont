<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package gorizont
 */

?>

		<footer class="footer">
	        <div class="footer__head container">
	            <div class="footer__mobile-head visible-mobile">
	                <div class="logo footer__logo">
	                    <a href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php echo esc_attr(get_bloginfo('name')); ?>">
	                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/icons/logo.svg'); ?>" alt="" width="95" height="78">
	                    </a>
	                </div>
	                <div class="phone">
	                    <a href="tel:+79999999999" class="phone__tel">+7(999)999-99-99</a>
	                    <a href="#login-form" rel="modal:open" class="phone__callback">Связаться с нами</a>
	                </div>
	            </div>
	            <div class="logo footer__logo hidden-mobile">
	                <a href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php echo esc_attr(get_bloginfo('name')); ?>">
	                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/icons/logo.svg'); ?>" alt="" width="95" height="78">
	                </a>
	            </div>

	            <div class="footer__menu">
	                <h2 class="h4">Направления</h2>
	                <nav aria-label="Направления">
	                    <ul>
	                        <li><a href="<?php echo esc_url(home_url('/tours-direct')); ?>">Альпинизм и трекинг</a></li>
	                        <li><a href="<?php echo esc_url(home_url('/#excursion')); ?>">Экскурсионные туры</a></li>
	                        <li><a href="<?php echo esc_url(home_url('/#children')); ?>">Экскурсии и мероприятия для детей</a></li>
	                        <li><a href="<?php echo esc_url(home_url('/#products')); ?>">Авторская продукция</a></li>
	                    </ul>
	                </nav>
	            </div>

	            <div class="footer__menu">
	                <h2 class="h4 footer__h4">О компании</h2>
	                <nav aria-label="О компании">
                    <ul>
                        <li><a href="#develop-form" rel="modal:open">Отзывы</a></li>
                        <li><a href="#develop-form" rel="modal:open">Команда</a></li>
                        <li><a href="#develop-form" rel="modal:open">Контакты</a></li>
                    </ul>
                </nav>
	            </div>

	            <div class="phone hidden-mobile">
	                <a href="tel:+79999999999" class="phone__tel">+7(999)999-99-99</a>
	                <a href="#login-form" rel="modal:open"  class="phone__callback">Связаться с нами</a>
	            </div>
	        </div>
	        <div class="footer__down container">
	            <p class="footer-info">© Центр туризма «Горизонт». <time datetime="<?php echo esc_attr(wp_date('Y')); ?>"><?php echo esc_html(wp_date('Y')); ?></time></p>
            <p class="footer-info">Разработка сайта</p>
            <p class="footer-info">Политика конфиденциальности</p>
        </div>
		</footer><!-- #colophon -->

<?php wp_footer(); ?>

	</body>
</html>
