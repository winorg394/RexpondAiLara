# Authentication Guide

This document explains how authentication works in the application using Laravel Sanctum for API token authentication.

## Overview

The API uses Laravel Sanctum for token-based authentication. Each user receives a bearer token upon successful login or registration, which must be included in subsequent requests to protected endpoints.

## Authentication Flow

1. **Registration**: Create a new user account
2. **Login**: Authenticate and receive an access token
3. **Making Authenticated Requests**: Include the token in the Authorization header
4. **Refreshing Tokens**: Get a new token when needed
5. **Logout**: Invalidate the current token

## Endpoints

### 1. Register

Create a new user account.

- **URL**: `/api/register`
- **Method**: `POST`
- **Content-Type**: `application/json`
- **Request Body**:
  ```json
  {
    "first_name": "John",
    "last_name": "Doe",
    "email": "john@example.com",
    "password": "yourpassword",
    "password_confirmation": "yourpassword"
  }
  ```
- **Success Response**:
  - **Code**: `201 Created`
  - **Body**:
    ```json
    {
      "user": {
        "first_name": "John",
        "last_name": "Doe",
        "email": "john@example.com",
        "updated_at": "2023-01-01T12:00:00.000000Z",
        "created_at": "2023-01-01T12:00:00.000000Z",
        "id": 1
      },
      "access_token": "1|abcdefghijklmnopqrstuvwxyz",
      "token_type": "Bearer"
    }
    ```

### 2. Login

Authenticate and receive an access token.

- **URL**: `/api/login`
- **Method**: `POST`
- **Content-Type**: `application/json`
- **Request Body**:
  ```json
  {
    "email": "john@example.com",
    "password": "yourpassword"
  }
  ```
- **Success Response**:
  - **Code**: `200 OK`
  - **Body**:
    ```json
    {
      "user": {
        "id": 1,
        "first_name": "John",
        "last_name": "Doe",
        "email": "john@example.com",
        "email_verified_at": null,
        "created_at": "2023-01-01T12:00:00.000000Z",
        "updated_at": "2023-01-01T12:00:00.000000Z"
      },
      "access_token": "1|abcdefghijklmnopqrstuvwxyz",
      "token_type": "Bearer"
    }
    ```
- **Error Response**:
  - **Code**: `422 Unprocessable Entity`
  - **Body**:
    ```json
    {
      "message": "The given data was invalid.",
      "errors": {
        "email": ["The provided credentials are incorrect."]
      }
    }
    ```

### 3. Get Authenticated User

Get the currently authenticated user's data.

- **URL**: `/api/me`
- **Method**: `GET`
- **Headers**:
  - `Authorization`: `Bearer your_access_token_here`
  - `Accept`: `application/json`
- **Success Response**:
  - **Code**: `200 OK`
  - **Body**:
    ```json
    {
      "id": 1,
      "first_name": "John",
      "last_name": "Doe",
      "email": "john@example.com",
      "email_verified_at": null,
      "created_at": "2023-01-01T12:00:00.000000Z",
      "updated_at": "2023-01-01T12:00:00.000000Z"
    }
    ```

### 4. Refresh Token

Refresh the current access token.

- **URL**: `/api/refresh`
- **Method**: `POST`
- **Headers**:
  - `Authorization`: `Bearer your_access_token_here`
  - `Accept`: `application/json`
- **Success Response**:
  - **Code**: `200 OK`
  - **Body**:
    ```json
    {
      "access_token": "2|newaccesstoken1234567890",
      "token_type": "Bearer"
    }
    ```

### 5. Logout

Invalidate the current access token.

- **URL**: `/api/logout`
- **Method**: `POST`
- **Headers**:
  - `Authorization`: `Bearer your_access_token_here`
  - `Accept`: `application/json`
- **Success Response**:
  - **Code**: `200 OK`
  - **Body**:
    ```json
    {
      "message": "Successfully logged out"
    }
    ```

## Making Authenticated Requests

Include the access token in the `Authorization` header for all protected endpoints:

```
Authorization: Bearer your_access_token_here
```

## Security Considerations

1. Always use HTTPS in production
2. Store tokens securely (e.g., in HTTP-only cookies for web applications)
3. Implement proper password policies
4. Set appropriate token expiration times in `config/sanctum.php`
5. Regularly rotate tokens for sensitive operations

## Token Management

- Tokens are stored in the `personal_access_tokens` table
- Each token is associated with a user and has abilities
- Tokens can be revoked manually or when a user logs out
- Token expiration can be configured in `config/sanctum.php`

## Error Handling

Common error responses include:

- `401 Unauthorized`: Invalid or missing token
- `403 Forbidden`: Valid token but insufficient permissions
- `419 CSRF Token Mismatch`: Missing or invalid CSRF token for session-based auth
- `422 Unprocessable Entity`: Validation errors in the request

## Rate Limiting

Authentication endpoints are rate-limited to prevent abuse. The default is 60 requests per minute per IP address.
