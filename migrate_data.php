<?php
// Complete Data Migration Script from SQLite to MySQL

echo "=== Starting Data Migration from SQLite to MySQL ===\n\n";

try {
    // Connect to SQLite
    $sqliteDb = new PDO('sqlite:database/database.sqlite');
    $sqliteDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ Connected to SQLite database\n";

    // Connect to MySQL
    $mysqlDb = new PDO(
        'mysql:host=127.0.0.1;dbname=fillosoft_invoicer;charset=utf8mb4',
        'root',
        ''
    );
    $mysqlDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ Connected to MySQL database\n\n";

    // Define tables to migrate in order (respecting foreign keys)
    $tables = [
        'users',
        'businesses',
        'clients',
        'invoices',
        'invoice_items',
        'cache',
        'cache_locks',
        'sessions',
        'jobs',
        'job_batches',
        'failed_jobs'
    ];

    $totalMigrated = 0;

    foreach ($tables as $table) {
        try {
            echo "Processing table: $table\n";

            // Get all data from SQLite
            $stmt = $sqliteDb->query("SELECT * FROM $table");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($rows) === 0) {
                echo "  → No data to migrate\n\n";
                continue;
            }

            echo "  → Found " . count($rows) . " records\n";

            // Disable foreign key checks temporarily
            $mysqlDb->exec("SET FOREIGN_KEY_CHECKS=0");

            // Insert each row into MySQL
            $migrated = 0;
            foreach ($rows as $row) {
                $columns = array_keys($row);
                $placeholders = array_fill(0, count($columns), '?');

                $sql = sprintf(
                    "INSERT INTO %s (%s) VALUES (%s)",
                    $table,
                    implode(', ', $columns),
                    implode(', ', $placeholders)
                );

                $insertStmt = $mysqlDb->prepare($sql);
                $insertStmt->execute(array_values($row));
                $migrated++;
            }

            // Re-enable foreign key checks
            $mysqlDb->exec("SET FOREIGN_KEY_CHECKS=1");

            echo "  ✓ Migrated $migrated records\n\n";
            $totalMigrated += $migrated;

        } catch (Exception $e) {
            echo "  ⚠ Error with table $table: " . $e->getMessage() . "\n\n";
        }
    }

    echo "\n=== Migration Summary ===\n";
    echo "Total records migrated: $totalMigrated\n\n";

    // Verify migration
    echo "=== Verification ===\n";
    $verifyTables = ['users', 'businesses', 'clients', 'invoices', 'invoice_items'];

    foreach ($verifyTables as $table) {
        try {
            $stmt = $mysqlDb->query("SELECT COUNT(*) as count FROM $table");
            $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            echo "$table: $count records in MySQL\n";
        } catch (Exception $e) {
            echo "$table: Error - " . $e->getMessage() . "\n";
        }
    }

    echo "\n✓ Migration completed successfully!\n";

} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
