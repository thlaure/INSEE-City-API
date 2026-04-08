Feature: City API
  As an API consumer
  I want to retrieve French cities
  So that I can display city information

  Scenario: GET /api/v1/cities returns empty collection when no cities exist
    Given there are no cities in the database
    When I send a "GET" request to "/api/v1/cities"
    Then the response status code should be 200
    And the response should be JSON
    And the JSON collection should be empty
    And the response content type should contain "application/ld+json"

  Scenario: GET /api/v1/cities returns all cities
    Given the following cities exist:
      | inseeCode | name  | departmentCode | regionCode |
      | 75056     | Paris | 75             | 11         |
      | 69123     | Lyon  | 69             | 84         |
    When I send a "GET" request to "/api/v1/cities"
    Then the response status code should be 200
    And the response should be JSON
    And the JSON collection should have 2 items
    And the JSON response "totalItems" should equal "2"

  Scenario: GET /api/v1/cities filters by name (partial, case-insensitive)
    Given the following cities exist:
      | inseeCode | name  | departmentCode | regionCode |
      | 75056     | Paris | 75             | 11         |
      | 69123     | Lyon  | 69             | 84         |
    When I send a "GET" request to "/api/v1/cities?name=par"
    Then the response status code should be 200
    And the JSON collection should have 1 items
    And the JSON response "totalItems" should equal "1"

  Scenario: GET /api/v1/cities filters by departmentCode (exact match)
    Given the following cities exist:
      | inseeCode | name     | departmentCode | regionCode |
      | 75056     | Paris    | 75             | 11         |
      | 75008     | Paris 8e | 75             | 11         |
      | 69123     | Lyon     | 69             | 84         |
    When I send a "GET" request to "/api/v1/cities?departmentCode=75"
    Then the response status code should be 200
    And the JSON collection should have 2 items

  Scenario: GET /api/v1/cities filters by regionCode (exact match)
    Given the following cities exist:
      | inseeCode | name  | departmentCode | regionCode |
      | 75056     | Paris | 75             | 11         |
      | 69123     | Lyon  | 69             | 84         |
    When I send a "GET" request to "/api/v1/cities?regionCode=84"
    Then the response status code should be 200
    And the JSON collection should have 1 items

  Scenario: GET /api/v1/cities response has correct content type
    Given there are no cities in the database
    When I send a "GET" request to "/api/v1/cities"
    Then the response status code should be 200
    And the response content type should contain "application/ld+json"

  Scenario: GET /api/v1/cities rejects empty name filter
    Given there are no cities in the database
    When I send a "GET" request to "/api/v1/cities?name="
    Then the response status code should be 422
    And the response should be JSON

  Scenario: GET /api/v1/cities rejects empty departmentCode filter
    Given there are no cities in the database
    When I send a "GET" request to "/api/v1/cities?departmentCode="
    Then the response status code should be 422
    And the response should be JSON

  Scenario: GET /api/v1/cities rejects empty regionCode filter
    Given there are no cities in the database
    When I send a "GET" request to "/api/v1/cities?regionCode="
    Then the response status code should be 422
    And the response should be JSON

  Scenario: GET /api/v1/cities rejects page less than 1
    Given there are no cities in the database
    When I send a "GET" request to "/api/v1/cities?page=0"
    Then the response status code should be 422
    And the response should be JSON

  Scenario: GET /api/v1/cities rejects itemsPerPage greater than 100
    Given there are no cities in the database
    When I send a "GET" request to "/api/v1/cities?itemsPerPage=101"
    Then the response status code should be 422
    And the response should be JSON

  Scenario: GET /api/v1/cities/{inseeCode} returns a single city
    Given the following cities exist:
      | inseeCode | name    | departmentCode | regionCode |
      | 2A004     | Ajaccio | 2A             | 94         |
    When I send a "GET" request to "/api/v1/cities/2A004"
    Then the response status code should be 200
    And the response should be JSON
    And the JSON response "inseeCode" should equal "2A004"
    And the JSON response "name" should equal "Ajaccio"
    And the JSON response "departmentCode" should equal "2A"
    And the response content type should contain "application/ld+json"

  Scenario: GET /api/v1/cities/{inseeCode} returns 404 for unknown INSEE code
    Given there are no cities in the database
    When I send a "GET" request to "/api/v1/cities/UNKNOWN"
    Then the response status code should be 404
    And the response should be JSON
