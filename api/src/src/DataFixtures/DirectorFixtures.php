<?php

namespace App\DataFixtures;

use App\Entity\Director;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DirectorFixtures extends Fixture
{
    const CREATED_QUANTITY = 50;

    public function load(ObjectManager $manager)
    {
        // Create some directors
        // for ($i = 1; $i <= static::CREATED_QUANTITY; $i++) {
        //     $director = new Director();
        //     $director->setName("Chapter $i");
        //     $director->setCurrentState("PROJECT");

        //     $region = $this->getReference("Region_" . mt_rand(1, RegionFixtures::CREATED_QUANTITY));
        //     $director = $this->getReference("Director_" . mt_rand(1, DirectorFixtures::CREATED_QUANTITY));

        //     $chapter->setDirector($director);
        //     $chapter->setRegion($region);

        //     $manager->persist($chapter);
        //     $this->addReference("Chapter_$i", $chapter);
        // }

        // $manager->flush();
    }
}
