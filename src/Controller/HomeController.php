<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        // Si l'utilisateur est connecté, le rediriger vers sa page d'accueil
        if ($this->getUser()) {
            if (in_array('ROLE_DEVELOPER', $this->getUser()->getRoles())) {
                return $this->redirectToRoute('app_developer_dashboard');
            } elseif (in_array('ROLE_COMPANY', $this->getUser()->getRoles())) {
                return $this->redirectToRoute('app_company_dashboard');
            }
        }

        // Sinon afficher la page d'accueil
        return $this->render('home/index.html.twig');
    }
}
