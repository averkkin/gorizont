<?php
/**
 * Расписание туров: даты, прошедшие выезды, счётчики и ссылки на страницы расписания.
 *
 * Дата тура хранится текстом («28 июня 2026»), поэтому разбираем её сами.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Типы, у которых прошедшие выезды скрываются, а расписание идёт по дате.
 * У выездных туров даты бывают условными, их не трогаем.
 */
function gorizont_schedule_is_dated( $tour_type ) {
    return 'local_tours' === $tour_type;
}

/**
 * «28 июня 2026» → метка времени начала дня. Если разобрать не удалось — null.
 */
function gorizont_parse_tour_date( $text ) {
    $months = array(
        'января' => 1, 'февраля' => 2, 'марта' => 3, 'апреля' => 4, 'мая' => 5, 'июня' => 6,
        'июля' => 7, 'августа' => 8, 'сентября' => 9, 'октября' => 10, 'ноября' => 11, 'декабря' => 12,
    );

    $text = function_exists( 'mb_strtolower' ) ? mb_strtolower( trim( (string) $text ), 'UTF-8' ) : strtolower( trim( (string) $text ) );

    if ( ! preg_match( '/(\d{1,2})\s+([а-яё]+)\s+(\d{4})/u', $text, $matches ) || ! isset( $months[ $matches[2] ] ) ) {
        return null;
    }

    $date = DateTimeImmutable::createFromFormat( '!Y-n-j', $matches[3] . '-' . $months[ $matches[2] ] . '-' . (int) $matches[1], wp_timezone() );

    return $date ? $date->getTimestamp() : null;
}

/**
 * Прошёл ли выезд. Сегодняшний ещё показываем.
 */
function gorizont_tour_is_past( $tour_id ) {
    $date = gorizont_parse_tour_date( carbon_get_post_meta( $tour_id, 'tour_start_date' ) );
    $today = ( new DateTimeImmutable( 'today', wp_timezone() ) )->getTimestamp();

    return null !== $date && $date < $today;
}

/**
 * Для датированных типов: убрать прошедшие выезды и отсортировать по дате.
 * Выезды без разборчивой даты остаются в конце.
 */
function gorizont_prepare_schedule_posts( array $posts, $tour_type ) {
    if ( ! gorizont_schedule_is_dated( $tour_type ) ) {
        return $posts;
    }

    $posts = array_values( array_filter( $posts, function ( $post ) {
        return ! gorizont_tour_is_past( $post->ID );
    } ) );

    usort( $posts, function ( $a, $b ) {
        $a_date = gorizont_parse_tour_date( carbon_get_post_meta( $a->ID, 'tour_start_date' ) );
        $b_date = gorizont_parse_tour_date( carbon_get_post_meta( $b->ID, 'tour_start_date' ) );

        return ( $a_date ?? PHP_INT_MAX ) <=> ( $b_date ?? PHP_INT_MAX );
    } );

    return $posts;
}

/**
 * Условие выборки туров типа: основной тип или отмечен в «Показывать также на страницах».
 * Carbon хранит каждое значение set-поля отдельной строкой с ключом вида _tour_extra_types|||0|value.
 */
function gorizont_tour_type_meta_query( $tour_type ) {
    return array(
        'relation' => 'OR',
        array( 'key' => '_tour_type', 'value' => $tour_type ),
        array( 'key' => '_tour_extra_types|||', 'compare_key' => 'LIKE', 'value' => $tour_type ),
    );
}

/**
 * Сколько туров покажет расписание этого типа.
 */
function gorizont_count_schedule_tours( $tour_type ) {
    $posts = get_posts( array(
        'post_type'      => 'tours',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'meta_query'     => array( gorizont_tour_type_meta_query( $tour_type ) ),
    ) );

    return count( gorizont_prepare_schedule_posts( $posts, $tour_type ) );
}

/**
 * Ссылка на страницу «Расписание туров», где выбран этот тип. Если такой страницы нет — $fallback.
 */
function gorizont_schedule_page_url( $tour_type, $fallback = '' ) {
    $pages = get_posts( array(
        'post_type'      => 'page',
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'meta_query'     => array(
            array( 'key' => '_wp_page_template', 'value' => 'template-tour-schedule.php' ),
            array( 'key' => '_tour_type_filter', 'value' => $tour_type ),
        ),
    ) );

    return $pages ? get_permalink( $pages[0] ) : $fallback;
}

/**
 * «3 тура», «5 туров».
 */
function gorizont_plural_tours( $count ) {
    $mod100 = $count % 100;
    $mod10  = $count % 10;

    if ( $mod100 >= 11 && $mod100 <= 14 ) {
        $word = 'туров';
    } elseif ( 1 === $mod10 ) {
        $word = 'тур';
    } elseif ( $mod10 >= 2 && $mod10 <= 4 ) {
        $word = 'тура';
    } else {
        $word = 'туров';
    }

    return $count . ' ' . $word;
}
