<?php

/**
 * Script per verificare e correggere il file .env
 */

$envFile = __DIR__ . '/.env';

if (!file_exists($envFile)) {
    echo "❌ File .env non trovato\n";
    exit(1);
}

echo "🔍 Verifica File .env\n";
echo "=====================\n\n";

$envContent = file_get_contents($envFile);
$lines = explode("\n", $envContent);

$errors = [];
$cleanLines = [];

foreach ($lines as $lineNum => $line) {
    $line = trim($line);
    $lineNumActual = $lineNum + 1;
    
    // Salta righe vuote e commenti
    if (empty($line) || strpos($line, '#') === 0) {
        $cleanLines[] = $line;
        continue;
    }
    
    // Verifica formato chiave=valore
    if (strpos($line, '=') === false) {
        $errors[] = "Riga {$lineNumActual}: Formato non valido (manca =)";
        continue;
    }
    
    list($key, $value) = explode('=', $line, 2);
    $key = trim($key);
    $value = trim($value);
    
    // Verifica che la chiave sia valida
    if (empty($key) || !preg_match('/^[A-Z_][A-Z0-9_]*$/', $key)) {
        $errors[] = "Riga {$lineNumActual}: Chiave non valida: {$key}";
        continue;
    }
    
    // Per DB_PORT, verifica che sia un numero
    if ($key === 'DB_PORT' && !empty($value) && !is_numeric($value)) {
        $errors[] = "Riga {$lineNumActual}: DB_PORT deve essere un numero";
        continue;
    }
    
    $cleanLines[] = $line;
}

if (!empty($errors)) {
    echo "❌ Errori trovati:\n";
    foreach ($errors as $error) {
        echo "   - {$error}\n";
    }
    echo "\n";
}

// Crea file pulito
$cleanContent = implode("\n", $cleanLines);

// Assicurati che le variabili MySQL ci siano
$requiredVars = [
    'DB_CONNECTION=mysql',
    'DB_HOST=localhost',
    'DB_PORT=3307',
    'DB_DATABASE=blog_db',
    'DB_USERNAME=root',
    'DB_PASSWORD=',
];

$existingVars = [];
foreach ($cleanLines as $line) {
    foreach ($requiredVars as $var) {
        $varName = explode('=', $var)[0];
        if (strpos($line, $varName . '=') === 0) {
            $existingVars[] = $varName;
        }
    }
}

// Aggiungi variabili mancanti
foreach ($requiredVars as $var) {
    $varName = explode('=', $var)[0];
    if (!in_array($varName, $existingVars)) {
        $cleanContent .= "\n{$var}";
    }
}

// Salva file corretto
file_put_contents($envFile, $cleanContent);

echo "✅ File .env verificato e corretto!\n";
echo "📝 Variabili MySQL configurate:\n";
foreach ($requiredVars as $var) {
    echo "   {$var}\n";
}

