#!/bin/bash

# Base URL - Update this with your application's base URL
BASE_URL="http://localhost:8000/api"
# Device name for token association
DEVICE_NAME="iPhone 13"

# 1. Register a new user
echo "=== Testing User Registration ==="
curl -X POST \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "first_name": "John",
    "last_name": "Doe",
    "email": "john.doe@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "device_name": "'$DEVICE_NAME'"
  }' \
  "$BASE_URL/register"

echo -e "\n\n"

# 2. Login with the registered user
echo "=== Testing User Login ==="
LOGIN_RESPONSE=$(curl -s -X POST \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "john.doe@example.com",
    "password": "password123",
    "device_name": "'$DEVICE_NAME'"
  }' \
  "$BASE_URL/login")

echo "$LOGIN_RESPONSE"
echo -e "\n\n"

# Extract the access token from the login response
ACCESS_TOKEN=$(echo "$LOGIN_RESPONSE" | grep -o '"access_token":"[^"]*' | cut -d'"' -f4)

# 3. Get the authenticated user's data
echo "=== Testing Get Authenticated User ==="
curl -X GET \
  -H "Authorization: Bearer $ACCESS_TOKEN" \
  -H "Accept: application/json" \
  "$BASE_URL/me"

echo -e "\n\n"

# 4. Refresh the access token
echo "=== Testing Token Refresh ==="
REFRESH_RESPONSE=$(curl -s -X POST \
  -H "Authorization: Bearer $ACCESS_TOKEN" \
  -H "Accept: application/json" \
  "$BASE_URL/refresh")

echo "$REFRESH_RESPONSE"
echo -e "\n\n"

# Extract the new access token from the refresh response
NEW_ACCESS_TOKEN=$(echo "$REFRESH_RESPONSE" | grep -o '"access_token":"[^"]*' | cut -d'"' -f4)

# 5. Logout
echo "=== Testing Logout ==="
curl -X POST \
  -H "Authorization: Bearer $NEW_ACCESS_TOKEN" \
  -H "Accept: application/json" \
  "$BASE_URL/logout"

echo -e "\n\n=== Authentication Flow Test Complete ==="
