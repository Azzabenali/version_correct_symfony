<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted; // ← Ajouté !
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[Route('/event')]
#[IsGranted('ROLE_ADMIN')] 
#[IsGranted('ROLE_USER')]// ← Protection globale du contrôleur
final class EventController extends AbstractController
{
    #[Route(name: 'app_event_index', methods: ['GET'])]
    public function index(EventRepository $eventRepository): Response
    {
        return $this->render('event/index.html.twig', [
            'events' => $eventRepository->findAll(),
        ]);
    }

   #[Route('/new', name: 'app_event_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $event = new Event();
    $form = $this->createForm(EventType::class, $event);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        /** @var UploadedFile $imageFile */
        $imageFile = $form->get('image')->getData();

        if ($imageFile) {
            $newFilename = uniqid().'.'.$imageFile->guessExtension();

            $imageFile->move(
                $this->getParameter('kernel.project_dir').'/public/uploads/events',
                $newFilename
            );

            $event->setImage($newFilename);
        }

        $entityManager->persist($event);
        $entityManager->flush();

        return $this->redirectToRoute('app_event_index');
    }

    return $this->render('event/new.html.twig', [
        'event' => $event,
        'form' => $form,
    ]);
}


    #[Route('/{id}', name: 'app_event_show', methods: ['GET'])]
    public function show(Event $event): Response
    {
        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_event_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, Event $event, EntityManagerInterface $entityManager): Response
{
    $oldImage = $event->getImage();

    $form = $this->createForm(EventType::class, $event);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        /** @var UploadedFile $imageFile */
        $imageFile = $form->get('image')->getData();

        if ($imageFile) {
            $newFilename = uniqid().'.'.$imageFile->guessExtension();

            $imageFile->move(
                $this->getParameter('kernel.project_dir').'/public/uploads/events',
                $newFilename
            );

            $event->setImage($newFilename);
        } else {
            $event->setImage($oldImage);
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_event_index');
    }

    return $this->render('event/edit.html.twig', [
        'event' => $event,
        'form' => $form,
    ]);
}

    #[Route('/{id}', name: 'app_event_delete', methods: ['POST'])]
    public function delete(Event $event, EntityManagerInterface $em, Request $request): Response
{
    // Vérification CSRF
    if (!$this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
        throw $this->createAccessDeniedException('Token CSRF invalide.');
    }

    // Suppression de l'événement
    $em->remove($event);
    $em->flush();

    $this->addFlash('success', 'Événement supprimé avec succès.');

    return $this->redirectToRoute('app_event_index'); // ou la page où tu affiches tes events
}}