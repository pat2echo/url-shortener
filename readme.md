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
- Analytics dashboard
- Custom domain support
- User management
- Rate limiting
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
- Duplicate original URLs should return the same short URL
- Both endpoints must return JSON responses

#### Non-Functional Requirements
- Fast response time (<100ms)
- High availability
- Scalable storage solution
- Simple API structure
- Comprehensive error handling

#### Technical Requirements
- 

- Requirements Analysis & Implementation Approach
- Design & deliverables
- Postman endpoints
- PHPunit tests
- Read me instructions
- Deployment guide
- Deploy test version -- d1kit.com
- Run instructions
- Docker-compose
- Web server config

## Analysis

## Design

## Implement

## Deploy

## Document

## Regression Test

## Release & Operations Manual