<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * 
     * @Route("/", name="index")
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

     /**
     * @Route("/home", name="home")
     *
     */
    public function home()
    {
        $repo = $this->getDoctrine()->getRepository(User::class);
        $users = $repo->findAll();
        return $this->render('home/home.html.twig', [
            'controller_name' => 'HomeController',
            'users' => $users
        ]);
    }

}
