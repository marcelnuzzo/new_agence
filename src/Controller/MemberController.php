<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MemberController extends AbstractController
{
    /**
     * @Route("/member_index", name="member_index")
     */
    public function member_index()
    {
        
        $user = $this->getUser();      
        $role = $user->getRoles();
        
        return $this->render('member/member_index.html.twig', [
            'controller_name' => 'MemberController',
            'user' => $user,
            'role' => $role
        ]);
    }
}
