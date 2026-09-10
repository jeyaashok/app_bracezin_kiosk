# Social Authentication API Documentation

This document describes the REST API endpoints for social media authentication.

## Base URL
```
/api/auth
```

## Endpoints

### 1. Google Authentication
**POST** `/api/auth/google`

Authenticate user using Google OAuth2 token.

#### Request Body
```json
{
  "token": "your_google_access_token",
  "email": "user@example.com",
  "name": "User Name"
}
```

**Parameters:**
- `token` (required): Google OAuth2 access token
- `email` (optional): User email address (used if not provided by Google)
- `name` (optional): User name (used if not provided by Google)

#### Success Response (200)
```json
{
  "success": true,
  "message": "Social authentication successful",
  "data": {
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "token_type": "Bearer",
    "expires_in": 3600,
    "user": {
      "id": 1,
      "name": "User Name",
      "email": "user@example.com",
      "username": "user-name"
    }
  }
}
```

#### Error Response (401)
```json
{
  "success": false,
  "message": "Invalid google token",
  "errors": {
    "token": "Failed to verify google token"
  }
}
```

---

### 2. Facebook Authentication
**POST** `/api/auth/facebook`

Authenticate user using Facebook OAuth2 token.

#### Request Body
```json
{
  "token": "your_facebook_access_token",
  "email": "user@example.com",
  "name": "User Name"
}
```

**Parameters:**
- `token` (required): Facebook OAuth2 access token
- `email` (optional): User email address
- `name` (optional): User name

#### Success Response (200)
```json
{
  "success": true,
  "message": "Social authentication successful",
  "data": {
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "token_type": "Bearer",
    "expires_in": 3600,
    "user": {
      "id": 2,
      "name": "Facebook User",
      "email": "fbuser@example.com",
      "username": "facebook-user"
    }
  }
}
```

---

### 3. GitHub Authentication
**POST** `/api/auth/github`

Authenticate user using GitHub OAuth2 token.

#### Request Body
```json
{
  "token": "your_github_access_token",
  "email": "user@example.com",
  "name": "User Name"
}
```

**Parameters:**
- `token` (required): GitHub OAuth2 access token
- `email` (optional): User email address
- `name` (optional): User name

#### Success Response (200)
```json
{
  "success": true,
  "message": "Social authentication successful",
  "data": {
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "token_type": "Bearer",
    "expires_in": 3600,
    "user": {
      "id": 3,
      "name": "GitHub User",
      "email": "ghuser@example.com",
      "username": "github-user"
    }
  }
}
```

---

## Error Responses

### 422 - Validation Error
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "token": ["The token field is required."]
  }
}
```

### 500 - Server Error
```json
{
  "success": false,
  "message": "Authentication failed",
  "error": "Error details here"
}
```

---

## Usage Example (cURL)

### Google Authentication
```bash
curl -X POST http://localhost/api/auth/google \
  -H "Content-Type: application/json" \
  -d '{
    "token": "ya29.a0AfH6SMBx...",
    "name": "John Doe"
  }'
```

### Facebook Authentication
```bash
curl -X POST http://localhost/api/auth/facebook \
  -H "Content-Type: application/json" \
  -d '{
    "token": "EAABsbCS...",
    "name": "John Doe"
  }'
```

### GitHub Authentication
```bash
curl -X POST http://localhost/api/auth/github \
  -H "Content-Type: application/json" \
  -d '{
    "token": "ghp_16C7e42F...",
    "name": "John Doe"
  }'
```

---

## Authentication

After successful social authentication, use the returned `access_token` to authenticate subsequent API requests:

```bash
curl -X GET http://localhost/api/user \
  -H "Authorization: Bearer {access_token}"
```

---

## Token Validation

The API validates tokens with the respective social media providers:
- **Google**: Uses Google OAuth2 tokeninfo endpoint
- **Facebook**: Uses Facebook Graph API
- **GitHub**: Uses GitHub API with Bearer token authentication

---

## Features

✅ Automatic user creation on first login
✅ Email verification for social accounts
✅ JWT token generation for API access
✅ Support for Google, Facebook, and GitHub
✅ Unique username generation
✅ User data synchronization from social providers
✅ Comprehensive error handling and validation

---

## Requirements

- Laravel 11+
- tymon/jwt-auth package
- guzzlehttp/guzzle for HTTP requests
- Social media provider API credentials configured
