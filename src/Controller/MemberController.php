<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MemberController extends AbstractController
{
    /**
     * @Route("/member_index", name="member_index")
     */
    public function member_index( EntityManagerInterface $manager)
    {
        
        $user = $this->getUser();
        $user->getId();
        if($user->getId() == 1)
            $role = ['ROLE_ADMIN'];
        else
            $role = ['ROLE_USER'];
        $user->setRoles($role);
        $manager->persist($user);         
        $manager->flush();
        
        return $this->render('member/member_index.html.twig', [
            'controller_name' => 'MemberController',
            'user' => $user,
            'role' => $role
        ]);
    }
}
