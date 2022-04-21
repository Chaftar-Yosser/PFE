<?php

namespace App\Entity;

use App\Repository\SuiviLeaveRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SuiviLeaveRepository::class)]
class SuiviLeave
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string')]
    private $annees;

    #[ORM\Column(type: 'string')]
    private $mois;

    #[ORM\Column(type: 'float')]
    private $total;

    #[ORM\Column(type: 'float')]
    private $pris;

    #[ORM\Column(type: 'float')]
    private $restant;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'suiviLeaves')]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getAnnees()
    {
        return $this->annees;
    }

    /**
     * @param mixed $annees
     * @return SuiviLeave
     */
    public function setAnnees($annees)
    {
        $this->annees = $annees;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMois()
    {
        return $this->mois;
    }

    /**
     * @param mixed $mois
     * @return SuiviLeave
     */
    public function setMois($mois)
    {
        $this->mois = $mois;
        return $this;
    }



    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getPris(): ?float
    {
        return $this->pris;
    }

    public function setPris(float $pris): self
    {
        $this->pris = $pris;

        return $this;
    }

    public function getRestant(): ?float
    {
        return $this->restant;
    }

    public function setRestant(float $restant): self
    {
        $this->restant = $restant;

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
