<?php

namespace App\Controller;

use App\Entity\Projects;
use App\Entity\Search;
use App\Entity\Sprint;
use App\Entity\Tasks;
use App\Entity\User;
use App\Form\SearchType;
use App\Form\TasksType;
use App\Repository\TasksRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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

        // filter
        $search = new Search();
        $form = $this->createForm(SearchType::class,$search);
        $form->handleRequest($request);

        // pagination
        $Tasks = $paginator->paginate(
            $tasks = $this->repository->getTasks($search),
            /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            3 /*limit per page*/
        );
        return $this->render('task/index.html.twig', [
            'Tasks' => $Tasks,
            'form' => $form->createView()
        ]);
    }

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
        return $this->render('task/sprintTasks.html.twig', [
            'sprint' => $sprint,
            'tasks' => $tasks,
        ]);
    }

    /**
     * @Route("/ajax" , name="ajax")
     */
    public function ajaxAction(Request $request) {

        if ($request->isXmlHttpRequest() || $request->request->get('id')) {
            $task = $this->repository->find($request->request->get('id'));
            $task->setStatus($request->request->get('close') ? Tasks::STATUS_TERMINE : Tasks::STATUS_ENCOURS );
            $this->em->persist($task);
            $this->em->flush();
            return new JsonResponse("success");
        }
    }

    /**
     * @Route("/ajax-user-tasks", name="ajaxUsertasks")
     */
    public function ajaxuserTasks(Request $request , UserRepository $userRepository){
        //si la requete est de type AJAX
        if ($request->isXmlHttpRequest() && $request->request->has('id')) {
            $user = $userRepository->find($request->request->get('id'));
            $tasks = $user->getTasks();
            $userTasks = [];
            foreach ($tasks as $task){
                $userTasks[] = [
                    'id' => $task->getId(),
                    'title' => $task->getTaskName(),
                    'start' => $task->getDateDebut()->format('Y-m-d H:i:s'),
                    'end' => $task->getDateFin()->format('Y-m-d H:i:s'),
                    'status' => $task->getStatus(),
                ];
            }

            return new JsonResponse([
                "success" => true,
                'tasks' =>  $userTasks
            ]);
        }

    }



    /**
     * @param Request $request
     * @param Tasks $Tasks
     * @return Response
     * @package App\Controller
     * @Route("/update/{id}" ,name="update_task")
     */
    public function update(Request $request, Tasks $Tasks)
    {
        $form = $this->createForm(TasksType::class, $Tasks);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $this->em->persist($Tasks);
            $this->em->flush();
            $this->addFlash('success' , 'tâche modifié avec succés!');
            return $this->redirectToRoute('task_index');
        }
        return $this->render('task/update.html.twig', [
            'task' =>$Tasks,
            'form' => $form->createView(),
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
            return $this->render('pages/404.html.twig');
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
            return $this->render('pages/404.html.twig');
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
            return $this->render('pages/404.html.twig');
        }

        $this->em->remove($Tasks);
        $this->em->flush();
        $this->addFlash('success' , 'tâche supprimé avec succés');
        return $this->redirectToRoute('task_index');
    }

}
