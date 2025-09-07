#!/bin/bash

# Verify OTP and get verification token
curl -X POST \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "john.doe@example.com",
    "otp": "A1B2C3"
  }' \
  "http://localhost:8000/api/auth/otp/verify"
