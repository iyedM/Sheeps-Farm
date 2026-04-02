<?php

namespace App\Controller;

use App\Service\DashboardService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(DashboardService $dashboardService): Response
    {
        return $this->render('dashboard/index.html.twig', [
            'totalMoutons' => $dashboardService->getTotalMoutons(),
            'vendus' => $dashboardService->getVendus(),
            'nonVendus' => $dashboardService->getNonVendus(),
            'nombreVaccins' => $dashboardService->getNombreVaccins(),
            'valeurCheptel' => $dashboardService->getValeurCheptel(),
            'genreData' => $dashboardService->getByGenre(),
            'raceData' => $dashboardService->getByRace(),
            'origineData' => $dashboardService->getByOrigine(),
            'grangeData' => $dashboardService->getMoutonsParGrangeNonVendus(),
            'evolutionData' => $dashboardService->getEvolutionParMois(),
        ]);
    }
}
