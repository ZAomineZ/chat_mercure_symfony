<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

trait Timestamp
{
    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    /**
     * @return mixed
     */
    public function getCreatedAt(): mixed
    {
        return $this->createdAt;
    }

    #[ORM\PrePersist()]
    public function prePersist()
    {
        $this->createdAt = new DateTime();
    }
}
