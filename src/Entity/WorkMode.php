<?php

namespace App\Entity;

use App\Repository\WorkModeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkModeRepository::class)]
class WorkMode
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10)]
    private ?string $work_mode = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWorkMode(): ?string
    {
        return $this->work_mode;
    }

    public function setWorkMode(string $work_mode): self
    {
        $this->work_mode = $work_mode;

        return $this;
    }
}
