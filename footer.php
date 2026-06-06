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
                    <a href="/"><img src="<?php echo get_template_directory_uri(); ?>/assets/img/icons/logo.svg" alt="logo" width="95" height="78"></a>
                </div>
                <div class="phone">
                    <a href="" class="phone__tel">+7(999)999-99-99</a>
                    <a href="" class="phone__callback">Связаться с нами</a>
                </div>
            </div>
            <div class="logo footer__logo hidden-mobile">
                <a href="/"><img src="<?php echo get_template_directory_uri(); ?>/assets/img/icons/logo.svg" alt="logo" width="95" height="78"></a>
            </div>

            <div class="footer__menu">
                <h4 class="h4">Направления</h4>
                <nav>
                    <ul>
                        <li><a href="#mountaineering">Альпинизм и трекинг</a></li>
                        <li><a href="#excursion">Экскурсионные туры</a></li>
                        <li><a href="#children">Экскурсии и мероприятия для детей</a></li>
                        <li><a href="#products">Авторская продукция</a></li>
                    </ul>
                </nav>
            </div>

            <div class="footer__menu">
                <h4 class="h4 footer__h4">О компании</h4>
                <nav>
                    <ul>
                        <li><a href="#develop-form" rel="modal:open">Отзывы</a></li>
                        <li><a href="#develop-form" rel="modal:open">Команда</a></li>
                        <li><a href="#develop-form" rel="modal:open">Контакты</a></li>
                    </ul>
                </nav>
            </div>

            <div class="phone hidden-mobile">
                <a href="" class="phone__tel">+7(999)999-99-99</a>
                <a href="#login-form" rel="modal:open"  class="phone__callback">Связаться с нами</a>
            </div>
        </div>
        <div class="footer__down container">
            <p class="footer-info">© Центр туризма горизонт. 2025</p>
            <p class="footer-info">Разработка сайта</p>
            <p class="footer-info">Политика конфиденциальности</p>
        </div>
		</footer><!-- #colophon -->

<?php wp_footer(); ?>

	</body>
</html>
