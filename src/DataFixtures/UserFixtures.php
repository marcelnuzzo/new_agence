<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
 
class UserFixtures extends Fixture
{

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load( ObjectManager $manager)
    {
        
        $user = new User();

        $role = ['ROLE_SUPER_ADMIN'];

        $user->setEmail('nuzzo.marcel@aliceadsl.fr')
             ->setPassword($this->encoder->encodePassword(
                    $user,
                    '1234'
                ))
             ->setUsername('marcel')
             ->setRoles($role);

             $manager->persist($user);

        $manager->flush();
    }
}
