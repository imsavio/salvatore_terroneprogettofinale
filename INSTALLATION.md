# Guida all'Installazione - Blog Laravel

Questa guida ti accompagnerà passo dopo passo nell'installazione del blog Laravel.

## 📋 Prerequisiti

Prima di iniziare, assicurati di avere installato:

### Software Richiesto

- **PHP 8.1 o superiore** con estensioni:
  - BCMath
  - Ctype
  - cURL
  - DOM
  - Fileinfo
  - JSON
  - Mbstring
  - OpenSSL
  - PCRE
  - PDO
  - Tokenizer
  - XML
  - GD (per gestione immagini)

- **Composer** (gestore dipendenze PHP)
- **Node.js 16+** e **NPM**
- **Database** (MySQL 5.7+, PostgreSQL 10+, o SQLite 3.8+)
- **Web Server** (Apache 2.4+ o Nginx 1.18+)

### Verifica Prerequisiti

```bash
# Verifica PHP
php --version

# Verifica Composer
composer --version

# Verifica Node.js
node --version

# Verifica NPM
npm --version
```

## 🚀 Installazione Step-by-Step

### Step 1: Clona il Repository

```bash
git clone https://github.com/your-username/blog-laravel.git
cd blog-laravel
```

### Step 2: Installa Dipendenze PHP

```bash
composer install
```

### Step 3: Installa Dipendenze Node.js

```bash
npm install
```

### Step 4: Configurazione Ambiente

```bash
# Copia il file di configurazione
cp .env.example .env

# Genera la chiave dell'applicazione
php artisan key:generate
```

### Step 5: Configurazione Database

Modifica il file `.env` con le tue credenziali:

```env
# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=blog_laravel
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

#### Per SQLite (più semplice per sviluppo)

```env
DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=blog_laravel
# DB_USERNAME=your_username
# DB_PASSWORD=your_password
```

E crea il file database:

```bash
touch database/database.sqlite
```

### Step 6: Esegui Migrazioni

```bash
php artisan migrate
```

### Step 7: Popola Database (Opzionale)

```bash
# Per sviluppo - dati di test
php artisan db:seed

# Per produzione - dati reali
php artisan db:seed --class=ProductionSeeder
```

### Step 8: Compila Assets

```bash
# Per sviluppo
npm run dev

# Per produzione
npm run build
```

### Step 9: Configura Storage

```bash
# Crea il link simbolico per storage
php artisan storage:link
```

### Step 10: Avvia l'Applicazione

```bash
php artisan serve
```

Visita `http://localhost:8000` per vedere l'applicazione.

## 🔧 Configurazione Avanzata

### Configurazione Mail

Per abilitare l'invio email, configura nel `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Configurazione Cache

```env
# Redis (raccomandato per produzione)
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# File (per sviluppo)
CACHE_DRIVER=file
```

### Configurazione Session

```env
# Database (raccomandato per produzione)
SESSION_DRIVER=database

# File (per sviluppo)
SESSION_DRIVER=file
```

## 🐳 Installazione con Docker

### Usando Laravel Sail

```bash
# Installa Sail
composer require laravel/sail --dev

# Avvia i container
./vendor/bin/sail up -d

# Esegui migrazioni
./vendor/bin/sail artisan migrate

# Popola database
./vendor/bin/sail artisan db:seed
```

### Docker Compose Personalizzato

```yaml
version: '3.8'
services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
    ports:
      - "8000:8000"
    volumes:
      - .:/var/www/html
    environment:
      - APP_ENV=local
    depends_on:
      - db
      - redis

  db:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: blog_laravel
      MYSQL_USER: laravel
      MYSQL_PASSWORD: password
    ports:
      - "3306:3306"
    volumes:
      - mysql_data:/var/lib/mysql

  redis:
    image: redis:alpine
    ports:
      - "6379:6379"

volumes:
  mysql_data:
```

## 🔍 Verifica Installazione

### Test di Base

```bash
# Verifica configurazione
php artisan about

# Test database
php artisan tinker
>>> DB::connection()->getPdo();

# Test mail
php artisan tinker
>>> Mail::raw('Test email', function($msg) { $msg->to('test@example.com'); });

# Test cache
php artisan tinker
>>> Cache::put('test', 'Hello World');
>>> Cache::get('test');
```

### Test Completi

```bash
# Esegui tutti i test
php artisan test

# Test con coverage
php artisan test --coverage
```

## 🚨 Risoluzione Problemi

### Errori Comuni

#### 1. Errore "Class not found"

```bash
composer dump-autoload
```

#### 2. Errore "Permission denied" su storage

```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

#### 3. Errore "Key not set"

```bash
php artisan key:generate
```

#### 4. Errore "Database connection"

Verifica le credenziali nel file `.env` e che il database esista.

#### 5. Errore "Storage link"

```bash
php artisan storage:link
```

### Log di Debug

```bash
# Visualizza log
tail -f storage/logs/laravel.log

# Pulisci cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## 📊 Performance

### Ottimizzazioni per Produzione

```bash
# Installa dipendenze ottimizzate
composer install --no-dev --optimize-autoloader

# Cache configurazione
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Compila assets
npm run build
```

### Configurazione Web Server

#### Apache

```apache
<VirtualHost *:80>
    ServerName blog.local
    DocumentRoot /path/to/blog-laravel/public
    
    <Directory /path/to/blog-laravel/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    # Gzip compression
    LoadModule deflate_module modules/mod_deflate.so
    <Location />
        SetOutputFilter DEFLATE
        SetEnvIfNoCase Request_URI \
            \.(?:gif|jpe?g|png)$ no-gzip dont-vary
        SetEnvIfNoCase Request_URI \
            \.(?:exe|t?gz|zip|bz2|sit|rar)$ no-gzip dont-vary
    </Location>
</VirtualHost>
```

#### Nginx

```nginx
server {
    listen 80;
    server_name blog.local;
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

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/javascript application/xml+rss application/json;
}
```

## 🔒 Sicurezza

### Configurazioni di Sicurezza

1. **Cambia password di default**:
   ```bash
   php artisan tinker
   >>> $user = User::find(1);
   >>> $user->password = Hash::make('new-password');
   >>> $user->save();
   ```

2. **Configura HTTPS**:
   ```bash
   # Usa Let's Encrypt
   sudo certbot --apache -d your-domain.com
   ```

3. **Imposta permessi corretti**:
   ```bash
   chmod -R 755 storage
   chmod -R 755 bootstrap/cache
   ```

## 📞 Supporto

Se incontri problemi durante l'installazione:

1. Controlla i log in `storage/logs/laravel.log`
2. Verifica che tutti i prerequisiti siano soddisfatti
3. Apri una issue su GitHub con:
   - Sistema operativo
   - Versione PHP
   - Messaggio di errore completo
   - Passi per riprodurre il problema

## 🎉 Completamento

Una volta completata l'installazione, dovresti essere in grado di:

- ✅ Accedere all'applicazione su `http://localhost:8000`
- ✅ Registrare un nuovo utente
- ✅ Creare e gestire articoli
- ✅ Utilizzare tutte le funzionalità del blog

Buon lavoro con il tuo nuovo blog Laravel! 🚀
