#!/bin/bash

# Send OTP to user's email
curl -X POST \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "john.doe@example.com"
  }' \
  "http://localhost:8000/api/auth/otp/send"
