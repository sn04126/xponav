# XpoNav - Laravel Backend Testing Guide

## Prerequisites
- PHP 8.2+, MySQL running, Composer installed
- `.env` configured (copy from `.env.example` if needed)

## 1. Setup
```bash
cd xponav
composer install
php artisan migrate
php artisan db:seed --class=PlanSeeder
php artisan serve
```
Server runs at: http://127.0.0.1:8000

## 2. Authentication Testing

### Register
```bash
curl -X POST http://127.0.0.1:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Test User","email":"test@test.com","password":"password123"}'
```

### Get Verification Code
Check: `storage/logs/verification_codes.txt`

### Verify Email
```bash
curl -X POST http://127.0.0.1:8000/api/verify-email \
  -H "Content-Type: application/json" \
  -d '{"email":"test@test.com","code":"CODE_FROM_FILE"}'
```
Response includes `token` - save this!

### Login
```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@test.com","password":"password123"}'
```

## 3. Forgot Password Testing
```bash
# Step 1: Request reset code
curl -X POST http://127.0.0.1:8000/api/forgot-password \
  -H "Content-Type: application/json" \
  -d '{"email":"test@test.com"}'

# Check storage/logs/verification_codes.txt for code

# Step 2: Reset password
curl -X POST http://127.0.0.1:8000/api/reset-password \
  -H "Content-Type: application/json" \
  -d '{"email":"test@test.com","code":"CODE","password":"newpassword123","password_confirmation":"newpassword123"}'
```

## 4. Social Auth Testing

### Google OAuth (browser)
1. Open: http://127.0.0.1:8000/auth/google
2. Sign in with Google account
3. Redirects to: `xponav://auth/callback?token=XXX&user=BASE64&provider=google`
4. On desktop, you'll see the deep link URL (copy token from URL)

### For Unity testing
- The deep link `xponav://` scheme needs Android device
- For desktop testing, use the JSON API callback endpoint

## 5. Subscription Testing

### Get Plans
```bash
curl http://127.0.0.1:8000/api/subscriptions/plans \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Check Status
```bash
curl http://127.0.0.1:8000/api/subscriptions/status \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## 6. Heatmap Testing

### Upload Position Tracks
```bash
curl -X POST http://127.0.0.1:8000/api/analytics/position-tracks \
  -H "Content-Type: application/json" \
  -d '{"exhibit_id":1,"floor_plan_id":1,"tracks":[{"x":10.5,"y":0,"z":20.3,"timestamp":"2026-03-07T10:00:00Z"},{"x":11.2,"y":0,"z":21.5,"timestamp":"2026-03-07T10:00:03Z"}]}'
```

### Get Heatmap Data
```bash
curl "http://127.0.0.1:8000/api/analytics/heat-map?exhibit_id=1&floor_plan_id=1&period=week"
```

## 7. Admin Panel Testing
1. Open: http://127.0.0.1:8000/admin/login
2. Login: admin@xponav.com / admin123
3. Test pages:
   - Dashboard: /admin/dashboard
   - Subscriptions: /admin/subscriptions
   - Navigation: /admin/navigation
   - Heat Maps: /admin/heat-maps
4. Test responsive: resize browser to mobile width (< 768px)

## 8. Stripe Webhook Testing
```bash
# Install Stripe CLI
stripe listen --forward-to localhost:8000/api/stripe/webhook

# In another terminal, trigger test event
stripe trigger payment_intent.succeeded
```

## 9. Navigation Sessions
```bash
# Create session
curl -X POST http://127.0.0.1:8000/api/navigation-sessions \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{"exhibit_id":1,"floor_plan_id":1}'

# Log event
curl -X POST http://127.0.0.1:8000/api/navigation-sessions/event \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{"session_id":1,"event_type":"destination_selected","event_data":"LOC_GrandLobby"}'
```
