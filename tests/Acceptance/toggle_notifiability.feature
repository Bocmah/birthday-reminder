Feature: toggle notifiablity
    In order to control whether I'm notified even if there are no upcoming birthdays
    As an observer
    I need to issue the "notify" command

    Scenario: I'm notified even if there are no upcoming birthdays and I want to turn it off
        Given I'm notified even if there are no upcoming birthdays
        When I issue the "notify" command
        Then I should receive message "Теперь вы будете получать уведомления, только если скоро у кого-то день рождения."

    Scenario: I'm notified only if there are upcoming birthdays and I want to always be notified
        Given I'm notified only if there are upcoming birthdays
        When I issue the "notify" command
        Then I should receive message "Теперь вы будете получать уведомления, даже если в ближайшее время нет дней рождений."
