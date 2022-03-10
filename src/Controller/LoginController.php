<?php
namespace App\Controller;


use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    /**
     * @Route("/login" , name="login")
     */
    public function login( AuthenticationUtils $authenticationUtils)
    {
        // Connexion
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('/login.html.twig' , [
            'last_Username' => $lastUsername,
            'error' => $error
        ]);

    }
}