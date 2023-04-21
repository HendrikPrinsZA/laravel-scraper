#!/usr/bin/env bash

#######################################
# Simple confirm prompt
# Arguments:
#   Prompt (defaults to "Are you sure")
# Returns:
#   0 if yes
#   1 if no
#######################################
helper_confirm() {
  prompt=${1:-Are you sure}

  while true; do
    read -p "$prompt? [y/N] " yn
    case $yn in
    [Yy]*) return 0 ;;
    [Nn]*) return 1 ;;
    *) echo "Expected yes [y/Y/yes] or no [n/N/no]" ;;
    esac
  done
}

#######################################
# Determine if command exists
# Arguments:
#   The command, example) 'sed'
# Returns:
#   0 if yes
#   1 if no
#######################################
helper_command_exists() {
  cmd="$1"

  if command -v "$cmd" >/dev/null; then
    return 0
  fi

  return 1
}

#######################################
# Determine if file exists
# Arguments:
#   The file path, example) './vendor/bin/sail'
# Returns:
#   0 if yes
#   1 if no
#######################################
helper_file_exists() {
  file="$1"

  if [ -f "$file" ]; then
    return 0
  fi

  return 1
}
