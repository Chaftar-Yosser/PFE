<?php

namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: QuestionRepository::class)]

/**
 * @ORM\Entity
 * @UniqueEntity(fields="title", message="Question already exists")
 */

class Question
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255 , unique: true)]
    private $title;

    #[ORM\Column(type: 'integer', length: 255)]
    private $score;

    #[ORM\OneToMany(mappedBy: 'question', targetEntity: Reponse::class, cascade:["remove"])]

    private $reponses;

    #[ORM\ManyToMany(targetEntity: Quiz::class, mappedBy: 'questions')]
    private $quizzes;

    #[ORM\ManyToOne(targetEntity: Skills::class, inversedBy: 'questions')]
    #[ORM\JoinColumn(nullable: false)]
    private $skills;

    public function __construct()
    {
        $this->reponses = new ArrayCollection();
        $this->quizzes = new ArrayCollection();
    }

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
     * @return Question
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * @param mixed $score
     * @return Question
     */
    public function setScore($score)
    {
        $this->score = $score;
        return $this;
    }

    /**
     * @return Collection<int, Reponse>
     */
    public function getReponses(): Collection
    {
        return $this->reponses;
    }

    public function addReponse(Reponse $reponse): self
    {
        if (!$this->reponses->contains($reponse)) {
            $this->reponses[] = $reponse;
            $reponse->setQuestion($this);
        }

        return $this;
    }

    public function removeReponse(Reponse $reponse): self
    {
        if ($this->reponses->removeElement($reponse)) {
            // set the owning side to null (unless already changed)
            if ($reponse->getQuestion() === $this) {
                $reponse->setQuestion(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Quiz>
     */
    public function getQuizzes(): Collection
    {
        return $this->quizzes;
    }

    public function addQuiz(Quiz $quiz): self
    {
        if (!$this->quizzes->contains($quiz)) {
            $this->quizzes[] = $quiz;
            $quiz->addQuestion($this);
        }

        return $this;
    }

    public function removeQuiz(Quiz $quiz): self
    {
        if ($this->quizzes->removeElement($quiz)) {
            $quiz->removeQuestion($this);
        }

        return $this;
    }

    public function getSkills(): ?Skills
    {
        return $this->skills;
    }

    public function setSkills(?Skills $skills): self
    {
        $this->skills = $skills;

        return $this;
    }

}
