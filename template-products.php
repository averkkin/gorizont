<?php
/*
 * Template Name: Авторская продукция
 */

get_header();

$page_id     = get_the_ID();
$hero_title  = carbon_get_post_meta($page_id, 'products_hero_title') ?: get_the_title();
$hero_desc   = carbon_get_post_meta($page_id, 'products_hero_desc');
$hero_image  = carbon_get_post_meta($page_id, 'products_hero_image');

$categories = get_terms(array(
        'taxonomy'   => 'product_cat',
        'hide_empty' => true,
        'orderby'    => 'term_id', // в порядке создания: шевроны первыми
));

$products = new WP_Query(array(
        'post_type'      => 'products',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => array('menu_order' => 'ASC', 'date' => 'DESC'),
));
?>
<main id="primary" class="site-main products-page">

    <section class="products-hero" aria-labelledby="products-hero-title">
        <div class="container products-hero__inner">
            <div class="products-hero__content">
                <h1 id="products-hero-title" class="h1 products-hero__title">
                    <?php echo wp_kses_post($hero_title); ?>
                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/icons/line-decor.svg'); ?>" alt="" class="h2-decor" aria-hidden="true">
                </h1>
                <?php if ($hero_desc) : ?>
                    <p class="products-hero__desc"><?php echo esc_html($hero_desc); ?></p>
                <?php endif; ?>
                <div class="actions">
                    <a class="button" href="#products-catalog">смотреть продукцию</a>
                    <a class="button button--outline" href="<?php echo esc_url(gorizont_whatsapp_url()); ?>" target="_blank" rel="noopener">написать в WhatsApp</a>
                </div>
            </div>
            <?php if ($hero_image) : ?>
                <div class="products-hero__image">
                    <?php echo wp_get_attachment_image($hero_image, 'large', false, array('alt' => '')); ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="products-catalog" id="products-catalog" aria-label="Каталог продукции" data-js-products>
        <div class="container products-catalog__inner">
            <div class="products-catalog__toolbar">
                <label class="visually-hidden" for="products-search">Поиск по товарам</label>
                <input class="products-catalog__search" id="products-search" type="search" placeholder="Поиск" autocomplete="off" data-js-products-search>

                <label class="visually-hidden" for="products-sort">Порядок товаров</label>
                <select class="products-catalog__sort" id="products-sort" data-js-products-sort>
                    <option value="default">Порядок: по умолчанию</option>
                    <option value="price-asc">Сначала дешевле</option>
                    <option value="price-desc">Сначала дороже</option>
                </select>
            </div>

            <?php if (!empty($categories) && !is_wp_error($categories)) : ?>
                <fieldset class="products-catalog__filter" data-js-products-filter>
                    <legend class="products-catalog__filter-title">
                        <button type="button" data-js-products-all>Все</button>
                    </legend>
                    <?php foreach ($categories as $category) : ?>
                        <label class="products-catalog__check">
                            <input type="checkbox" value="<?php echo esc_attr($category->slug); ?>">
                            <span class="box" aria-hidden="true"></span>
                            <?php echo esc_html($category->name); ?>
                        </label>
                    <?php endforeach; ?>
                </fieldset>
            <?php endif; ?>

            <div class="products-catalog__grid" data-js-products-grid>
                <?php
                if ($products->have_posts()) :
                    $index = 0;
                    while ($products->have_posts()) : $products->the_post();
                        $product_id  = get_the_ID();
                        $price       = (int) carbon_get_post_meta($product_id, 'product_price');
                        $terms       = wp_get_post_terms($product_id, 'product_cat', array('fields' => 'slugs'));
                        $search_text = get_the_title() . ' ' . get_the_excerpt();
                        $search_text = function_exists('mb_strtolower') ? mb_strtolower($search_text, 'UTF-8') : strtolower($search_text);
                        ?>
                        <a
                                class="product-card"
                                href="#login-form"
                                rel="modal:open"
                                data-categories="<?php echo esc_attr(implode(' ', $terms)); ?>"
                                data-price="<?php echo esc_attr($price); ?>"
                                data-order="<?php echo esc_attr($index++); ?>"
                                data-search="<?php echo esc_attr($search_text); ?>"
                        >
                            <span class="product-card__image">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('medium_large', array('alt' => '')); ?>
                                <?php else : ?>
                                    <svg width="85" height="68" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <rect x="3" y="4" width="18" height="16" rx="2" stroke="currentColor" stroke-width="1.5"/>
                                        <circle cx="9" cy="10" r="2" stroke="currentColor" stroke-width="1.5"/>
                                        <path d="M3 17l5-5 4 4 3-3 6 6" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                                    </svg>
                                <?php endif; ?>
                            </span>
                            <span class="product-card__info">
                                <span class="product-card__title"><?php the_title(); ?></span>
                                <?php if (has_excerpt()) : ?>
                                    <span class="product-card__desc"><?php echo esc_html(get_the_excerpt()); ?></span>
                                <?php endif; ?>
                                <?php if ($price) : ?>
                                    <span class="product-card__price"><?php echo esc_html(number_format_i18n($price) . ' ₽'); ?></span>
                                <?php endif; ?>
                            </span>
                        </a>
                    <?php
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </div>
            <p class="products-catalog__empty" data-js-products-empty <?php echo $products->have_posts() ? 'hidden' : ''; ?>>Товары не найдены.</p>
        </div>
    </section>

    <?php get_template_part('template-parts/reviews-section', null, array('slider_id' => 'reviews-slider-products')); ?>
</main>

<?php
get_footer();
