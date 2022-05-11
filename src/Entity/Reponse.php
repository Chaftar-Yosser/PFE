<?php

namespace App\Entity;

use App\Repository\ReponseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReponseRepository::class)]
class Reponse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $title;

    #[ORM\ManyToOne(targetEntity: Question::class, inversedBy: 'reponses')]
    #[ORM\JoinColumn(nullable: false)]
    private $question;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $isCorrect;


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     * @return Reponse
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getIsCorrect(): ?bool
    {
        return $this->isCorrect;
    }

    public function setIsCorrect(?bool $isCorrect): self
    {
        $this->isCorrect = $isCorrect;

        return $this;
    }





}
