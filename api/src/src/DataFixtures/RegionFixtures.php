<?php

namespace App\DataFixtures;

use App\Entity\Region;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RegionFixtures extends Fixture
{
    const CREATED_QUANTITY = 20;

    public function load(ObjectManager $manager)
    {
        // Create some regions
        for ($i = 1; $i <= static::CREATED_QUANTITY; $i++) {
            $region = new Region();
            $region->setName("Regione $i");
            $region->setNotes("Nota $i");

            $manager->persist($region);
            $this->addReference("Region_$i", $region);
        }

        $manager->flush();
    }
}
