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

        $growthSpeed  = $input['growthSpeed'] / 100;
        $growthRam    = $input['growthRam'] / 100;
        $growthStor   = $input['growthStorage'] / 100;

        $pCPUCount    = $ratio > 0 ? $vcpuTot / $ratio : 0;
        $baseCompute  = $pCPUCount * $speedRef;
        $baseRAM      = $ramTot;
        $baseStor     = $storTot;

        $records = [];
        for ($year = 0; $year <= $spanYears; $year++) {
            $fs = pow(1 + $growthSpeed, $year);
            $fr = pow(1 + $growthRam, $year);
            $ft = pow(1 + $growthStor, $year);

            $yearCompute = $baseCompute * $fs;
            $yearRAM     = $baseRAM * $fr;
            $yearStor    = $baseStor * $ft;
            $yearVcpu    = $speedRef > 0 ? $yearCompute / $speedRef : 0;
            $yearVcpuTot = $yearVcpu * $ratio;

            $records[$year] = [
                'pCPUCount'       => $pCPUCount,
                'vcpuTotal'       => $yearVcpuTot,
                'computeGHz'      => $yearCompute,
                'ramGiB'          => $yearRAM,
                'storageGiB'      => $yearStor,
                'avgVcpuPerVm'    => $vmCount ? $yearVcpuTot / $vmCount : 0,
                'avgSpeedPerVm'   => $vmCount ? $yearCompute / $vmCount : 0,
                'avgRamPerVmGiB'  => $vmCount ? $yearRAM / $vmCount : 0,
                'avgStorPerVmTiB' => $vmCount ? ($yearStor / $vmCount) / 1024 : 0,
            ];
        }

        return [
            'spanYears' => $spanYears,
            'records'   => $records,
        ];
    }
}