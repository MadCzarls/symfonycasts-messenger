<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: 'App\Repository\ImagePostRepository')]
class ImagePost
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups('image:output')]
    private ?int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $filename;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups('image:output')]
    private ?string $originalFilename;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Groups('image:output')]
    private $ponkaAddedAt;

    #[ORM\Column(type: 'datetime')]
    #[Groups('image:output')]
    private $createdAt;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();

    }//end __construct()


    public function getId(): ?int
    {
        return $this->id;

    }//end getId()


    public function getFilename(): ?string
    {
        return $this->filename;

    }//end getFilename()


    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;

    }//end setFilename()


    public function getOriginalFilename(): ?string
    {
        return $this->originalFilename;

    }//end getOriginalFilename()


    public function setOriginalFilename(string $originalFilename): self
    {
        $this->originalFilename = $originalFilename;

        return $this;

    }//end setOriginalFilename()


    public function getPonkaAddedAt(): ?DateTimeInterface
    {
        return $this->ponkaAddedAt;

    }//end getPonkaAddedAt()


    public function markAsPonkaAdded(): self
    {
        $this->ponkaAddedAt = new DateTimeImmutable();

        return $this;

    }//end markAsPonkaAdded()


    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;

    }//end getCreatedAt()


}//end class
