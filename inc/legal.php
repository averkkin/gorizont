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
