<?php

if ( !defined ("ABSPATH") ) {exit;}

use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action('carbon_fields_register_fields', 'crb_attach_page_and_tour_fields');
function crb_attach_page_and_tour_fields() {
    // 1. Поля для шаблона страницы (Hero-блок и фильтрация)
    Container::make('post_meta', 'Настройки страницы расписания')
        ->where('post_template', '=', 'template-tour-schedule.php')
        ->add_fields(array(
            // Поля для Hero-блока
            Field::make('text', 'hero_title', 'Заголовок Hero')->set_help_text('Можно использовать <br> для переноса'),
            Field::make('textarea', 'hero_desc', 'Краткое описание на главном экране'),
            Field::make('image', 'hero_bg', 'Фоновое изображение')->set_value_type('url'),

            // Настройка фильтрации туров для этой страницы
            Field::make('select', 'tour_type_filter', ' Какие туры выводить на этой странице?')
                ->add_options(array(
                    'all' => 'Все',
                    'local_tours' => 'Местные туры',
                    'intl_tours' => 'Выездные туры',
                    'local_excursions' => 'Местные экскурсии',
                    'intl_excursions' => 'Выездные экскурсии',
                    'kids_tours' => 'Школьные поездки',
                )),
        ));

    // Описание направления и форматы — между Hero и расписанием. Блок выводится, только если заполнен
    Container::make('post_meta', 'Описание направления и форматы')
        ->where('post_template', '=', 'template-tour-schedule.php')
        ->add_fields(array(
            Field::make('text', 'schedule_about_title', 'Заголовок описания'),
            Field::make('textarea', 'schedule_about_text', 'Текст описания')
                ->set_help_text('Абзацы разделяйте пустой строкой. Если пусто — желтый блок описания не выводится'),
            Field::make('complex', 'schedule_about_meta', 'Плашки под описанием')
                ->set_max(3)
                ->add_fields(array(
                    Field::make('text', 'label', 'Подпись (например: Где:)')->set_width(50),
                    Field::make('text', 'text', 'Значение')->set_width(50),
                ))
                ->set_header_template('<%- label %>'),

            Field::make('text', 'schedule_formats_title', 'Заголовок форматов')->set_default_value('Форматы походов'),
            Field::make('complex', 'schedule_formats', 'Форматы (вкладки)')
                ->set_layout('tabbed-horizontal')
                ->add_fields(array(
                    Field::make('text', 'tab', 'Название вкладки (например: 1 день)'),
                    Field::make('text', 'title', 'Заголовок формата (например: Однодневные туры)'),
                    Field::make('textarea', 'text', 'Как проходит')
                        ->set_help_text('Абзацы разделяйте пустой строкой'),
                    Field::make('textarea', 'included', 'Что входит в стоимость (каждый пункт с новой строки)'),
                    Field::make('textarea', 'routes', 'Маршруты (каждый с новой строки)'),
                ))
                ->set_header_template('<%- tab %>'),
        ));
}

// 2. Регистрируем кастомный тип записей "Туры и экскурсии" (если еще не создан)
add_action('init', 'register_tours_cpt');
function register_tours_cpt() {
    register_post_type('tours', array(
        'labels' => array(
            'name' => 'Туры и экскурсии',
            'singular_name' => 'Тур/Экскурсия',
            'add_new' => 'Добавить тур/экскурсию',
            'add_new_item' => 'Добавить новый тур',
            'edit_item' => 'Редактировать тур',
        ),
        'public' => true,
        'has_archive' => false,
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
        'menu_icon' => 'dashicons-palmtree',
    ));
}

// 3. Добавляем таксономию или просто категорию Carbon Fields для разделения типов туров
add_action('carbon_fields_register_fields', 'crb_attach_tour_type');
function crb_attach_tour_type() {
    Container::make('post_meta', 'Параметры тура')
        ->where('post_type', '=', 'tours')
        ->add_fields(array(
            Field::make('select', 'tour_type', 'Тип мероприятия')
                ->add_options(array(
                    'local_tours' => 'Местные туры',
                    'intl_tours' => 'Выездные туры',
                    'local_excursions' => 'Местные экскурсии',
                    'intl_excursions' => 'Выездные экскурсии',
                    'kids_tours' => 'Школьные поездки',
                )),
            Field::make('select', 'tour_region', 'Куда (для фильтра школьных поездок)')
                ->add_options(array(
                    '' => '—',
                    'russia' => 'По России',
                    'abroad' => 'За рубеж',
                )),
            Field::make('checkbox', 'is_popular', 'Популярный тур (для фильтра)'),
            Field::make('select', 'tour_duration', 'Длительность')
                ->add_options(array(
                    '1' => 'Однодневный',
                    '2' => 'Двухдневный',
                    '3' => '3-х дневный',
                    '5' => '5-ти дневный',
                    '7' => '7-ми дневный',
                    'other' => 'Другое',
                )),
            Field::make('text', 'tour_start_date', 'Дата начала (текстом, например: 28 июня 2026)'),
            Field::make('select', 'tour_difficulty_stars', 'Уровень сложности (количество иконок)')
                ->add_options(array(
                    '1' => '1 из 5',
                    '2' => '2 из 5',
                    '3' => '3 из 5',
                    '4' => '4 из 5',
                    '5' => '5 из 5',
                )),
        ));
}

add_action('carbon_fields_register_fields', 'crb_attach_single_tour_fields');
function crb_attach_single_tour_fields() {
    Container::make('post_meta', 'Детали тура / Экскурсии')
        ->where('post_type', '=', 'tours')
        ->add_fields(array(
            // 1. Блок "Описание тура" (желтая плашка)
            Field::make('textarea', 'tour_hero_description', 'Описание тура'),
            Field::make('text', 'tour_duration_days', 'Продолжительность (например: 4 дня / 3 ночи)'),
            Field::make('text', 'tour_difficulty', 'Сложность (например: доступно всем)'),
            Field::make('text', 'tour_format', 'Формат (например: Автобусно-пешеходный тур)'),

            // 2. Блок "Программа по дням" (Сложный повторяемый элемент)
            Field::make('complex', 'tour_program', 'Программа по дням')
                ->set_layout('tabbed-vertical')
                ->add_fields(array(
                    Field::make('text', 'day_number', 'Номер дня (например: 1 день)'),
                    Field::make('text', 'day_title', 'Название дня'),
                    Field::make('image', 'day_img', 'Картинка дня')->set_value_type('url'),
                    Field::make('media_gallery', 'day_images', 'Фотографии дня'),

                    Field::make('complex', 'day_points', 'Пункты программы на этот день')
                        ->add_fields(array(
                            Field::make('text', 'point_text', 'Текст пункта')
                        ))
                        ->set_header_template('<%- point_text %>')
                        ->set_layout('tabbed-vertical')
                )),

            // 3. Условия участия
            Field::make('textarea', 'tour_included', 'Что включено (каждый пункт с новой строки)'),
            Field::make('textarea', 'tour_excluded', 'Что НЕ включено (каждый пункт с новой строки)'),
            Field::make('text', 'tour_price', 'Стоимость от (цифра или строка, например: 25 000)'),
            Field::make('select', 'tour_currency', 'Валюта стоимости')
                ->add_options(array(
                    'rub' => '₽',
                    'usd' => '$',
                ))
                ->set_default_value('rub'),

            // 4. Галерея тура
            Field::make('media_gallery', 'tour_gallery', 'Галерея картинок'),

            // 5. Выбор команды (Гид / Инструктор) — Сюда можно подтягивать кастомный CPT гидов или пользователей
            Field::make('association', 'tour_guides', 'Выберите гидов/команду')
                ->set_types(array(
                    array(
                        'type'      => 'post',
                        'post_type' => 'guides', // Предположим, у вас будет CPT 'guides' для команды
                    )
                ))
                ->set_help_text('Для экскурсий этот блок автоматически скроется на сайте'),

            Field::make('association', 'tour_reviews', 'Отзывы для страницы тура')
                ->set_types(array(
                    array(
                        'type'      => 'post',
                        'post_type' => 'reviews',
                    )
                ))
                ->set_help_text('Если отзывы не выбраны, на странице будут показаны последние опубликованные отзывы.'),

            Field::make('complex', 'tour_more_info', 'Дополнительная информация')
                ->set_layout('tabbed-vertical')
                ->add_fields(array(
                    Field::make('text', 'more_info_title', 'Заголовок раздела')
                        ->set_help_text('Например: Обувь или Одежда'),
                    Field::make('complex', 'more_info_items', 'Пункты раздела')
                        ->set_layout('tabbed-vertical')
                        ->add_fields(array(
                            Field::make('text', 'more_info_text', 'Текст пункта'),
                        ))
                        ->set_header_template('<%- more_info_text %>'),
                ))
                ->set_header_template('<%- more_info_title %>'),
        ));
}
