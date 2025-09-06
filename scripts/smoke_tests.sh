#!/usr/bin/env bash

set -euo pipefail

BASE_URL=${BASE_URL:-"http://127.0.0.1:8000"}

function hit() {
  local method="$1" path="$2" data="${3:-}"
  echo -e "\n[${method}] ${BASE_URL}${path}"
  if [ -n "$data" ]; then
    curl -s -S -X "$method" -H 'Content-Type: application/json' "${BASE_URL}${path}" -d "$data" | jq . || true
  } else
    curl -s -S -X "$method" "${BASE_URL}${path}" | jq . || true
  fi
}

echo "== Smoke tests =="
echo "BASE_URL=${BASE_URL}"

# Public endpoints
hit GET "/api/settings/data"
hit GET "/api/games/all"
hit GET "/api/search/games?term=slot"
hit GET "/api/spin/prizes"

echo -e "\n== Auth flow (JWT) =="
if [ -n "${AUTH_NAME:-}" ] && [ -n "${AUTH_PASSWORD:-}" ]; then
  RESP=$(curl -s -S -X POST "${BASE_URL}/api/auth/login" -H 'Content-Type: application/json' -d "{\"name\":\"${AUTH_NAME}\",\"password\":\"${AUTH_PASSWORD}\"}")
  TOKEN=$(echo "$RESP" | jq -r '.access_token // empty')
  if [ -n "$TOKEN" ] && [ "$TOKEN" != "null" ]; then
    echo "Logged in. Token acquired."
    echo -e "\n[GET] /api/auth/me"
    curl -s -S -H "Authorization: Bearer ${TOKEN}" "${BASE_URL}/api/auth/me" | jq . || true
    echo -e "\n[GET] /api/user/cpf"
    curl -s -S -H "Authorization: Bearer ${TOKEN}" "${BASE_URL}/api/user/cpf" | jq . || true
  else
    echo "Login failed or token missing." >&2
    echo "$RESP" | jq . || true
  fi
else
  echo "AUTH_NAME and/or AUTH_PASSWORD not set; skipping auth tests."
fi

echo "\nDone."

