<?php

namespace App\Controller;


use App\Entity\Leave;
use App\Entity\Tasks;
use App\Repository\TasksRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $repository;
    private $em;

    public function __construct(EntityManagerInterface $em, UserRepository $userRepository)
    {
        $this->em = $em;
        $this->repository = $userRepository;
    }


    /**
     * @Route("/home" , name="home_index")
     * @return Response
     */

    public function index(TasksRepository $tasksRepository, Request $request): Response
    {
        $user = $this->getUser();
//        dd($user);
        if ($this->isGranted('ROLE_ADMIN')) {
            $events = $tasksRepository->getTaskByDate();
        } else {
            $events = $tasksRepository->getTaskByDate($user);
        }
        // afficher les tâches sur une calendrier
        $tasks = [];
        /** @var Tasks $event */
        foreach ($events as $event) {
            switch ($event->getPriorite()) {
                case "Elevée":
                    $color = "#F32424";
                    $Textcolor ="#FFFFFF";
                    break;
                case "Normal":
                    $color = "#06283D";
                    $Textcolor ="#FFFFFF";
                    break;
                case "Moyenne":
                    $color = "#FFA500";
                    $Textcolor ="#FFFFFF";
                    break;
                default:
                    $color = "#08D9D6";
                    $Textcolor ="#FFFFFF";
                    break;
            }
            $tasks[] = [
                'type' => "TASK",
                'id' => $event->getId(),
                'start' => $event->getDateDebut()->format('Y-m-d H:i:s'),
                'end' => $event->getDateFin()->format('Y-m-d H:i:s'),
                'title' => $event->getTaskName(),
                'status' => $event->getStatus(),
                'backgroundColor' => $color,
                'textColor' => $Textcolor,
                'editUrl' => $this->generateUrl('edit_task', ['taskId' => $event->getId(), 'projectId' => $event->getProjects()->getId()])
            ];
        }
        $users = $this->repository->findAll();

        // afficher les congés sur une calendrier
        $leaves = $this->em->getRepository(Leave::class)->getLeaveByDateAndUser($user);
        /** @var Leave $event */
        foreach ($leaves as $leave) {


            switch ($leave->getStatus()) {

                default:
                    $color = "#9A86A4";
                    $Textcolor ="#FFFFFF";
                    break;
            }



            if ($leave->getStatus() == Leave::STATUS_ACCEPTER) {
                $tasks[] = [
                    'type' => "LEAVE",
                    'id' => $leave->getId(),
                    'start' => $leave->getStartDate()->format('Y-m-d'),
                    'end' => $leave->getEndDate()->format('Y-m-d'),
                    'status' => $leave->getStatus(),
                    'title' => $leave->getLeaveType()->getName(),
                    'backgroundColor' => $color,
                    'textColor' => $Textcolor,
                ];
            }
        }

        $holidays[] = [
            "title" => "Journée des martyrs",
            "start" => "2022-04-09",
            "holiday" => "1",
        ];
        $holidays[] = [
            "title" => "Fête du Travail",
            "start" => "2022-05-01",
            "holiday" => "1",
        ];
        $holidays[] = [
            "title" => "Fête de la République",
            "start" => "2022-07-25",
            "holiday" => "1",
        ];
        $tasks = array_merge($holidays, $tasks);

        // formulaire d'ajout de congés
        $newLeave = new Leave();
        $form = $this->createForm(\App\Form\LeaveType::class, $newLeave, ['currentUser' => $user, "create" => true]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newLeave->setStatus(Leave::STATUS_ENCOURS);
            $this->em->persist($newLeave);
            $this->em->flush();
            $this->addFlash('success', 'demande crée avec succés!');
            return $this->redirectToRoute('home_index');
        }

        return $this->render('pages/home.html.twig', [
            'data' => json_encode($tasks),
            'events' => $events,
            'users' => $users,
            'form' => $form->createView()
        ]);
    }

    public function notfound(): Response
    {
        return $this->render('pages/404.html.twig');
    }
}