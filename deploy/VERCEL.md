# Vercel-e free deploy (card chara)

> **Live:** https://waliullah-portfolio-five.vercel.app (admin: `/admin`).
> GitHub repo Vercel-e connect kora ache, tai `main` branch-e `git push` korlei auto deploy hoy.

Site ar admin ekshathe deploy hoy:
- website: `https://<project>.vercel.app`
- admin: `https://<project>.vercel.app/admin`

Prottekbar GitHub-e push korlei Vercel nijei notun version deploy kore.

Kibhabe kaj kore:
- Vercel `Dockerfile` diye ekta PHP container (FrankenPHP) banay.
- Database thake **Neon Postgres**-e, Vercel-er Storage tab theke free-te pawa jay.
- Vercel-er disk-e kono file thake na, tai chhobi ar CV **link hishebe** dite hobe (Google Drive ityadi). Admin-e upload button lukano thakbe.

## 1. Local-e chhobi ar CV-ke link banan (ekbar)
Apnar profile photo ar CV ekhon `public/uploads`-e ache. Vercel-e egulo jabe na, tai:
1. Photo ar CV Google Drive-e upload korun.
2. Duto file-er sharing **"Anyone with the link"** kore din.
3. Local admin-e jan (http://127.0.0.1:8000/admin → Settings → Profile). Photo ar CV-er link box-e Drive-er share link paste kore **Save** korun.

## 2. Vercel account ar project
1. https://vercel.com-e **Continue with GitHub** diye sign up korun. Plan jiggesh korle **Hobby (free)** nin.
2. **Add New → Project** chapun, tarpor `waliullahPortfolio` repo **Import** korun.
3. Build settings-e kichu bodlaben na. Repo-r `vercel.json` ar `Dockerfile`-ei shob setting deya ache.
4. **Deploy** chapun. Prothom deploy-te database na thakay error asha shabhabik, sheta niye chinta korben na.

## 3. Database (Neon Postgres) jog korun
1. Project-er **Storage** tab-e jan → **Create Database** → **Neon** → **Free** plan → Create → project-e **Connect**.
   - Card chaile ekhanei thamun ar amake janan.
2. Eta nijei `DATABASE_URL` ar `DATABASE_URL_UNPOOLED` set kore debe.

## 4. Environment variables
Project **Settings → Environment Variables**-e ei duto add korun (Production):

| Name | Value |
|------|-------|
| `APP_KEY` | local-e `php artisan key:generate --show` chaliye je value ashe (`base64:...`) |
| `APP_URL` | `https://<apnar-project>.vercel.app` |

## 5. Apnar content Neon-e copy korun (ekbar)
1. Storage → Neon database → **.env.local** ba **Quickstart** tab theke `DATABASE_URL_UNPOOLED`-er value copy korun.
2. Project folder-e PowerShell-e chalan:
   ```powershell
   php artisan portfolio:copy-to "POSTGRES_URL_EKHANE"
   ```
3. Eta Neon-e table banabe, tarpor apnar shob content copy korbe: settings, projects, skills, services, experience, blog, messages, ar admin login. Admin-er email ar password local-er motoi thakbe.

> Ei URL-e database-er password ache. Kauke pathaben na, chat-eo paste korben na.

## 6. Redeploy
**Deployments** tab-e shesh deploy-er **⋯ → Redeploy** chapun. Tarpor site ar `/admin` khule dekhun.

## Pore ja ja korben
- **Code bodlale:** `git push` korlei auto deploy hobe. Notun migration thakle container start howar shomoy nije theke chole jay, Neon-e alada kore chalate hoy na.
- **Content bodlale:** live admin (`/admin`) theke korben. Content Neon-e thake, deploy korleo muche na.
- **Chhobi ba PDF:** shob shomoy link hishebe din. Google Drive-e sharing "Anyone with the link" kora thakte hobe.
- 5 minute keu site-e na ele container ghumiye pore. Tarpor prothom visit ektu slow hote pare.

## Error hole
- **500 error:** Vercel-e **Logs** tab-e error-ta dekhun, ar amake copy kore pathan.
- **Build fail:** Deployments → je deploy fail koreche shetay click korun → **Build Logs**. Shesher 30-40 line amake pathan.
