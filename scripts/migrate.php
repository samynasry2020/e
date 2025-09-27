#!/usr/bin/env php
<?php
declare(strict_types=1);

$root = realpath(__DIR__ . '/..');
require $root . '/app/Helpers/autoload.php';
require $root . '/config/config.php';

function read_schema_sql(string $path): string {
  if (!is_file($path)) {
    throw new RuntimeException('Schema file not found: ' . $path);
  }
  $sql = file_get_contents($path);
  if ($sql === false) {
    throw new RuntimeException('Failed to read schema file: ' . $path);
  }
  return $sql;
}

function main(array $argv): int {
  $options = getopt('', ['fresh', 'help']);
  if (isset($options['help'])) {
    echo "Usage: php scripts/migrate.php [--fresh]\n";
    echo "Loads /sql/schema.sql into the configured database. --fresh will drop all known tables first.\n";
    return 0;
  }
  $pdo = pdo();
  if (isset($options['fresh'])) {
    echo "Dropping known tables...\n";
    $tables = [
      'audit_log','invoices','clins','awards','submissions','proposals','bom_items','boms','quotes','rfqs','suppliers',
      'opportunity_changes','documents','files','opportunity_naics','opportunities','contacts','agencies','sessions','users','settings'
    ];
    foreach ($tables as $t) {
      $pdo->exec('DROP TABLE IF EXISTS `' . $t . '`');
    }
  }

  $schemaPath = $GLOBALS['root'] . '/sql/schema.sql';
  $sql = read_schema_sql($schemaPath);
  echo "Applying schema from $schemaPath...\n";
  $pdo->exec($sql);
  echo "Migration complete.\n";
  return 0;
}

exit(main($argv));

