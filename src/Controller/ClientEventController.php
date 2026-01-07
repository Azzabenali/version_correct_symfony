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
    public function index(Request $request, EventRepository $eventRepo, CategoryRepository $catRepo, LieuRepository $lieuRepo): Response
    {
        $categoryId = $request->query->get('category');
        $lieuId = $request->query->get('lieu');
        $date = $request->query->get('date');
        $prix = $request->query->get('prix');

        $qb = $eventRepo->createQueryBuilder('e')
                        ->leftJoin('e.category', 'c')
                        ->leftJoin('e.lieu', 'l')
                        ->addSelect('c')
                        ->addSelect('l');

        if ($categoryId) {
            $category = $catRepo->find($categoryId);
            if ($category) $qb->andWhere('e.category = :category')->setParameter('category', $category);
        }

        if ($lieuId) {
            $lieu = $lieuRepo->find($lieuId);
            if ($lieu) $qb->andWhere('e.lieu = :lieu')->setParameter('lieu', $lieu);
        }

        if ($date) {
            $dateObj = \DateTime::createFromFormat('Y-m-d', $date);
            if ($dateObj) $qb->andWhere('e.date >= :date')->setParameter('date', $dateObj->setTime(0,0,0));
        }

        if ($prix) {
            $qb->andWhere('e.prix <= :prix')->setParameter('prix', (float)$prix);
        }

        $qb->andWhere('e.date >= :now')->setParameter('now', new \DateTime());

        $events = $qb->getQuery()->getResult();

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
        if (!$event) throw $this->createNotFoundException('Événement introuvable.');

        return $this->render('client/event/show.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/events/{id}/reserve', name: 'client_event_reserve', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function reserve(int $id, Request $request, EventRepository $eventRepository, EntityManagerInterface $em): Response
    {
        $event = $eventRepository->find($id);
        if (!$event) throw $this->createNotFoundException('Événement introuvable.');
        if ($event->getDate() < new \DateTime()) {
            $this->addFlash('danger', 'Impossible de réserver un événement passé.');
            return $this->redirectToRoute('client_event_index');
        }

        $tickets = (int) $request->request->get('tickets', 1);
        if ($tickets < 1 || $tickets > $event->getNombreDePlaces()) {
            $this->addFlash('danger', 'Nombre de tickets invalide.');
            return $this->redirectToRoute('client_event_show', ['id' => $id]);
        }

        $reservation = new Reservation();
        $reservation->setEvent($event)
                    ->setUser($this->getUser())
                    ->setNumbertickets($tickets)
                    ->setTotalprice($tickets * $event->getPrix())
                    ->setResdate(new \DateTime());

        for ($i = 0; $i < $tickets; $i++) {
            $ticket = new Ticket();
            $ticket->setName('Ticket ' . ($i + 1));
            $ticket->setPrice($event->getPrix());
            $ticket->setReservation($reservation);
            $ticket->setEvent($event);
            $reservation->addTicket($ticket);
        }

        $event->setNombreDePlaces($event->getNombreDePlaces() - $tickets);

        $em->persist($reservation);
        $em->persist($event);
        $em->flush();

        $this->addFlash('success', 'Réservation effectuée avec succès !');
        return $this->redirectToRoute('client_event_show', ['id' => $id]);
    }
}
