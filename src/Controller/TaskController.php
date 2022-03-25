<?php

namespace App\Controller;

use App\Entity\Projects;
use App\Entity\Sprint;
use App\Entity\Tasks;
use App\Entity\User;
use App\Form\TasksType;
use App\Repository\TasksRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @package App\Controller
 * @Route("/task" )
 */
class TaskController extends AbstractController
{

    private $em;
    private $repository;

    public function __construct(EntityManagerInterface $em , TasksRepository $tasksRepository)
    {
        $this->em = $em;
        $this->repository=$tasksRepository;

    }

    /**
     * @Route("/" , name="task_index")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        // pagination
        $Tasks = $paginator->paginate(
            $tasks = $this->repository->getTasks(), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            3 /*limit per page*/
        );
        return $this->render('task/index.html.twig', [
            'Tasks' => $Tasks,
        ]);
    }

//    /**
//     * @Route("/project/{id}", name="project_tasks")
//     * @param Projects $projects
//     * @return Response
//     */
//    public function showProjectTasks(Projects $projects): Response
//    {
//        $taskRepository = $this->em->getRepository(Tasks::class);
//        $tasks = $taskRepository->findBy(["Projects" => $projects]);
//        return $this->render('task/projectTasks.html.twig', [
//            'project' => $projects,
//            'tasks' => $tasks,
//        ]);
//    }
    /**
     * @Route("/sprint-tasks/{id}", name="sprint_tasks")
     * @param Sprint $sprint
     * @return Response
     */
    public function showSprintTasks(Sprint $sprint): Response
    {
        $user = $this->getUser();
        $taskRepository = $this->em->getRepository(Tasks::class);
        // affichage des tâches selon les sprints pour l'utilisateur courant
        $tasks = $taskRepository->getTasksByUserAndSprint($user, $sprint);
        return $this->render('task/projectTasks.html.twig', [
            'sprint' => $sprint,
            'tasks' => $tasks,
        ]);
    }


    /**
     * @param Request $request
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     * @package App\Controller
     * @Route("/create" ,name="create_task")
     */
    public function create(Request $request)
    {
        if (!$this->isGranted("ROLE_ADMIN")){
            throw new AccessDeniedException('Need ROLE_ADMIN!');
        }

        $Tasks = new Tasks();
        $form = $this->createForm(TasksType::class, $Tasks);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            //todo: check persistence
            /** @var User $user */
            foreach ($form['users']->getData()->getValues() as $user) {
                $user->addTask($Tasks);
                $this->em->persist($user);
                $Tasks->addUser($user);
            }
            $this->em->persist($Tasks);
            $this->em->flush();
            $this->addFlash('success' , 'Tâche crée avec succés!');
            return $this->redirectToRoute('task_index');
        }

        return $this->render('task/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param Tasks $Tasks
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     * @package App\Controller
     * @Route("/edit/{id}" ,name="edit_task")
     */
    public function edit(Request $request, Tasks $Tasks)
    {
        if (!$this->isGranted("ROLE_ADMIN")){
            throw new AccessDeniedException('Need ROLE_ADMIN!');
        }

        $form = $this->createForm(TasksType::class, $Tasks);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            //todo: check persistence
            //mazelet fama mochkell : tnajem tziid ayy w modification le
            /** @var User $user */
            foreach ($form['users']->getData()->getValues() as $user) {
                $user->addTask($Tasks);
                $this->em->persist($user);
                $Tasks->addUser($user);
            }
            $this->em->persist($Tasks);
            $this->em->flush();
            $this->addFlash('success' , 'tâche modifié avec succés!');
            return $this->redirectToRoute('task_index');
        }
        return $this->render('task/edit.html.twig', [
            'task' =>$Tasks,
            'form' => $form->createView(),
        ]);

    }


    /**
     * @Route("/delete/{id}", name="delete_task")
     * @param Tasks $Tasks
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Tasks $Tasks)
    {
        if (!$this->isGranted("ROLE_ADMIN")){
            throw new AccessDeniedException('Need ROLE_ADMIN!');
        }

        $this->em->remove($Tasks);
        $this->em->flush();
        $this->addFlash('success' , 'tâche supprimé avec succés');
        return $this->redirectToRoute('task_index');
    }

}
