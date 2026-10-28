<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action( 'init', 'register_reviews_cpt' );
function register_reviews_cpt() {
    register_post_type( 'reviews', array(
        'labels' => array(
            'name'               => 'Отзывы',
            'singular_name'      => 'Отзыв',
            'menu_name'          => 'Отзывы',
            'all_items'          => 'Все отзывы',
            'add_new'            => 'Добавить отзыв',
            'add_new_item'       => 'Добавить новый отзыв',
            'edit_item'          => 'Редактировать отзыв',
            'new_item'           => 'Новый отзыв',
            'view_item'          => 'Посмотреть отзыв',
            'search_items'       => 'Найти отзыв',
            'not_found'          => 'Отзывов не найдено',
            'not_found_in_trash' => 'В корзине отзывов нет',
        ),
        'public'       => true,
        'has_archive'  => true,
        'rewrite'      => array( 'slug' => 'reviews' ),
        'supports'     => array( 'title', 'page-attributes' ),
        'menu_icon'    => 'dashicons-format-quote',
        'menu_position'=> 21,
        // Классический экран редактирования: только имя автора и поля Carbon Fields
        'show_in_rest' => false,
    ) );
}

add_filter( 'enter_title_here', 'gorizont_reviews_title_placeholder', 10, 2 );
function gorizont_reviews_title_placeholder( $placeholder, $post ) {
    return $post->post_type === 'reviews' ? 'Имя автора отзыва, например: Татьяна Гнутова' : $placeholder;
}

// Ярлык (адрес) отзыва не нужен: отзывы выводятся только в блоке на сайте
add_action( 'add_meta_boxes_reviews', 'gorizont_reviews_remove_slug_box' );
function gorizont_reviews_remove_slug_box() {
    remove_meta_box( 'slugdiv', 'reviews', 'normal' );
}

add_action( 'carbon_fields_register_fields', 'crb_attach_review_fields' );
function crb_attach_review_fields() {
    Container::make( 'post_meta', 'Отзыв' )
        ->where( 'post_type', '=', 'reviews' )
        ->add_fields( array(
            Field::make( 'radio', 'review_type', 'Вид отзыва' )
                ->set_default_value( 'screenshot' )
                ->add_options( array(
                    'screenshot' => 'Скриншот из мессенджера',
                    'text'       => 'Текст',
                ) ),

            Field::make( 'image', 'review_screenshot', 'Скриншот' )
                ->set_required( true )
                ->set_help_text( 'Обрежьте скрин до самого сообщения: без меню «Ответить / Скопировать» и строки реакций. По клику на сайте скрин открывается целиком.' )
                ->set_conditional_logic( array(
                    array( 'field' => 'review_type', 'value' => 'screenshot' ),
                ) ),
            Field::make( 'text', 'review_caption', 'Подпись под скрином' )
                ->set_help_text( 'Необязательно. Например: Казбек, июль 2025' )
                ->set_conditional_logic( array(
                    array( 'field' => 'review_type', 'value' => 'screenshot' ),
                ) ),

            Field::make( 'textarea', 'review_text', 'Текст отзыва' )
                ->set_required( true )
                ->set_rows( 6 )
                ->set_conditional_logic( array(
                    array( 'field' => 'review_type', 'value' => 'text' ),
                ) ),
            Field::make( 'select', 'review_rating', 'Оценка' )
                ->set_default_value( '5' )
                ->add_options( array(
                    '1' => '1 из 5',
                    '2' => '2 из 5',
                    '3' => '3 из 5',
                    '4' => '4 из 5',
                    '5' => '5 из 5',
                ) )
                ->set_conditional_logic( array(
                    array( 'field' => 'review_type', 'value' => 'text' ),
                ) ),
            Field::make( 'image', 'review_avatar', 'Фото автора' )
                ->set_help_text( 'Необязательно. Без фото выводится первая буква имени.' )
                ->set_conditional_logic( array(
                    array( 'field' => 'review_type', 'value' => 'text' ),
                ) ),
        ) );
}

/**
 * Отзыв выводится скриншотом, если выбран этот вид и скрин загружен.
 */
function gorizont_review_screenshot_id( $review_id ) {
    if ( carbon_get_post_meta( $review_id, 'review_type' ) === 'text' ) {
        return 0;
    }

    return (int) carbon_get_post_meta( $review_id, 'review_screenshot' );
}

/**
 * Порядок отзывов на сайте: сначала по полю «Порядок», затем новые выше.
 */
function gorizont_reviews_orderby() {
    return array(
        'menu_order' => 'ASC',
        'date'       => 'DESC',
    );
}

add_action( 'pre_get_posts', 'gorizont_reviews_archive_query' );
function gorizont_reviews_archive_query( $query ) {
    if ( is_admin() || ! $query->is_main_query() || ! $query->is_post_type_archive( 'reviews' ) ) {
        return;
    }

    $query->set( 'posts_per_page', 24 );
    $query->set( 'orderby', gorizont_reviews_orderby() );
}

// Колонки в списке отзывов в админке
add_filter( 'manage_reviews_posts_columns', 'gorizont_reviews_admin_columns' );
function gorizont_reviews_admin_columns( $columns ) {
    return array(
        'cb'             => $columns['cb'],
        'review_preview' => 'Отзыв',
        'title'          => 'Автор',
        'review_caption' => 'Подпись',
        'menu_order'     => 'Порядок',
        'date'           => $columns['date'],
    );
}

add_action( 'manage_reviews_posts_custom_column', 'gorizont_reviews_admin_column_content', 10, 2 );
function gorizont_reviews_admin_column_content( $column, $post_id ) {
    if ( $column === 'review_preview' ) {
        $screenshot_id = gorizont_review_screenshot_id( $post_id );

        if ( $screenshot_id ) {
            echo wp_get_attachment_image( $screenshot_id, 'thumbnail', false, array( 'style' => 'width:80px;height:80px;object-fit:cover;object-position:top;border-radius:6px;' ) );
        } else {
            $text = carbon_get_post_meta( $post_id, 'review_text' );
            echo $text ? esc_html( wp_trim_words( $text, 12 ) ) : '<span style="color:#b32d2e;">Нет скриншота</span>';
        }
    }

    if ( $column === 'review_caption' ) {
        echo esc_html( carbon_get_post_meta( $post_id, 'review_caption' ) );
    }

    if ( $column === 'menu_order' ) {
        echo (int) get_post_field( 'menu_order', $post_id );
    }
}

add_filter( 'manage_edit-reviews_sortable_columns', 'gorizont_reviews_sortable_columns' );
function gorizont_reviews_sortable_columns( $columns ) {
    $columns['menu_order'] = 'menu_order';

    return $columns;
}

add_action( 'admin_head-edit.php', 'gorizont_reviews_admin_columns_style' );
function gorizont_reviews_admin_columns_style() {
    if ( get_current_screen()->post_type !== 'reviews' ) {
        return;
    }

    echo '<style>.column-review_preview{width:96px}.column-menu_order{width:80px}</style>';
}
