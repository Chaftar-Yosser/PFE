<?php

namespace App\Controller;

use App\Entity\Projects;
use App\Entity\Skills;
use App\Entity\Sprint;
use App\Entity\Tasks;
use App\Entity\User;
use App\Form\ProjectType;
use App\Repository\ProjectsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @package App\Controller
 * @Route("/project")
 */
class ProjectController extends AbstractController
{
    private $em;
    private $repository;

    public function __construct(EntityManagerInterface $em , ProjectsRepository $projectsRepository)
    {
        $this->em = $em;
        $this->repository=$projectsRepository;
    }

    /**
     * @Route("/" , name="project_index")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $user = $this->getUser();
        if ($this->isGranted('ROLE_ADMIN')){
            $projects = [];
            foreach ($this->repository->findAll() as $project){
                // calcul de percentage d'avancement de chaque projet
                $total = 0;
                foreach ($project->getSprints() as $projectSprint){
                    $total += $this->em->getRepository(Sprint::class)->getSprintAdvancement($projectSprint);
                }

                if ($project->getSprints()->count() == 0 ){
                    $percent = 0;
                }else {
                    $percent = $total / $project->getSprints()->count();
                }

                $projects[] = [
                    "project" => $project,
                    "percent" => round($percent, 2),
                ];
            }
        }else{
//            $projects = $user->getProjects();
            foreach ($user->getProjects() as $project){
                // calcul de percentage d'avancement de chaque projet
                $total = 0;
                foreach ($project->getSprints() as $projectSprint){
                    $total += $this->em->getRepository(Sprint::class)->getSprintAdvancement($projectSprint);
                }

                if ($project->getSprints()->count() == 0 ){
                    $percent = 0;
                }else {
                    $percent = $total / $project->getSprints()->count();
                }

                $projects[] = [
                    "project" => $project,
                    "percent" => round($percent, 2),
                ];
            }
        }

        //pagination
        $Project = $paginator->paginate(
            $projects, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            3 /*limit per page*/
        );
        return $this->render('project/index.html.twig',[
            'projects' => $Project,
        ]);
    }
    /**
     * @Route("/project-details/{id}", name="show_details")
     * @return Response
     */
    public function showProjectDetail(Projects $project): Response
    {
        $suggestedUsers = $this->em->getRepository(User::class)->getUsersByProjectSkills($project);
        return $this->render('project/showdetails.html.twig', [
            'project' => $project,
            'tasks' => $project->getTasks(),
            'suggestedUsers' => $suggestedUsers,
        ]);
    }

    /**
     * @ParamConverter("project", options={"mapping": {"id": "id"}})
     * @ParamConverter("user", options={"mapping": {"userId": "id"}})
     * @Route("/project/{id}/addUser/{userId}" , name="affect_user")
     * @param Projects $project
     * @param $userId
     * @param MailerInterface $mailer
     * @return Response
     */
    public function addUserToProject(Projects $project, $userId , MailerInterface $mailer): Response
    {

        $user =$this->em->getRepository(User::class)->find($userId);
        $project->addUser($user);
        $this->em->persist($project);
        $this->em->flush();
//      envoie de mail pour notifier user à quel projet il travail
        $email = (new Email())
            ->from($this->getParameter("mailer_sender")) // toujours l'envoie avec le mail déclarer dans le service yaml
            ->to($user->getEmail())
            ->subject('Affectation au projet ')
            ->text('Sending emails is fun again!')
            ->html('Vous êtes affecter à ' . $project->getName())
        ;
        $mailer->send($email);

        $this->addFlash('success' , 'User affecter au projet avec succés!'); // ne foctionne pas
        return $this->redirectToRoute('show_details', ['id' => $project->getId()]);
    }

    /**
     * @Route("/project/{id}/removeUser/{userId}" , name="remove_user")
     * @param Projects $project
     * @param $userId
     * @return Response
     */
    public function removeUserToProject(Projects $project, $userId , MailerInterface $mailer): Response
    {

        $user =$this->em->getRepository(User::class)->find($userId);
        $project->removeUser($user);
        $this->em->persist($project);
        $this->em->flush();
        $email = (new Email())
            ->from($this->getParameter("mailer_sender"))
            ->to($user->getEmail())
            ->subject('attachement du projet ')
            ->text('Sending emails is fun again!')
            ->html('Vous êtes détacher de ' . $project->getName())
        ;
        $mailer->send($email);

        $this->addFlash('success' , 'User remove au projet avec succés!');
        return $this->redirectToRoute('show_details', ['id' => $project->getId()]);
    }


    /**
     * @param Request $request
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     * @package App\Controller
     * @Route("/create" ,name="create_project")
     */
    public function createProject(Request $request)
    {
        if (!$this->isGranted("ROLE_ADMIN")){
            return $this->render('pages/404.html.twig');
        }

        $Project = new Projects();
        $form = $this->createForm(ProjectType::class, $Project, [
            'create' =>true
        ]);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $this->em->persist($Project);
            $this->em->flush();
            $this->addFlash('success' , 'Projet crée avec succés!');
            return $this->redirectToRoute('project_index' , ["id" => $Project->getId()]);
        }

        return $this->render('project/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param Projects $Project
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     * @package App\Controller
     * @Route("/edit/{id}" ,name="edit_project")
     */
    public function editProject(Request $request, Projects $Project)
    {
        if (!$this->isGranted("ROLE_ADMIN")){
            return $this->render('pages/404.html.twig');
        }

        $form = $this->createForm(ProjectType::class, $Project);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $this->em->persist($Project);
            $this->em->flush();
            $this->addFlash('success' , 'projet modifié avec succés!');
            return $this->redirectToRoute('project_index');
        }
        return $this->render('project/edit.html.twig', [
            'project' =>$Project,
            'form' => $form->createView(),
        ]);

    }


    /**
     * @Route("/delete/{id}", name="delete_project")
     * @param Projects $Project
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteProject(Projects $Project)
    {
        if (!$this->isGranted("ROLE_ADMIN")){
            return $this->render('pages/404.html.twig');
        }

        $this->em->remove($Project);
        $this->em->flush();
        $this->addFlash('success' , 'projet supprimé avec succés');
        return $this->redirectToRoute('project_index');
    }
}
