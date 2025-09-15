<?php
namespace App;

class OutputRenderer
{
    public function render(array $data): void
    {
        $spanYears = $data['spanYears'];
        $records   = $data['records'];
        
        if ($input['inputMode'] === 'measured') {
            echo '<p><em>Using measured Compute Capacity (' . number_format($input['measuredCompute'], 2) . ' GHz) as baseline.</em></p>';
        } else {
            echo '<p><em>Estimating Compute Capacity from vCPU/pCPU ratio and speed reference.</em></p>';
        }
        echo '<h2>Projected Sizing Over ' . $spanYears . ' Year(s)</h2>';
        echo '<table border="1" cellpadding="6" cellspacing="0">';
        echo '<tr>
                <th>Year</th>
                <th>pCPU Count</th>
                <th>Total vCPUs</th>
                <th>Total Compute (GHz)</th>
                <th>Total RAM (GiB)</th>
                <th>Total Storage (GiB)</th>
                <th>Avg vCPU/VM</th>
                <th>Avg Speed/VM (GHz)</th>
                <th>Avg RAM/VM (GiB)</th>
                <th>Avg Storage/VM (TiB)</th>
            </tr>';

        foreach ($records as $year => $r) {
            echo '<tr>';
            echo "<td>{$year}</td>";
            echo "<td>" . number_format($r['pCPUCount'], 0) . "</td>";
            echo "<td>" . number_format($r['vcpuTotal'], 2) . "</td>";
            echo "<td>" . number_format($r['computeGHz'], 2) . "</td>";
            echo "<td>" . number_format($r['ramGiB'], 2) . "</td>";
            echo "<td>" . number_format($r['storageGiB'], 2) . "</td>";
            echo "<td>" . number_format($r['avgVcpuPerVm'], 2) . "</td>";
            echo "<td>" . number_format($r['avgSpeedPerVm'], 2) . "</td>";
            echo "<td>" . number_format($r['avgRamPerVmGiB'], 2) . "</td>";
            echo "<td>" . number_format($r['avgStorPerVmTiB'], 2) . "</td>";
            echo '</tr>';
        }

        echo '</table>';
        echo '<p><small>Year 0 = baseline. Growth applied to compute speed only.</small></p>';
        echo '<a href="form.html">New Calculation</a>';
    }

    public function renderError(string $message): void
    {
        echo "<p style='color:red;'>Error: {$message}</p>";
        echo '<a href="form.html">Back</a>';
    }
}