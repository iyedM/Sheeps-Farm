<?php

namespace App\Service;

use App\Repository\MoutonRepository;
use App\Repository\VaccinRepository;

class DashboardService
{
    public function __construct(
        private readonly MoutonRepository $moutonRepository,
        private readonly VaccinRepository $vaccinRepository,
    ) {
    }

    public function getTotalMoutons(): int
    {
        return $this->moutonRepository->count([]);
    }

    public function getVendus(): int
    {
        return $this->moutonRepository->countVendus();
    }

    public function getNonVendus(): int
    {
        return $this->moutonRepository->countNonVendus();
    }

    public function getByGenre(): array
    {
        return $this->moutonRepository->getByFieldCounts('genre');
    }

    public function getByRace(): array
    {
        return $this->moutonRepository->getByFieldCounts('race');
    }

    public function getByOrigine(): array
    {
        return $this->moutonRepository->getByFieldCounts('origine');
    }

    public function getMoutonsParGrangeNonVendus(): array
    {
        return $this->moutonRepository->getNonVendusParGrange();
    }

    public function getEvolutionParMois(): array
    {
        return $this->moutonRepository->getEvolutionParMois();
    }

    public function getNombreVaccins(): int
    {
        return $this->vaccinRepository->countAll();
    }

    public function getValeurCheptel(): float
    {
        return $this->moutonRepository->sumValeurCheptel();
    }
}
