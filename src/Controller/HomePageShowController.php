<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomePageShowController extends AbstractController
{
    /**
     * @Route("/homepage", name="home_page_show")
     */
    public function index()
    {
        return $this->render('home_page_show/index.html.twig', [
            'controller_name' => 'HomePageShowController',
        ]);
    }

//    public function list(): Response
//    {
//
//    }
}
