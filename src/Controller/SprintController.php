<?php

namespace App\Controller;

use App\Entity\Projects;
use App\Entity\Sprint;
use App\Form\SprintType;
use App\Repository\SprintRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/sprint')]
class SprintController extends AbstractController
{

    private EntityManagerInterface $em;
    private SprintRepository $repository;

    public function __construct(EntityManagerInterface $em , SprintRepository $sprintRepository)
    {
        $this->em = $em;
        $this->repository=$sprintRepository;

    }


    /**
     * @Route("/project-sprints/{id}" , name="sprint_index")
     * @param Projects $project
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(Projects $project, PaginatorInterface $paginator , Request $request): Response
    {

        $sprint = $paginator->paginate(
            $this->repository->findBy(["project" => $project]), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            3 /*limit per page*/
        );
        // calcul de percentage d'avancement d'un sprint
        $total = 0;
        foreach ($project->getSprints() as $projectSprint){
            $total += $this->em->getRepository(Sprint::class)->getSprintAdvancement($projectSprint);
        }

        $nbSprint = $project->getSprints()->count();
        if ($nbSprint == 0 ){
            $percent = 0 ;
        }else{
         $percent = $total / $nbSprint;
        }

        return $this->render('sprint/index.html.twig', [
            'Sprint' => $sprint,
            'project' => $project,
            'percent' => round($percent, 2),
        ]);
    }


    /**
     * @param Request $request
     * @param Projects $project
     * @return Response
     * @package App\Controller
     * @Route("/project/{id}/create" ,name="create_sprint")
     */
    public function createsprint(Request $request, Projects $project): Response
    {
        if (!$this->isGranted("ROLE_ADMIN")){
            return $this->render('pages/404.html.twig');
        }

        $sprint = new Sprint();
        $sprint->setProject($project);
        $form = $this->createForm(SprintType::class, $sprint);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->em->persist($sprint);
            $this->em->flush();
            $this->addFlash('success' , 'Sprint crée avec succés!');
            return $this->redirectToRoute('sprint_index', ["id" => $sprint->getProject()->getId()]);
        }

        return $this->render('sprint/create.html.twig',[
            'form' => $form->createView(),
            'project' => $project,

        ]);
    }

    /**
     * @param Request $request
     * @param Sprint $sprint
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     * @package App\Controller
     * @Route("/edit/{id}" ,name="edit_sprint")
     */
    public function editsprint(Request $request, Sprint $sprint): Response
    {
        if (!$this->isGranted("ROLE_ADMIN")){
            return $this->render('pages/404.html.twig');
        }

        $form = $this->createForm(SprintType::class, $sprint);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $this->em->persist($sprint);
            $this->em->flush();
            $this->addFlash('success' , 'sprint modifié avec succés!');
            return $this->redirectToRoute('sprint_index', ["id" => $sprint->getProject()->getId()]);
        }
        return $this->render('sprint/edit.html.twig', [
            'sprint' =>$sprint,
            'project' => $sprint->getProject(),
            'form' => $form->createView(),
        ]);

    }

    /**
     * @Route("/delete/{id}", name="delete_sprint")
     * @param Sprint $sprint
     * @IsGranted("ROLE_ADMIN")
     * @return Response
     */
    public function deletesprint(Sprint $sprint): Response
    {
        if (!$this->isGranted("ROLE_ADMIN")){
            return $this->render('pages/404.html.twig');
        }

        $this->em->remove($sprint);
        $this->em->flush();
        $this->addFlash('success' , 'sprint supprimé avec succés');
        return $this->redirectToRoute('sprint_index', ["id" => $sprint->getProject()->getId()]);
    }
}
