<?php

/**
 * Script per testare la connessione MySQL e diagnosticare problemi
 */

echo "🔍 Test Connessione MySQL\n";
echo "========================\n\n";

// Credenziali da testare
$configs = [
    [
        'host' => 'localhost',
        'port' => '3307',
        'username' => 'root',
        'password' => '',
        'desc' => 'root/(vuota)@localhost:3307'
    ],
];

foreach ($configs as $config) {
    echo "🧪 Test: {$config['desc']}\n";
    echo "--------------------------------\n";
    
    try {
        $dsn = "mysql:host={$config['host']};port={$config['port']};charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5
        ];
        
        $pdo = new PDO($dsn, $config['username'], $config['password'], $options);
        
        echo "✅ Connessione riuscita!\n";
        
        // Test query semplice
        $stmt = $pdo->query("SELECT VERSION() as version");
        $result = $stmt->fetch();
        echo "📊 Versione MySQL: " . $result['version'] . "\n";
        
        // Lista database
        $stmt = $pdo->query("SHOW DATABASES");
        $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "📁 Database disponibili: " . count($databases) . "\n";
        
        echo "\n✅ Questa configurazione funziona!\n";
        echo "💡 Usa queste credenziali nel file .env:\n";
        echo "   DB_HOST={$config['host']}\n";
        echo "   DB_PORT={$config['port']}\n";
        echo "   DB_USER={$config['username']}\n";
        echo "   DB_PASSWORD={$config['password']}\n\n";
        
        break; // Ferma al primo successo
        
    } catch (PDOException $e) {
        echo "❌ Errore: " . $e->getMessage() . "\n\n";
    }
}

echo "\n💡 Soluzioni Possibili:\n";
echo "   1. Verifica la password di root in TablePlus\n";
echo "   2. Prova a resettare la password MySQL\n";
echo "   3. Crea un nuovo utente MySQL con permessi\n";
echo "   4. Verifica che MySQL sia in esecuzione\n";

