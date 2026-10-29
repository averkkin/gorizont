<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Carbon_Fields\Container;
use Carbon_Fields\Field;

/**
 * Настройки сайта: контакты и Telegram-бот для заявок (Настройки сайта в меню админки).
 */
add_action( 'carbon_fields_register_fields', 'gorizont_register_contacts_options' );
function gorizont_register_contacts_options() {
    Container::make( 'theme_options', 'Контакты и заявки' )
        ->set_icon( 'dashicons-phone' )
        ->add_tab( 'Контакты', array(
            Field::make( 'text', 'contact_phone', 'Телефон в шапке и подвале' )
                ->set_default_value( '8(3952)93-90-93' ),
            Field::make( 'text', 'contact_email', 'Почта' )
                ->set_default_value( 'gorizont-tour@yandex.ru' ),
            Field::make( 'complex', 'contact_people', 'Контакты менеджеров в подвале' )
                ->set_layout( 'tabbed-horizontal' )
                ->add_fields( array(
                    Field::make( 'text', 'name', 'Имя' )->set_width( 50 ),
                    Field::make( 'text', 'phone', 'Телефон' )->set_width( 50 ),
                ) )
                ->set_default_value( array(
                    array( 'name' => 'Сергей', 'phone' => '+79642710683' ),
                    array( 'name' => 'Анастасия', 'phone' => '+79501128904' ),
                ) ),
        ) )
        ->add_tab( 'Реквизиты', array(
            Field::make( 'html', 'legal_help' )->set_html( '<p>Реквизиты подставляются в юридические документы сайта (раздел «Юридические документы»).</p>' ),
            Field::make( 'text', 'legal_full_name', 'Полное наименование' )
                ->set_default_value( 'Общество с ограниченной ответственностью «Центр туризма Горизонт»' ),
            Field::make( 'text', 'legal_short_name', 'Краткое наименование' )
                ->set_default_value( 'ООО «Центр туризма Горизонт»' ),
            Field::make( 'text', 'legal_address', 'Юридический адрес' )
                ->set_default_value( '664003, г. Иркутск, пер. Пионерский, д. 13, оф. 4' ),
            Field::make( 'text', 'legal_inn', 'ИНН' )->set_width( 33 )->set_default_value( '3808185809' ),
            Field::make( 'text', 'legal_kpp', 'КПП' )->set_width( 33 )->set_default_value( '380801001' ),
            Field::make( 'text', 'legal_ogrn', 'ОГРН' )->set_width( 33 )->set_default_value( '1083808014388' ),
            Field::make( 'text', 'legal_date', 'Документы действуют с' )
                ->set_default_value( '29 октября 2026 г.' ),
        ) )
        ->add_tab( 'Telegram-бот', array(
            Field::make( 'html', 'telegram_help' )->set_html(
                '<p>1. В Telegram откройте <b>@BotFather</b>, команда <code>/newbot</code>, получите токен.<br>'
                . '2. Создайте группу для заявок (или напишите боту лично) и добавьте туда бота.<br>'
                . '3. Узнайте ID чата, например через бота <b>@getmyid_bot</b> (для группы ID начинается с минуса).<br>'
                . '4. Вставьте токен и ID ниже и сохраните.</p>'
            ),
            Field::make( 'text', 'telegram_bot_token', 'Токен бота' ),
            Field::make( 'text', 'telegram_chat_id', 'ID чата для заявок' ),
            Field::make( 'text', 'lead_emails', 'Почта для заявок' )
                ->set_help_text( 'Каждая заявка дублируется на эту почту. Можно несколько адресов через запятую. Если пусто — на почту из вкладки «Контакты».' ),
        ) );
}

function gorizont_contact( $key, $default = '' ) {
    $value = function_exists( 'carbon_get_theme_option' ) ? carbon_get_theme_option( $key ) : '';

    return $value !== '' && $value !== null ? $value : $default;
}

function gorizont_phone_href( $phone ) {
    return 'tel:' . preg_replace( '/[^\d+]/', '', $phone );
}

/**
 * +79642710683 → +7 (964) 271-06-83. Номера другого вида выводятся как введены.
 */
function gorizont_phone_format( $phone ) {
    $digits = preg_replace( '/\D/', '', $phone );

    if ( strlen( $digits ) !== 11 || ! in_array( $digits[0], array( '7', '8' ), true ) ) {
        return $phone;
    }

    return sprintf( '+7 (%s) %s-%s-%s', substr( $digits, 1, 3 ), substr( $digits, 4, 3 ), substr( $digits, 7, 2 ), substr( $digits, 9, 2 ) );
}

function gorizont_phone() {
    return gorizont_contact( 'contact_phone', '8(3952)93-90-93' );
}

function gorizont_email() {
    return gorizont_contact( 'contact_email', 'gorizont-tour@yandex.ru' );
}

function gorizont_contact_people() {
    $people = function_exists( 'carbon_get_theme_option' ) ? carbon_get_theme_option( 'contact_people' ) : array();

    return is_array( $people ) ? $people : array();
}

/**
 * Приём заявки: форма отправляется через admin-ajax, текст уходит в Telegram.
 */
add_action( 'wp_ajax_gorizont_lead', 'gorizont_handle_lead' );
add_action( 'wp_ajax_nopriv_gorizont_lead', 'gorizont_handle_lead' );
function gorizont_handle_lead() {
    // Ловушка для ботов: поле скрыто от людей
    if ( ! empty( $_POST['website'] ) ) {
        wp_send_json_success();
    }

    $name  = sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) );
    $phone = sanitize_text_field( wp_unslash( $_POST['phone'] ?? '' ) );
    $page  = esc_url_raw( wp_unslash( $_POST['page'] ?? '' ) );

    if ( empty( $_POST['consent'] ) ) {
        wp_send_json_error( array( 'message' => 'Нужно согласие на обработку персональных данных' ), 400 );
    }

    if ( $name === '' || strlen( preg_replace( '/\D/', '', $phone ) ) < 6 ) {
        wp_send_json_error( array( 'message' => 'Укажите имя и телефон' ), 400 );
    }

    // Защита от спама: не больше 20 заявок в час с одного адреса
    $key   = 'gorizont_lead_' . md5( $_SERVER['REMOTE_ADDR'] ?? '' );
    $count = (int) get_transient( $key );
    if ( $count >= 20 ) {
        wp_send_json_error( array( 'message' => 'Слишком много заявок, позвоните нам' ), 429 );
    }
    set_transient( $key, $count + 1, HOUR_IN_SECONDS );

    $token = gorizont_contact( 'telegram_bot_token' );
    $chat  = gorizont_contact( 'telegram_chat_id' );

    $forms = array(
        'login-form'   => 'Оставьте заявку',
        'develop-form' => 'Раздел в разработке',
    );
    $form   = $forms[ sanitize_key( wp_unslash( $_POST['form'] ?? '' ) ) ] ?? '';
    $source = mb_substr( sanitize_text_field( wp_unslash( $_POST['source'] ?? '' ) ), 0, 200 );

    $page_title = '';
    if ( $page ) {
        $post_id    = url_to_postid( $page );
        $page_title = $post_id ? get_the_title( $post_id ) : '';
        if ( ! $page_title ) {
            $path       = trim( (string) wp_parse_url( $page, PHP_URL_PATH ), '/' );
            $page_title = $path === '' ? 'Главная' : urldecode( $path );
        }
    }

    $html = "🔔 <b>Новая заявка</b>\n\n"
        . '👤 ' . esc_html( $name ) . "\n"
        . '📞 ' . esc_html( $phone ) . "\n"
        . ( $form ? "\n📝 Форма: " . esc_html( $form ) : '' )
        . ( $source ? "\n🖱 Кнопка: " . esc_html( $source ) : '' )
        . ( $page ? "\n📄 Страница: <a href=\"" . esc_url( $page ) . '">' . esc_html( $page_title ) . '</a>' : '' )
        . "\n🕒 " . wp_date( 'd.m.Y, H:i' );

    $text = "Новая заявка с сайта\n\nИмя: {$name}\nТелефон: {$phone}\n"
        . ( $form ? "\nФорма: {$form}" : '' )
        . ( $source ? "\nКнопка: {$source}" : '' )
        . ( $page ? "\nСтраница: {$page_title}\n{$page}" : '' )
        . "\nВремя: " . wp_date( 'd.m.Y, H:i' );

    // Заявка уходит и в Telegram, и на почту; достаточно, чтобы дошла хотя бы одним путём
    $emails    = array_filter( array_map( 'trim', explode( ',', gorizont_contact( 'lead_emails', gorizont_email() ) ) ), 'is_email' );
    $mail_sent = $emails && wp_mail( $emails, "Заявка с сайта: {$name}, {$phone}", $text, array(
        'From: Горизонт | Заявки <noreply@' . wp_parse_url( home_url(), PHP_URL_HOST ) . '>',
    ) );

    $sent = false;
    if ( $token && $chat ) {
        $response = wp_remote_post( "https://api.telegram.org/bot{$token}/sendMessage", array(
            'timeout' => 10,
            'body'    => array(
                'chat_id'                  => $chat,
                'text'                     => $html,
                'parse_mode'               => 'HTML',
                'disable_web_page_preview' => 'true',
            ),
        ) );
        $sent = ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) === 200;
    }

    if ( ! $sent && ! $mail_sent ) {
        wp_send_json_error( array( 'message' => 'Не удалось отправить, позвоните нам' ), 500 );
    }

    wp_send_json_success();
}

add_action( 'wp_enqueue_scripts', function () {
    wp_localize_script( 'my-theme-index', 'gorizontLead', array(
        'url' => admin_url( 'admin-ajax.php' ),
    ) );
}, 20 );
