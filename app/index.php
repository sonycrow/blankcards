<?php
// Use
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

// Init
set_time_limit(0);
ini_set('error_reporting', E_ALL & ~E_NOTICE);
ini_set('memory_limit', '1024M');
define('ROOT', dirname(__DIR__));



// Require
require_once(ROOT . '/vendor/autoload.php');

// Obtiene la sección
try {
    $sectionName = ucfirst(trim($_REQUEST['section'] ?? ''));
    $sectionName = empty($sectionName) ? 'Index' : $sectionName;
    $section = new $sectionName($sectionName);
}
catch (Exception $ex) {
    die("Section error: " . $ex->getMessage());
}

// Loader y Twig
$options = array();
$loader  = new FilesystemLoader(ROOT . '/src/html');
$twig    = new Environment($loader, $options);

// Render de la página
try {
    echo $twig->render(strtolower($sectionName) . '.html.twig', $section->getRenderVars());
}
catch (Exception $ex) {
    die("Render error: " . $ex->getMessage());
}