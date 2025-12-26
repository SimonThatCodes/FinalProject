<?php

/**
 * Quick script to check if Google OAuth is configured
 * Run: php check-google-oauth.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$clientId = config('services.google.client_id');
$clientSecret = config('services.google.client_secret');
$redirect = config('services.google.redirect');

echo "\n=== Google OAuth Configuration Check ===\n\n";

if (empty($clientId)) {
    echo "❌ GOOGLE_CLIENT_ID is NOT set\n";
    echo "   Please add it to your .env file:\n";
    echo "   GOOGLE_CLIENT_ID=your-client-id-here\n\n";
} else {
    echo "✅ GOOGLE_CLIENT_ID is set: " . substr($clientId, 0, 30) . "...\n";
}

if (empty($clientSecret)) {
    echo "❌ GOOGLE_CLIENT_SECRET is NOT set\n";
    echo "   Please add it to your .env file:\n";
    echo "   GOOGLE_CLIENT_SECRET=your-client-secret-here\n\n";
} else {
    echo "✅ GOOGLE_CLIENT_SECRET is set: " . substr($clientSecret, 0, 10) . "...\n";
}

if (empty($redirect)) {
    echo "⚠️  GOOGLE_REDIRECT_URI is NOT set (using default)\n";
} else {
    echo "✅ GOOGLE_REDIRECT_URI is set: $redirect\n";
}

echo "\n";

if (empty($clientId) || empty($clientSecret)) {
    echo "📝 Next Steps:\n";
    echo "   1. Get credentials from: https://console.cloud.google.com/apis/credentials\n";
    echo "   2. Add them to backend/.env file\n";
    echo "   3. Restart your server: php artisan serve\n";
    echo "   4. Run this script again to verify\n\n";
    exit(1);
} else {
    echo "🎉 Google OAuth is properly configured!\n";
    echo "   You can now use Google login/registration.\n\n";
    exit(0);
}



