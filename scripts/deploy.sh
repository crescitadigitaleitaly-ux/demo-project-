#!/usr/bin/env bash
# Uploads the theme to Hostinger file storage over TUS.
# Needs TUS_URL, TUS_AUTH and TUS_REST from the Hostinger "generate upload URL" call (short-lived, never commit them).
# Usage: TUS_URL=... TUS_AUTH=... TUS_REST=... scripts/deploy.sh <remote-theme-dir-name>
set -euo pipefail
DEST="wp-content/themes/${1:?remote theme dir name}"
SRC="$(cd "$(dirname "$0")/../theme/driftline" && pwd)"
fail=0
while IFS= read -r -d '' f; do
  rel="${f#$SRC/}"
  size=$(wc -c < "$f" | tr -d ' ')
  enc=$(python3 -c "import urllib.parse,sys;print(urllib.parse.quote(sys.argv[1]))" "$DEST/$rel")
  c1=$(curl -sS -o /dev/null -w "%{http_code}" -X POST "$TUS_URL/$enc?override=true" -H "X-Auth: $TUS_AUTH" -H "X-Auth-Rest: $TUS_REST" -H "Tus-Resumable: 1.0.0" -H "Upload-Length: $size" -H "Upload-Offset: 0")
  c2=$(curl -sS -o /dev/null -w "%{http_code}" -X PATCH "$TUS_URL/$enc?override=true" -H "X-Auth: $TUS_AUTH" -H "X-Auth-Rest: $TUS_REST" -H "Tus-Resumable: 1.0.0" -H "Content-Type: application/offset+octet-stream" -H "Upload-Offset: 0" --data-binary "@$f")
  if [ "$c1" != "201" ] || [ "$c2" != "204" ]; then echo "FAIL $rel ($c1/$c2)"; fail=1; fi
done < <(find "$SRC" -type f -print0)
[ "$fail" = 0 ] && echo "Uploaded $(find "$SRC" -type f | wc -l) files to $DEST"
exit $fail
