<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Документы раздела «Юридические документы»: дочерние страницы по порядку.
 * Администратор видит и черновики, чтобы проверить раздел до публикации.
 */
function gorizont_legal_docs( $parent_id ) {
    $statuses = current_user_can( 'edit_pages' ) ? array( 'publish', 'draft', 'private' ) : array( 'publish' );

    return get_posts( array(
        'post_type'      => 'page',
        'post_parent'    => $parent_id,
        'post_status'    => $statuses,
        'posts_per_page' => -1,
        'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
    ) );
}

/**
 * Реквизиты из «Контакты и заявки → Реквизиты» внутри текстов документов:
 * [gorizont_req key="short_name"], [gorizont_req key="ids"], [gorizont_req key="site"] и т.д.
 */
add_shortcode( 'gorizont_req', 'gorizont_req_shortcode' );
function gorizont_req_shortcode( $atts ) {
    $key = $atts['key'] ?? '';

    if ( $key === 'email' ) {
        $email = gorizont_email();

        return '<a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>';
    }

    if ( $key === 'site' ) {
        $url = untrailingslashit( home_url() );

        return '<a href="' . esc_url( $url ) . '">' . esc_html( $url ) . '</a>';
    }

    if ( $key === 'ids' ) {
        $ids = array_filter( array(
            'ИНН'  => gorizont_contact( 'legal_inn' ),
            'КПП'  => gorizont_contact( 'legal_kpp' ),
            'ОГРН' => gorizont_contact( 'legal_ogrn' ),
        ) );
        $parts = array();
        foreach ( $ids as $label => $value ) {
            $parts[] = $label . ': ' . $value;
        }

        return esc_html( implode( ', ', $parts ) );
    }

    return esc_html( gorizont_contact( 'legal_' . sanitize_key( $key ) ) );
}

/**
 * Ссылка на документ раздела по его адресу: gorizont_legal_url( 'privacy' ).
 */
function gorizont_legal_url( $slug = '' ) {
    return home_url( '/legal/' . ( $slug ? $slug . '/' : '' ) );
}
