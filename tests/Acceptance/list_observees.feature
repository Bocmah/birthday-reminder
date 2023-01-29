Feature: list observees
    In order to see my observees
    As an observer
    I need to issue the "list" command

    Scenario: I have two observees
        Given I observe users
            | id  | firstName | lastName | birthdate  |
            | 333 | James     | Dean     | 05.09.1985 |
            | 444 | Kate      | Watts    | 11.11.2001 |
        When I issue the "list" command
        Then I should receive message
"""
*id333 (James Dean) - 05.09.1985
*id444 (Kate Watts) - 11.11.2001
"""

    Scenario: I have no observees
        When I issue the "list" command
        Then I should receive message "Вы еще не отслеживаете день рождения ни одного юзера."
