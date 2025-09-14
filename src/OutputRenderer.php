<?php
namespace App;

class OutputRenderer
{
    public function render(array $data): void
    {
        $years = $data['spanYears'];
        $recs  = $data['records'];

        echo '<h2>Projected Sizing Over Time</h2>';
        echo '<table border="1" cellpadding="6" cellspacing="0">';
        echo '<tr>
                <th>Year</th>
                <th>Total vCPUs</th>
                <th>Total Compute (GHz)</th>
                <th>Total RAM (GiB)</th>
                <th>Total Storage (GiB)</th>
                <th>Avg vCPU per VM</th>
                <th>Avg Speed per VM (GHz)</th>
                <th>Avg RAM per VM (GiB)</th>
                <th>Avg Storage per VM (TiB)</th>
            </tr>';

        foreach ($recs as $year => $r) {
            echo '<tr>';
            echo "<td>{$year}</td>";
            echo "<td>" . number_format($r['vcpuTotal'], 1) . "</td>";
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
        echo '<p><small>Growth span: 0 to ' . $years . ' year(s). Year 0 represents current baseline.</small></p>';
        echo '<a href="form.html">New Calculation</a>';
    }

    public function renderError(string $message): void
    {
        echo "<p style='color:red;'>Error: {$message}</p>";
        echo '<a href="form.html">Back</a>';
    }
}