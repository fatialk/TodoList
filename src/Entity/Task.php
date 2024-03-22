<?php

namespace App\Entity;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[Table(name: 'task')]
#[ORM\Entity()]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_MUTABLE)]
    private ?DateTime $createdAt;

    #[Assert\NotBlank(message: "Vous devez saisir un titre.")]
    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $title;

    #[Assert\NotBlank(message: "Vous devez saisir un contenu.")]
    #[ORM\Column(type: Types::TEXT)]
    private string $content;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isDone;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'tasks')]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false)]
    private ?user $user = null;

    public function __construct()
    {
        $this->createdAt = new \Datetime();
        $this->isDone = false;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function isDone()
    {
        return $this->isDone;
    }

    public function toggle($flag)
    {
        $this->isDone = $flag;
    }

    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(?user $user): static
    {
        $this->user = $user;

        return $this;
    }
}
