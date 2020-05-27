<?php

namespace App\Formatter;

use App\Entity\Chapter;

class ChapterFormatter
{
    private const DIRECTOR_BASE_DATA = 1;
    private const DIRECTOR_FULL_DATA = 0;

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
    private function format(Chapter $chapter, $directorDataType): array
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
            'director'        => $directorDataType == self::DIRECTOR_BASE_DATA ? $this->directorFormatter->formatBase($chapter->getDirector()) : $this->directorFormatter->formatFull($chapter->getDirector()),
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
}
