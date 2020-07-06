<?php

namespace App\Formatter;

use App\Entity\Randa;
use App\Entity\Chapter;
use App\Repository\RanaRepository;
use App\Repository\RanaLifecycleRepository;

class ChapterFormatter
{
    private const DIRECTOR_BASE_DATA = 1;
    private const DIRECTOR_FULL_DATA = 0;

    /** @var DirectorFormatter */
    protected $directorFormatter;

    /** @var RanaRepository */
    protected $ranaRepository;

    /** @var RanaLifecycleRepository */
    protected $ranaLifecycleRepository;

    /** ChapterFormatter constructor */
    public function __construct(
        DirectorFormatter $directorFormatter,
        RanaRepository $ranaRepository,
        RanaLifecycleRepository $ranaLifecycleRepository
    ) {
        $this->directorFormatter = $directorFormatter;
        $this->ranaRepository = $ranaRepository;
        $this->ranaLifecycleRepository = $ranaLifecycleRepository;
    }

    /**
     * @param Chapter $chapter
     *
     * @return array
     */
    private function format(Chapter $chapter, $directorDataType): array
    {
        $details = [
            'chapterLaunch'   => [
                'actual' => $chapter->getActualLaunchChapterDate() ? $chapter->getActualLaunchChapterDate()->format("Y-m-d") : null,
                'prev'   => $chapter->getPrevLaunchChapterDate() ? $chapter->getPrevLaunchChapterDate()->format("Y-m-d") : null
            ],
            'closureDate'     => $chapter->getClosureDate() ? $chapter->getClosureDate()->format("Y-m-d") : null,
            'coreGroupLaunch' => [
                'actual' => $chapter->getActualLaunchCoregroupDate() ? $chapter->getActualLaunchCoregroupDate()->format("Y-m-d") : null,
                'prev'   => $chapter->getPrevLaunchCoregroupDate() ? $chapter->getPrevLaunchCoregroupDate()->format("Y-m-d") : null
            ],
            'currentState'    => $chapter->getCurrentState(),
            'director'        => $directorDataType == self::DIRECTOR_BASE_DATA ? $this->directorFormatter->formatBase($chapter->getDirector()) : $this->directorFormatter->formatFull($chapter->getDirector()),
            'id'              => $chapter->getId(),
            'members'         => $chapter->getMembers(),
            'name'            => $chapter->getName(),
            'resume'          => [
                'actual' => $chapter->getActualResumeDate() ? $chapter->getActualResumeDate()->format("Y-m-d") : null,
                'prev'   => $chapter->getPrevResumeDate() ? $chapter->getPrevResumeDate()->format("Y-m-d") : null
            ],
            'suspDate'        => $chapter->getSuspDate() ? $chapter->getSuspDate()->format("Y-m-d") : null
        ];

        return $details;
    }

    /**
     * @param Chapter $chapter
     *
     * @return array
     */
    public function formatBase(Chapter $chapter): array
    {
        return $this->format($chapter, self::DIRECTOR_BASE_DATA);
    }

    /**
     * @param Chapter $chapter
     *
     * @return array
     */
    public function formatFull(Chapter $chapter): array
    {
        return $this->format($chapter, self::DIRECTOR_FULL_DATA);
    }

    /**
     * @param Chapter $chapter
     *
     * @return array
     */
    public function formatWithStatus(Chapter $chapter, ?Randa $randa): array
    {
        $c = $this->formatBase($chapter);
        if ($randa) {

            $rana = $this->ranaRepository->findOneBy([
                "chapter" => $chapter,
                "randa" => $randa
            ]);
            if ($rana) {
                $lifecycle = $this->ranaLifecycleRepository->findOneBy([
                    "rana" => $rana,
                    "currentTimeslot" => $randa->getCurrentTimeslot()
                ]);
                $c["state"] = $lifecycle->getCurrentState();
            } else {
                $c["state"] = "TODO";
            }
        } else {
            $c["state"] = "TODO";
        }
        return $c;
    }
}
