<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

$directory = new RecursiveDirectoryIterator(__DIR__ . '/resources/views');
$iterator = new RecursiveIteratorIterator($directory);
$permissionsFound = [];

foreach ($iterator as $info) {
    if ($info->isFile() && $info->getExtension() == 'php') {
        $content = file_get_contents($info->getPathname());
        preg_match_all('/@can\([\'"]([^\'"]+)[\'"]\)/', $content, $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $p) $permissionsFound[] = $p;
        }
        
        preg_match_all('/@canany\(\[([^\]]+)\]\)/', $content, $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $permsList) {
                preg_match_all('/[\'"]([^\'"]+)[\'"]/', $permsList, $subMatches);
                if (!empty($subMatches[1])) {
                    foreach ($subMatches[1] as $p) $permissionsFound[] = $p;
                }
            }
        }
    }
}

$uniquePermissions = array_unique($permissionsFound);
$companyRole = Role::where('name', 'company')->first();

if (!$companyRole) {
    die("Error: Company role not found.\n");
}

app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

foreach ($uniquePermissions as $permName) {
    $permName = trim($permName);
    if (empty($permName)) continue;
    
    echo "Ensuring: $permName\n";
    // Create using the exact case found in Blade
    $p = Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
    // Sync to role using object to avoid case-sensitive lookup issues
    $companyRole->givePermissionTo($p);
}

echo "SUCCESS: Synced " . count($uniquePermissions) . " permissions.\n";
unlink(__FILE__); // Automatically delete itself after running
