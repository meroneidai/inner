# InnerPath Deployment

## Requirements
- PHP 8.3+
- Composer
- Node.js 20+
- MySQL 8+ (production)
- Queue worker
- Scheduler cron

## Setup
```bash
cp .env.example .env
php artisan key:generate
# Configure MySQL + OPENROUTER_API_KEY in .env
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
```

## Runtime
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:work --queue=default
```

Cron:
```
* * * * * cd /path/to/innerpath && php artisan schedule:run >> /dev/null 2>&1
```

## Environment essentials
- `APP_LOCALE=ar`
- `AI_PROVIDER=openrouter`
- `OPENROUTER_API_KEY=...`
- `OPENROUTER_ANALYSIS_MODEL=openai/gpt-4o`
- `AI_AUTO_ANALYZE=true`
- Admin can override defaults at `/admin/ai` and edit prompts at `/admin/prompts`
- `TRANSCRIPTION_PROVIDER=openai`
- `DB_CONNECTION=mysql`
- `QUEUE_CONNECTION=database` (or redis)
- `DREAM_MEDIA_DISK=local` or `s3`

## Human Inner Profile
```bash
php artisan migrate --force
php artisan db:seed --class=Database\\Seeders\\ProfileQuestionBankSeeder --force
```

- Multi-step onboarding: `/onboarding/welcome`
- Inner world: `/profile/inner-world`
- Admin question bank: `/admin/questions`
- Analytical consent controls AI context inclusion
- Hypotheses require explicit user confirmation

## Notifications & email
- In-app center: `/notifications` (bell in nav)
- Channels: database + mail (per user prefs in `/settings`)
- Scheduled: hourly daily-care & dream-reminder (by preferred hour), daily growth reminder, weekly/monthly reports
- Configure mail in `.env` (`MAIL_MAILER=smtp` or `log` locally)
- Test: `php artisan innerpath:notify-test user@innerpath.test --type=care`

Required for production:
```
MAIL_MAILER=smtp
MAIL_HOST=...
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_FROM_ADDRESS=hello@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

Also run both:
```
php artisan queue:work
* * * * * php artisan schedule:run
```

## Notes
- Dreams/audio are private; do not expose storage publicly.
- Admins do not see dream contents by default.
- Never commit `.env`.
- Do not send full profiles to AI providers — only filtered context.
