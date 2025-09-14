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

    $growthSpeed   = $input['growthSpeed'] / 100;
    $growthRam     = $input['growthRam'] / 100;
    $growthStorage = $input['growthStorage'] / 100;

    $pCPUCount            = $ratio > 0 ? ceil($vcpu / $ratio) : 0;
    $totalComputeGHz      = $pCPUCount * $speed;
    $totalRAMGiB          = $ram;
    $totalStorageGiB      = $storage;

    // Apply annual growth
    $projectedComputeGHz  = $totalComputeGHz * (1 + $growthSpeed);
    $projectedRAMGiB      = $totalRAMGiB * (1 + $growthRam);
    $projectedStorageGiB  = $totalStorageGiB * (1 + $growthStorage);

    // Averages
    $avgSpeedPerVm        = $vmCount > 0 ? $totalComputeGHz / $vmCount : 0;
    $avgRamPerVmGiB       = $vmCount > 0 ? $ram / $vmCount : 0;
    $avgStoragePerVmTiB   = $vmCount > 0 ? ($storage / $vmCount) / 1024 : 0;

    $avgProjSpeedPerVm    = $vmCount > 0 ? $projectedComputeGHz / $vmCount : 0;
    $avgProjRamPerVmGiB   = $vmCount > 0 ? $projectedRAMGiB / $vmCount : 0;
    $avgProjStoragePerVmTiB = $vmCount > 0 ? ($projectedStorageGiB / $vmCount) / 1024 : 0;

    return [
        'pCPUCount'               => $pCPUCount,
        'totalComputeGHz'         => $totalComputeGHz,
        'totalRAMGiB'             => $totalRAMGiB,
        'totalStorageGiB'         => $totalStorageGiB,
        'projectedComputeGHz'     => $projectedComputeGHz,
        'projectedRAMGiB'          => $projectedRAMGiB,
        'projectedStorageGiB'      => $projectedStorageGiB,
        'avgSpeedPerVm'           => $avgSpeedPerVm,
        'avgRamPerVmGiB'          => $avgRamPerVmGiB,
        'avgStoragePerVmTiB'      => $avgStoragePerVmTiB,
        'avgProjSpeedPerVm'       => $avgProjSpeedPerVm,
        'avgProjRamPerVmGiB'      => $avgProjRamPerVmGiB,
        'avgProjStoragePerVmTiB'  => $avgProjStoragePerVmTiB,
    ];
    }
}