<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Carbon_Fields\Container;
use Carbon_Fields\Field;

// Товары авторской продукции: выводятся только карточками на странице «Авторская продукция», своей страницы у товара нет
add_action( 'init', 'register_products_cpt' );
function register_products_cpt() {
    register_post_type( 'products', array(
        'labels' => array(
            'name'               => 'Товары',
            'singular_name'      => 'Товар',
            'menu_name'          => 'Авторская продукция',
            'all_items'          => 'Все товары',
            'add_new'            => 'Добавить товар',
            'add_new_item'       => 'Добавить новый товар',
            'edit_item'          => 'Редактировать товар',
            'new_item'           => 'Новый товар',
            'search_items'       => 'Найти товар',
            'not_found'          => 'Товаров не найдено',
            'not_found_in_trash' => 'В корзине товаров нет',
        ),
        'public'        => false,
        'show_ui'       => true,
        'show_in_menu'  => true,
        'supports'      => array( 'title', 'excerpt', 'thumbnail', 'page-attributes' ),
        'menu_icon'     => 'dashicons-tag',
        'menu_position' => 22,
        'show_in_rest'  => false,
    ) );

    register_taxonomy( 'product_cat', 'products', array(
        'labels' => array(
            'name'          => 'Категории товаров',
            'singular_name' => 'Категория',
            'menu_name'     => 'Категории',
            'all_items'     => 'Все категории',
            'add_new_item'  => 'Добавить категорию',
            'edit_item'     => 'Редактировать категорию',
        ),
        'public'            => false,
        'show_ui'           => true,
        'show_admin_column' => true,
        'hierarchical'      => true,
        'show_in_rest'      => false,
    ) );
}

add_filter( 'enter_title_here', 'gorizont_products_title_placeholder', 10, 2 );
function gorizont_products_title_placeholder( $placeholder, $post ) {
    return $post->post_type === 'products' ? 'Название товара, например: Шеврон «Горизонт»' : $placeholder;
}

add_action( 'carbon_fields_register_fields', 'crb_attach_product_fields' );
function crb_attach_product_fields() {
    Container::make( 'post_meta', 'Товар' )
        ->where( 'post_type', '=', 'products' )
        ->add_fields( array(
            Field::make( 'html', 'product_help' )->set_html(
                '<p>Короткое описание под названием — поле «Отрывок». Фото — «Изображение записи». Порядок по умолчанию — поле «Порядок» справа, меньше число — выше.</p>'
            ),
            Field::make( 'text', 'product_price', 'Цена, ₽ (только число, например: 1200)' )
                ->set_attribute( 'type', 'number' ),
        ) );

    Container::make( 'post_meta', 'Первый экран' )
        ->where( 'post_template', '=', 'template-products.php' )
        ->add_fields( array(
            Field::make( 'text', 'products_hero_title', 'Заголовок' ),
            Field::make( 'textarea', 'products_hero_desc', 'Текст под заголовком' ),
            Field::make( 'image', 'products_hero_image', 'Фото справа' ),
        ) );
}
