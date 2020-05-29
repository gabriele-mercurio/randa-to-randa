<?php

namespace App\DataFixtures;

use App\Entity\Chapter;
use App\Repository\ChapterRepository;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ChapterFixtures extends Fixture implements DependentFixtureInterface
{
    const CREATED_QUANTITY = 100;

    /** @var ChapterRepository */
    private $chapterRepository;

    public function __construct(
        ChapterRepository $chapterRepository
    ) {
        $this->chapterRepository = $chapterRepository;
    }

    public function getDependencies()
    {
        return [
            DirectorFixtures::class,
            RegionFixtures::class
        ];
    }

    public function load(ObjectManager $manager)
    {
        // Create some chapters
        for ($i = 1; $i <= static::CREATED_QUANTITY; $i++) {
            $endDateRange = new DateTime("2020-12-31");
            $startDateRange = new DateTime("2020-01-01");
            $endDateRange = $endDateRange->format("U");
            $startDateRange = $startDateRange->format("U");
            $today = new DateTime();
            $dates = [];

            for ($j = 1; $j <= 8; $j++) {
                $dates[] = new DateTime(date("Y-m-d", mt_rand($startDateRange, $endDateRange)));
            }
            sort($dates);

            $currentState = $this->chapterRepository::CHAPTER_CURRENT_STATE_PROJECT;
            $actualLaunchChatperDate = $actualLaunchCoregroupDate = $actualResumeDate = $closureDate = $prevResumeDate = $suspDate = null;
            $prevLaunchCoregroupDate = array_shift($dates);
            if ($prevLaunchCoregroupDate < $today && $dates[0] < $today) {
                $actualLaunchCoregroupDate = array_shift($dates);
                $currentState = mt_rand(1, 4) == 1 ? $currentState : $this->chapterRepository::CHAPTER_CURRENT_STATE_CORE_GROUP;
            }

            $prevLaunchChapterDate = array_shift($dates);
            if ($prevLaunchChapterDate < $today && $dates[0] < $today) {
                $actualLaunchChatperDate = array_shift($dates);
                $currentState = $currentState == $this->chapterRepository::CHAPTER_CURRENT_STATE_PROJECT ? $currentState : (mt_rand(1, 4) == 1 ? $currentState : $this->chapterRepository::CHAPTER_CURRENT_STATE_CHAPTER);
            }

            if ($currentState == $this->chapterRepository::CHAPTER_CURRENT_STATE_CHAPTER) {
                if ($dates[0] < $today && mt_rand(1, 5) == 1) {
                    $suspDate = array_shift($dates);
                    $prevResumeDate = array_shift($dates);
                    $actualResumeDate = $dates[0] < $today ? array_shift($dates) : null;
                }

                $closureDate = mt_rand(1, 5) == 1 ? array_shift($dates) : null;
            }

            $director = $this->getReference("Director_" . mt_rand(1, DirectorFixtures::CREATED_QUANTITY));
            $region = $this->getReference("Region_" . mt_rand(1, RegionFixtures::CREATED_QUANTITY));

            $chapter = new Chapter();
            $chapter->setActualLaunchChatperDate($actualLaunchChatperDate);
            $chapter->setActualLaunchCoregroupDate($actualLaunchCoregroupDate);
            $chapter->setActualResumeDate($actualResumeDate);
            $chapter->setClosureDate($closureDate);
            $chapter->setCurrentState($currentState);
            $chapter->setDirector($director);
            $chapter->setMembers(mt_rand(100, 300));
            $chapter->setName("Chapter $i");
            $chapter->setPrevLaunchChatperDate($prevLaunchChapterDate);
            $chapter->setPrevLaunchCoregroupDate($prevLaunchCoregroupDate);
            $chapter->setPrevResumeDate($prevResumeDate);
            $chapter->setRegion($region);
            $chapter->setSuspDate($suspDate);

            $manager->persist($chapter);
            $this->addReference("Chapter_$i", $chapter);
        }

        $manager->flush();
    }
}
