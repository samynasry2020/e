#!/usr/bin/env php
<?php
declare(strict_types=1);

require dirname(__DIR__) . '/config/bootstrap.php';

use App\Core\Database;

$pdo = Database::pdo();

$dir = dirname(__DIR__) . '/database/migrations';
$files = glob($dir . '/*.sql');
sort($files, SORT_NATURAL);

$pdo->exec("CREATE TABLE IF NOT EXISTS migrations (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, version VARCHAR(255) NOT NULL, checksum CHAR(64) NOT NULL, applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, UNIQUE KEY uk_version (version)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

$stmt = $pdo->query('SELECT version, checksum FROM migrations');
$applied = [];
foreach ($stmt->fetchAll() as $row) {
    $applied[$row['version']] = $row['checksum'];
}

$appliedCount = 0;
foreach ($files as $file) {
    $version = basename($file);
    $sql = file_get_contents($file) ?: '';
    $checksum = hash('sha256', $sql);
    if (isset($applied[$version])) {
        if ($applied[$version] !== $checksum) {
            fwrite(STDERR, "Checksum mismatch for $version.\n");
            exit(2);
        }
        continue;
    }
    echo "Applying $version...\n";
    $pdo->beginTransaction();
    try {
        $pdo->exec($sql);
        $ins = $pdo->prepare('INSERT INTO migrations (version, checksum) VALUES (?, ?)');
        $ins->execute([$version, $checksum]);
        $pdo->commit();
        $appliedCount++;
    } catch (Throwable $e) {
        $pdo->rollBack();
        fwrite(STDERR, 'Migration failed: ' . $e->getMessage() . "\n");
        exit(1);
    }
}

echo $appliedCount === 0 ? "No migrations to apply.\n" : ("Applied $appliedCount migration(s).\n");

