<?php

namespace App\Controller;

use App\Entity\User;
//use App\Form\RegistrationFormType;
use App\Security\UserAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin_index", name="admin_index")
     */
    public function admin_index()
    {
        return $this->render('admin/admin_index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/admin_user", name="admin_user")
     */
    public function user()
    {
        $repo = $this->getDoctrine()->getRepository(User::class);
        $users = $repo->findAll();
        
        return $this->render('admin/admin_user.html.twig', [
            'controller_name' => 'AdminController',
            'users'=> $users
        ]);
    }

    /**
     * @Route("/admin/newUser", name="admin_createUser")
     * @Route("/admin/editUser/{id}", name="admin_editUser")
     */
    public function formUser( \Swift_Mailer $mailer,  Request $request, EntityManagerInterface $manager, User $user, UserPasswordEncoderInterface $encoder, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, UserAuthenticator $authenticator): response
    {
        $currentRoute = $request->attributes->get('_route');
        $route = "admin/createUser";
        if($currentRoute == "admin/newUser")
            $route = "admin_createUser";
        else if($currentRoute == "admin/editUser/{id}")
            $route = "admin_editUser";

        if(!$user) {
            $user = new User();
        }
        
        
        $form = $this->createFormBuilder($user)
                     ->add('email')
                     ->add('password', PasswordType::class)
                     ->getForm();
   
            $form->handleRequest($request);
           
        if($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getPassword());

            $user->setPassword($hash);
            
            if(!$user->getId()) {
                $editMode = 0;
                $this->addFlash('success', 'User créé');
            }
            else {
                $editMode = 1;
                $this->addFlash('success', 'User modifié');
            }
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($user);         
            $manager->flush();
            //$this->addFlash('success', 'Bien créé');
            
            $body="Email : ".$user->getEmail();

            $message = (new \Swift_Message('Hello Email'))
                        ->setFrom('nuzzomarcel358@gmail.com')
                        ->setTo('nuzzo.marcel@aliceadsl.fr')
                        ->setBody($body,
                                'text/html'
                            );
            $mailer->send($message);
            $this->addFlash('success', 'Votre compte à bien été enregistré.');

            return $this->redirectToRoute('admin_index');
        }
        
        $html = ".html.twig";
        return $this->render($route.$html, [
            'formUser' => $form->createView(),
            'editMode' => $user->getId() !== null
        ]);
        
    }

    /**
     * @Route("/admin/index_user/{id}/deleteUser", name="admin_deleteUser")
     */
    public function deleteUser($id, EntityManagerInterface $Manager, Request $request)
    {
        $repo = $this->getDoctrine()->getRepository(User::class);
        $user = $repo->find($id);

        $Manager->remove($user);
        $Manager->flush();
        
        return $this->redirectToRoute('admin_user');
       
    }


}
