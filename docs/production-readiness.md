# Production Readiness Checklist

This checklist covers the first security and reliability pass for the public frontend, community, realtime, push, media, and queue systems.

## Environment

- Set `APP_ENV=production`.
- Set `APP_DEBUG=false`.
- Set `APP_URL` to the canonical HTTPS domain.
- Keep secrets out of git and rotate `APP_KEY`, database, mail, Reverb, Redis, WebPush, and third-party credentials per environment.
- Run `php artisan about` during deployment verification and confirm the environment is production.

## Rate Limits

Route-level throttles are applied to high-risk write/poll endpoints:

- Auth: login, register, password reset, password confirmation, password update.
- Community: forum topic, forum post, forum image upload, AI assistant, likes, bookmarks, reports.
- Chat and DM: live chat send/typing, private message send/edit/delete/reaction/read/typing.
- Comments: news, announcement, video, gallery comments.
- Push: subscription create/update/delete/config.
- Polling: search instant, live activity, live chat, DM latest/count/typing, presence heartbeat.

Production should still add upstream limits at the web server or CDN layer for `/login`, `/register`, `/forum/gorsel`, `/canli-sohbet/mesaj`, `/mesajlar/*/mesaj`, and `/rapor/*`.

## Uploads And Storage

- Current image limits are config driven:
  - Normal user: `MEDIA_IMAGE_DEFAULT_LIMIT_MB=15`
  - Trusted user: `MEDIA_IMAGE_TRUSTED_LIMIT_MB=20`
  - Moderator/admin: `MEDIA_IMAGE_MODERATOR_ADMIN_LIMIT_MB=50`
- Keep `MEDIA_IMAGE_MAX_PIXELS` conservative to prevent decompression bombs.
- Run `php artisan storage:link` after deployment and confirm `public/storage` points to `storage/app/public`.
- Never expose `storage/app/private`.
- Keep video upload disabled until a separate media-processing pipeline exists.

## Logs

Recommended production values:

```env
LOG_CHANNEL=daily
LOG_STACK=daily
LOG_LEVEL=info
LOG_DAILY_DAYS=14
```

Use `LOG_LEVEL=debug` only for short incident windows. Forward critical errors to an external monitor when available.

## Queue Workers

Database queue fallback:

```bash
php artisan queue:work database --queue=broadcasts,realtime,notifications,media,safety,default --tries=3 --backoff=5 --timeout=60 --sleep=1
```

Redis queue without Horizon:

```bash
php artisan queue:work redis --queue=broadcasts,realtime,notifications,media,safety,default --tries=3 --backoff=5 --timeout=60 --sleep=1
```

Run workers under Supervisor, systemd, or the hosting platform process manager. Use `php artisan queue:failed` and `php artisan queue:monitor broadcasts,realtime,notifications,media,safety,default --max=100` during health checks.

## Reverb

Production command:

```bash
php artisan reverb:start --host=0.0.0.0 --port=8080
```

Recommended deployment notes:

- Terminate TLS at the reverse proxy or load balancer.
- Set `BROADCAST_CONNECTION=reverb`.
- Set `REVERB_HOST`, `REVERB_PORT`, and `REVERB_SCHEME` to the public websocket endpoint.
- Keep queue workers running because broadcast events use queue names such as `broadcasts` and `notifications`.

## Cache And Optimize

Run after dependency install, migrations, and env setup:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Run during maintenance or rollback:

```bash
php artisan optimize:clear
```

## Backups

- Back up the database before every deploy and on a regular schedule.
- Back up `storage/app/public` and any private media directories.
- Store at least one backup copy off-server.
- Test restore flow periodically, not only backup creation.
- Include `.env` values in secret management, not in normal file backups.

## Deployment Order

1. Pull/release code.
2. Install dependencies with production flags.
3. Confirm `.env` production values.
4. Run `php artisan migrate --force`.
5. Run `php artisan storage:link`.
6. Run `php artisan config:cache`, `route:cache`, and `view:cache`.
7. Restart queue workers.
8. Restart Reverb if used.
9. Check `/up`, public home page, login, forum page, DM page, notification count, and upload endpoint.
10. Check logs and failed jobs.

