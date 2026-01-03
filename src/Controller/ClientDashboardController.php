<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class ClientDashboardController extends AbstractController
{
    #[Route('/client/dashboard', name: 'app_client_dashboard')]
    public function index(): Response
    {
        return $this->render('client_dashboard/index.html.twig');
    }
}
