<?php

declare(strict_types=1);

return [
    'validation' => [
        'command' => [
            'invalid_format' => 'Некорректный формат команды. Пожалуйста, используйте формат {valid_format}.',
        ],
        'date' => [
            'invalid_format' => 'Некорректный формат даты. Пожалуйста, используйте формат {valid_format}.',
        ],
        'vk_id' => [
            'invalid_format' => 'Некорректный формат VK id. VK id может быть либо целочисленным значением, либо именем страницы, которое задал пользователь.',
        ],
    ],
    'user' => [
        'retrieve' => [
            'unknown_error' => 'Произошла неизвестная ошибка при попытке получить данные о пользователе с id {vk_id}.',
            'deactivated' => 'Не удалось получить данные о юзере с id {vk_id}, поскольку он деактивирован.',
        ],
        'retrieve_unknown_error' => 'Произошла неизвестная ошибка при попытке получить данные о пользователе с id {vk_id}.',
        'not_found_on_the_platform' => 'Пользователь с id {id} не найден.',
    ],
    'observee' => [
        'started_observing' => 'Теперь вы следите за днем рождения пользователя с id {id}.',
        'already_observing' => 'Вы уже следите за днем рождения пользователя с id {id}.',
        'not_observing' => 'Вы не следите за днем рождения пользователя с id {id}.',
        'birthday_changed' => 'День рождения пользователя с id {id} был измененен.',
    ],
    'birthdays_on_date' => '{date} дни рождения у этих людей:',
    'no_upcoming_birthdays' => 'Сегодня и завтра дней рождения не предвидится.',
    'unexpected_error' => 'Произошла непредвиденная ошибка.'
];
