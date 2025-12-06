<?php

/**
 * Test standalone per verificare la configurazione MySQL
 * (Senza Laravel)
 */

echo "🧪 Test Configurazione MySQL Standalone\n";
echo "========================================\n\n";

// Test 1: Verifica estensione PDO MySQL
echo "1️⃣  VERIFICA ESTENSIONE PDO MYSQL\n";
echo "--------------------------------\n";

if (extension_loaded('pdo_mysql')) {
    echo "✅ Estensione PDO MySQL: ABILITATA\n";
} else {
    echo "❌ Estensione PDO MySQL: NON ABILITATA\n";
    echo "💡 Soluzione: Apri C:\\php\\8.0\\php.ini e abilita:\n";
    echo "   extension=pdo_mysql\n";
    echo "   extension=mysqli\n\n";
    exit(1);
}

echo "\n";

// Test 2: Verifica file .env
echo "2️⃣  VERIFICA FILE .ENV\n";
echo "----------------------\n";

$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    echo "✅ File .env trovato\n";
    
    // Leggi e verifica contenuto
    $envContent = file_get_contents($envFile);
    $lines = explode("\n", $envContent);
    
    $requiredVars = ['DB_HOST', 'DB_PORT', 'DB_USER', 'DB_PASSWORD', 'DB_NAME'];
    $foundVars = [];
    
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) {
            continue;
        }
        
        foreach ($requiredVars as $var) {
            if (strpos($line, $var . '=') === 0) {
                $foundVars[] = $var;
                echo "✅ {$var} configurato\n";
            }
        }
    }
    
    if (count($foundVars) < count($requiredVars)) {
        echo "⚠️  Alcune variabili mancanti nel .env\n";
    }
} else {
    echo "❌ File .env non trovato\n";
    echo "💡 Crea il file .env con le credenziali\n";
}

echo "\n";

// Test 3: Test connessione MySQL
echo "3️⃣  TEST CONNESSIONE MYSQL\n";
echo "--------------------------\n";

try {
    // Carica credenziali
    $host = 'localhost';
    $port = '3307';
    $username = 'root';
    $password = '';
    $database = 'blog_db';
    
    // Prova connessione senza database (per crearlo)
    $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ];
    
    $pdo = new PDO($dsn, $username, $password, $options);
    echo "✅ Connessione a MySQL riuscita\n";
    
    // Verifica se il database esiste
    $stmt = $pdo->query("SHOW DATABASES LIKE 'blog_db'");
    $dbExists = $stmt->rowCount() > 0;
    
    if ($dbExists) {
        echo "✅ Database 'blog_db' esiste\n";
        
        // Prova connessione al database
        $pdo->exec("USE blog_db");
        echo "✅ Connessione al database 'blog_db' riuscita\n";
        
        // Verifica tabella articles
        $stmt = $pdo->query("SHOW TABLES LIKE 'articles'");
        $tableExists = $stmt->rowCount() > 0;
        
        if ($tableExists) {
            echo "✅ Tabella 'articles' esiste\n";
            
            // Conta articoli
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM articles");
            $result = $stmt->fetch();
            echo "📊 Articoli nel database: " . $result['total'] . "\n";
        } else {
            echo "⚠️  Tabella 'articles' non esiste\n";
            echo "💡 Esegui: php setup.php\n";
        }
    } else {
        echo "⚠️  Database 'blog_db' non esiste\n";
        echo "💡 Esegui: php setup.php\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Errore di connessione: " . $e->getMessage() . "\n";
    echo "\n💡 Verifica:\n";
    echo "   1. MySQL è in esecuzione?\n";
    echo "   2. Le credenziali sono corrette? (root/root@localhost)\n";
    echo "   3. L'utente root ha i permessi?\n";
    exit(1);
}

echo "\n";

// Test 4: Test classe Database
echo "4️⃣  TEST CLASSE DATABASE\n";
echo "------------------------\n";

try {
    require_once __DIR__ . '/config/database.php';
    
    $db = Database::getInstance();
    echo "✅ Classe Database istanziata correttamente\n";
    
    $connection = $db->getConnection();
    echo "✅ Connessione PDO ottenuta\n";
    
} catch (Exception $e) {
    echo "❌ Errore: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n";

// Test 5: Test classe ArticleManager
echo "5️⃣  TEST CLASSE ARTICLEMANAGER\n";
echo "------------------------------\n";

try {
    require_once __DIR__ . '/app/ArticleManager.php';
    
    $articleManager = new ArticleManager();
    echo "✅ Classe ArticleManager istanziata correttamente\n";
    
    // Test lettura articoli
    $articles = $articleManager->getAllArticles('published');
    echo "✅ Metodo getAllArticles() funziona\n";
    echo "📄 Articoli pubblicati trovati: " . count($articles) . "\n";
    
} catch (Exception $e) {
    echo "❌ Errore: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n";
echo "✅ TUTTI I TEST COMPLETATI CON SUCCESSO!\n";
echo "🎉 Il sistema è pronto per l'uso.\n";

