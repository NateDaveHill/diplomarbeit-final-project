# Diplomarbeit Applikationsentwicklung - Nathan Hill

Mein Online Store wird auch auf Railway mit einer CI/CD Deployment Pipeline für testing zwecken LIVE gehosted um direkt den Webshop auch anzusehen und zu testen.

**Live Demo:** [Webshop auf Railway ansehen](https://your-app.up.railway.app) _(URL nach Deployment aktualisieren)_

📦 **Willst du selbst deployen?** Siehe [RAILWAY_DEPLOYMENT.md](./RAILWAY_DEPLOYMENT.md) für eine vollständige Anleitung! 

## Voraussetzungen

### Unterstützte Betriebssysteme

- ✅ **macOS** (empfohlen)
- ✅ **Linux** (alle Distributionen)
- ⚠️ **Windows**: Benötigt WSL (Windows Subsystem for Linux)

### Software-Installation

Bevor du startest, stelle sicher, dass folgende Software installiert ist:

1. **Nix Package Manager**
   ```bash
   curl --proto '=https' --tlsv1.2 -sSf -L https://install.determinate.systems/nix | sh -s -- install
   ```

2. **direnv**
   ```bash
   nix profile install nixpkgs#direnv
   ```

3. **direnv in deiner Shell aktivieren**

   **Welche Shell habe ich?**
   ```bash
   echo $SHELL
   ```
   - Ausgabe `/bin/zsh` → Du verwendest **Zsh** (Standard auf macOS)
   - Ausgabe `/bin/bash` → Du verwendest **Bash** (Standard auf Linux)

   **Für Zsh** (macOS Standard):

   Öffne `~/.zshrc` in einem Editor und füge diese Zeile hinzu:
   ```bash
   eval "$(direnv hook zsh)"
   ```

   **Für Bash** (Linux Standard):

   Öffne `~/.bashrc` in einem Editor und füge diese Zeile hinzu:
   ```bash
   eval "$(direnv hook bash)"
   ```

   Nach dem Hinzufügen, lade die Konfiguration neu:
   ```bash
   source ~/.zshrc    # für Zsh
   # ODER
   source ~/.bashrc   # für Bash
   ```

   **Was macht dieser Hook?**

   Der Hook sorgt dafür, dass direnv automatisch die Entwicklungsumgebung lädt, wenn du ins Projektverzeichnis wechselst. Ohne den Hook müsstest du jedes Mal manuell `devenv shell` ausführen.

## Installation

1. **Repository klonen**
   ```bash
   git clone <repository-url>
   cd diplomarbeit-final-project
   ```

2. **Entwicklungsumgebung erlauben**
   ```bash
   direnv allow
   ```

   Dies lädt automatisch alle benötigten Abhängigkeiten (PHP, MySQL, etc.)

3. **Projekt starten**
   ```bash
   devenv up
   ```

   Beim ersten Start wird automatisch:
   - MySQL gestartet
   - Die Datenbank `webshop_edv` erstellt
   - Alle Tabellen angelegt
   - Ein Admin-Benutzer erstellt

4. **Webshop öffnen**

   Öffne deinen Browser und gehe zu: **http://localhost:8000**

## Standard-Zugangsdaten

**Admin-Login:**
- Benutzername: `admin`
- Passwort: `Pass1234word`

⚠️ **WICHTIG:** Ändere nach dem ersten Login das Passwort über dein Profil!

## Entwicklungsserver stoppen

Drücke `Ctrl + C` im Terminal, in dem `devenv up` läuft.

## Troubleshooting

### Problem: Server startet nicht / Tasks zeigen "completed"

**Lösung:**
```bash
rm -rf .devenv/state/process-compose
devenv up
```

### Problem: MySQL-Verbindungsfehler

**Lösung:**
```bash
# Stoppe alle Prozesse
pkill -f mysqld
pkill -f "php.*8000"

# Starte neu
devenv up
```

### Problem: Datenbank zurücksetzen

**Lösung:**
```bash
rm -rf .devenv/state/mysql
devenv up
```

Die Datenbank wird beim nächsten Start neu erstellt.

## Projektstruktur

```
.
├── Controller/          # Backend-Logik (auth, cart, admin)
│   ├── config.php       # Datenbank-Konfiguration
│   ├── auth.php         # Authentifizierung
│   ├── cart_handler.php # Warenkorb-Funktionen
│   └── admin_handler.php # Admin-Funktionen
│
├── Model/              # Datenbank-Setup
│   └── setup.php       # Erstellt Tabellen & Admin-User
│
├── View/               # Frontend (HTML/CSS/JS)
│   ├── index.php       # Produktübersicht
│   ├── admin.php       # Admin-Dashboard
│   ├── cart.php        # Warenkorb
│   ├── profile.php     # Benutzerprofil
│   ├── product.php     # Produktdetails
│   ├── style.css       # Styles
│   └── main.js         # JavaScript
│
└── devenv.nix          # Entwicklungsumgebung-Konfiguration
```

## Features

- ✅ Benutzerregistrierung & Login
- ✅ Produktkatalog mit Suche
- ✅ Warenkorb & Bestellungen
- ✅ Admin-Dashboard
  - Produktverwaltung
  - Bestellverwaltung
  - Benutzerverwaltung
- ✅ Responsive Design

## Technologien

- **Backend:** PHP 8.3
- **Datenbank:** MySQL 8.0
- **Frontend:** Vanilla JavaScript, CSS
- **Dev Environment:** Nix + devenv
