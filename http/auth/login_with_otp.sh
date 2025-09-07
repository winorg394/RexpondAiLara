#!/bin/bash

# Login with OTP
curl -X POST \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "john.doe@example.com",
    "otp": "A1B2C3",
    "device_name": "iPhone 13"
  }' \
  "http://localhost:8000/api/auth/login-with-otp"
