<?php

namespace App\Controller;

use App\Service\NavbarHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils, NavbarHelper $navbarHelper): Response
    {
        if($this->getUser())
        {
            $this->addFlash('error', 'You are already signed in.');
            return $this->redirectToRoute('index');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        $navbar = $navbarHelper->retrieveLoggedOutBar();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error, 'navbar' => $navbar,]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {

        //I've got this configured already. Security.yaml is taking care of it.
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
