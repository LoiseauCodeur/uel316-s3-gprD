<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DefaultController extends AbstractController
{
    public function index()
    {
        // Vérifie si l'utilisateur est authentifié
        if (!$this->getUser()) {
            // Redirige vers la page de login si l'utilisateur n'est pas authentifié
            return $this->redirectToRoute('app_login'); // Remplacez 'app_login' par le nom de votre route de login
        }

        // Si l'utilisateur est authentifié, affiche la page index
        return $this->render('Default/index.html.twig');
    }
}
