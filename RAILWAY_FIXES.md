# Railway Sleeping Database Fixes

## Was wurde geändert?

Ich habe den Code angepasst, um mit Railway's Free Tier MySQL zu funktionieren, die automatisch "einschläft" nach Inaktivität.

### Geänderte Dateien:

#### 1. **start.sh** - Startet Container mit DB-Wartelogik
- Wartet bis zu 2 Minuten (60 Retries × 2 Sekunden) bis DB aufwacht
- Parsed DATABASE_URL automatisch
- Zeigt klare Fortschritts-Meldungen
- Führt setup.php automatisch aus nachdem DB bereit ist

#### 2. **Controller/config.php** - Verbindung mit Retry-Logik
- Parsed DATABASE_URL von Railway korrekt
- 3 Verbindungsversuche mit 2 Sekunden Pause
- Bessere Fehlermeldungen die das "Sleeping DB" Problem erklären
- Funktioniert auch mit HTTPS Proxies (Railway)

#### 3. **Model/setup.php** - Datenbank Setup
- Funktioniert jetzt mit DATABASE_URL (Railway) UND .env (lokal)
- Erstellt automatisch Datenbank & Tabellen
- Fügt Admin-User und Sample-Produkte ein

#### 4. **View/debug.php** - Debug-Hilfe
- Besuche `/debug.php` um zu sehen was falsch läuft
- Zeigt DATABASE_URL, parsed values, connection status
- **LÖSCHE diese Datei nach dem Deployment!**

## Wie funktioniert es jetzt?

### Bei jedem Deployment:
1. Docker Container startet
2. `start.sh` läuft:
   - Parsed DATABASE_URL
   - Wartet bis MySQL aufgewacht ist (kann 30-60 Sekunden dauern)
   - Führt `setup.php` aus (erstellt Tabellen, Admin-User)
   - Startet Apache

### Bei normalen Web-Requests:
- PHP verbindet zur DB mit 3 Retry-Versuchen
- Falls DB schläft: Wartet max 6 Sekunden (3×2s), zeigt dann hilfreiche Fehlermeldung

## Nächste Schritte - Deployment:

```bash
# 1. Commit alle Änderungen
git add .
git commit -m "Fix Railway deployment with sleeping MySQL database"

# 2. Push zu GitHub (triggert Auto-Deployment)
git push origin main
```

## Was du in Railway Logs sehen solltest:

```
======================================
Starting Railway deployment...
======================================
✓ DATABASE_URL detected, parsing...
  Host: mysql.railway.internal
  Port: 3306
  Database: railway
  User: root

======================================
Waiting for MySQL database...
Note: Free tier DB may be sleeping and can take 30-60 seconds to wake up
======================================
⏳ Attempt 5/60: Still waiting for database to wake up...
⏳ Attempt 10/60: Still waiting for database to wake up...

✓ Database connection successful!

======================================
Running database setup...
======================================
Using DATABASE_URL from Railway...
Database Setup Configuration:
  Host: mysql.railway.internal:3306
  Database: railway
  User: root
  Password: ***

[1/4] Connecting to MySQL server...
[2/4] Creating database 'railway' if it does not exist...
✓ Database checked/created successfully

[3/4] Creating tables...
✓ User table created
✓ Products table created
✓ Orders table created
✓ Order_items Table created

[4/4] Inserting default data...
✓ Admin user created
   Username: admin
   Password: Pass1234word
✓ Sample products inserted

✓ Setup Complete!

======================================
Starting Apache web server...
======================================
```

## Troubleshooting:

### Falls es immer noch nicht funktioniert:

1. **Check ob MySQL Service läuft:**
   - Railway Dashboard → Dein Projekt → MySQL Service
   - Status muss "Active" sein

2. **Besuche debug.php:**
   ```
   https://diplomarbeit-final-project-production.up.railway.app/debug.php
   ```
   - Zeigt dir genau was das Problem ist

3. **Check die Railway Logs:**
   - Web Service → Deployments → Latest
   - Suche nach Fehlermeldungen

4. **Falls "DATABASE_URL is set: NO":**
   - Services sind nicht linked
   - Oder MySQL Service wurde noch nicht hinzugefügt

## Wichtig:

⚠️ **Das Sleeping-Problem bleibt bestehen!**
- Die ersten 1-2 Besucher nach Inaktivität müssen 30-60 Sekunden warten
- Danach funktioniert alles normal für ~5 Minuten
- Das ist normal bei Railway Free Tier!

✅ **Aber jetzt funktioniert es wenigstens korrekt:**
- Container startet erfolgreich
- Datenbank wird aufgesetzt
- Besucher sehen hilfreiche Fehlermeldung falls DB noch schläft
- Nach Aufwachen funktioniert alles

## Nach erfolgreicher Deployment:

1. ✅ Test die Site
2. ✅ Login mit admin / Pass1234word
3. ✅ Ändere das Admin-Passwort!
4. ⚠️ **LÖSCHE `View/debug.php`** (Sicherheitsrisiko!)
