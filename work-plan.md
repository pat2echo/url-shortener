## Action Plan

### Task Breakdown
#### In Scope
- Use PHP/Laravel considering job description
- 2 RESTful endpoints that support HTTP POST requests for URL encoding/decoding API
- Use of disk caching for persistence with lifespan of 30 days
- Basic validation and error handling
- Documentation and testing
- Use of github actions to run phpunit test cases (tentative)

#### Out of Scope
- Endpoint security, User authentication/authorization
- URL scanning for malicious content
- Analytics dashboard
- Custom domain support
- User management
- Rate limiting
- Alerts for error rates and response times
- Code style checker enforcement is ignored
- Publicily accessible endpoints for demonstration

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
- Feature flag: Log slow url encoding/decoding process
- Integration test cases for frontend developers or others

#### Non-Functional Requirements
- Fast response time (<100ms)
- High availability
- Scalable storage solution
- Simple API structure
- Comprehensive error handling


## Analysis & Implementation Approach
### Core Approach
- Base62 Encoding
- Feature Flagging
- Logging
- Ability to handle duplicate requests within a short timeframe

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
- Generate Random Byte
-- Test for existing shortcode
-- Generate 6-character codes
-- Create dedicated index file for each url, due to performance consideration
- Test for existing shortcode code
- Hash original URLs to quickly check for duplicates  (based on app settings/feature flags)
- Store url
- Write log (based on app settings/feature flags)


### Data Structure
```
- urls
  - url (string, indexed)
  - short_code (string, unique, indexed)
  - date (unix_timestamp)
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


## Deploy
- environmemt linux
- install docker and docker compose
- navigate to docker directory
- start service