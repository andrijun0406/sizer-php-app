<?php
namespace App;

class VMCalculator
{
    public function calculate(array $input): array
    {
        $vmCount = $input['vmCount'];
        $vcpu    = $input['vcpuCount'];
        $ratio   = $input['pcpuRatio'];
        $speed   = $input['speed'];
        $ram     = $input['ram'];
        $storage = $input['storage'];

        $pCPUCount       = $ratio > 0 ? ceil($vcpu / $ratio) : 0;
        $totalComputeGHz = $pCPUCount * $speed;

        // Averages; avoid division by zero
        $avgVcpuPerVm     = $vmCount > 0 ? $vcpu / $vmCount : 0;
        $avgSpeedPerVm    = $vmCount > 0 ? $totalComputeGHz / $vmCount : 0;
        $avgRamPerVmGiB   = $vmCount > 0 ? $ram / $vmCount : 0;
        $avgStoragePerVmTiB = $vmCount > 0 ? ($storage / $vmCount) / 1024 : 0;

        return [
            'pCPUCount'           => $pCPUCount,
            'totalComputeGHz'     => $totalComputeGHz,
            'totalRAMGiB'         => $ram,
            'totalStorageGiB'     => $storage,
            'avgVcpuPerVm'        => $avgVcpuPerVm,
            'avgSpeedPerVm'       => $avgSpeedPerVm,
            'avgRamPerVmGiB'      => $avgRamPerVmGiB,
            'avgStoragePerVmTiB'  => $avgStoragePerVmTiB,
        ];
    }
}