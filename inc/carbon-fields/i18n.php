<?php
/**
 * Русский интерфейс полей Carbon Fields в админке.
 *
 * Carbon Fields отдаёт переводы в JS, перебирая $translations->entries как «msgid => entry».
 * С WordPress 6.5 переводы хранятся в WP_Translations, у которого ключи entries числовые, —
 * в браузер уходят «0, 1, 2…» вместо исходных строк, и интерфейс остаётся английским.
 * Пересобираем словарь с правильными ключами и дополняем строками, которых нет в переводе плагина.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_filter('carbon_fields_config', function ($config) {
    if (!isset($config['config']['locale']) || !is_array($config['config']['locale'])) {
        return $config;
    }

    $locale = array('' => isset($config['config']['locale']['']) ? $config['config']['locale'][''] : array('domain' => 'carbon-fields-ui'));

    $translations = get_translations_for_domain('carbon-fields-ui');
    foreach ((array) $translations->entries as $entry) {
        if (empty($entry->singular) || empty($entry->translations)) {
            continue;
        }
        $key = !empty($entry->context) ? $entry->context . "\004" . $entry->singular : $entry->singular;
        $locale[$key] = $entry->translations;
    }

    // Свои формулировки — проще, чем в переводе плагина
    $overrides = array(
        'Select Image'              => 'Выбрать фото',
        'Use Image'                 => 'Выбрать',
        'Select Attachments'        => 'Выбрать фотографии',
        'Use Attachments'           => 'Выбрать',
        'Select File'               => 'Выбрать файл',
        'Use File'                  => 'Выбрать',
        'There are no entries yet.' => 'Пока ничего не добавлено.',
        'Add %s'                    => 'Добавить %s',
        'Duplicate'                 => 'Дублировать',
        'Remove'                    => 'Удалить',
        'Collapse'                  => 'Свернуть',
        'Expand All'                => 'Развернуть все',
        'Collapse All'              => 'Свернуть все',
        'Search...'                 => 'Поиск...',
        'No options.'               => 'Нет вариантов.',
        'No results found.'         => 'Ничего не найдено.',
        'This field is required.'   => 'Обязательное поле.',
    );
    foreach ($overrides as $msgid => $msgstr) {
        $locale[$msgid] = array($msgstr);
    }

    $config['config']['locale'] = $locale;
    return $config;
});
