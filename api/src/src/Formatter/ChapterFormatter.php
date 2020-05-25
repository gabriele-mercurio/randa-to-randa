<?php

namespace App\Formatter;

use App\Entity\Chapter;

class ChapterFormatter
{
    public const DIRECTOR_BASE_DATA = true;
    public const DIRECTOR_FULL_DATA = false;

    /** @var DirectorFormatter */
    protected $directorFormatter;

    /** ChapterFormatter constructor */
    public function __construct(
        DirectorFormatter $directorFormatter
    ) {
        $this->directorFormatter = $directorFormatter;
    }

    /**
     * @param Chapter $chapter
     *
     * @return array
     */
    private function format(Chapter $chapter): array
    {
        $details = [
            'chapterLaunch'   => [
                'actual' => $chapter->getActualLaunchChatperDate()->format("Y-m-d"),
                'prev'   => $chapter->getPrevLaunchChatperDate()->format("Y-m-d")
            ],
            'closureDate'     => $chapter->getClosureDate()->format("Y-m-d"),
            'coreGroupLaunch' => [
                'actual' => $chapter->getActualLaunchCoregroupDate()->format("Y-m-d"),
                'prev'   => $chapter->getPrevLaunchCoregroupDate()->format("Y-m-d")
            ],
            'currentState'    => $chapter->getCurrentState(),
            'id'              => $chapter->getId(),
            'members'         => $chapter->getMembers(),
            'name'            => $chapter->getName(),
            'suspDate'        => $chapter->getSuspDate()->format("Y-m-d")
        ];

        return $details;
    }

    /**
     * @param Chapter $chapter
     *
     * @return array
     */
    public function formatFull(Chapter $chapter, $directorBaseData = self::DIRECTOR_BASE_DATA): array
    {
        $details = array_merge($this->format($chapter), [
            'director' => $directorBaseData ? $this->directorFormatter->formatBase($chapter->getDirector()) : $this->directorFormatter->formatFull($chapter->getDirector())
        ]);

        return $details;
    }
}
