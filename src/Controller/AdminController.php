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
     * @Route("/admin/editUser/{id}", name="admin_editUser")
     */
    public function formUser( \Swift_Mailer $mailer,  Request $request, EntityManagerInterface $manager, User $user, UserPasswordEncoderInterface $encoder, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, UserAuthenticator $authenticator): response
    {
        
        $form = $this->createFormBuilder($user)
                     ->add('username')
                     ->add('email')
                     ->add('password', PasswordType::class)
                     ->getForm();
   
            $form->handleRequest($request);
           
        if($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getPassword());

            $user->setPassword($hash);
            
            $this->addFlash('success', 'User modifié');
            
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($user);         
            $manager->flush();
            
            $body="Username : ".$user->getUsername().'</br>'."Email : ".$user->getEmail();

            $message = (new \Swift_Message('Agence3'))
                        ->setFrom('nuzzomarcel358@gmail.com')
                        ->setTo('nuzzo.marcel@aliceadsl.fr')
                        ->setBody($body,
                                'text/html'
                            );
            $mailer->send($message);
            $this->addFlash('warning', 'Votre compte à bien été modifié.');

            return $this->redirectToRoute('home');
        }
        
        $html = ".html.twig";
        return $this->render('admin/admin_editUser'.$html, [
            'formUser' => $form->createView(),
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

    /**
    * @Route("/admin/ajouteRole", name="admin_ajouteRole")
    */
    public function ajoutRole(EntityManagerInterface $em)
    {
        
        $repo = $this->getDoctrine()->getRepository(User::class);
        $users = $repo->findAll();
      
    $role = ['ROLE_ADMIN'];
    
    $user = $this->getUser();
    
    if($user->getId() == 1)
        $role = ['ROLE_ADMIN'];
    else
        $role = ['ROLE_USER'];
    
    //dd($user->getId());
    
    $user->setRoles($role);
    
    $em = $this->getDoctrine()->getManager();
    $em->persist($user);
    $em->flush();
    
    return $this->render('admin/admin_ajouteRole.html.twig', [
        'controller_name' => 'adminController',
        'users' => $users
    ]);
    }
}
