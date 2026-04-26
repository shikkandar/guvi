#!/bin/bash

echo "╔════════════════════════════════════════════════════════════╗"
echo "║                 GUVI - Start Server Guide                  ║"
echo "╚════════════════════════════════════════════════════════════╝"
echo ""

echo "1️⃣  Checking Database Connections..."
php /Users/flowkiqinc/guvi/check-connections.php

echo ""
echo "2️⃣  Start Server Command:"
echo ""
echo "    cd /Users/flowkiqinc/guvi"
echo "    php -S localhost:8000"
echo ""
echo "3️⃣  Access Application:"
echo "    http://localhost:8000"
echo ""
