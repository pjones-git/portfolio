#!/bin/bash

# Start the PHP development server for the Miniport admin interface
echo "🚀 Starting PHP development server..."
echo ""
echo "📱 Admin interface: http://localhost:8000/admin-standalone.html"
echo "🌐 Your website: http://localhost:8000/"
echo "🧪 Test endpoints: http://localhost:8000/test-server.php"
echo ""
echo "✅ Deployment system is ready!"
echo "   • Make changes in the admin interface"
echo "   • Click '🚀 Deploy to Site' to update your live website"
echo ""
echo "Press Ctrl+C to stop the server"
echo "================================================"
echo ""

# Start the PHP built-in server
php -S localhost:8000
