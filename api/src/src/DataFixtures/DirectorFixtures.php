<?php

namespace App\DataFixtures;

use App\Entity\Director;
use App\Util\Constants;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class DirectorFixtures extends Fixture implements DependentFixtureInterface
{
    const CREATED_QUANTITY = 60;

    /** @var Constants */
    private $constants;

    public function __construct(
        Constants $constants
    ) {
        $this->constants = $constants;
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            RegionFixtures::class
        ];
    }

    public function load(ObjectManager $manager)
    {
        $areaDirectors = $assistantDirectors = [];

        // Create some directors
        for ($i = 1; $i <= static::CREATED_QUANTITY; $i++) {
            $region = $this->getReference("Region_" . mt_rand(1, RegionFixtures::CREATED_QUANTITY));
            $user = $this->getReference("User_" . mt_rand(1, UserFixtures::CREATED_QUANTITY));

            $rand = mt_rand(1, 6);
            switch (true) {
                case $rand == 1:
                    $role = $this->constants::ROLE_EXECUTIVE;
                break;
                case $rand > 1 && $rand < 4:
                    $role = $this->constants::ROLE_AREA;
                break;
                case $rand >= 4:
                    $role = $this->constants::ROLE_ASSISTANT;
                break;
            }

            $director = new Director();
            $director->setFixedPercentage(0);
            $director->setFreeAccount(false);
            $director->setGreenLightPercentage(25);
            $director->setAreaPercentage(25);
            $director->setGreyLightPercentage(10);
            $director->setLaunchPercentage(25);
            $director->setPayType(mt_rand(1, 2) == 1 ? $this->constants::PAY_TYPE_ANNUAL : $this->constants::PAY_TYPE_MONTHLY);
            $director->setRedLightPercentage(15);
            $director->setRegion($region);
            $director->setRole($role);
            $director->setUser($user);
            $director->setYellowLightPercentage(20);

            $manager->persist($director);
            $this->addReference("Director_$i", $director);

            if ($role == $this->constants::ROLE_AREA) {
                $areaDirectors[] = $director;
            } elseif ($role == $this->constants::ROLE_ASSISTANT) {
                $assistantDirectors[] = $director;
            }
        }

        static::assignAreaDirectors($areaDirectors, $assistantDirectors);

        $manager->flush();
    }

    private static function assignAreaDirectors(&$areaDirectors, &$assistantDirectors) {
        shuffle($areaDirectors);
        shuffle($assistantDirectors);
        $canContinue = true;
        foreach ($areaDirectors as $supervisor) {
            if (!!count($assistantDirectors)) {
                $director = array_shift($assistantDirectors);
                $director->setSupervisor($supervisor);
            } else {
                $canContinue = false;
                break;
            }
        }
        if ($canContinue) {
            static::assignAreaDirectors($areaDirectors, $assistantDirectors);
        }
    }
}
