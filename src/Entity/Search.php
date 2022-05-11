<?php
namespace App\Entity;

class Search
{


    /**
     * @var null
     */
    private $sprint;

    /**
     * @var null
     */
    private $Projects;

    /**
     * @var null
     */
    private $status;

    /**
     * @var null
     */
    private $user;

    /**
     * @var null
     */
    private $type_contrat;

    /**
     * @var null
     */
    private $role;

    /**
     * @var null
     */
    private $Leave_type ;

    /**
     * @var null
     */
    private $userFrom ;

    /**
     * @var null
     */
    private $skills;

    /**
     * @return null
     */
    public function getSkills()
    {
        return $this->skills;
    }

    /**
     * @param null $skills
     * @return Search
     */
    public function setSkills($skills)
    {
        $this->skills = $skills;
        return $this;
    }


    /**
     * @return null
     */
    public function getSprint()
    {
        return $this->sprint;
    }

    /**
     * @param null $sprint
     * @return Search
     */
    public function setSprint($sprint)
    {
        $this->sprint = $sprint;
        return $this;
    }

    /**
     * @return null
     */
    public function getProjects()
    {
        return $this->Projects;
    }

    /**
     * @param null $Projects
     * @return Search
     */
    public function setProjects($Projects)
    {
        $this->Projects = $Projects;
        return $this;
    }

    /**
     * @return null
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param null $status
     * @return Search
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return null
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param null $user
     * @return Search
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return null
     */
    public function getTypeContrat()
    {
        return $this->type_contrat;
    }

    /**
     * @param null $type_contrat
     * @return Search
     */
    public function setTypeContrat($type_contrat)
    {
        $this->type_contrat = $type_contrat;
        return $this;
    }

    /**
     * @return null
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param null $role
     * @return Search
     */
    public function setRole($role)
    {
        $this->role = $role;
        return $this;
    }

    /**
     * @return null
     */
    public function getLeaveType()
    {
        return $this->Leave_type;
    }

    /**
     * @param null $Leave_type
     * @return Search
     */
    public function setLeaveType($Leave_type)
    {
        $this->Leave_type = $Leave_type;
        return $this;
    }

    /**
     * @return null
     */
    public function getUserFrom()
    {
        return $this->userFrom;
    }

    /**
     * @param null $userFrom
     * @return Search
     */
    public function setUserFrom($userFrom)
    {
        $this->userFrom = $userFrom;
        return $this;
    }





}