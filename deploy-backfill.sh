#!/bin/bash

echo "🚀 Deploying to production server..."
echo ""

ssh -p2223 kpsx9679@kp-spamsdammarwulan.com << 'ENDSSH'
cd ~/pamsimas
echo "📥 Pulling latest code..."
git pull
echo ""
echo "🔄 Running backfill command..."
php artisan tagihan:backfill
echo ""
echo "✅ Deployment complete!"
ENDSSH
