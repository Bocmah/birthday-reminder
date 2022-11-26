Feature: start observing
    In order to start observing
    As an observer
    I need to issue the "start observing" command

    Scenario: new observee
        When I issue the "start observing" command with user id 333 and birthdate 15.10.1996
        Then I should receive message "Теперь вы следите за днем рождения пользователя с id 333."
        And I should see user with id 333, first name "James", last name "Dean" and birthdate 15.10.1996 in observees list

    Scenario: already observing user
        Given I observe user with id 333
        When I issue the "start observing" command with user id 333 and birthdate 15.10.1996
        Then I should receive message "Вы уже следите за днем рождения пользователя с id 333."

    Scenario: observee was not found on the platform
        When I issue the "start observing" command with user id 404 and birthdate 15.10.1996
        Then I should receive message "Пользователь с id 404 не найден."
