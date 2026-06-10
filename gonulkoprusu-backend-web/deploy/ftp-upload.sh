#!/usr/bin/env bash
# Gonul Koprusu - FTP upload via curl (public_html deployment)
# Usage: bash deploy/ftp-upload.sh

set -uo pipefail

PROJECT_ROOT="$(cd "$(dirname "$0")/.." && pwd)"
ENV_FILE="$PROJECT_ROOT/.env"
WEB_ROOT="public_html"

if [[ ! -f "$ENV_FILE" ]]; then
    echo "Hata: .env bulunamadi ($ENV_FILE)" >&2
    exit 1
fi

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

FTP_HOST="${ENV_VARS[FTP_HOST]:-}"
FTP_USER="${ENV_VARS[FTP_USERNAME]:-}"
FTP_PASS="${ENV_VARS[FTP_PASSWORD]:-}"

if [[ -z "$FTP_HOST" || -z "$FTP_USER" || -z "$FTP_PASS" ]]; then
    echo "Hata: FTP_HOST, FTP_USERNAME ve FTP_PASSWORD .env icinde tanimli olmali." >&2
    exit 1
fi

EXCLUDE_DIRS=(vendor node_modules .git deploy tests .idea .vscode __MACOSX storage/logs storage/framework/cache storage/framework/sessions storage/framework/views)
EXCLUDE_FILES=(.gitignore phpunit.xml .env.example .gitkeep .env .DS_Store)

should_skip() {
    local rel="$1"
    local name="$2"
    local part
    local prefix

    for excluded in "${EXCLUDE_FILES[@]}"; do
        [[ "$name" == "$excluded" ]] && return 0
    done

    for prefix in storage/logs storage/framework/cache storage/framework/sessions storage/framework/views; do
        [[ "$rel" == "$prefix"/* || "$rel" == "$prefix" ]] && return 0
    done

    IFS='/' read -ra parts <<< "$rel"
    for part in "${parts[@]}"; do
        for dir in "${EXCLUDE_DIRS[@]}"; do
            [[ "$part" == "$dir" ]] && return 0
        done
    done

    return 1
}

remote_path_for() {
    local rel="$1"
    if [[ "$rel" == public/* ]]; then
        echo "${WEB_ROOT}/${rel#public/}"
    else
        echo "${WEB_ROOT}/${rel}"
    fi
}

echo "=== Gonul Koprusu FTP Upload (curl) ==="
echo "Server: $FTP_HOST"
echo "Target: /$WEB_ROOT"
echo ""

mapfile -d '' FILES < <(
    find "$PROJECT_ROOT" -type f -print0 |
    while IFS= read -r -d '' file; do
        rel="${file#"$PROJECT_ROOT"/}"
        name="$(basename "$file")"
        should_skip "$rel" "$name" && continue
        printf '%s\0' "$rel"
    done
)

TOTAL=${#FILES[@]}
OK=0
FAIL=0
COUNT=0

echo "Yukleniyor: $TOTAL dosya..."
echo ""

for rel in "${FILES[@]}"; do
    rel="${rel%$'\r'}"
    [[ -z "$rel" ]] && continue
    local_file="$PROJECT_ROOT/$rel"
  remote="$(remote_path_for "$rel")"
    ftp_url="ftp://${FTP_HOST}/${remote}"

    if curl --ftp-pasv -s -f --ftp-create-dirs -T "$local_file" "$ftp_url" --user "${FTP_USER}:${FTP_PASS}" >/dev/null 2>&1; then
        ((OK++)) || true
    else
        ((FAIL++)) || true
        echo "FAIL: $rel -> $remote" >&2
    fi

    ((COUNT++)) || true
    if (( COUNT % 20 == 0 || COUNT == TOTAL )); then
        echo "[$COUNT/$TOTAL] son: $rel"
    fi
done

for folder in uploads uploads/profiles uploads/posts uploads/stories; do
    curl --ftp-pasv -s -Q "MKD $WEB_ROOT/$folder" "ftp://${FTP_HOST}/" --user "${FTP_USER}:${FTP_PASS}" >/dev/null 2>&1 || true
done

echo ""
echo "=== Sonuc: $OK basarili, $FAIL hata ==="
echo "Site: https://gonulkoprusu.com"
echo ""
echo "SONRAKI ADIMLAR:"
echo "1. vendor/ icin: bash deploy/ftp-upload-vendor.sh"
echo "2. Sunucuda: php artisan config:clear (veya deploy/clear-route-cache.php)"
echo "3. storage/ izinleri 755"

[[ "$FAIL" -eq 0 ]]
