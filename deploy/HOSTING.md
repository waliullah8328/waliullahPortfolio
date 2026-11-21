# Free hosting (card chara) — step by step

Website ar admin ekta Laravel app, tai ekbar upload korlei duto live:
`https://yoursite.../` (website) ar `https://yoursite.../admin` (admin).

Ei guide free **PHP shared hosting**-er jonno (jemon InfinityFree). Sign up-e shudhu email lage, card lage na.

## Hosting-e ja thaka lagbe
- **PHP 8.3 ba tar beshi** (control panel-e PHP version select korte parle 8.3+ diben)
- **SQLite (pdo_sqlite)** extension. Apnar database ekta file: `database/database.sqlite`
- File Manager ba FTP access

## 1. Account khulun
1. Hosting site-e sign up korun, tarpor **Create Account** chapun.
2. Ekta free subdomain nin, jemon `ripon.infinityfreeapp.com`.
3. Account active howa porjonto opekkha korun (kokhono kokhono kichu minute lage).
4. Control panel-e **PHP version** option thakle **8.3 ba tar beshi** select korun.

## 2. Upload zip banan (apnar PC-te)
Project folder-e PowerShell khule chalan:

```powershell
.\deploy\build-shared-hosting.ps1 -Url https://ripon.infinityfreeapp.com -Php 8.3
```

- `-Url`-e apnar asol live address din, `https://` shoho.
- Hosting-e PHP 8.4 ba 8.5 thakle `-Php` shei version-e din.
- Output file: `deploy\dist\portfolio-upload.zip` (~10 MB). Er moddhe apnar local database ar upload kora shob kichu (photo, CV) ache, tai live site dekhte thik local-er motoi hobe.

## 3. Upload ar extract
1. File Manager-e **htdocs** folder khulun.
2. Hosting-er default file-gulo (`index2.html` ityadi) delete korun.
3. `portfolio-upload.zip` upload korun.
4. Zip-ta **htdocs-er bhitore-i Extract** korun. Extract korar por `htdocs`-e sorasori `app`, `public`, `vendor`, `.htaccess`, `.env` dekhte paben, ar kono folder-er bhitore thakbe na.
5. Zip file-ta delete kore din.

> File Manager zip upload ba extract korte na dile: zip-ta PC-te extract korun, tarpor **FileZilla** (FTP) diye shob file `htdocs`-e upload korun. Hosting-er control panel-e FTP host, username ar password paben. Prothom bar ~6,700 file upload hote shomoy lagbe.

## 4. Check korun
- `https://yoursite/` khulun, website asha uchit.
- `https://yoursite/admin` khulun, local-e je email ar password diye login koren, shetai diye login korun.
- **Login korar por shathe shathe Admin → Account theke password bodle nin.**
- Settings theke ekta chhobi upload kore dekhun upload kaj korche kina.

Error ashle:
- "could not find driver": hosting-e SQLite nei. Onno hosting dekhte hobe, ba MySQL-e shorate hobe (amake bolen).
- "500 error" ba blank page: PHP version 8.3-er kom. Control panel theke bariye din, ba build-e thik `-Php` din.

## 5. Pore code update korle
Live site-e admin theke ja edit korben, sheta server-er database-e thake. Tai update-er shomoy **`-CodeOnly`** use korun, jate server-er data muche na jay:

```powershell
.\deploy\build-shared-hosting.ps1 -Url https://ripon.infinityfreeapp.com -Php 8.3 -CodeOnly
```

Ei zip-e `.env`, database ar uploads thake na. Upload kore `htdocs`-e extract korun, ar purono file overwrite hote din.

> `-CodeOnly` chara notun build upload korben na, tahole live site-er shob edit muche jabe.

## Backup
Majhe majhe File Manager/FTP theke ei duto download kore rakhun:
- `htdocs/database/database.sqlite` (shob content)
- `htdocs/public/uploads/` (chhobi, CV)
