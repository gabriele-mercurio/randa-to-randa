<?php

namespace App\Formatter;

use App\Entity\Randa;
use App\Repository\NewMemberRepository;
use App\Repository\RanaLifecycleRepository;
use App\Repository\RetentionRepository;
use Doctrine\Common\Util\Debug;

class RandaFormatter
{
    private const REGION_BASE_DATA = 1;
    private const REGION_FULL_DATA = 0;

    /** @var NewMemberRepository */
    private $newMemberRepository;

    /** @var RanaLifecycleRepository */
    private $ranaLifecycleRepository;

    /** @var RetentionRepository */
    private $retentionRepository;

    /** @var RegionFormatter */
    private $regionFormatter;

    /** RandaFormatter constructor */
    public function __construct(
        NewMemberRepository $newMemberRepository,
        RanaLifecycleRepository $ranaLifecycleRepository,
        RetentionRepository $retentionRepository,
        RegionFormatter $regionFormatter
    ) {
        $this->newMemberRepository = $newMemberRepository;
        $this->ranaLifecycleRepository = $ranaLifecycleRepository;
        $this->retentionRepository = $retentionRepository;
        $this->regionFormatter = $regionFormatter;
    }

    /**
     * @param Randa $randa
     *
     * @return array
     */
    private function format(Randa $randa, $regionDataType): array
    {
        $details = [
            'currentTimeslot' => $randa->getCurrentTimeslot(),
            'id'              => $randa->getId(),
            'region'          => $regionDataType == self::REGION_BASE_DATA ? $this->regionFormatter->formatBase($randa->getRegion()) : $this->regionFormatter->formatFull($randa->getRegion()),
            'year'            => $randa->getYear()
        ];

        return $details;
    }

    /**
     * @param Randa $randa
     *
     * @return array
     */
    public function formatBase(Randa $randa): array
    {
        return $this->format($randa, self::REGION_BASE_DATA);
    }

    /**
     * @param Randa $randa
     *
     * @return array
     */
    public function formatData(Randa $randa): array
    {
        $df = new DirectorFormatter();
        $cf = new ChapterFormatter($df);
        $ranaFormatter = new RanaFormatter($cf, $this->newMemberRepository, $this->ranaLifecycleRepository, $this, $this->retentionRepository);

        return array_merge($this->format($randa, self::REGION_BASE_DATA), [
            'ranas' => array_map(function ($rana) use ($ranaFormatter) {
                return $ranaFormatter->formatCustomData($rana);
            }, $randa->filteredRanas->toArray())
        ]);
    }

    /**
     * @param Randa $randa
     *
     * @return array
     */
    public function formatFull(Randa $randa): array
    {
        return $this->format($randa, self::REGION_FULL_DATA);
    }
}
