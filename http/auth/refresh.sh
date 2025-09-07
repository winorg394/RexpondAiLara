#!/bin/bash

# Refresh the access token
# Replace YOUR_ACCESS_TOKEN with a valid token from login
curl -X POST \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Accept: application/json" \
  "http://localhost:8000/api/refresh"
