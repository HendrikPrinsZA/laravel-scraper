#!/usr/bin/env bash

: '
# Setup Script
Convenient script to drive the initial setup process
'

# Constants
PATH_TO_SCRIPT_DIR="$(
  cd -- "$(dirname "$0")" >/dev/null 2>&1
  pwd -P
)"
PATH_TO_REPO="$PATH_TO_SCRIPT_DIR/.."

# Imports
. $PATH_TO_REPO/bin/helpers.sh

# Check that docker exists
command="docker"
if ! helper_command_exists "$command"; then
  echo "Error: Command '$command' required, see https://docs.docker.com/engine/install/"
  echo "Hint: brew install --cask docker"
  exit 1
fi

# Check that composer exists
command="composer"
if ! helper_command_exists "$command"; then
  echo "Error: Command '$command' required, see https://docs.docker.com/engine/install/"
  echo "Hint: brew install composer"
  exit 1
fi

# Install dependencies
composer install

# Check that sail exists
file="./vendor/bin/sail"
if ! helper_file_exists "$file"; then
  echo "Notice: File '$file' not found"
  exit 1
fi

#######################################
# Ensure that we have an .env
# Arguments:
#   Prompt (defaults to "Are you sure")
# Returns:
#   None
#######################################
deal_with_env() {
  if [ -f "$PATH_TO_REPO/.env" ]; then
    if ! helper_confirm "Overwrite existing .env with .env.example"; then
      echo "Notice: Using existing .env"
      return
    fi
  fi

  if [ ! -f ".env.example" ]; then
    echo "Error: Expected to find .env.example"
    exit 1
  fi

  # Backup existing env file
  if [ -f "$PATH_TO_REPO/.env" ]; then
    date=$(date +%Y%m%d_%H%M%S)
    target="$PATH_TO_REPO/.env.bck-$date"
    mv "$PATH_TO_REPO/.env" "$target"
    echo "Notice: Backed up existing env $target"
  fi

  # Copy example env
  cp "$PATH_TO_REPO/.env.example" "$PATH_TO_REPO/.env"
  echo "Copied .env.example to .env"

  # Safety check
  if [ ! -f "$PATH_TO_REPO/.env" ]; then
    echo "Error: You need an .env!"
    exit 1
  fi
}

# Ensure the .env file is set
deal_with_env

# Load the env variables
source $PATH_TO_REPO/.env

# Restrict to local env
if [ ! "${APP_ENV}" == "local" ]; then
  echo "Error: This script is only allowed to run from 'local'"
  exit 1
fi

# Restrict to local environment
# - More checks needed here
if [ ! "${APP_ENV}" == "local" ]; then
  echo "Error: This script is only allowed to run from 'local'"
  exit 1
fi

if helper_confirm "Remove all docker artefacts for this environment"; then
    ./vendor/bin/sail down --rmi all -v
fi

# Launch sail environment
./vendor/bin/sail down && ./vendor/bin/sail up -d --build

# Generate app key
./vendor/bin/sail artisan key:generate --no-interaction

# Migrate
./vendor/bin/sail artisan migrate --seed

exit 0

if helper_confirm "Import demo data"; then

  if ! helper_command_exists mysql; then
    echo "Error: Command 'mysql' required"
    echo "Hint: brew install mysql-client"
    exit 1
  fi

  docker exec -it finstack-mysql mysql -uroot -p$DB_ROOT_PASSWORD -e "DROP DATABASE IF EXISTS $DB_DATABASE; CREATE DATABASE $DB_DATABASE;"
  mysql -h127.0.0.1 -uroot -p$DB_ROOT_PASSWORD < database/demo-data.sql
  ./vendor/bin/sail artisan migrate
fi

if helper_confirm "Create test database/s"; then
  docker exec -it finstack-mysql mysql -uroot -p$DB_ROOT_PASSWORD -e "DROP DATABASE IF EXISTS $DB_DATABASE_TESTING; CREATE DATABASE $DB_DATABASE_TESTING;"
fi

if helper_confirm "Run tests"; then
  ./vendor/bin/sail test
fi
