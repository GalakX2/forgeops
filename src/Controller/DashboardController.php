<?php

namespace App\Controller;

use App\Repository\IncidentRepository;
use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(IncidentRepository $incidentRepo, ServiceRepository $serviceRepo): Response
    {
        return $this->render('dashboard/index.html.twig', [
            'nbOpen' => $incidentRepo->count(['status' => 'open']),
            'nbSev1' => $incidentRepo->count(['severity' => 'sev1']),
            'nbServices' => $serviceRepo->count([]),
            // Les 10 derniers incidents
            'recents' => $incidentRepo->findBy([], ['startedAt' => 'DESC'], 10),
        ]);
    }
}