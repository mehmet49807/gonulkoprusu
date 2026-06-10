#!/usr/bin/env bash
# vendor.zip ve yardimci dosyalari FTP ile yukler
# Usage: bash deploy/ftp-upload-vendor.sh

set -uo pipefail

PROJECT_ROOT="$(cd "$(dirname "$0")/.." && pwd)"
ENV_FILE="$PROJECT_ROOT/.env"

declare -A ENV_VARS=()
while IFS= read -r line || [[ -n "$line" ]]; do
    line="${line%$'\r'}"
    [[ "$line" =~ ^[[:space:]]*# ]] && continue
    [[ "$line" =~ ^[[:space:]]*$ ]] && continue
    if [[ "$line" =~ ^[[:space:]]*([^=]+)=(.*)$ ]]; then
        key="$(echo "${BASH_REMATCH[1]}" | xargs)"
        value="$(echo "${BASH_REMATCH[2]}" | xargs | sed 's/^"\(.*\)"$/\1/')"
        ENV_VARS["$key"]="$value"
    fi
done < "$ENV_FILE"

FTP_HOST="${ENV_VARS[FTP_HOST]}"
FTP_USER="${ENV_VARS[FTP_USERNAME]}"
FTP_PASS="${ENV_VARS[FTP_PASSWORD]}"

upload_file() {
    local local_path="$1"
    local remote_path="$2"

    if [[ ! -f "$local_path" ]]; then
        echo "SKIP (not found): $local_path"
        return 0
    fi

    local size_mb
    size_mb="$(du -m "$local_path" | cut -f1)"
    echo -n "Uploading $remote_path (${size_mb} MB)... "

    if curl --ftp-pasv -s -f -T "$local_path" "ftp://${FTP_HOST}/${remote_path}" --user "${FTP_USER}:${FTP_PASS}" >/dev/null; then
        echo "OK"
        return 0
    fi

    echo "FAIL"
    return 1
}

echo "=== Vendor FTP Upload ==="
upload_file "$PROJECT_ROOT/deploy/vendor.zip" "public_html/vendor.zip"
upload_file "$PROJECT_ROOT/deploy/unzip-vendor.php" "public_html/unzip-vendor.php"
upload_file "$PROJECT_ROOT/composer.lock" "public_html/composer.lock"
upload_file "$PROJECT_ROOT/composer.phar" "public_html/composer.phar"

echo ""
echo "Simdi tarayicidan acin:"
echo "https://gonulkoprusu.com/unzip-vendor.php?key=GONUL_SETUP_2026"
