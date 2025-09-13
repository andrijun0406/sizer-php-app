<?php
namespace App;

class InputHandler
{
    public function getInput(): array
    {
        return [
            'vmCount'   => intval($_POST['vmCount'] ?? 0),
            'vcpuCount' => intval($_POST['vcpuCount'] ?? 0),
            'pcpuRatio' => intval($_POST['pcpuRatio'] ?? 0),
            'speed'     => floatval($_POST['speed'] ?? 0),
            'ram'       => intval($_POST['ram'] ?? 0),
            'storage'   => intval($_POST['storage'] ?? 0),
        ];
    }
}