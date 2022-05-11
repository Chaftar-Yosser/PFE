<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
/**
 * @ORM\Entity
 * @UniqueEntity(fields="email", message="Email already exists")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255 , unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private $email;

    #[ORM\Column(type: 'json', length: 255, nullable: true)]
    private $role = [];

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private $password;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Length(min:2,max: 30)]
    private $fullname;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min:2,max: 30)]
    private $firstname;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min:2,max: 30)]
    private $lastname;


    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\Image( maxWidth: 4000, maxHeight: 4000,
         allowLandscape: true , allowPortrait: true)]
    private $image;

    #[ORM\ManyToMany(targetEntity: Tasks::class, inversedBy: 'users')]
    private $Tasks;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Contrat::class)]
    private $contrats;

    #[ORM\OneToMany(mappedBy: 'userTo', targetEntity: Leave::class)]
    private $leaves;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: SuiviLeave::class)]
    private $suiviLeaves;

    #[ORM\ManyToMany(targetEntity: Skills::class, inversedBy: 'users')]
    private $skills;

    #[ORM\ManyToMany(targetEntity: Projects::class, inversedBy: 'users')]
    private $projects;

    #[ORM\ManyToMany(targetEntity: Quiz::class, mappedBy: 'users')]
    private $quizzes;




    public function __construct()
    {
        $this->Tasks = new ArrayCollection();
        $this->contrats = new ArrayCollection();
        $this->leaves = new ArrayCollection();
        $this->suiviLeaves = new ArrayCollection();
        $this->skills = new ArrayCollection();
        $this->projects = new ArrayCollection();
        $this->quizzes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return array
     */
    public function getRole(): array
    {
        return $this->role;
    }

    /**
     * @param array $role
     * @return User
     */
    public function setRole(array $role): User
    {
        $this->role = $role;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    public function setFullname(string $fullname): self
    {
        $this->fullname = $fullname;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->role;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        // TODO: Implement getUserIdentifier() method.
        return (string) $this->email;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, Tasks>
     */
    public function getTasks(): Collection
    {
        return $this->Tasks;
    }


    public function addTask(Tasks $task): self
    {
        if (!$this->Tasks->contains($task)) {
            $this->Tasks[] = $task;
        }

        return $this;
    }

    public function removeTask(Tasks $task): self
    {
        $this->Tasks->removeElement($task);

        return $this;
    }

    /**
     * @return Collection<int, Contrat>
     */
    public function getContrats(): Collection
    {
        return $this->contrats;
    }

    public function addContrat(Contrat $contrat): self
    {
        if (!$this->contrats->contains($contrat)) {
            $this->contrats[] = $contrat;
            $contrat->setUser($this);
        }

        return $this;
    }

    public function removeContrat(Contrat $contrat): self
    {
        if ($this->contrats->removeElement($contrat)) {
            // set the owning side to null (unless already changed)
            if ($contrat->getUser() === $this) {
                $contrat->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Leave>
     */
    public function getLeaves(): Collection
    {
        return $this->leaves;
    }

    public function addLeaf(Leave $leaf): self
    {
        if (!$this->leaves->contains($leaf)) {
            $this->leaves[] = $leaf;
            $leaf->setUserTo($this);
        }

        return $this;
    }

    public function removeLeaf(Leave $leaf): self
    {
        if ($this->leaves->removeElement($leaf)) {
            // set the owning side to null (unless already changed)
            if ($leaf->getUserTo() === $this) {
                $leaf->setUserTo(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SuiviLeave>
     */
    public function getSuiviLeaves(): Collection
    {
        return $this->suiviLeaves;
    }

    public function addSuiviLeaf(SuiviLeave $suiviLeaf): self
    {
        if (!$this->suiviLeaves->contains($suiviLeaf)) {
            $this->suiviLeaves[] = $suiviLeaf;
            $suiviLeaf->setUser($this);
        }

        return $this;
    }

    public function removeSuiviLeaf(SuiviLeave $suiviLeaf): self
    {
        if ($this->suiviLeaves->removeElement($suiviLeaf)) {
            // set the owning side to null (unless already changed)
            if ($suiviLeaf->getUser() === $this) {
                $suiviLeaf->setUser(null);
            }
        }

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
     * @return Collection<int, Projects>
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Projects $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects[] = $project;
        }

        return $this;
    }

    public function removeProject(Projects $project): self
    {
        $this->projects->removeElement($project);

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
            $quiz->addUser($this);
        }

        return $this;
    }

    public function removeQuiz(Quiz $quiz): self
    {
        if ($this->quizzes->removeElement($quiz)) {
            $quiz->removeUser($this);
        }

        return $this;
    }

}
