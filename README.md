# NovaBlog

NovaBlog è un blog moderno costruito con Laravel 12 e Bootstrap 5. Il progetto offre un'esperienza editoriale pulita e minimale in stile corporate (Tesla/Apple inspired), mantenendo al tempo stesso il codice aderente alle pratiche viste a lezione.

## ✨ Funzionalità principali

- Autenticazione completa con Laravel Breeze (Blade + Bootstrap)
- Dashboard personale con statistiche in tempo reale
- CRUD articoli con upload immagine di copertina, tag dinamici e pubblicazione programmata
- Pagina contatti con salvataggio messaggi ed invio email di notifica
- Layout responsive con componenti personalizzati Bootstrap 5
- Seeders e factory per popolare rapidamente un ambiente demo
- Test feature (Pest) per convalidare flusso articoli e form contatti

## 🧰 Requisiti

- PHP 8.2+
- Composer
- Node.js 18+
- NPM 9+
- SQLite (default) oppure MySQL/PostgreSQL

## ⚙️ Installazione

1. **Clona la repo e accedi alla cartella**
```bash
   git clone <repo-url> novablog
   cd novablog
```

2. **Installa dipendenze backend e frontend**
```bash
composer install
npm install
```

3. **Configura l'ambiente**
```bash
cp .env.example .env
php artisan key:generate
```

   Aggiorna, se necessario, le variabili `APP_NAME`, `APP_URL`, `MAIL_*` e le credenziali DB.

4. **Esegui le migrazioni e popola dati demo**
```bash
   php artisan migrate --seed
```

5. **Collega lo storage pubblico per le immagini**
```bash
   php artisan storage:link
```

6. **Compila gli asset**
```bash
   npm run dev   # durante lo sviluppo
   # oppure
   npm run build # per produzione
```

7. **Avvia il server**
```bash
php artisan serve
```

L'applicazione sarà disponibile su `http://localhost:8000`.

## 👤 Credenziali demo

Dopo il seeding troverai un account amministratore già pronto:

```
Email: admin@novablog.test
Password: password
```

## 🧪 Testing

Esegui l'intera suite:
```bash
php artisan test
```

I test coprono il flusso di pubblicazione articoli e l'invio del form contatti.

## 🗂 Struttura principale

- `app/Http` — Controller, Form Request e Policy
- `app/Models` — Modelli Eloquent con relazioni (Article, Tag, ContactMessage)
- `resources/views` — Blade templates responsivi (layout, homepage, CRUD, dashboard)
- `resources/scss` — Tema principale SCSS con override Bootstrap
- `routes/web.php` — Rotte web semplificate e leggibili
- `tests/Feature` — Test Pest per i flussi fondamentali

## 🚀 Deploy (checklist rapida)

- `composer install --no-dev --optimize-autoloader`
- `php artisan migrate --force`
- `php artisan config:cache && php artisan route:cache`
- `npm run build`
- Configurare cron per backup o queue se necessario

## 🤝 Supporto

Per domande e chiarimenti: `team@novablog.test` oppure utilizza il form contatti integrato.

Buon lavoro con NovaBlog! 🚀
