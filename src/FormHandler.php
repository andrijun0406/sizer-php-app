<?php
namespace App;

class FormHandler
{
    public function getInput(): array
    {
        return [
            'value1' => $_POST['value1'] ?? null,
            'value2' => $_POST['value2'] ?? null,
        ];
    }
}