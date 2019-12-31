<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Search;
use App\Entity\Product;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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

    /**
     * @Route("search", name="search")
     */
    public function search(Request $request)
    {
           
        $repo = $this->getDoctrine()->getRepository(Product::class);
        $products = $repo->findAll();

        $searches = new Search();
        $form = $this->createFormBuilder($searches)
                     ->add('titleProduct',TextType::class, [
                         'label' => 'Titre du produit'
                     ])
                     ->getForm();

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {
                
                $key = $form['titleProduct']->getData();
                $searches = $this->getDoctrine()->getRepository(Product::class)->findOneBySearch($key);  
                
            }
            return $this->render('home/search.html.twig', [
                'controller_name' => 'HomeController',
                'formSearch' => $form->createView(),
                'searches' =>  $searches,
                'products' => $products,
            ]);

    }
}
