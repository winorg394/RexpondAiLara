#!/bin/bash

# Register a new user with OTP verification
curl -X POST \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "first_name": "John",
    "last_name": "Doe",
    "email": "john.doe@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "otp": "A1B2C3",
    "verification_token": "your_verification_token_here",
    "device_name": "iPhone 13"
  }' \
  "http://localhost:8000/api/auth/register-with-otp"
