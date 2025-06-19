<?php

// Charge les variables d'environnement si l'application n'est pas Laravel complète
// Ce n'est pas nécessaire si ce script est exécuté DANS le contexte de votre projet Laravel
// via 'php test_sqlsrv_remote.php' car Laravel ne charge pas le .env dans ce cas.
// Pour un test simple, on peut charger manuellement ou coder en dur les valeurs pour le test.

// Récupérer les variables d'environnement de Laravel (si ce script est exécuté après le boot de Laravel)
// Sinon, mettez les valeurs en dur pour le test
$DB_HOST = getenv('DB_HOST') ?: '172.31.5.21\DBINS'; // Remplacez par votre IP\Instance
$DB_DATABASE = getenv('DB_DATABASE') ?: 'operator_db';
$DB_USERNAME = getenv('DB_USERNAME') ?: 'user_operators';
$DB_PASSWORD = getenv('DB_PASSWORD') ?: 'mypassword'; // Remplacez par votre mot de passe réel
$DB_ENCRYPT = getenv('DB_ENCRYPT') ?: 'true';

// Les options de chiffrement sont importantes
$encrypt = filter_var($DB_ENCRYPT, FILTER_VALIDATE_BOOLEAN); // Convertit 'true'/'false' en booléen
$trustServerCertificate = true; // Toujours mettre à true si certificat auto-signé ou non approuvé

echo "Tentative de connexion à SQL Server...\n";
echo "Server: {$DB_HOST}\n";
echo "Database: {$DB_DATABASE}\n";
echo "Username: {$DB_USERNAME}\n";
echo "Encrypt: " . ($encrypt ? 'True' : 'False') . "\n";
echo "TrustServerCertificate: " . ($trustServerCertificate ? 'True' : 'False') . "\n\n";

// Configuration de la chaîne de connexion DSN
$dsn = "sqlsrv:Server={$DB_HOST},1433;Database={$DB_DATABASE};Encrypt=" . ($encrypt ? '1' : '0') . ";TrustServerCertificate=" . ($trustServerCertificate ? '1' : '0');

try {
    // Tente la connexion PDO
    $conn = new PDO($dsn, $DB_USERNAME, $DB_PASSWORD);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Active les exceptions PDO pour les erreurs

    echo "Connexion réussie !\n";

    // Testez une requête simple
    $stmt = $conn->query("SELECT @@VERSION AS SQLServerVersion");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Version de SQL Server : " . $row['SQLServerVersion'] . "\n";

    // Vérifiez si la table 'migrations' existe (comme le ferait artisan migrate)
    $query = "SELECT (case when object_id(N'migrations', 'U') is null then 0 else 1 end) as [exists]";
    $stmt = $conn->query($query);
    $exists = $stmt->fetch(PDO::FETCH_ASSOC)['exists'];
    echo "Table 'migrations' existe : " . ($exists ? 'Oui' : 'Non') . "\n";

    $conn = null; // Ferme la connexion

} catch (PDOException $e) {
    echo "Erreur de connexion PDO :\n";
    echo "Code SQLSTATE : " . $e->getCode() . "\n";
    echo "Message : " . $e->getMessage() . "\n";
    print_r($conn ? $conn->errorInfo() : []); // Affiche plus d'infos si la connexion a été tentée
} catch (Exception $e) {
    echo "Erreur inattendue : " . $e->getMessage() . "\n";
}

?>