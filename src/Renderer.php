<?php
namespace App;

class Renderer
{
    public function render(float $result): void
    {
        echo "<h1>Result: {$result}</h1>";
        echo '<a href="form.html">Try again</a>';
    }

    public function renderError(string $message): void
    {
        echo "<p style='color: red;'>{$message}</p>";
        echo '<a href="form.html">Back</a>';
    }
}