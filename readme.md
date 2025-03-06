# URL SHORTENER SERVICE
- Tech Stack: PHP/Laravel

## Brief
**Objective:** 
Your assignment is to implement a URL shortening service.

**Brief:** 
Create a URL shortening service where you enter a URL such as https://www.thisisalongdomain.com/with/some/parameters?and=here_too and it returns a short URL such as http://short.est/GeAi9K.

**Tasks:**

Two endpoints are required:

/encode - Encodes a URL to a shortened URL

/decode - Decodes a shortened URL to its original URL

Both endpoints should return JSON. There is no restriction on how your encode/decode algorithm should work. You just need to make sure that a URL can be encoded to a short URL and the short URL can be decoded to the original URL.

You do not need to persist short URLs if you don't need to you can keep them in memory. Provide detailed instructions on how to run your assignment in a separate markdown file or readme.

Cover all functionality with tests.


## [Deliverable](./docs/readme.md)
**Key Resources**
- [Detailed Instructions on How to Run Assignment](./docs/readme.md)
- [Postman Collection for Integration Tests](https://www.postman.com/restless-sunset-44843/workspace/url-shortener/collection/31925882-d7636ed2-4143-43e3-bc20-65f846b47d47?action=share&creator=31925882)
- Github Action Workflows for automatic running of Continuous Integration (CI) Unit Test Cases
- PHPUnit to run local unit test cases  

## [Implementation Work Plan](./work-plan.md)
This highlights my thought process and considerations, its very important and I urge you to take a look.

## Key Functionality Code Artefacts
- laravel-app/app/services/UrlShortenerService.php: core program logic
- laravel-app/app/HTTP/Controllers/UrlShortenerController.php
- laravel-app/config/url_shortener.php: contains settings / feature flags
- laravel-app/routes/api.php: manage api routes for endpoints
- laravel-app/.env.simple-example: used to generate env file