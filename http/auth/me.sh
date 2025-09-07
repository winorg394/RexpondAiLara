#!/bin/bash

# Get the authenticated user's data
# Replace YOUR_ACCESS_TOKEN with a valid token from login
curl -X GET \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Accept: application/json" \
  "http://localhost:8000/api/me"
