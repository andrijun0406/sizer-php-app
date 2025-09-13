<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\FormHandler;
use App\Calculator;
use App\Renderer;

$form = new FormHandler();
$input = $form->getInput();

$renderer = new Renderer();

if (
    isset($input['value1'], $input['value2']) &&
    is_numeric($input['value1']) &&
    is_numeric($input['value2'])
) {
    $calculator = new Calculator();
    $result = $calculator->sum((float)$input['value1'], (float)$input['value2']);
    $renderer->render($result);
} else {
    $renderer->renderError('Invalid input; please enter numeric values.');
}