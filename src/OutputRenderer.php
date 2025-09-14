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

        echo "<h2>Average VM “T‑Shirt Size”</h2>";
        echo "<ul>";
        echo "<li>Avg vCPU per VM: " . number_format($results['avgVcpuPerVm'], 2) . "</li>";
        echo "<li>Avg Speed per VM (GHz): " . number_format($results['avgSpeedPerVm'], 2) . "</li>";
        echo "<li>Avg RAM per VM (GiB): " . number_format($results['avgRamPerVmGiB'], 2) . "</li>";
        echo "<li>Avg Storage per VM (TiB): " . number_format($results['avgStoragePerVmTiB'], 2) . "</li>";
        echo "</ul>";

        echo '<a href="form.html">New Calculation</a>';
    }

    public function renderError(string $message): void
    {
        echo "<p style='color:red;'>Error: {$message}</p>";
        echo '<a href="form.html">Back</a>';
    }
}