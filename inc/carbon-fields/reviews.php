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
            'name'          => 'Отзывы',
            'singular_name' => 'Отзыв',
            'add_new'       => 'Добавить отзыв',
            'add_new_item'  => 'Добавить новый отзыв',
            'edit_item'     => 'Редактировать отзыв',
        ),
        'public'       => true,
        'has_archive'  => true,
        'rewrite'      => array( 'slug' => 'reviews' ),
        'supports'     => array( 'title', 'editor', 'thumbnail' ),
        'menu_icon'    => 'dashicons-format-quote',
        'show_in_rest' => true,
    ) );
}

add_action( 'carbon_fields_register_fields', 'crb_attach_review_fields' );
function crb_attach_review_fields() {
    Container::make( 'post_meta', 'Параметры отзыва' )
        ->where( 'post_type', '=', 'reviews' )
        ->add_fields( array(
            Field::make( 'select', 'review_rating', 'Оценка' )
                ->set_default_value( '5' )
                ->add_options( array(
                    '1' => '1 из 5',
                    '2' => '2 из 5',
                    '3' => '3 из 5',
                    '4' => '4 из 5',
                    '5' => '5 из 5',
                ) ),
        ) );
}
