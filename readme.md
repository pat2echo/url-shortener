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


## Action Plan
- env setup: use my work station or csee virual lab when on the move.
- review the brief

### Task Breakdown
#### In Scope
- Use PHP/Laravel considering job description
- 2 RESTful endpoints that support HTTP POST requests for URL encoding/decoding API
- Use of disk caching for persistence with lifespan of 30 days
- Basic validation and error handling
- Documentation and testing
- Publicily accessible endpoints for demonstration
- Use of github actions to run test cases (tentative)

#### Out of Scope
- Endpoint security, User authentication/authorization
- URL scanning for malicious content
- Analytics dashboard
- Custom domain support
- User management
- Rate limiting
- Alerts for error rates and response times
- Code style checker enforcement is ignored

#### Security Considerations
- Input validation to prevent injection
- CSRF protection for non-API routes
- No sensitive data is being stored

#### CI/CD Considerations
- Version control using git & github to provide insight on task implementation
- GitHub Actions for automated testing
- Docker for consistent deployment
- Environment-specific configurations

### Process flow
#### URL Encoding Process
1. Receive URL through API endpoint
2. Validate URL format
3. Check if URL already exists in storage
4. If exists, return existing short URL
5. If new, generate unique short code
6. Store mapping between original URL and short code
7. Return formatted short URL

#### URL Decoding Process
1. Receive short URL through API endpoint
2. Extract short code from URL
3. Look up original URL in storage
4. If found, return original URL
5. If not found, return appropriate error

### Requirements Analysis
#### Functional Requirements
- System must encode long URLs to short URLs
- System must decode short URLs back to original URLs
- Short URLs must be unique
- Both endpoints must return JSON responses
- HTTP status codes for errors
- Feature flag: Duplicate original URLs should return the same short URL
- Feature flag: Validate URL format/pattern
- Feature flag: Validate existence of URL (ping url to ensure its reachable before encoding)
- Feature flag: Limit length of URL

#### Evaluation Metrics
- Feature flag: Enable Logging
- Feature flag: Log daily usage
- Feature flag: Log duplicate urls
- Feature flag: Log slow url encoding/decoding process

#### Non-Functional Requirements
- Fast response time (<100ms)
- High availability
- Scalable storage solution
- Simple API structure
- Comprehensive error handling


## Analysis & Implementation Approach
### Core Approach
- Hash-based Systems
- Counter-based Systems
- Base64 Encoding

### Design Considerations

1. **Collision Avoidance**
   - Use sufficiently long shortened strings (6-8 characters minimum)
   - Implement collision detection and resolution strategies

2. **Security Considerations**
   - Avoid sequential IDs that are easily guessable
   - Use cryptographic techniques to prevent URL enumeration

3. **Performance Optimization**
   - Use distributed ID generation (e.g., Twitter's Snowflake algorithm)
   - Implement efficient database indexing

4. **Analytics Integration**
   - Store metadata with each URL for tracking
   - Implement efficient click analytics


## Design
- Generate Incremental serial number for each hour of the day
-- create dedicated index (local storage file) for each month, e.g Yn
-- get current date: Yn and check index if it exists
- Use base64 encoding (a-z, A-Z, 0-9) for short codes
- Test for existing serial number
- Generate 6-character codes
- Test for existing 6-character code
- **is flag enabled?** Hash original URLs to quickly check for duplicates
- Store url
- **is flag enabled?** Write log


### Data Structure
```
- urls
  - id (primary key)
  - original_url (string, indexed)
  - short_code (string, unique, indexed)
  - created_at (timestamp)
  - expires_at (timestamp, nullable)
```

### Algorithm
- Use base62 encoding (a-z, A-Z, 0-9) for short codes
- Generate 6-character codes for ~56 billion unique combinations
- Hash original URLs to quickly check for duplicates

### Deliverables
- Action Plan
- Laravel application codebase
- API documentation
- Docker configuration
- Test suite
- Deployment guide
- README with instructions

### Encode URL Endpoint
- **Endpoint:** `POST /api/encode`
- **Content-Type:** `application/json`
- **Request Body:**
  ```json
  {
      "url": "https://d1kit.com/url-shortener/long/path"
  }
  ```
- **Success Response:**
  ```json
  {
      "original_url": "https://d1kit.com/url-shortener/long/path",
      "short_url": "http://short.est/AbC123"
  }
  ```
- **Error Response:**
  ```json
  {
      "error": "Invalid URL format"
  }
  ```

### Decode URL Endpoint
- **Endpoint:** `POST /api/decode`
- **Content-Type:** `application/json`
- **Request Body:**
  ```json
  {
      "url": "http://short.est/AbC123"
  }
  ```
- **Success Response:**
  ```json
  {
      "short_url": "http://short.est/AbC123",
      "original_url": "https://d1kit.com/url-shortener/long/path"
  }
  ```
- **Error Response:**
  ```json
  {
      "error": "Short URL not found"
  }
  ```

### Postman Collection

```json
{
  "info": {
    "name": "URL Shortener API",
    "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
  },
  "item": [
    {
      "name": "Encode URL",
      "request": {
        "method": "POST",
        "header": [
          {
            "key": "Content-Type",
            "value": "application/json"
          }
        ],
        "body": {
          "mode": "raw",
          "raw": "{\n    \"url\": \"https://www.thisisalongdomain.com/with/some/parameters?and=here_too\"\n}"
        },
        "url": {
          "raw": "{{base_url}}/encode",
          "host": ["{{base_url}}"],
          "path": ["encode"]
        }
      }
    },
    {
      "name": "Decode URL",
      "request": {
        "method": "POST",
        "header": [
          {
            "key": "Content-Type",
            "value": "application/json"
          }
        ],
        "body": {
          "mode": "raw",
          "raw": "{\n    \"url\": \"http://short.est/GeAi9K\"\n}"
        },
        "url": {
          "raw": "{{base_url}}/decode",
          "host": ["{{base_url}}"],
          "path": ["decode"]
        }
      }
    }
  ],
  "variable": [
    {
      "key": "base_url",
      "value": "http://localhost:8000/api"
    }
  ]
}
```


## Implement

## Deploy
- environmemt linux
- install docker and docker compose
- navigate to docker directory
- start service

## Document

## Regression Test

## Release & Operations Manual


curl -X POST http://localhost/api/v1/encode      -H "Content-Type: application/json"      -d '{"url":"https://example.com/long/pathogyd"}'

curl -X POST http://localhost/api/v1/decode      -H "Content-Type: application/json"      -d '{"url":"http://short.est/4EIq1Q"}'