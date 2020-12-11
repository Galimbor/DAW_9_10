<?php

namespace App\Service;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NavbarHelper extends AbstractController
{
    public function retrieveLoggedOutBar()
    {
        $navbar = array();
        $navbar['menu1'] = "Home";
        $navbar['menu1link'] = 'href="' . $this->generateUrl('index'). '"';
        $navbar['menu2'] = "Login";
        $navbar['menu2link'] = 'href="' . $this->generateUrl('app_login'). '"';
        $navbar['menu3'] = "Register";
        $navbar['menu3link'] = 'href="' . $this->generateUrl('app_register'). '"';
        return $navbar;
    }


    public function retrieveLoggedInBar()
    {
        $navbar = array();
        $navbar['menu1'] = "Home";
        $navbar['menu1link'] = 'href="' . $this->generateUrl('index'). '"';
        $navbar['menu2'] = "Post blog";
        $navbar['menu2link'] = 'href="' . $this->generateUrl('post'). '"';
        $navbar['menu3'] = "Logout";
        $navbar['menu3link'] = 'href="' . $this->generateUrl('app_logout'). '"';
        return $navbar;
    }


    public function retrievePostBar()
    {
        $navbar = array();
        $navbar['menu1'] = "Home";
        $navbar['menu1link'] = 'href="' . $this->generateUrl('index'). '"';
        $navbar['menu2'] = "Post blog";
        $navbar['menu2link'] = 'href="' . $this->generateUrl('post'). '"';
        $navbar['menu3'] = "";
        $navbar['menu3link'] = '';
        return $navbar;
    }
}