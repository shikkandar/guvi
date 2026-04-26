#!/bin/bash
set -e

cd "$(dirname "$0")"

echo ""
echo "╔════════════════════════════════════════════════════════════╗"
echo "║           GUVI - Authentication System                     ║"
echo "║           Checking All Connections...                      ║"
echo "╚════════════════════════════════════════════════════════════╝"
echo ""

CHECK_OUTPUT=$(php check-connections.php 2>&1)
echo "$CHECK_OUTPUT"
echo ""

if echo "$CHECK_OUTPUT" | grep -q "SOME CONNECTIONS FAILED"; then
    echo "╔════════════════════════════════════════════════════════════╗"
    echo "║  ❌ CANNOT START - Fix connection issues first             ║"
    echo "╚════════════════════════════════════════════════════════════╝"
    echo ""
    echo "Missing connections detected. Run this first:"
    echo "  php check-connections.php"
    echo ""
    exit 1
fi

PORT_TO_USE="${PORT:-8000}"
echo "╔════════════════════════════════════════════════════════════╗"
echo "║  ✅ ALL CONNECTIONS OK - Starting Server...               ║"
echo "╚════════════════════════════════════════════════════════════╝"
echo ""
echo "📱 Application URL: http://0.0.0.0:${PORT_TO_USE}"
echo "📂 Project Path:    $(pwd)"
echo ""
echo "Press Ctrl+C to stop the server"
echo ""
exec php -S 0.0.0.0:"${PORT_TO_USE}"
