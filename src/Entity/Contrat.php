<?php

namespace App\Entity;

use App\Repository\ContratRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContratRepository::class)]
class Contrat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $status;

    #[ORM\Column(type: 'string', length: 255)]
    private $duree;

    #[ORM\Column(type: 'datetime')]
    private $date_debut;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $date_fin;

    #[ORM\ManyToOne(targetEntity: TypeContrat::class, inversedBy: 'contrats')]
    #[ORM\JoinColumn(nullable: false)]
    private $type_contrat;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'contrats')]
    private $user;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getDuree(): ?string
    {
        return $this->duree;
    }

    public function setDuree(string $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTimeInterface $date_debut): self
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateFin()
    {
        return $this->date_fin;
    }

    /**
     * @param mixed $date_fin
     * @return Contrat
     */
    public function setDateFin($date_fin)
    {
        $this->date_fin = $date_fin;
        return $this;
    }

    public function getTypeContrat(): ?TypeContrat
    {
        return $this->type_contrat;
    }

    public function setTypeContrat(?TypeContrat $type_contrat): self
    {
        $this->type_contrat = $type_contrat;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }







}
