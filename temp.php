<?php

require __DIR__.'/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Application;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$app = new Application(__DIR__);

$id = 6;

try {
    DB::statement("UPDATE account_requests SET status = 'approved' WHERE id = ?", [$id]);
    echo "Account request with ID $id updated to 'approved'";
} catch (\Exception $e) {
    echo "Error updating account request: " . $e->getMessage();
}
?>
