<?php

require_once __DIR__ . '/config/database-standalone.php';

echo "🚀 Setup Database Blog\n";
echo "=====================\n\n";

try {
    $host = 'localhost';
    $port = '3307';
    $username = 'root';
    $password = '';
    
    $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ];
    
    $pdo = new PDO($dsn, $username, $password, $options);
    
    echo "✅ Connessione a MySQL riuscita\n\n";
    
    $sqlFile = __DIR__ . '/database/setup.sql';
    
    if (!file_exists($sqlFile)) {
        throw new Exception("File SQL non trovato: $sqlFile");
    }
    
    $sql = file_get_contents($sqlFile);
    $sql = preg_replace('/--.*$/m', '', $sql);
    $commands = array_filter(array_map('trim', explode(';', $sql)));
    
    echo "📝 Esecuzione script SQL...\n\n";
    
    foreach ($commands as $command) {
        if (!empty($command)) {
            try {
                $pdo->exec($command);
                echo "✅ Comando eseguito\n";
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'already exists') === false) {
                    echo "⚠️  Avviso: " . $e->getMessage() . "\n";
                }
            }
        }
    }
    
    echo "\n✅ Setup completato con successo!\n";
    echo "📊 Database 'blog_db' creato e popolato\n";
    echo "📝 Tabella 'articles' creata con successo\n";
    echo "📄 Articoli di esempio inseriti\n\n";
    
    $pdo->exec("USE blog_db");
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM articles");
    $result = $stmt->fetch();
    
    echo "📈 Statistiche:\n";
    echo "   - Totale articoli: " . $result['total'] . "\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as published FROM articles WHERE status = 'published'");
    $result = $stmt->fetch();
    echo "   - Articoli pubblicati: " . $result['published'] . "\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as draft FROM articles WHERE status = 'draft'");
    $result = $stmt->fetch();
    echo "   - Bozze: " . $result['draft'] . "\n\n";
    
    echo "🎉 Tutto pronto! Puoi ora usare il blog.\n";
    
} catch (Exception $e) {
    echo "❌ Errore: " . $e->getMessage() . "\n";
    echo "\n💡 Verifica:\n";
    echo "   1. MySQL è in esecuzione?\n";
    echo "   2. Le credenziali sono corrette?\n";
    echo "   3. L'utente root ha i permessi per creare database?\n";
    exit(1);
}

