#!/bin/bash

cd /Users/flowkiqinc/guvi

echo ""
echo "╔════════════════════════════════════════════════════════════╗"
echo "║           GUVI - Authentication System                     ║"
echo "║           Checking All Connections...                      ║"
echo "╚════════════════════════════════════════════════════════════╝"
echo ""

# Check connections
php check-connections.php

# Extract result
RESULT=$(php check-connections.php 2>&1 | grep -c "SOME CONNECTIONS FAILED")

echo ""

if [ $RESULT -eq 0 ]; then
    echo "╔════════════════════════════════════════════════════════════╗"
    echo "║  ✅ ALL CONNECTIONS OK - Starting Server...               ║"
    echo "╚════════════════════════════════════════════════════════════╝"
    echo ""
    echo "📱 Application URL: http://localhost:8000"
    echo "📂 Project Path:    /Users/flowkiqinc/guvi"
    echo ""
    echo "Press Ctrl+C to stop the server"
    echo ""
    php -S localhost:8000
else
    echo "╔════════════════════════════════════════════════════════════╗"
    echo "║  ❌ CANNOT START - Fix connection issues first             ║"
    echo "╚════════════════════════════════════════════════════════════╝"
    echo ""
    echo "Missing connections detected. Run this first:"
    echo "  php check-connections.php"
    echo ""
    exit 1
fi
