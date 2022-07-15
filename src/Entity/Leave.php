<?php

namespace App\Entity;

use App\Repository\LeaveRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Translation\Exception\IncompleteDsnException;
use Symfony\Component\Translation\Provider\Dsn;

#[ORM\Entity(repositoryClass: LeaveRepository::class)]
#[ORM\Table(name: '`leave`')]
class Leave
{
    const STATUS_ENCOURS="En cours";
    const STATUS_ACCEPTER="Accepter";
    const STATUS_REFUSER="Réfuser";



    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime')]
    private $startDate;

    #[ORM\Column(type: 'datetime')]
    private $endDate;

    #[ORM\Column(type: 'string', length: 255)]
    private $status;

    #[ORM\ManyToOne(targetEntity: LeaveType::class, inversedBy: 'leaves')]
    #[ORM\JoinColumn(nullable: false)]
    private $Leave_type;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'leaves')]
    #[ORM\JoinColumn(nullable: false)]
    private $userTo;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'leaves')]
    #[ORM\JoinColumn(nullable: false)]
    private $userFrom;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
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

    public function getLeaveType(): ?LeaveType
    {
        return $this->Leave_type;
    }

    public function setLeaveType(?LeaveType $Leave_type): self
    {
        $this->Leave_type = $Leave_type;

        return $this;
    }

    public function getUserTo(): ?User
    {
        return $this->userTo;
    }

    public function setUserTo(?User $userTo): self
    {
        $this->userTo = $userTo;

        return $this;
    }

    public function getUserFrom(): ?User
    {
        return $this->userFrom;
    }

    public function setUserFrom(?User $userFrom): self
    {
        $this->userFrom = $userFrom;

        return $this;
    }



}



