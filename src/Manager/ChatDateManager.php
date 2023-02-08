<?php

namespace App\Manager;

class ChatDateManager
{
    /**
     * @param string|\DateTimeInterface|null $datetime
     * @return string
     * @throws \Exception
     */
    public function format(?string|\DateTimeInterface $datetime): string
    {
        if (null === $datetime) {
            return '-';
        }
        if (!$datetime instanceof \DateTimeInterface) {
            $date = new \DateTime($datetime);
        } else {
            $date = $datetime;
        }
        if (date('Y-m-d') === $date->format('Y-m-d')) {
            return $date->format('H:i');
        } else {
            return $date->format('d.m.Y H:i');
        }
    }
}