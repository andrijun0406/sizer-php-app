<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
//$path = __DIR__ . '/../src/InputHandler.php';
//echo "Attempting to load: $path";
//var_dump(file_exists($path)); // returns true or false
require_once __DIR__ . '/vendor/autoload.php';
//require_once __DIR__ . '/src/InputHandler.php';
//require_once __DIR__ . '/src/VMCalculator.php';
//require_once __DIR__ . '/src/OutputRenderer.php';


use App\InputHandler;
use App\VMCalculator;
use App\OutputRenderer;

$inputHandler = new InputHandler();
$input        = $inputHandler->getInput();
$renderer     = new OutputRenderer();

// Basic validation
if ($input['vmCount'] <= 0 || $input['vcpuCount'] <= 0 || $input['pcpuRatio'] <= 0) {
    $renderer->renderError('VM count, vCPU count, and pCPU:vCPU ratio must be greater than zero.');
    exit;
}

$calculator = new VMCalculator();
$results    = $calculator->calculate($input);
$renderer->render($results);