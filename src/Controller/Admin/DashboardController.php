<?php

namespace App\Controller\Admin;

use App\Repository\CategoryRepository;
use App\Repository\EventRepository;
use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted; // ← À ajouter !

class DashboardController extends AbstractController
{
    #[Route('/admin', name: 'admin_dashboard')]
    #[IsGranted('ROLE_ADMIN')]  // ← Ajoute cette ligne ici !
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