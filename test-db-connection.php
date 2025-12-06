<?php

/**
 * Script per testare la connessione al database MySQL
 * 
 * Uso: php test-db-connection.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "🔍 Test connessione database MySQL...\n\n";
    
    // Verifica configurazione
    $connection = config('database.default');
    $host = config("database.connections.{$connection}.host");
    $database = config("database.connections.{$connection}.database");
    $username = config("database.connections.{$connection}.username");
    
    echo "📋 Configurazione:\n";
    echo "   Connection: {$connection}\n";
    echo "   Host: {$host}\n";
    echo "   Database: {$database}\n";
    echo "   Username: {$username}\n\n";
    
    // Test connessione
    DB::connection()->getPdo();
    
    echo "✅ Connessione riuscita!\n\n";
    
    // Test query semplice
    $result = DB::select('SELECT VERSION() as version');
    echo "📊 Versione MySQL: " . $result[0]->version . "\n";
    
    // Test database corrente
    $currentDb = DB::select('SELECT DATABASE() as db');
    echo "📊 Database corrente: " . ($currentDb[0]->db ?? 'N/A') . "\n";
    
    echo "\n✅ Tutti i test superati!\n";
    
} catch (\Exception $e) {
    echo "❌ Errore di connessione:\n";
    echo "   " . $e->getMessage() . "\n\n";
    echo "💡 Verifica:\n";
    echo "   1. MySQL è in esecuzione?\n";
    echo "   2. Le credenziali nel .env sono corrette?\n";
    echo "   3. Il database 'salvatore' esiste?\n";
    echo "   4. L'utente 'salvatore' ha i permessi necessari?\n";
    exit(1);
}

