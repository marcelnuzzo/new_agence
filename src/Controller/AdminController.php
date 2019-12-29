<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Service\envoiMail;

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
    public function formUser(EnvoiMail $envoiMail, \Swift_Mailer $mailer,  Request $request, EntityManagerInterface $manager, User $user, UserPasswordEncoderInterface $encoder): response
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
            
            $body="Username : ".$user->getUsername().'</br>'."Email : ".$user->getEmail().'</br>'."Utilisateur modifié";
            $message = $envoiMail->envoi($body);
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
    * @Route("/admin/listeRole", name="admin_listeRole")
    */
    public function listeRole()
    {
        
        $repo = $this->getDoctrine()->getRepository(User::class);
        $users = $repo->findAll();
        $repo = $this->getDoctrine()->getRepository(User::class);
        $user = $repo->findAll();
    
        return $this->render('admin/admin_listeRole.html.twig', [
            'controller_name' => 'adminController',
            'users' => $users,
            'user' => $user
        ]);
    }

    /**
     * @Route("/admin/editRole/{id}", name="admin_editRole")
     */
    public function editRole(\Swift_Mailer $mailer,User $user, Request $request, EntityManagerInterface $manager, envoiMail $envoiMail) 
    {
        $form = $this->createFormBuilder($user)
            ->add('roles', CollectionType::class, [
                'entry_type'   => ChoiceType::class,
                'entry_options'  => [
                    'label' => false,
                    'choices' => [
                        'Admin' => 'ROLE_ADMIN',
                        'Super admin' => 'ROLE_SUPER_ADMIN',
                        'User' => 'ROLE_USER'
                    ],
                ],
            ])
            ->getForm();

            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()) {
                $manager->persist($user);         
                $manager->flush();
                
                $body="Username : ".$user->getUsername().'</br>'."Email : ".$user->getEmail().'</br>'."Vous avez le role de : ".$user->getRoles()[0];          
                $message = $envoiMail->envoi($body);
                $mailer->send($message);
                
                return $this->redirectToRoute('admin_listeRole',['id' => $user->getId()
                ]);
            }
       
        return $this->render('admin/admin_editRole.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
