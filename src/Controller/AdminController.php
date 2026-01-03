<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminController extends AbstractController
{
   #[Route('/admin', name: 'admin_dashboard')]
#[IsGranted('ROLE_ADMIN')]
public function index(
    CategoryRepository $categoryRepo,
    EventRepository $eventRepo,
    ReservationRepository $reservationRepo
): Response
{
    return $this->render('admin/dashboard.html.twig', [
        'categories' => $categoryRepo->findAll(),
        'events' => $eventRepo->findAll(),
        'reservations' => $reservationRepo->findAll(),
    ]);
}
}