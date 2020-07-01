<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\RegionRepository;
use App\Repository\ChapterRepository;
use App\Repository\DirectorRepository;
use App\Repository\RanaRepository;
use App\Repository\RandaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ImportersController extends AbstractController
{
    /** @var ChapterRepository */
    private $chapterRepository;

    /** @var RegionRepository */
    private $regionRepository;

    /** @var DirectorRepository */
    private $directorRepository;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var UserRepository */
    private $userRepository;

    /** @var RanaRepository */
    private $ranaRepository;

    /** @var RandaRepository */
    private $randaRepository;

    public function __construct(
        ChapterRepository $chapterRepository,
        DirectorRepository $directorRepository,
        EntityManagerInterface $entityManager,
        RegionRepository $regionRepository,
        UserRepository $userRepository,
        RanaRepository $ranaRepository,
        RandaRepository $randaRepository
    ) {
        $this->chapterRepository = $chapterRepository;
        $this->directorRepository = $directorRepository;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->regionRepository = $regionRepository;
        $this->ranaRepository = $ranaRepository;
        $this->randaRepository = $randaRepository;
    }


    /**
     * Importer for chapters from the old DB
     *
     * @Route(path="/chapters/import", name="chapter_importer", methods={"POST"})
     *
     */
    public function importChaptersFromCSV(): Response
    {
        $chapters_names_map = [];

        // mapping nomi capitoli
        // $row = 1;
        // if (($handle = fopen("../resources/name_mapper.csv", "r")) !== FALSE) {
        //     while (($data = fgetcsv($handle, 1000)) !== FALSE) {
        //         if ($data[0] && $data[1]) {
        //             if (!array_key_exists($data[1], $chapters_names_map)) {
        //                 $chapters_names_map[$data[0]] = $data[1];
        //             }
        //         } else {
        //             if (!$data[0]) {
        //                 file_put_contents("chapters_log", "Missing old: " . $data[1] . "\n", FILE_APPEND);
        //             } else {
        //                 file_put_contents("chapters_log", "Missing new: " . $data[0] . "\n", FILE_APPEND);
        //             }
        //         }
        //         $row++;
        //     }
        // }


        // hashed regions
        // $row = 1;
        // $hashed_regions = [];
        // if (($handle = fopen("../resources/regions_mapping.csv", "r")) !== FALSE) {
        //     while (($data = fgetcsv($handle, 1000)) !== FALSE) {
        //         if (!isset($hashed_regions[$data[0]])) {
        //             $hashed_regions[$data[0]] = $data[1];
        //         }
        //     }
        //     fclose($handle);
        // }

        // hashed directors
        // $hashed_directors = [];
        // if (($handle = fopen("../resources/directors_mapping.csv", "r")) !== FALSE) {
        //     while (($data = fgetcsv($handle, 1000)) !== FALSE) {
        //         $hashed_directors[$data[0]] = $data[1];
        //     }
        // }

        // old chapters
        // if (($handle = fopen("../resources/old_chapters.csv", "r")) !== FALSE) {
        //     while (($data = fgetcsv($handle, 1000)) !== FALSE) {
        //         $row++;
        //         if ($row > 2) {
        //             $chapter_name = $data[3];
        //             if (isset($chapters_names_map[$chapter_name])) {
        //                 $chapter = $this->chapterRepository->findOneBy(["name" => $chapters_names_map[$chapter_name]]);
        //                 if (!$chapter) {
        //                     $chapter = new Chapter();
        //                 }
        //                 $chapter->setName($data[3]);
        //                 $chapter->setName($data[3]);
        //                 $chapterState = "CORE GROUP";
        //                 switch ($data[5]) {
        //                     case "CAPITOLO":
        //                         $chapterState = "CHAPTER";
        //                         break;
        //                     case "CHIUSO":
        //                         $chapterState = "CLOSED";
        //                         break;
        //                     case "PROGETTO":
        //                         $chapterState = "PROJECT";
        //                         break;
        //                 }
        //                 $chapter->setCurrentState($chapterState);
        //                 $chapter->setActualLaunchCoreGroupDate(Util::UTCDateTime($data[6]));
        //                 $chapter->setActualLaunchChapterDate(Util::UTCDateTime($data[7]));

        //                 if (isset($hashed_directors[$data[4]])) {
        //                     $directorEntity = $this->directorRepository->find($hashed_directors[$data[4]]);
        //                     $chapter->setDirector($directorEntity);
        //                     $chapter->setMembers($data[10]);

        //                     if (isset($hashed_regions[$data[2]])) {
        //                         $regionEntity = $this->regionRepository->find($hashed_regions[$data[2]]);
        //                         $chapter->setRegion($regionEntity);
        //                     }
        //                     $this->entityManager->persist($chapter);
        //                     $this->entityManager->flush();
        //                 } else {
        //                     file_put_contents("chapter_logs", "DIRECTOR NOT FOUND: " . $data[4] . "\n", FILE_APPEND);
        //                 }
        //             } else {
        //                 file_put_contents("chapter_logs", "CHAPTER NOT FOUND: " . $chapter_name[4] . "\n", FILE_APPEND);

        //             }
        //         }
        //     }
        //     fclose($handle);
        // }



        //set new members and retentions cosumptive
        // $row = 1;
        // $current_year = 2020;
        // $current_timeslot = "T2";
        // if (($handle = fopen("../resources/chapters_members.csv", "r")) !== FALSE) {
        //     while (($data = fgetcsv($handle, 1000)) !== FALSE) {
        //         if ($row > 2) {
        //             $chapter_name = $data[1];

        //             $c = $this->chapterRepository->findOneBy([
        //                 "name" => $chapter_name
        //             ]);
        //             if ($c) {
        //                 $rana = $this->ranaRepository->findOneBy([
        //                     "chapter" => $c
        //                 ]);
        //                 if ($rana) {
        //                     echo $rana->getId() . "\n";
        //                 }
        //                 if (!$rana) {
        //                     $rana = new Rana();
        //                     $region = $this->regionRepository->find($c->getRegion());
        //                     $randa = $this->randaRepository->findOneBy([
        //                         "region" => $region
        //                     ]);
        //                     if (!$randa) {
        //                         $randa = new Randa();
        //                         $randa->setRegion($region);
        //                         $randa->setYear($current_year);
        //                         $randa->setCurrentTimeslot($current_timeslot);
        //                         $this->entityManager->persist($randa);
        //                         $this->entityManager->flush();
        //                     }
        //                     $rana->setRanda($randa);
        //                     $rana->setChapter($c);
        //                     $this->entityManager->persist($rana);
        //                     $this->entityManager->flush();
        //                 }
        //             }
        //             $data = array_slice($data, 3, sizeof($data));
        //             $new_members_1 = ($data[1] ? $data[1] : 0) * 1;
        //             $retentions_1 = ($data[2] ? $data[2] : 0) * 1;
        //             $new_members_2 = ($data[6] ? $data[6] : 0) * 1;
        //             $retentions_2 = ($data[7] ? $data[7] : 0) * 1;
        //             $new_members_3 = ($data[11] ? $data[11] : 0) * 1;
        //             $retentions_3 = ($data[12] ? $data[12] : 0) * 1;
        //             $new_members_4 = ($data[16] ? $data[16] : 0) * 1;
        //             $retentions_4 = ($data[17] ? $data[17] : 0) * 1;
        //             $new_members_5 = ($data[21] ? $data[21] : 0) * 1;
        //             $retentions_5 = ($data[22] ? $data[22] : 0) * 1;
        //             $newMembers = new NewMember();
        //             $retentions = new Retention();
        //             $newMembers->setRana($rana);
        //             $newMembers->setValueType("CONS");
        //             $newMembers->setTimeslot($current_timeslot);
        //             $newMembers->setM1($new_members_1);
        //             $newMembers->setM2($new_members_2);
        //             $newMembers->setM3($new_members_3);
        //             $newMembers->setM4($new_members_4);
        //             $newMembers->setM5($new_members_5);

        //             $retentions->setRana($rana);
        //             $retentions->setValueType("CONS");
        //             $retentions->setTimeslot($current_timeslot);
        //             $retentions->setM1($retentions_1);
        //             $retentions->setM2($retentions_2);
        //             $retentions->setM3($retentions_3);
        //             $retentions->setM4($retentions_4);
        //             $retentions->setM5($retentions_5);

        //             $this->entityManager->persist($newMembers);
        //             $this->entityManager->persist($retentions);
                   
        //         }
        //         $this->entityManager->flush();
        //         $row++;
        //     }
        // }

        return new JsonResponse();
    }
}