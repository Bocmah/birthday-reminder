Feature: stop observing
    In order to stop observing
    As an observer
    I need to issue the "stop observing" command

    Scenario: stop observing the existing observee
        Given I observe user with id 333 and birthdate 15.10.1996
        When I issue the "stop observing" command with user id 333
        Then I should receive message "Вы больше не следите за днем рождения пользователя с id 333."
        And I should see no one in observees list

    Scenario: not observing user
        When I issue the "stop observing" command with user id 404
        Then I should receive message "Вы не следите за днем рождения пользователя с id 404."
