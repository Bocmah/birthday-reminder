Feature: get help
    In order to get description of all commands
    As an observer
    I need to issue the "help" command

    Scenario: get description of all commands
        When I issue the "get help" command
        Then I should receive message
"""
add id DD.MM.YYYY - Добавить день рождения. id - id юзера VK. Может быть как числовым, так и именем страницы, которое задал пользователь. DD.MM.YYYY - день рождения (например, 13.10.1996).

delete id - Удалить день рождения. id - id юзера VK.

update id DD.MM.YYYY - Изменить день рождения. id - id юзера VK. DD.MM.YYYY - новый день рождения (например, 05.06.2001).

list - Показать список дней рождения.

notify - Включает/выключает уведомления в случае, если ближайших дней рождений нет. Если включено, вы будете получать уведомления, даже если в ближайшее время нет дней рождений. Если отключено, то вы будете получать уведомления, только если скоро у кого-то день рождения.
"""
