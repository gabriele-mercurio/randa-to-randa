<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    const CREATED_QUANTITY = 20;

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail("admin@randa2randa.test");
        $user->setFirstName("Admin");
        $user->setIsAdmin(true);
        $user->setLastName("Admin");
        $user->securePassword("admin");
        $manager->persist($user);
        $this->addReference("User_1", $user);

        $user = new User();
        $user->setEmail("mirko.domenici@iocreoweb.site");
        $user->setFirstName("Mirko");
        $user->setIsAdmin(false);
        $user->setLastName("Domenici");
        $user->securePassword("123456");
        $manager->persist($user);
        $this->addReference("User_2", $user);

        $user = new User();
        $user->setEmail("elisa.pucci@iocreoweb.site");
        $user->setFirstName("Elisa");
        $user->setIsAdmin(false);
        $user->setLastName("Pucci");
        $user->securePassword("123456");
        $manager->persist($user);
        $this->addReference("User_3", $user);

        for ($i = 4; $i <= static::CREATED_QUANTITY; $i++) {
            $user = new User();
            $user->setEmail("user$i@randa2randa.test");
            $user->setFirstName("Utente");
            $user->setIsAdmin(false);
            $user->setLastName($i);
            $user->securePassword("123456");
            $manager->persist($user);
            $this->addReference("User_$i", $user);
        }

        $manager->flush();
    }
}
