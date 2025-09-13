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
        $totalRAMGiB     = $ram;
        $totalStorageGiB = $storage;

        return [
            'pCPUCount'       => $pCPUCount,
            'totalComputeGHz' => $totalComputeGHz,
            'totalRAMGiB'     => $totalRAMGiB,
            'totalStorageGiB' => $totalStorageGiB,
        ];
    }
}