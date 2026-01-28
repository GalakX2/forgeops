<?php

namespace App\Tests\Controller;

use App\Entity\Incident;
use App\Entity\Service;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FunctionalTest extends WebTestCase
{
    private $client;
    private $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()
            ->get('doctrine')
            ->getManager();
        
        // Nettoyage préventif des entités avant chaque test
        $this->entityManager->createQuery('DELETE FROM App\Entity\Incident')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\Service')->execute();

        // 1. On crée un Service obligatoire pour les tests
        $service = new Service();
        $service->setName('Service Test Auto');
        $service->setCriticality('high');
        $this->entityManager->persist($service);
        $this->entityManager->flush();
    }

    // Exigence 5.1 : GET / retourne 200 et contient "ForgeOps"
    public function testDashboard(): void
    {
        $crawler = $this->client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        // Vérifie la présence du titre (dans la navbar ou le h1)
        $this->assertAnySelectorTextContains('h1, .navbar-brand', 'ForgeOps');
    }

    // Exigence 5.2 : GET /incidents retourne 200
    public function testIncidentList(): void
    {
        $this->client->request('GET', '/incidents/');
        $this->assertResponseIsSuccessful();
    }

    // Exigence 5.3 : Création incident (POST + Assert DB)
    public function testCreateIncident(): void
    {
        // On récupère le service créé dans le setUp
        $service = $this->entityManager->getRepository(Service::class)->findOneBy(['name' => 'Service Test Auto']);

        $crawler = $this->client->request('GET', '/incidents/new');
        
        // Remplissage du formulaire
        $form = $crawler->selectButton('Enregistrer')->form([
            'incident[title]' => 'Incident Automatisé',
            'incident[status]' => 'open',
            'incident[severity]' => 'sev1',
            'incident[startedAt]' => '2025-01-01 10:00:00',
            'incident[summary]' => 'Test de création',
            // Pour un champ EntityType multiple (checkboxes), on passe l'ID
            'incident[services]' => [$service->getId()],
        ]);

        $this->client->submit($form);
        
        // Assert redirect vers détail
        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
        
        // Assert en DB : on vérifie que l'incident existe
        $incident = $this->entityManager->getRepository(Incident::class)->findOneBy(['title' => 'Incident Automatisé']);
        $this->assertNotNull($incident, 'L\'incident aurait dû être créé en base.');
        $this->assertEquals('open', $incident->getStatus());
        $this->assertCount(1, $incident->getServices(), 'L\'incident doit être lié au service.');
    }

    // Exigence 5.4 : Validation (Resolved sans date)
    public function testCreateIncidentValidationResolved(): void
    {
        $service = $this->entityManager->getRepository(Service::class)->findOneBy(['name' => 'Service Test Auto']);

        $crawler = $this->client->request('GET', '/incidents/new');
        
        // On tente de créer un incident résolu SANS date de résolution
        $form = $crawler->selectButton('Enregistrer')->form([
            'incident[title]' => 'Incident Invalide',
            'incident[status]' => 'resolved', 
            'incident[severity]' => 'sev2',
            'incident[startedAt]' => '2025-01-01 10:00:00',
            'incident[resolvedAt]' => '', 
            'incident[summary]' => 'Test validation',
            'incident[services]' => [$service->getId()],
        ]);

        $this->client->submit($form);

        // 1. On vérifie que la page se recharge (pas de redirection = échec du form)
        $this->assertResponseIsSuccessful(); 

        // 2. On vérifie que le message d'erreur est bien visible sur la page !
        $this->assertSelectorTextContains('html', 'La date de résolution est obligatoire');
    }
}