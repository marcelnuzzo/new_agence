<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     * @Route("/", name="home")
     */
    public function index()
    {
        $repo = $this->getDoctrine()->getRepository(User::class);
        $users = $repo->findAll();
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'users' => $users
        ]);
    }

}
