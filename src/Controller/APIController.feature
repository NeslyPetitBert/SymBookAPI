Background: 
    Given there are Users with the following details:
    | id | email                   | firstName  | lastName | password   |
    |  1 | npetitbert@vue-sym.test | PETIT BERT | Nesly    | myPassword |
    
Scenario: Different methods to serialize data before sending to API
    When I send a "POST" request to "/api/register"
    Then If the data is serialize, the response code should 200
    And The response header "Content-Type" should be equal to "application/json; charset=utf-8"
    And the response should contain json:
    """"
    {
        "id": "1",
        "email": "npetitbert@vue-sym.test",
        "firstName": "PETIT BERT",
        "lastName": "Nesly",
        "password": "myPassword"
    } 
    """"