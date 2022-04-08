<?php

namespace App\Controller;

use App\Entity\Projects;
use App\Form\ProjectType;
use App\Repository\ProjectsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
        //pagination
        $Project = $paginator->paginate(
            $this->repository->findAll(), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            3 /*limit per page*/
        );

        return $this->render('project/index.html.twig',[
            'projects' => $Project,

        ]);
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
            return $this->redirectToRoute('project_index');
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
