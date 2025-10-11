# API Documentation - Blog Laravel

Documentazione completa delle API REST disponibili nel blog Laravel.

## 🔐 Autenticazione

L'API utilizza Laravel Sanctum per l'autenticazione basata su token.

### Ottenere un Token

```http
POST /api/auth/login
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "password"
}
```

**Risposta:**
```json
{
    "token": "1|abcdef123456...",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "user@example.com",
        "username": "johndoe"
    }
}
```

### Registrazione

```http
POST /api/auth/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "user@example.com",
    "password": "password",
    "password_confirmation": "password",
    "username": "johndoe"
}
```

### Logout

```http
POST /api/auth/logout
Authorization: Bearer {token}
```

## 📝 Articoli

### Lista Articoli

```http
GET /api/articles
```

**Parametri Query:**
- `page` - Numero pagina (default: 1)
- `per_page` - Articoli per pagina (default: 15)
- `search` - Termine di ricerca
- `tag` - Filtra per tag (slug)
- `author` - Filtra per autore (ID)
- `published` - Solo articoli pubblicati (true/false)

**Risposta:**
```json
{
    "data": [
        {
            "id": 1,
            "title": "Titolo Articolo",
            "slug": "titolo-articolo",
            "excerpt": "Estratto dell'articolo...",
            "content": "Contenuto completo...",
            "featured_image": "path/to/image.jpg",
            "published_at": "2024-01-01T12:00:00.000000Z",
            "created_at": "2024-01-01T10:00:00.000000Z",
            "updated_at": "2024-01-01T12:00:00.000000Z",
            "user": {
                "id": 1,
                "name": "John Doe",
                "username": "johndoe"
            },
            "tags": [
                {
                    "id": 1,
                    "name": "Laravel",
                    "slug": "laravel",
                    "color": "#FF2D20"
                }
            ]
        }
    ],
    "links": {
        "first": "http://blog.local/api/articles?page=1",
        "last": "http://blog.local/api/articles?page=10",
        "prev": null,
        "next": "http://blog.local/api/articles?page=2"
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 10,
        "per_page": 15,
        "to": 15,
        "total": 150
    }
}
```

### Dettaglio Articolo

```http
GET /api/articles/{id}
```

**Risposta:**
```json
{
    "id": 1,
    "title": "Titolo Articolo",
    "slug": "titolo-articolo",
    "excerpt": "Estratto dell'articolo...",
    "content": "Contenuto completo...",
    "featured_image": "path/to/image.jpg",
    "published_at": "2024-01-01T12:00:00.000000Z",
    "created_at": "2024-01-01T10:00:00.000000Z",
    "updated_at": "2024-01-01T12:00:00.000000Z",
    "user": {
        "id": 1,
        "name": "John Doe",
        "username": "johndoe",
        "bio": "Bio dell'autore",
        "avatar": "path/to/avatar.jpg"
    },
    "tags": [
        {
            "id": 1,
            "name": "Laravel",
            "slug": "laravel",
            "color": "#FF2D20"
        }
    ]
}
```

### Crea Articolo

```http
POST /api/articles
Authorization: Bearer {token}
Content-Type: application/json

{
    "title": "Nuovo Articolo",
    "content": "Contenuto dell'articolo...",
    "excerpt": "Estratto dell'articolo...",
    "featured_image": "path/to/image.jpg",
    "published_at": "2024-01-01T12:00:00",
    "tags": [1, 2, 3]
}
```

**Risposta:**
```json
{
    "id": 2,
    "title": "Nuovo Articolo",
    "slug": "nuovo-articolo",
    "excerpt": "Estratto dell'articolo...",
    "content": "Contenuto dell'articolo...",
    "featured_image": "path/to/image.jpg",
    "published_at": "2024-01-01T12:00:00.000000Z",
    "created_at": "2024-01-01T10:00:00.000000Z",
    "updated_at": "2024-01-01T10:00:00.000000Z",
    "user": {
        "id": 1,
        "name": "John Doe",
        "username": "johndoe"
    },
    "tags": [
        {
            "id": 1,
            "name": "Laravel",
            "slug": "laravel",
            "color": "#FF2D20"
        }
    ]
}
```

### Aggiorna Articolo

```http
PUT /api/articles/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "title": "Articolo Aggiornato",
    "content": "Contenuto aggiornato...",
    "excerpt": "Estratto aggiornato...",
    "featured_image": "path/to/new-image.jpg",
    "published_at": "2024-01-01T12:00:00",
    "tags": [1, 2, 3]
}
```

### Elimina Articolo

```http
DELETE /api/articles/{id}
Authorization: Bearer {token}
```

**Risposta:**
```json
{
    "message": "Articolo eliminato con successo"
}
```

## 🏷️ Tag

### Lista Tag

```http
GET /api/tags
```

**Parametri Query:**
- `search` - Cerca per nome
- `popular` - Ordina per popolarità

**Risposta:**
```json
{
    "data": [
        {
            "id": 1,
            "name": "Laravel",
            "slug": "laravel",
            "color": "#FF2D20",
            "articles_count": 15,
            "created_at": "2024-01-01T10:00:00.000000Z",
            "updated_at": "2024-01-01T10:00:00.000000Z"
        }
    ]
}
```

### Dettaglio Tag

```http
GET /api/tags/{id}
```

### Crea Tag

```http
POST /api/tags
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "Nuovo Tag",
    "color": "#FF2D20"
}
```

### Aggiorna Tag

```http
PUT /api/tags/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "Tag Aggiornato",
    "color": "#00FF00"
}
```

### Elimina Tag

```http
DELETE /api/tags/{id}
Authorization: Bearer {token}
```

## 👤 Utenti

### Lista Utenti

```http
GET /api/users
```

**Parametri Query:**
- `page` - Numero pagina
- `per_page` - Utenti per pagina
- `search` - Cerca per nome o username

### Dettaglio Utente

```http
GET /api/users/{id}
```

**Risposta:**
```json
{
    "id": 1,
    "name": "John Doe",
    "username": "johndoe",
    "email": "user@example.com",
    "bio": "Bio dell'utente",
    "avatar": "path/to/avatar.jpg",
    "website": "https://johndoe.com",
    "location": "Milano, Italia",
    "created_at": "2024-01-01T10:00:00.000000Z",
    "updated_at": "2024-01-01T10:00:00.000000Z",
    "articles_count": 25,
    "published_articles_count": 20
}
```

### Profilo Utente Corrente

```http
GET /api/user/profile
Authorization: Bearer {token}
```

### Aggiorna Profilo

```http
PUT /api/user/profile
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "John Doe",
    "username": "johndoe",
    "email": "user@example.com",
    "bio": "Bio aggiornata",
    "website": "https://johndoe.com",
    "location": "Milano, Italia"
}
```

### Cambia Password

```http
PUT /api/user/password
Authorization: Bearer {token}
Content-Type: application/json

{
    "current_password": "old-password",
    "password": "new-password",
    "password_confirmation": "new-password"
}
```

## 📧 Contatti

### Invia Messaggio

```http
POST /api/contact
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "subject": "Oggetto del messaggio",
    "message": "Contenuto del messaggio..."
}
```

**Risposta:**
```json
{
    "message": "Messaggio inviato con successo"
}
```

## 🔍 Ricerca

### Ricerca Globale

```http
GET /api/search
```

**Parametri Query:**
- `q` - Termine di ricerca (obbligatorio)
- `type` - Tipo di contenuto (articles, users, tags)
- `page` - Numero pagina

**Risposta:**
```json
{
    "data": {
        "articles": [
            {
                "id": 1,
                "title": "Articolo Trovato",
                "slug": "articolo-trovato",
                "excerpt": "Estratto...",
                "user": {
                    "name": "John Doe",
                    "username": "johndoe"
                }
            }
        ],
        "users": [
            {
                "id": 1,
                "name": "John Doe",
                "username": "johndoe",
                "bio": "Bio..."
            }
        ],
        "tags": [
            {
                "id": 1,
                "name": "Laravel",
                "slug": "laravel",
                "color": "#FF2D20"
            }
        ]
    },
    "meta": {
        "total": 25,
        "query": "laravel"
    }
}
```

## 📊 Statistiche

### Statistiche Generali

```http
GET /api/stats
Authorization: Bearer {token}
```

**Risposta:**
```json
{
    "articles": {
        "total": 150,
        "published": 120,
        "drafts": 30
    },
    "users": {
        "total": 25,
        "active": 20
    },
    "tags": {
        "total": 50,
        "most_used": "Laravel"
    },
    "views": {
        "today": 150,
        "this_week": 1200,
        "this_month": 5000
    }
}
```

## 🏥 Health Check

### Health Check Base

```http
GET /api/health
```

**Risposta:**
```json
{
    "status": "ok",
    "timestamp": "2024-01-01T12:00:00.000000Z",
    "version": "1.0.0"
}
```

### Health Check Dettagliato

```http
GET /api/health/detailed
```

**Risposta:**
```json
{
    "status": "ok",
    "timestamp": "2024-01-01T12:00:00.000000Z",
    "checks": {
        "database": {
            "status": "ok",
            "message": "Database connection successful"
        },
        "cache": {
            "status": "ok",
            "message": "Cache system working"
        },
        "storage": {
            "status": "ok",
            "message": "Storage system working"
        },
        "mail": {
            "status": "ok",
            "message": "Mail configuration is set to: smtp"
        }
    }
}
```

## 📤 Upload File

### Upload Immagine

```http
POST /api/upload/image
Authorization: Bearer {token}
Content-Type: multipart/form-data

image: [file]
type: article|avatar
```

**Risposta:**
```json
{
    "url": "storage/images/2024/01/01/image.jpg",
    "path": "images/2024/01/01/image.jpg",
    "size": 1024000,
    "mime_type": "image/jpeg"
}
```

## ⚠️ Codici di Errore

### Errori HTTP

- `200` - Successo
- `201` - Creato con successo
- `400` - Richiesta non valida
- `401` - Non autenticato
- `403` - Non autorizzato
- `404` - Non trovato
- `422` - Errore di validazione
- `429` - Troppe richieste
- `500` - Errore del server

### Formato Errori

```json
{
    "message": "The given data was invalid.",
    "errors": {
        "email": [
            "The email field is required."
        ],
        "password": [
            "The password field is required."
        ]
    }
}
```

## 🔒 Rate Limiting

- **API Generale**: 60 richieste per minuto
- **Autenticazione**: 5 tentativi per minuto
- **Contatti**: 5 messaggi per minuto
- **Upload**: 10 upload per minuto

### Headers Rate Limiting

```http
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
X-RateLimit-Reset: 1640995200
```

## 📝 Esempi di Utilizzo

### JavaScript (Fetch)

```javascript
// Login
const login = async (email, password) => {
    const response = await fetch('/api/auth/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ email, password })
    });
    
    return await response.json();
};

// Ottieni articoli
const getArticles = async (token) => {
    const response = await fetch('/api/articles', {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        }
    });
    
    return await response.json();
};
```

### PHP (Guzzle)

```php
use GuzzleHttp\Client;

$client = new Client(['base_uri' => 'https://your-domain.com/api/']);

// Login
$response = $client->post('auth/login', [
    'json' => [
        'email' => 'user@example.com',
        'password' => 'password'
    ]
]);

$data = json_decode($response->getBody(), true);
$token = $data['token'];

// Ottieni articoli
$response = $client->get('articles', [
    'headers' => [
        'Authorization' => "Bearer {$token}",
        'Accept' => 'application/json'
    ]
]);
```

### cURL

```bash
# Login
curl -X POST https://your-domain.com/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email": "user@example.com", "password": "password"}'

# Ottieni articoli
curl -X GET https://your-domain.com/api/articles \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

## 🔄 Webhooks

### Eventi Disponibili

- `article.created` - Articolo creato
- `article.updated` - Articolo aggiornato
- `article.deleted` - Articolo eliminato
- `user.registered` - Utente registrato
- `contact.sent` - Messaggio inviato

### Configurazione Webhook

```http
POST /api/webhooks
Authorization: Bearer {token}
Content-Type: application/json

{
    "url": "https://your-app.com/webhook",
    "events": ["article.created", "user.registered"],
    "secret": "your-webhook-secret"
}
```

## 📚 SDK e Librerie

### JavaScript SDK

```bash
npm install blog-laravel-sdk
```

```javascript
import BlogAPI from 'blog-laravel-sdk';

const api = new BlogAPI({
    baseURL: 'https://your-domain.com/api',
    token: 'your-token'
});

// Usa l'API
const articles = await api.articles.list();
const article = await api.articles.create({
    title: 'New Article',
    content: 'Content...'
});
```

### PHP SDK

```bash
composer require your-org/blog-laravel-sdk
```

```php
use YourOrg\BlogLaravelSDK\BlogAPI;

$api = new BlogAPI([
    'base_url' => 'https://your-domain.com/api',
    'token' => 'your-token'
]);

$articles = $api->articles()->list();
$article = $api->articles()->create([
    'title' => 'New Article',
    'content' => 'Content...'
]);
```

## 🆘 Supporto

Per supporto API:

- 📧 Email: api-support@your-domain.com
- 📖 Documentazione: https://your-domain.com/docs/api
- 🐛 Bug Reports: https://github.com/your-org/blog-laravel/issues
- 💬 Chat: https://discord.gg/your-server
