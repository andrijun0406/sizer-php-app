<?php
namespace App;

class VMCalculator
{
    public function calculate(array $input): array
    {
        $spanYears = $input['growthYears'];
        $ratio     = $input['pcpuRatio'];
        $speedRef  = $input['speed'];
        $vmCount   = $input['vmCount'];
        $vcpuTot   = $input['vcpuCount'];
        $ramTot    = $input['ram'];
        $storTot   = $input['storage'];

        $growthSpeed = $input['growthSpeed'] / 100;

        // Baseline Year 0 values
        $pCPUCount     = $ratio > 0 ? $vcpuTot / $ratio : 0;
        $baseCompute   = $pCPUCount * $speedRef;

        $results = [];
        for ($year = 0; $year <= $spanYears; $year++) {
            $factorSpeed  = pow(1 + $growthSpeed, $year);

            $yearCompute  = $baseCompute * $factorSpeed;
            $yearVcpu     = $speedRef > 0 ? $yearCompute / $speedRef : 0;
            $yearVcpuTotal = $yearVcpu * $ratio;

            $results[$year] = [
                'pCPUCount'       => $pCPUCount,
                'vcpuTotal'       => $yearVcpuTotal,
                'computeGHz'      => $yearCompute,
                'avgVcpuPerVm'    => $vmCount ? $yearVcpuTotal / $vmCount : 0,
                'avgSpeedPerVm'   => $vmCount ? $yearCompute / $vmCount : 0,
            ];
        }

        return [
            'spanYears' => $spanYears,
            'records'   => $results,
        ];
    }
}