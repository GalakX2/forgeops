<?php

namespace App\Controller;

use App\Entity\Incident;
use App\Entity\Journal;
use App\Form\IncidentType;
use App\Form\JournalType;
use App\Repository\IncidentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/incidents')]
class IncidentController extends AbstractController
{
    // 3.2 Liste incidents avec filtres
    #[Route('/', name: 'app_incident_index', methods: ['GET'])]
    public function index(Request $request, IncidentRepository $repo): Response
    {
        $criteria = [];
        if ($s = $request->query->get('status')) $criteria['status'] = $s;
        if ($sev = $request->query->get('severity')) $criteria['severity'] = $sev;

        return $this->render('incident/index.html.twig', [
            'incidents' => $repo->findBy($criteria, ['startedAt' => 'DESC']),
        ]);
    }

    // 3.3 Créer incident
    #[Route('/new', name: 'app_incident_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $incident = new Incident();
        $form = $this->createForm(IncidentType::class, $incident);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($incident);
            $em->flush();
            return $this->redirectToRoute('app_incident_show', ['id' => $incident->getId()]);
        }

        return $this->render('incident/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // 3.4 Détail incident + 3.5 Ajout Journal (sur la même page ou route dédiée)
    #[Route('/{id}', name: 'app_incident_show', methods: ['GET'])]
    public function show(Incident $incident): Response
    {
        // Exigence : trier journaux par occurredAt ASC
        $journals = $incident->getJournals()->toArray();
        usort($journals, fn($a, $b) => $a->getOccurredAt() <=> $b->getOccurredAt());

        return $this->render('incident/show.html.twig', [
            'incident' => $incident,
            'sortedJournals' => $journals,
        ]);
    }

    // Route spécifique pour ajouter un journal (Exigence 3.5)
    #[Route('/{id}/journaux/new', name: 'app_incident_add_journal', methods: ['GET', 'POST'])]
    public function addJournal(Incident $incident, Request $request, EntityManagerInterface $em): Response
    {
        $journal = new Journal();
        $journal->setIncident($incident); // Liaison obligatoire
        
        $form = $this->createForm(JournalType::class, $journal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($journal);
            $em->flush();
            return $this->redirectToRoute('app_incident_show', ['id' => $incident->getId()]);
        }

        return $this->render('incident/add_journal.html.twig', [
            'incident' => $incident,
            'form' => $form->createView(),
        ]);
    }
}