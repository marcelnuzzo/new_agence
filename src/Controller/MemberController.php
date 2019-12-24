<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MemberController extends AbstractController
{
    /**
     * @Route("/member_index", name="member_index")
     */
    public function member_index()
    {
        return $this->render('member/member_index.html.twig', [
            'controller_name' => 'MemberController',
        ]);
    }
}
