# Blog Laravel

Un blog moderno e completo costruito con Laravel 10, featuring autenticazione, gestione articoli, sistema di tag, e molto altro.

## 🚀 Caratteristiche

- **Autenticazione completa** con Laravel Fortify
- **Gestione articoli** con CRUD completo
- **Sistema di tag** per categorizzazione
- **Upload immagini** per articoli e profili utente
- **Sistema di contatti** con notifiche email
- **Design responsive** con Bootstrap 5
- **API REST** per integrazioni
- **Testing completo** con PHPUnit e Laravel Dusk
- **Health checks** per monitoring
- **Sistema di backup** automatico

## 📋 Requisiti

- PHP >= 8.1
- Composer
- Node.js >= 16
- NPM o Yarn
- MySQL/PostgreSQL/SQLite
- Web server (Apache/Nginx)

## 🛠️ Installazione

### 1. Clona il repository

```bash
git clone https://github.com/your-username/blog-laravel.git
cd blog-laravel
```

### 2. Installa le dipendenze PHP

```bash
composer install
```

### 3. Installa le dipendenze Node.js

```bash
npm install
```

### 4. Configura l'ambiente

```bash
cp .env.example .env
php artisan key:generate
```

### 5. Configura il database

Modifica il file `.env` con le tue credenziali database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=blog_laravel
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 6. Esegui le migrazioni

```bash
php artisan migrate
```

### 7. Popola il database (opzionale)

```bash
# Per sviluppo
php artisan db:seed

# Per produzione
php artisan db:seed --class=ProductionSeeder
```

### 8. Compila gli assets

```bash
# Per sviluppo
npm run dev

# Per produzione
npm run build
```

### 9. Avvia il server

```bash
php artisan serve
```

L'applicazione sarà disponibile su `http://localhost:8000`

## 🔧 Configurazione Produzione

### 1. Ottimizza l'applicazione

```bash
# Installa dipendenze di produzione
composer install --no-dev --optimize-autoloader

# Cache configurazione e routes
php artisan config:cache
php artisan route:cache

# Compila assets
npm run build
```

### 2. Configura il web server

#### Apache (.htaccess incluso)

```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /path/to/blog-laravel/public
    
    <Directory /path/to/blog-laravel/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

#### Nginx

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/blog-laravel/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 3. Configura HTTPS

Usa Let's Encrypt per certificati SSL gratuiti:

```bash
sudo certbot --apache -d your-domain.com
```

### 4. Configura backup automatici

Aggiungi al crontab:

```bash
# Backup database ogni giorno alle 2:00
0 2 * * * cd /path/to/blog-laravel && php artisan backup:database --compress

# Backup files ogni giorno alle 3:00
0 3 * * * cd /path/to/blog-laravel && php artisan backup:files --compress
```

## 🧪 Testing

### Esegui tutti i test

```bash
php artisan test
```

### Test specifici

```bash
# Feature tests
php artisan test --testsuite=Feature

# Unit tests
php artisan test --testsuite=Unit

# Browser tests (richiede ChromeDriver)
php artisan dusk
```

### Coverage report

```bash
php artisan test --coverage
```

## 📚 API Documentation

### Endpoints principali

#### Articoli

- `GET /api/articles` - Lista articoli
- `GET /api/articles/{id}` - Dettaglio articolo
- `POST /api/articles` - Crea articolo (autenticato)
- `PUT /api/articles/{id}` - Aggiorna articolo (autenticato)
- `DELETE /api/articles/{id}` - Elimina articolo (autenticato)

#### Tag

- `GET /api/tags` - Lista tag
- `GET /api/tags/{id}` - Dettaglio tag
- `POST /api/tags` - Crea tag (autenticato)

#### Health Check

- `GET /health` - Health check base
- `GET /health/detailed` - Health check dettagliato

### Autenticazione API

Usa Laravel Sanctum per l'autenticazione API:

```bash
# Ottieni token
curl -X POST http://your-domain.com/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email": "user@example.com", "password": "password"}'

# Usa token per richieste autenticate
curl -X GET http://your-domain.com/api/articles \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## 🔒 Sicurezza

### Headers di sicurezza

L'applicazione include headers di sicurezza automatici:

- `X-Content-Type-Options: nosniff`
- `X-Frame-Options: DENY`
- `X-XSS-Protection: 1; mode=block`
- `Content-Security-Policy`
- `Strict-Transport-Security` (HTTPS)

### Rate Limiting

- Login: 5 tentativi per minuto
- Contatti: 5 messaggi per minuto
- API: 60 richieste per minuto

### Validazione input

Tutti gli input sono validati e sanitizzati:
- Upload immagini con validazione tipo e dimensione
- Protezione CSRF su tutti i form
- Honeypot per prevenire spam

## 📊 Monitoring

### Health Checks

- `/health` - Status base dell'applicazione
- `/health/detailed` - Controlli dettagliati (DB, cache, storage, mail)

### Logging

Tutti gli eventi sono loggati in `storage/logs/laravel.log`:
- Richieste HTTP con durata e memoria
- Errori e eccezioni
- Eventi di autenticazione

### Backup

Comandi di backup disponibili:

```bash
# Backup database
php artisan backup:database --compress

# Backup files
php artisan backup:files --compress
```

## 🚀 Deploy

### Checklist pre-deploy

- [ ] Test completi passano
- [ ] Assets compilati per produzione
- [ ] Configurazione cache attivata
- [ ] Variabili ambiente configurate
- [ ] Database migrato
- [ ] Backup configurato
- [ ] HTTPS configurato
- [ ] Monitoring attivo

### Deploy con Git

```bash
# Sul server
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
npm run build
```

## 🤝 Contribuire

1. Fork del progetto
2. Crea un branch per la tua feature (`git checkout -b feature/AmazingFeature`)
3. Commit delle modifiche (`git commit -m 'Add some AmazingFeature'`)
4. Push al branch (`git push origin feature/AmazingFeature`)
5. Apri una Pull Request

## 📝 Licenza

Questo progetto è sotto licenza MIT. Vedi il file `LICENSE` per dettagli.

## 📞 Supporto

Per supporto o domande:
- Apri una issue su GitHub
- Contatta: support@your-domain.com

## 🙏 Ringraziamenti

- [Laravel](https://laravel.com) - Framework PHP
- [Bootstrap](https://getbootstrap.com) - CSS Framework
- [Fortify](https://laravel.com/docs/fortify) - Autenticazione
- [Sanctum](https://laravel.com/docs/sanctum) - API Authentication