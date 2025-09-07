#!/bin/bash

# Request password reset OTP
curl -X POST http://localhost:8000/api/auth/password/otp \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com"
  }'
