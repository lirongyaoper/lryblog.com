#!/usr/bin/env bash
# set -e  # do not exit on error to allow graceful messages

# set-git-proxy.sh — Helper to set/unset/show Git proxy for this repository (local) or globally
# Usage examples:
#   scripts/set-git-proxy.sh set http 127.0.0.1 7890 --local
#   scripts/set-git-proxy.sh set https http://127.0.0.1:7890 --global
#   scripts/set-git-proxy.sh set socks5 127.0.0.1 1080 --local
#   scripts/set-git-proxy.sh unset --local
#   scripts/set-git-proxy.sh show
#   scripts/set-git-proxy.sh set-domain github.com http://127.0.0.1:7890 --local
# Notes:
# - By default, operations use --local (only for this repo). Pass --global to affect global config.
# - Supports http/https/socks5 schemes. For socks5h, use socks5h as scheme.

set -u

SCOPE="--local"  # default scope

print_help() {
  cat <<'EOF'
Usage:
  set-git-proxy.sh set <scheme> <host> <port> [--local|--global]
  set-git-proxy.sh set <scheme> <url> [--local|--global]           # url includes scheme://host:port
  set-git-proxy.sh unset [--local|--global]
  set-git-proxy.sh show [--local|--global]
  set-git-proxy.sh set-domain <domain> <proxy_url> [--local|--global]

Examples:
  scripts/set-git-proxy.sh set http 127.0.0.1 7890 --local
  scripts/set-git-proxy.sh set https http://127.0.0.1:7890 --global
  scripts/set-git-proxy.sh set socks5 127.0.0.1 1080 --local
  scripts/set-git-proxy.sh unset --local
  scripts/set-git-proxy.sh show
  scripts/set-git-proxy.sh set-domain github.com http://127.0.0.1:7890 --local

Notes:
  - Default scope is --local (this repository only). Use --global to affect all repos.
  - Supported schemes: http, https, socks5, socks5h.
  - You can also configure per-domain proxy for git via https.<domain>.proxy.
EOF
}

# Parse scope at the end if provided
parse_scope() {
  if [[ "${*: -1}" == "--global" ]]; then
    SCOPE="--global"
    # remove last arg
    set -- "${@:1:$(($#-1))}"
  elif [[ "${*: -1}" == "--local" ]]; then
    SCOPE="--local"
    set -- "${@:1:$(($#-1))}"
  fi
  # return the modified args by echoing
  echo "$@"
}

ensure_git_repo() {
  if [[ "$SCOPE" == "--local" ]]; then
    if ! git rev-parse --is-inside-work-tree >/dev/null 2>&1; then
      echo "Error: not inside a git repository. Use --global or run within a repo." >&2
      exit 1
    fi
  fi
}

make_url() {
  local scheme="$1" host="$2" port="$3"
  case "$host" in
    http://*|https://*|socks5://*|socks5h://*)
      echo "$host"  # already a full URL in host position
      ;;
    *)
      echo "$scheme://$host:${port:-}"
      ;;
  esac
}

cmd_set() {
  ensure_git_repo
  local scheme host port url
  if [[ $# -ge 2 ]]; then
    scheme="$1"; shift
    if [[ "$2" =~ ^[0-9]+$ ]]; then
      # scheme host port form (rare path where $2 is port by mistake)
      :
    fi
  fi
  if [[ $# -ge 2 ]]; then
    host="$1"; shift
  fi
  if [[ $# -ge 1 ]]; then
    port="$1"; shift || true
  fi
  url="$(make_url "$scheme" "$host" "${port:-}")"

  # Configure both http and https proxies via core.* and protocol.* scopes
  git config $SCOPE http.proxy "$url"
  git config $SCOPE https.proxy "$url"

  # Also set for lower-level curl (useful when git delegates)
  git config $SCOPE core.gitproxy ""

  echo "Proxy set ($SCOPE):"
  git config $SCOPE --get http.proxy || true
  git config $SCOPE --get https.proxy || true
}

cmd_unset() {
  ensure_git_repo
  git config $SCOPE --unset-all http.proxy 2>/dev/null || git config $SCOPE --unset http.proxy 2>/dev/null || true
  git config $SCOPE --unset-all https.proxy 2>/dev/null || git config $SCOPE --unset https.proxy 2>/dev/null || true
  # Remove any per-domain proxies as well? No, leave them. Use set-domain with empty URL to clear.
  echo "Proxy unset ($SCOPE)."
}

cmd_show() {
  ensure_git_repo
  echo "Scope: $SCOPE"
  echo "http.proxy = $(git config $SCOPE --get http.proxy 2>/dev/null || echo '(not set)')"
  echo "https.proxy = $(git config $SCOPE --get https.proxy 2>/dev/null || echo '(not set)')"
  # Show domain-specific proxies
  echo "Domain proxies:"
  git config $SCOPE --get-regexp '^https?\.[^ ]+\.proxy$' 2>/dev/null || echo "(none)"
}

cmd_set_domain() {
  ensure_git_repo
  local domain="$1" url="$2"
  if [[ -z "$domain" || -z "$url" ]]; then
    echo "Usage: set-git-proxy.sh set-domain <domain> <proxy_url> [--local|--global]" >&2
    exit 1
  fi
  git config $SCOPE "https.$domain.proxy" "$url"
  echo "Domain proxy set ($SCOPE): https.$domain.proxy = $url"
}

main() {
  if [[ $# -lt 1 ]]; then
    print_help; exit 0
  fi
  local args=("$@")
  # Extract scope if last arg is scope
  local without_scope
  # shellcheck disable=SC2046
  without_scope=$(parse_scope "${args[@]}")
  # shellcheck disable=SC2206
  args=($without_scope)

  case "${args[0]}" in
    -h|--help|help)
      print_help ;;
    set)
      if [[ ${#args[@]} -lt 3 ]]; then
        echo "Error: missing arguments for set" >&2
        print_help; exit 1
      fi
      # support: set <scheme> <host> <port?>
      local scheme="${args[1]}"
      if [[ "${args[2]}" == http://* || "${args[2]}" == https://* || "${args[2]}" == socks5://* || "${args[2]}" == socks5h://* ]]; then
        cmd_set "$scheme" "${args[2]}"
      else
        local host="${args[2]}"
        local port="${args[3]:-}"
        cmd_set "$scheme" "$host" "$port"
      fi
      ;;
    unset)
      cmd_unset ;;
    show)
      cmd_show ;;
    set-domain)
      if [[ ${#args[@]} -lt 3 ]]; then
        echo "Error: missing arguments for set-domain" >&2
        print_help; exit 1
      fi
      cmd_set_domain "${args[1]}" "${args[2]}" ;;
    *)
      echo "Unknown command: ${args[0]}" >&2
      print_help; exit 1 ;;
  esac
}

main "$@"
