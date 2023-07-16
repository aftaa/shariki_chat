<?php

namespace App\Service;

use App\Repository\WorkModeRepository;
use DateTime;
use LogicException;

class WorkModeService
{
    private const START_TIME = '09:00';
    private const END_TIME = '21:00';
    private const POSSIBLE_VALUES = [
        'bot',
        'operator',
    ];

    public function __construct(
        private readonly WorkModeRepository $workModeRepository,
        private bool                        $manual,
    )
    {
        $this->manual = !empty($_ENV['APP_MANUAL']);
    }

    /**
     * @return string
     */
    public function get(): string
    {
        if ($this->manual) {
            return $this->workModeRepository->find(1)->getWorkMode();
        } else {
            return $this->autoCalc();
        }
    }

    /**
     * @param string $workMode
     * @return void
     */
    public function set(string $workMode): void
    {
        if (!in_array($workMode, self::POSSIBLE_VALUES)) {
            throw new LogicException('Wrong work mode: ' . $workMode);
        }
        $workModeRow = $this->workModeRepository->findOneBy(['id' => 1]);
        $workModeRow->setWorkMode($workMode);
        $this->workModeRepository->save($workModeRow, true);
    }

    /**
     * @return string
     */
    private function autoCalc(): string
    {
        $time = (new DateTime())->format('H:i');
        if ($time >= self::START_TIME && $time < self::END_TIME) {
            return 'operator';
        } else {
            return 'bot';
        }
    }
}
