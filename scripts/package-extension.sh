#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
BUILD_DIR="$ROOT_DIR/public/extension-build/infinite-sugar"
OUTPUT_DIR="$ROOT_DIR/public/downloads"
OUTPUT_FILE="$OUTPUT_DIR/InfiniteSugar-Chrome-Extension-v1.6.2.zip"

if [[ ! -f "$BUILD_DIR/manifest.json" ]]; then
  echo "Extension manifest not found at $BUILD_DIR/manifest.json" >&2
  exit 1
fi

mkdir -p "$OUTPUT_DIR"
rm -f "$OUTPUT_FILE"

(
  cd "$BUILD_DIR"
  zip -qr "$OUTPUT_FILE" .
)

echo "$OUTPUT_FILE"
