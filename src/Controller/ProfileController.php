<?php

namespace App\Controller;

use App\Form\ProfileImageType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    #[IsGranted('ROLE_USER')]
    public function index(Request $request): Response
    {
        $user = $this->getUser();
        
        // Rediriger vers la vue appropriée selon le rôle
        if (in_array('ROLE_DEVELOPER', $user->getRoles())) {
            return $this->redirectToRoute('app_developer_profile');
        }
        
        if (in_array('ROLE_COMPANY', $user->getRoles())) {
            return $this->redirectToRoute('app_company_profile');
        }
        
        // Si l'utilisateur n'a pas de rôle spécifique
        return $this->redirectToRoute('app_home');
    }
}
