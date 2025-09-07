#!/bin/bash

# Reset password with OTP
curl -X POST http://localhost:8000/api/auth/password/reset \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "otp": "123456",
    "password": "new_password",
    "password_confirmation": "new_password"
  }'
