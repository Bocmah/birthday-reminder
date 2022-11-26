Feature: change birthdate
    In order to change birthdate
    As an observer
    I need to issue the "change birthdate" command

    Scenario: change birthdate of the existing observee
        Given I observe user with id 333, first name "James", last name "Dean" and birthdate 15.10.1996
        When I issue the "change birthdate" command with user id 333 and birthdate 09.05.1990
        Then I should receive message "День рождения пользователя с id 333 был измененен."
        And I should see user with id 333, first name "James", last name "Dean" and birthdate 09.05.1990 in observees list

    Scenario: not observing user
        When I issue the "change birthdate" command with user id 404 and birthdate 09.05.1990
        Then I should receive message "Вы не следите за днем рождения пользователя с id 404."
