<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package gorizont
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function gorizont_body_classes( $classes ) {
	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	// Adds a class of no-sidebar when there is no sidebar present.
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'no-sidebar';
	}

	return $classes;
}
add_filter( 'body_class', 'gorizont_body_classes' );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function gorizont_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'gorizont_pingback_header' );

/**
 * Ссылка на первую опубликованную страницу с этим шаблоном. Если такой страницы нет — пустая строка.
 */
function gorizont_template_page_url( $template ) {
	$pages = get_posts( array(
		'post_type'      => 'page',
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'meta_key'       => '_wp_page_template',
		'meta_value'     => $template,
	) );

	return $pages ? get_permalink( $pages[0] ) : '';
}

/**
 * Положение широкой обложки: какую часть кадра оставить при обрезке.
 */
function gorizont_hero_position_style( $position ) {
    $map = array( 'top' => 'center 20%', 'bottom' => 'center 85%' );

    return isset( $map[ $position ] ) ? ' style="object-position: ' . $map[ $position ] . '"' : '';
}

/**
 * Фото дня в программе тура. Рамка широкая (500×280), поэтому вертикальный снимок
 * показываем целиком по центру, а по бокам — его же размытую копию, чтобы не резать людей.
 */
function gorizont_timeline_image( $image_id ) {
    // data-full — фото в полном размере для окна просмотра (initPhotoLightbox)
    $image = wp_get_attachment_image( $image_id, 'large', false, array(
        'class'      => 'timeline-gallery__img',
        'data-full'  => wp_get_attachment_image_url( $image_id, 'full' ),
        'tabindex'   => '0',
        'role'       => 'button',
        'aria-label' => 'Увеличить фото',
    ) );
    $meta  = wp_get_attachment_metadata( $image_id );

    $is_tall = ! empty( $meta['width'] ) && ! empty( $meta['height'] ) && $meta['width'] / $meta['height'] < 1.45; // 4:3 и уже

    if ( ! $is_tall ) {
        return $image;
    }

    $backdrop = wp_get_attachment_image_url( $image_id, 'medium' );

    return '<span class="timeline-photo timeline-photo--fit" style="' . esc_attr( '--photo-backdrop: url(' . esc_url( $backdrop ) . ')' ) . '">' . $image . '</span>';
}
