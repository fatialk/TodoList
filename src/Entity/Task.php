<?php

namespace App\Entity;

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
    private $id;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_MUTABLE)]
    private $createdAt;

    #[Assert\NotBlank(message: "Vous devez saisir un titre.")]
    #[ORM\Column(type: Types::STRING, length: 255)]
    private $title;

    #[Assert\NotBlank(message: "Vous devez saisir un contenu.")]
    #[ORM\Column(type: Types::TEXT)]
    private $content;

    #[ORM\Column(type: Types::BOOLEAN)]
    private $isDone;

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
}
