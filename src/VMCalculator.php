<?php
namespace App;

class VMCalculator
{
    public function calculate(array $input): array
    {
        $spanYears = max(1, $input['growthYears']);
        $ratio     = $input['pcpuRatio'];
        $speedRef  = $input['speed']; // reference speed per pCPU
        $vmCount   = $input['vmCount'];
        $vcpuTot   = $input['vcpuCount'];
        $ramTot    = $input['ram'];
        $storTot   = $input['storage'];

        $growthSpeed   = $input['growthSpeed'] / 100;
        $growthRam     = $input['growthRam'] / 100;
        $growthStor    = $input['growthStorage'] / 100;

        $pCPUCount     = $ratio > 0 ? ceil($vcpuTot / $ratio) : 0;

        // Pre-calc base values
        $baseComputeGHz = $pCPUCount * $speedRef;
        $baseRAMGiB     = $ramTot;
        $baseStorageGiB = $storTot;

        $results = [];
        for ($year = 0; $year <= $spanYears; $year++) {
            $factorSpeed = pow(1 + $growthSpeed, $year);
            $factorRam   = pow(1 + $growthRam, $year);
            $factorStor  = pow(1 + $growthStor, $year);

            $yearCompute = $baseComputeGHz * $factorSpeed;
            $yearRAM     = $baseRAMGiB * $factorRam;
            $yearStor    = $baseStorageGiB * $factorStor;

            // derive vCPU from compute (divide by reference speed)
            $yearVcpu = $speedRef > 0 ? $yearCompute / $speedRef : 0;

            // averages
            $avgSpeedPerVm    = $vmCount > 0 ? $yearCompute / $vmCount : 0;
            $avgRamPerVmGiB   = $vmCount > 0 ? $yearRAM / $vmCount : 0;
            $avgStorPerVmTiB  = $vmCount > 0 ? ($yearStor / $vmCount) / 1024 : 0;
            $avgVcpuPerVm     = $vmCount > 0 ? $yearVcpu / $vmCount : 0;

            $results[$year] = [
                'computeGHz'     => $yearCompute,
                'ramGiB'         => $yearRAM,
                'storageGiB'     => $yearStor,
                'vcpuTotal'      => $yearVcpu,
                'avgSpeedPerVm'  => $avgSpeedPerVm,
                'avgRamPerVmGiB' => $avgRamPerVmGiB,
                'avgStorPerVmTiB'=> $avgStorPerVmTiB,
                'avgVcpuPerVm'   => $avgVcpuPerVm,
            ];
        }

        return [
            'spanYears' => $spanYears,
            'records'   => $results,
        ];
    }
}