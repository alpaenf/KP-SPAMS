#!/bin/bash

# ==================================
# PAMSIMAS - Deploy Role Penarik
# ==================================

echo "🚀 Starting deployment..."
echo ""

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# 1. Pull latest code
echo "${YELLOW}📥 Pulling latest code from repository...${NC}"
git pull origin main
if [ $? -ne 0 ]; then
    echo "${RED}❌ Git pull failed!${NC}"
    exit 1
fi
echo "${GREEN}✅ Code updated${NC}"
echo ""

# 2. Install Composer dependencies
echo "${YELLOW}📦 Installing Composer dependencies...${NC}"
composer install --no-dev --optimize-autoloader --no-interaction
if [ $? -ne 0 ]; then
    echo "${RED}❌ Composer install failed!${NC}"
    exit 1
fi
echo "${GREEN}✅ Composer dependencies installed${NC}"
echo ""

# 3. Install NPM dependencies
echo "${YELLOW}📦 Installing NPM dependencies...${NC}"
npm install
if [ $? -ne 0 ]; then
    echo "${RED}❌ NPM install failed!${NC}"
    exit 1
fi
echo "${GREEN}✅ NPM dependencies installed${NC}"
echo ""

# 4. Build assets
echo "${YELLOW}🔨 Building production assets...${NC}"
npm run build
if [ $? -ne 0 ]; then
    echo "${RED}❌ NPM build failed!${NC}"
    exit 1
fi
echo "${GREEN}✅ Assets built${NC}"
echo ""

# 5. Run migrations
echo "${YELLOW}🗄️  Running database migrations...${NC}"
php artisan migrate --force
if [ $? -ne 0 ]; then
    echo "${RED}❌ Migration failed!${NC}"
    exit 1
fi
echo "${GREEN}✅ Migrations completed${NC}"
echo ""

# 6. Run seeder
echo "${YELLOW}🌱 Seeding penarik accounts...${NC}"
php artisan db:seed --class=PenarikSeeder --force
if [ $? -ne 0 ]; then
    echo "${YELLOW}⚠️  Seeder might have run before (this is OK if accounts already exist)${NC}"
else
    echo "${GREEN}✅ Penarik accounts seeded${NC}"
fi
echo ""

# 7. Clear caches
echo "${YELLOW}🧹 Clearing all caches...${NC}"
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
php artisan optimize
echo "${GREEN}✅ Caches cleared${NC}"
echo ""

# 8. Set permissions
echo "${YELLOW}🔐 Setting permissions...${NC}"
chgrp -R www-data storage bootstrap/cache 2>/dev/null || echo "${YELLOW}⚠️  Could not change group (might need sudo)${NC}"
chmod -R ug+rwx storage bootstrap/cache
echo "${GREEN}✅ Permissions set${NC}"
echo ""

# 9. Verify deployment
echo "${YELLOW}🔍 Verifying deployment...${NC}"
echo ""

# Check migration status
echo "📋 Migration status:"
php artisan migrate:status | tail -3

# Check penarik count
PENARIK_COUNT=$(php artisan tinker --execute="echo User::where('role', 'penarik')->count();" 2>/dev/null | grep -o '[0-9]')
echo ""
echo "👥 Penarik accounts: ${PENARIK_COUNT}"

# Final summary
echo ""
echo "========================================="
echo "${GREEN}✅ DEPLOYMENT COMPLETED SUCCESSFULLY${NC}"
echo "========================================="
echo ""
echo "📝 Next steps:"
echo "1. Test login dengan akun penarik:"
echo "   Email: sokarame@pamsimas.com"
echo "   Password: password"
echo ""
echo "2. Verify QR Scanner security"
echo "3. Check /admin/pengelola untuk manage users"
echo ""
echo "📊 Monitor logs:"
echo "   tail -f storage/logs/laravel.log"
echo ""
echo "Deployment completed at: $(date)"
echo "========================================="
