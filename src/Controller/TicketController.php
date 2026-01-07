<?php

namespace App\Controller;
use App\Form\TicketType; 
use App\Entity\Ticket;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/ticket')]
#[IsGranted('ROLE_ADMIN')]
final class TicketController extends AbstractController
{
    // Liste uniquement les tickets déjà réservés
   #[Route(name: 'app_ticket_index', methods: ['GET'])]
public function index(TicketRepository $ticketRepository): Response
{
    $tickets = $ticketRepository->createQueryBuilder('t')
    ->leftJoin('t.reservation', 'r')
    ->leftJoin('r.user', 'u')
    ->leftJoin('t.event', 'e')
    ->leftJoin('e.reservations', 'res')   // <-- join des reservations
    ->addSelect('r, u, e, res')           // <-- fetch pour hydrater
    ->getQuery()
    ->getResult();


    return $this->render('ticket/index.html.twig', [
        'tickets' => $tickets,
    ]);
}

    // Visualiser un ticket
    #[Route('/{id}', name: 'app_ticket_show', methods: ['GET'])]
    public function show(Ticket $ticket): Response
    {
        return $this->render('ticket/show.html.twig', [
            'ticket' => $ticket,
        ]);
    }

    // Supprimer un ticket
    #[Route('/{id}/delete', name: 'app_ticket_delete', methods: ['POST'])]
    public function delete(Request $request, Ticket $ticket, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ticket->getId(), $request->request->get('_token'))) {
            $entityManager->remove($ticket);
            $entityManager->flush();
            $this->addFlash('success', 'Ticket supprimé avec succès.');
        }

        return $this->redirectToRoute('app_ticket_index');
    }
#[Route('/{id}/edit', name: 'app_ticket_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, Ticket $ticket, EntityManagerInterface $entityManager): Response
{
    $form = $this->createForm(TicketType::class, $ticket);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();
        $this->addFlash('success', 'Ticket mis à jour avec succès.');
        return $this->redirectToRoute('app_ticket_index');
    }

    return $this->render('ticket/edit.html.twig', [
        'ticket' => $ticket,
        'form' => $form->createView(),
    ]);
}


}
