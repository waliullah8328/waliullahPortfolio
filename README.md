# Portfolio CMS (Laravel 13)

A modern, fully customizable personal portfolio with an admin dashboard.

## Run it

```bash
php artisan serve          # http://127.0.0.1:8000
```

After changing CSS/JS or Blade classes, rebuild the assets with `npm run build` (or run `npm run dev` while you work).

**Admin:** http://127.0.0.1:8000/admin — `admin@example.com` / `password`. Change these under **Admin → Account**.

## What you can manage from the dashboard

| Section | What it does |
|---|---|
| Dashboard | Stats, recent messages, a chart of this week's messages, recent posts |
| Projects | Add, edit, delete; cover image; category; tech stack; live and repo links; featured, published, sort order |
| Skills | Name, category, proficiency %, icon URL, visibility |
| Experience | Work and education timeline; each description line becomes a bullet point |
| Blog | Markdown editor with toolbar and live preview, cover image, tags, drafts and scheduled posts, view counter |
| Recommendations | Add your own, or approve or hide the ones visitors submit from the site |
| Messages | Inbox for the contact form: read/unread, search, reply by email |
| Settings | Name, role, typing words, photo, **CV upload**, about text, stats, contact and social links, accent colors, default theme, turning each section on or off, SEO, favicon, footer |

## Notes

- Database: SQLite (`database/database.sqlite`). To use MySQL, change the `DB_*` values in `.env` and run `php artisan migrate --seed`.
- Uploads are stored in `public/uploads` (no `storage:link` needed).
- To add a new setting, add one line to `SettingController::GROUPS`. Read it in Blade with `setting('key')`.
- `php artisan migrate:fresh --seed` resets everything to the demo content.
