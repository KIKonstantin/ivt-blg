#!/bin/bash

# 1. Start a local server in the background
echo "Starting local server..."
php artisan serve --port=8001 > /dev/null 2>&1 &
SERVER_PID=$!

# Wait for server to boot
sleep 5

# 2. Run the export targeting the local server
echo "Exporting site..."
# We override APP_URL to point to our temporary local server
APP_URL=http://127.0.0.1:8001 php artisan export

# 3. Stop the local server
echo "Stopping local server..."
kill $SERVER_PID

# 4. Patch the generated files
# Replace local URL with production URL in all HTML files
echo "Patching links for GitHub Pages..."
find docs -type f -name "*.html" -exec sed -i '' 's|http://127.0.0.1:8001|https://kikonstantin.github.io/ivt-blg|g' {} +

echo "✅ Build complete! You can now commit and push the 'docs' folder."
