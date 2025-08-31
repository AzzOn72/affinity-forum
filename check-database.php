<?php
/**
 * Comprehensive Database Checker
 * Shows all tables, structure, and data counts
 */

require_once 'config.php';

echo "<!DOCTYPE html>";
echo "<html><head><title>Database Checker</title>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    .table th { background-color: #f2f2f2; }
    .success { color: green; }
    .warning { color: orange; }
    .error { color: red; }
    .info { color: blue; }
    .section { margin: 30px 0; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
    .section h2 { margin-top: 0; color: #333; }
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 20px 0; }
    .stat-card { background: #f8f9fa; padding: 15px; border-radius: 5px; text-align: center; }
    .stat-number { font-size: 24px; font-weight: bold; color: #007bff; }
    .stat-label { color: #666; margin-top: 5px; }
</style>";
echo "</head><body>";

echo "<h1>🔍 Comprehensive Database Checker</h1>";

try {
    $pdo = getDBConnection();
    echo "<p class='success'>✅ Database connected successfully</p>";
    
    // Get all tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<div class='section'>";
    echo "<h2>📊 Database Overview</h2>";
    echo "<p><strong>Total Tables:</strong> " . count($tables) . "</p>";
    echo "<p><strong>Database Name:</strong> " . DB_NAME . "</p>";
    echo "<p><strong>Host:</strong> " . DB_HOST . "</p>";
    echo "</div>";
    
    // Check each table
    $totalRecords = 0;
    $tableDetails = [];
    
    foreach ($tables as $table) {
        echo "<div class='section'>";
        echo "<h2>📋 Table: $table</h2>";
        
        // Get table structure
        $stmt = $pdo->query("DESCRIBE `$table`");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h3>Structure:</h3>";
        echo "<table class='table'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        
        foreach ($columns as $column) {
            echo "<tr>";
            echo "<td>{$column['Field']}</td>";
            echo "<td>{$column['Type']}</td>";
            echo "<td>{$column['Null']}</td>";
            echo "<td>{$column['Key']}</td>";
            echo "<td>{$column['Default']}</td>";
            echo "<td>{$column['Extra']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Get record count
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM `$table`");
            $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            $totalRecords += $count;
            
            echo "<h3>Data:</h3>";
            echo "<p><strong>Total Records:</strong> <span class='stat-number'>$count</span></p>";
            
            // Show sample data (first 5 records)
            if ($count > 0) {
                $stmt = $pdo->query("SELECT * FROM `$table` LIMIT 5");
                $sampleData = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo "<h4>Sample Data (First 5 records):</h4>";
                echo "<table class='table'>";
                
                if (!empty($sampleData)) {
                    // Headers
                    echo "<tr>";
                    foreach (array_keys($sampleData[0]) as $header) {
                        echo "<th>$header</th>";
                    }
                    echo "</tr>";
                    
                    // Data
                    foreach ($sampleData as $row) {
                        echo "<tr>";
                        foreach ($row as $value) {
                            $displayValue = $value;
                            if (is_null($value)) $displayValue = '<em>NULL</em>';
                            elseif (strlen($value) > 50) $displayValue = substr($value, 0, 50) . '...';
                            echo "<td>$displayValue</td>";
                        }
                        echo "</tr>";
                    }
                }
                echo "</table>";
            }
            
            $tableDetails[$table] = $count;
            
        } catch (Exception $e) {
            echo "<p class='error'>❌ Error counting records: " . $e->getMessage() . "</p>";
        }
        
        echo "</div>";
    }
    
    // Summary statistics
    echo "<div class='section'>";
    echo "<h2>📈 Summary Statistics</h2>";
    
    echo "<div class='stats-grid'>";
    echo "<div class='stat-card'>";
    echo "<div class='stat-number'>" . count($tables) . "</div>";
    echo "<div class='stat-label'>Total Tables</div>";
    echo "</div>";
    
    echo "<div class='stat-card'>";
    echo "<div class='stat-number'>$totalRecords</div>";
    echo "<div class='stat-label'>Total Records</div>";
    echo "</div>";
    
    if (isset($tableDetails['users'])) {
        echo "<div class='stat-card'>";
        echo "<div class='stat-number'>{$tableDetails['users']}</div>";
        echo "<div class='stat-label'>Users</div>";
        echo "</div>";
    }
    
    if (isset($tableDetails['threads'])) {
        echo "<div class='stat-card'>";
        echo "<div class='stat-number'>{$tableDetails['threads']}</div>";
        echo "<div class='stat-label'>Threads</div>";
        echo "</div>";
    }
    
    if (isset($tableDetails['posts'])) {
        echo "<div class='stat-card'>";
        echo "<div class='stat-number'>{$tableDetails['posts']}</div>";
        echo "<div class='stat-label'>Posts</div>";
        echo "</div>";
    }
    
    if (isset($tableDetails['categories'])) {
        echo "<div class='stat-card'>";
        echo "<div class='stat-number'>{$tableDetails['categories']}</div>";
        echo "<div class='stat-label'>Categories</div>";
        echo "</div>";
    }
    
    if (isset($tableDetails['subforums'])) {
        echo "<div class='stat-card'>";
        echo "<div class='stat-number'>{$tableDetails['subforums']}</div>";
        echo "<div class='stat-label'>Subforums</div>";
        echo "</div>";
    }
    echo "</div>";
    
    // Check for missing essential tables
    $essentialTables = ['users', 'categories', 'subforums', 'threads', 'posts'];
    $missingTables = array_diff($essentialTables, $tables);
    
    if (!empty($missingTables)) {
        echo "<h3>⚠️ Missing Essential Tables:</h3>";
        echo "<ul>";
        foreach ($missingTables as $table) {
            echo "<li class='warning'>$table</li>";
        }
        echo "</ul>";
        echo "<p><a href='setup-basic-tables.php' class='info'>🔧 Run Setup Script</a></p>";
    } else {
        echo "<h3 class='success'>✅ All essential tables are present!</h3>";
    }
    
    echo "</div>";
    
    // Database health check
    echo "<div class='section'>";
    echo "<h2>🏥 Database Health Check</h2>";
    
    // Check table sizes
    try {
        $stmt = $pdo->query("
            SELECT 
                table_name,
                ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size (MB)',
                table_rows
            FROM information_schema.tables 
            WHERE table_schema = '" . DB_NAME . "'
            ORDER BY (data_length + index_length) DESC
        ");
        $tableSizes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($tableSizes)) {
            echo "<h3>Table Sizes:</h3>";
            echo "<table class='table'>";
            echo "<tr><th>Table</th><th>Size (MB)</th><th>Rows</th></tr>";
            
            foreach ($tableSizes as $table) {
                echo "<tr>";
                echo "<td>{$table['table_name']}</td>";
                echo "<td>{$table['Size (MB)']}</td>";
                echo "<td>{$table['table_rows']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } catch (Exception $e) {
        echo "<p class='warning'>⚠️ Could not get table size information: " . $e->getMessage() . "</p>";
    }
    
    echo "</div>";
    
    echo "<div class='section'>";
    echo "<h2>🎯 Next Steps</h2>";
    echo "<p><a href='index.php'>🏠 Go to Homepage</a></p>";
    echo "<p><a href='setup-basic-tables.php'>🔧 Setup Missing Tables</a></p>";
    echo "<p><a href='setup-sample-data.php'>📝 Add Sample Data</a></p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Database connection failed: " . $e->getMessage() . "</p>";
}

echo "</body></html>";
?>
