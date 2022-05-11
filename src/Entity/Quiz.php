<?php

namespace App\Entity;

use App\Repository\QuizRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuizRepository::class)]
class Quiz
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'string', length: 255)]
    private $nombrequestion;

    #[ORM\ManyToMany(targetEntity: Question::class, inversedBy: 'quizzes')]
    private $questions;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'quizzes')]
    private $users;

    #[ORM\ManyToMany(targetEntity: Skills::class, inversedBy: 'quizzes')]
    private $skills;

    #[ORM\Column(type: 'time', nullable: true)]
    private $Time;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->skills = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getNombrequestion(): ?string
    {
        return $this->nombrequestion;
    }

    public function setNombrequestion(string $nombrequestion): self
    {
        $this->nombrequestion = $nombrequestion;

        return $this;
    }

    /**
     * @return Collection<int, Question>
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions[] = $question;
        }

        return $this;
    }

    public function removeQuestion(Question $question): self
    {
        $this->questions->removeElement($question);

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        $this->users->removeElement($user);

        return $this;
    }

    /**
     * @return Collection<int, Skills>
     */
    public function getSkills(): Collection
    {
        return $this->skills;
    }

    public function addSkill(Skills $skill): self
    {
        if (!$this->skills->contains($skill)) {
            $this->skills[] = $skill;
        }

        return $this;
    }

    public function removeSkill(Skills $skill): self
    {
        $this->skills->removeElement($skill);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTime()
    {
        return $this->Time;
    }

    /**
     * @param mixed $Time
     * @return Quiz
     */
    public function setTime($Time)
    {
        $this->Time = $Time;
        return $this;
    }

}
