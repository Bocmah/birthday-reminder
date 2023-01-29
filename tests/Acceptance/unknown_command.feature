Feature: report unknown command
    Scenario: I issue unknown command
        When I issue the "unknown_command" command
        Then I should receive message "Такой команды я не знаю... Наберите help, чтобы увидеть список доступных команд."
