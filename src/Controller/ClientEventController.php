<?php

namespace App\Controller;
use App\Entity\Ticket;
use App\Entity\Reservation;
use App\Repository\EventRepository;
use App\Repository\CategoryRepository;
use App\Repository\LieuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ClientEventController extends AbstractController
{
    #[Route('/events', name: 'client_event_index')]
    public function index(
        EventRepository $eventRepo,
        CategoryRepository $catRepo,
        LieuRepository $lieuRepo
    ): Response
    {
        $events = $eventRepo->findAll();
        $categories = $catRepo->findAll();
        $lieux = $lieuRepo->findAll();

        return $this->render('client/event/index.html.twig', [
            'events' => $events,
            'categories' => $categories,
            'lieux' => $lieux,
        ]);
    }

    #[Route('/events/{id}', name: 'client_event_show')]
    public function show(int $id, EventRepository $eventRepository): Response
    {
        $event = $eventRepository->find($id);

        if (!$event) {
            throw $this->createNotFoundException('Événement introuvable');
        }

        return $this->render('client/event/show.html.twig', [
            'event' => $event,
        ]);
    }

  #[Route('/events/{id}/reserve', name: 'client_event_reserve', methods: ['POST'])]
#[IsGranted('ROLE_USER')]
public function reserve(
    int $id,
    Request $request,
    EventRepository $eventRepository,
    EntityManagerInterface $em
): Response
{
    $event = $eventRepository->find($id);
    if (!$event) {
        throw $this->createNotFoundException('Événement introuvable');
    }

    $tickets = (int) $request->request->get('tickets', 1);

    if ($tickets < 1 || $tickets > $event->getNombreDePlaces()) {
        $this->addFlash('danger', 'Nombre de tickets invalide.');
        return $this->redirectToRoute('client_event_show', ['id' => $id]);
    }

    // Crée la réservation
    $reservation = new Reservation();
    $reservation->setEvent($event)
                ->setUser($this->getUser())
                ->setNumbertickets($tickets)
                ->setTotalprice($tickets * $event->getPrix())
                ->setResdate(new \DateTime());

    // Crée les tickets et les lie à la réservation
    for ($i = 0; $i < $tickets; $i++) {
        $ticket = new Ticket();
        $ticket->setName('Ticket ' . ($i + 1));
        $ticket->setPrice($event->getPrix());
        $ticket->setReservation($reservation); // 🔑 IMPORTANT
        $ticket->setEvent($event);

        $reservation->addTicket($ticket); // ajoute à la collection
    }

    // Décrémente le nombre de places
    $event->setNombreDePlaces($event->getNombreDePlaces() - $tickets);

    // Persist et flush
    $em->persist($reservation);
    $em->persist($event);
    $em->flush();

    $this->addFlash('success', 'Réservation effectuée avec succès !');

    return $this->redirectToRoute('client_event_show', ['id' => $id]);
}
}