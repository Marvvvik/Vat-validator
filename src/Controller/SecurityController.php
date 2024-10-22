<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        return $this->render('security/login.html.twig');
    }

    #[Route('/', name: 'home')]
    public function home(): Response
    {
        return $this->render('vat_validation/index.html.twig');
    }

    #[Route('/api/logout', name: 'api_logout', methods: ['POST'])]
    public function logout(TokenStorageInterface $tokenStorage): void
    {
        $tokenStorage->setToken(null);
    }
}




