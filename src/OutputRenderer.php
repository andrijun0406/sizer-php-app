<?php
namespace App;

class OutputRenderer
{
    public function render(array $results): void
    {
        echo "<h2>Results</h2>";
        echo "<ul>";
        echo "<li>Required pCPUs: " . $results['pCPUCount'] . "</li>";
        echo "<li>Total Compute (GHz): " . $results['totalComputeGHz'] . "</li>";
        echo "<li>Total RAM (GiB): " . $results['totalRAMGiB'] . "</li>";
        echo "<li>Total Storage (GiB): " . $results['totalStorageGiB'] . "</li>";
        echo "</ul>";
        echo '<a href="form.html">New Calculation</a>';
    }

    public function renderError(string $msg): void
    {
        echo "<p style='color:red;'>Error: {$msg}</p>";
        echo '<a href="form.html">Back</a>';
    }
}