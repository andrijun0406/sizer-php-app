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
if ($input['inputMode'] === 'measured') {
    // Measured path: only require vmCount and measuredCompute
    if ($input['vmCount'] <= 0 || $input['measuredCompute'] <= 0) {
        $renderer->renderError('VM count and measured compute capacity must be greater than zero.');
        exit;
    }
} else {
    // Estimate path: require vmCount, vcpuCount, pcpuRatio, speedReference
    if ($input['vmCount'] <= 0 || $input['vcpuCount'] <= 0 || $input['pcpuRatio'] <= 0 || $input['speed'] <= 0) {
        $renderer->renderError('VM count, vCPU count, pCPU:vCPU ratio, and speed reference must be greater than zero.');
        exit;
    }
}

$calculator = new VMCalculator();
$results    = $calculator->calculate($input);
$renderer->render($results);