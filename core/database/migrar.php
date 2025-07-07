<?php

if(!$fresh) {
	$argv = $_SERVER['argv'];
	$refresh = in_array('--refresh', $argv) || in_array('refresh', $argv);
} else {
	$refresh = true;
}
require_once __DIR__ . '/src/Schema.php';
require_once __DIR__ . '/src/Migration.php';
require_once __DIR__ . '/src/Migrator.php';

try {
	$pdo = new PDO("mysql:host={$db['hostname']};dbname={$db['database']}", $db['username'], $db['password'], [
	   PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
	]);

	Schema::setConnection($pdo);

	$migrator = new Migrator($pdo);
	$migrator->run($refresh);

   $next = true;
} catch (Exception $e) {
	$next = false;
   $message = "❌ Error en la instalación: " . $e->getMessage();
}