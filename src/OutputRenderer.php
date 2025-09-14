<?php
namespace App;

class OutputRenderer
{
    public function render(array $r): void
    {
        echo "<h2>Current Requirements</h2><ul>";
        echo "<li>pCPUs: {$r['pCPUCount']}</li>";
        echo "<li>Total Compute (GHz): {$r['totalComputeGHz']}</li>";
        echo "<li>Total RAM (GiB): {$r['totalRAMGiB']}</li>";
        echo "<li>Total Storage (GiB): {$r['totalStorageGiB']}</li>";
        echo "</ul>";

        echo "<h2>Projected Requirements (1 Year Growth)</h2><ul>";
        echo "<li>Total Compute (GHz): " . number_format($r['projectedComputeGHz'], 2) . "</li>";
        echo "<li>Total RAM (GiB): " . number_format($r['projectedRAMGiB'], 2) . "</li>";
        echo "<li>Total Storage (GiB): " . number_format($r['projectedStorageGiB'], 2) . "</li>";
        echo "</ul>";

        echo "<h2>Average VM “T‑Shirt Size”</h2><ul>";
        echo "<li>Avg Speed per VM (GHz): " . number_format($r['avgSpeedPerVm'], 2) . "</li>";
        echo "<li>Avg RAM per VM (GiB): " . number_format($r['avgRamPerVmGiB'], 2) . "</li>";
        echo "<li>Avg Storage per VM (TiB): " . number_format($r['avgStoragePerVmTiB'], 2) . "</li>";
        echo "</ul>";

        echo "<h2>Projected Avg VM Size (1 Year Later)</h2><ul>";
        echo "<li>Avg Speed per VM (GHz): " . number_format($r['avgProjSpeedPerVm'], 2) . "</li>";
        echo "<li>Avg RAM per VM (GiB): " . number_format($r['avgProjRamPerVmGiB'], 2) . "</li>";
        echo "<li>Avg Storage per VM (TiB): " . number_format($r['avgProjStoragePerVmTiB'], 2) . "</li>";
        echo "</ul>";

        echo '<a href="form.html">New Calculation</a>';
    }

    public function renderError(string $message): void
    {
        echo "<p style='color:red;'>Error: {$message}</p>";
        echo '<a href="form.html">Back</a>';
    }
}