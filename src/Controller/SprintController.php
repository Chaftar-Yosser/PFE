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

    private $em;
    private $repository;

    public function __construct(EntityManagerInterface $em , SprintRepository $sprintRepository)
    {
        $this->em = $em;
        $this->repository=$sprintRepository;

    }



    /**
     * @Route("/project-sprints/{id}" , name="sprint_index")
     * @return Response
     */
    public function index(Projects $projects, PaginatorInterface $paginator , Request $request): Response
    {
        $sprint = $paginator->paginate(
            $this->repository->findBy(["project" => $projects]), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            3 /*limit per page*/
        );
        return $this->render('sprint/index.html.twig', [
            'Sprint' => $sprint,
        ]);
    }


    /**
     * @param Request $request
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     * @package App\Controller
     * @Route("/create" ,name="create_sprint")
     */
    public function createsprint(Request $request)
    {
        if (!$this->isGranted("ROLE_ADMIN")){
            return $this->render('pages/404.html.twig');
        }

        $sprint = new Sprint();
        $form = $this->createForm(SprintType::class, $sprint);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->em->persist($sprint);
            $this->em->flush();
            $this->addFlash('success' , 'Sprint crée avec succés!');
            return $this->redirectToRoute('sprint_index', ["id" => $sprint->getProject()->getId()]);
        }

        return $this->render('sprint/create.html.twig',[
            'form' => $form->createView()
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
    public function editsprint(Request $request, Sprint $sprint)
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
            'form' => $form->createView(),
        ]);

    }

    /**
     * @Route("/delete/{id}", name="delete_sprint")
     * @param Sprint $sprint
     * @IsGranted("ROLE_ADMIN")
     */
    public function deletesprint(Sprint $sprint)
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
