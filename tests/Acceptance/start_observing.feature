Feature: start observing
  In order to start observing
  As an observer
  I need to issue the "start observing" command

  Scenario: new observee
      When I issue the "start observing" command with user id 333 and birthdate 15.10.1996
      Then I should receive "Теперь вы следите за днем рождения пользователя с id 333." message
      And I should see user with id 333, name "James Dean" and birthdate 15.10.1996 in observees list

  Scenario: already observing user
      Given I observe user with id 333
      When I issue the "start observing" command with user id 333 and birthdate 15.10.1996
      Then I should receive "Вы уже следите за днем рождения пользователя с id 333." message

  Scenario: observee was not found on the platform
      When I issue the "start observing" command with user id 404 and birthdate 15.10.1996
      Then I should receive "Пользователь с id 404 не найден." message
