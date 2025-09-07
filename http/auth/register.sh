#!/bin/bash

# Register a new user
curl -X POST \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "first_name": "John",
    "last_name": "Doe",
    "email": "john.doe@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "device_name": "iPhone 13"
  }' \
  "http://localhost:8000/api/register"
