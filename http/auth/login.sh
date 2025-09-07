#!/bin/bash

# Login with user credentials
curl -X POST \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "john.doe@example.com",
    "password": "password123",
    "device_name": "iPhone 13"
  }' \
  "http://localhost:8000/api/login"
