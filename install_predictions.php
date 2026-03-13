#!/usr/bin/env php
<?php

/**
 * Prediction System Installation Script
 * Automates setup of the inventory prediction system
 * Created: February 2, 2026
 */

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║   Inventory Prediction System - Installation & Setup           ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\n";

// Check PHP version
echo "[1/6] Checking PHP version... ";
if (version_compare(PHP_VERSION, '8.0.0', '>=')) {
    echo "✓ OK (PHP " . PHP_VERSION . ")\n";
} else {
    echo "✗ FAILED\n";
    echo "     Required: PHP 8.0+, Found: " . PHP_VERSION . "\n";
    exit(1);
}

// Check for required extensions
echo "[2/6] Checking required PHP extensions... ";
$required_extensions = ['mysqli', 'json'];
$missing = [];
foreach ($required_extensions as $ext) {
    if (!extension_loaded($ext)) {
        $missing[] = $ext;
    }
}

if (empty($missing)) {
    echo "✓ OK\n";
} else {
    echo "✗ FAILED\n";
    echo "     Missing: " . implode(', ', $missing) . "\n";
    exit(1);
}

// Check for Python
echo "[3/6] Checking Python installation... ";
$python_path = shell_exec('which python 2>/dev/null') ?: shell_exec('where python 2>nul');
$python_path = trim($python_path);

if (empty($python_path)) {
    // Try python3
    $python_path = shell_exec('which python3 2>/dev/null') ?: shell_exec('where python3 2>nul');
    $python_path = trim($python_path);
}

if (!empty($python_path)) {
    echo "✓ OK\n";
    echo "     Python: " . $python_path . "\n";
} else {
    echo "⚠ WARNING - Python not found\n";
    echo "     Predictions will not work without Python\n";
    echo "     Install from: https://www.python.org/downloads/\n";
}

// Check directories
echo "[4/6] Creating required directories... ";
$dirs = [
    'app/logs',
    'app/scripts'
];

$all_created = true;
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        if (!mkdir($dir, 0755, true)) {
            echo "✗ FAILED\n";
            echo "     Could not create: $dir\n";
            $all_created = false;
        }
    }
}

if ($all_created) {
    echo "✓ OK\n";
} else {
    exit(1);
}

// Check database
echo "[5/6] Checking database connection... ";
try {
    require 'app/classes/Database.class.php';
    $db = Database::connection();
    
    // Check if prediction tables exist
    $result = $db->query("SHOW TABLES LIKE 'inventory_demand_forecast'");
    
    if ($result->num_rows > 0) {
        echo "✓ OK\n";
        echo "     Prediction tables already exist\n";
    } else {
        echo "⚠ WARNING - Prediction tables not found\n";
        echo "     Please run: mysql -u root -p inventory < database/prediction_schema.sql\n";
    }
} catch (Exception $e) {
    echo "✗ FAILED\n";
    echo "     Error: " . $e->getMessage() . "\n";
    exit(1);
}

// File permissions
echo "[6/6] Checking file permissions... ";
$writable = [
    'app/logs'
];

$all_writable = true;
foreach ($writable as $dir) {
    if (!is_writable($dir)) {
        echo "✗ FAILED\n";
        echo "     Not writable: $dir\n";
        $all_writable = false;
    }
}

if ($all_writable) {
    echo "✓ OK\n";
} else {
    exit(1);
}

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║                   Setup Complete!                              ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\n";

echo "Next Steps:\n";
echo "1. Ensure prediction tables are created:\n";
echo "   mysql -u root -p inventory < database/prediction_schema.sql\n\n";

echo "2. Install Python dependencies (if using predictions):\n";
echo "   pip install pandas numpy scikit-learn scipy\n\n";

echo "3. Test the prediction engine:\n";
echo "   php app/scripts/prediction_scheduler.php daily\n\n";

echo "4. Setup cron jobs (optional):\n";
echo "   Review app/scripts/CRON_SETUP.txt\n\n";

echo "5. Access the prediction dashboard:\n";
echo "   http://localhost/inventory_v2/admin/predictions.php\n\n";

echo "Documentation: PREDICTION_SYSTEM_GUIDE.md\n\n";
