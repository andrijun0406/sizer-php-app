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

        $growthSpeed   = $input['growthSpeed'] / 100;
        $growthRam     = $input['growthRam'] / 100;
        $growthStor    = $input['growthStorage'] / 100;

        $pCPUCount = $ratio > 0 ? ceil($vcpuTot / $ratio) : 0;
        $baseCompute = $pCPUCount * $speedRef;

        $records = [];
        for ($year = 0; $year <= $spanYears; $year++) {
            $factorSpeed = pow(1 + $growthSpeed, $year);
            $factorRam   = pow(1 + $growthRam, $year);
            $factorStor  = pow(1 + $growthStor, $year);

            $yearCompute   = $baseCompute * $factorSpeed;
            $yearRAM       = $ramTot * $factorRam;
            $yearStor      = $storTot * $factorStor;
            $yearVcpuTotal = $speedRef > 0 ? $yearCompute / $speedRef : 0;

            $records[$year] = [
                'pCPUCount'       => $pCPUCount,
                'vcpuTotal'       => $yearVcpuTotal,
                'computeGHz'      => $yearCompute,
                'ramGiB'          => $yearRAM,
                'storageGiB'      => $yearStor,
                'avgVcpuPerVm'    => $vmCount ? $yearVcpuTotal / $vmCount : 0,
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