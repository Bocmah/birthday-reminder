Feature: send confirmation key
    Scenario: valid confirmation key
        When API receives request with "confirmation" type
        Then it should respond with confirmation key
