<?php

namespace App\Controller;

use App\Entity\Leave;
use App\Entity\Projects;
use App\Entity\SuiviLeave;
use App\Entity\Tasks;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    private $repository;
    private $em;

    public function __construct(EntityManagerInterface $em, UserRepository $userRepository)
    {
        $this->em = $em;
        $this->repository = $userRepository;
    }

    /**
     * @Route("/" , name="dashboard_index")
     * @IsGranted("ROLE_USER")
     * @return Response
     */

    public function index(): Response
    {
        $user = $this->getUser();
        $users = $this->repository->findAll();
        // calcul de nb totale des projets
        $projectsCount = $this->em->getRepository(Projects::class)->getProjectsCount();

        // calcul de nb de tâches par user and status + pourcentage
        $taskCount = $this->em->getRepository(Tasks::class)->getTasksByUserAndStatus($user, Tasks::STATUS_TERMINE);
        $taskCountenCour = $this->em->getRepository(Tasks::class)->getTasksByUserAndStatus($user, Tasks::STATUS_ENCOURS);
        $nbTasks = $user->getTasks()->count();
        if ($nbTasks == 0 ){
            $percentTask = 0 ;
        }else{
            $percentTask = ( $taskCount / $nbTasks )* 100 ;
        }

        $CurrentYear = (new \DateTime())->format('Y');
        $PreviousYear = $CurrentYear-1;

        $userConges = $this->em->getRepository(SuiviLeave::class)->findBy(["user" => $user, "annees" => $PreviousYear]);
        $conges = [];
        foreach ($userConges as $conge )
        {
            $conges[] = $conge->getPris();
        }


        $userConges = $this->em->getRepository(SuiviLeave::class)->findBy(["user" => $user, "annees" => $CurrentYear]);
        $Conges = [];
        foreach ($userConges as $conge )
        {
            $Conges[] = $conge->getPris();
        }


        // calcul de nb de projet par user
        $projectUser = $this->em->getRepository(Projects::class)->getProjectsByUser($user);

        return $this->render('dashboard/index.html.twig', [
            'users' => $users,
            'projectsCount' => $projectsCount,
            'taskCount' => $taskCount,
            'taskCountenCour' => $taskCountenCour,
            'percentTask' => round($percentTask , 2),
            'projectUser' => $projectUser,
            'userConges' => $userConges,
            'conges' => $conges,
            'Conges' => $Conges,
        ]);
    }
}
