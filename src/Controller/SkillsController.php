<?php

namespace App\Controller;

use App\Entity\Skills;
use App\Form\SkillsType;
use App\Repository\SkillsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class UserController
 * @package App\Controller
 * @Route("/skills")
 */

class SkillsController extends AbstractController
{
    private $em;
    private $repository;

    public function __construct(EntityManagerInterface $em , SkillsRepository $skillsRepository)
    {
        $this->em = $em;
        $this->repository= $skillsRepository;
    }

    #[Route('/', name: 'skills_index')]
    public function index(PaginatorInterface $paginator , Request $request): Response
    {
        $skills = $paginator->paginate(
            $this->repository->findAll(),
            $request->query->getInt('page', 1),
            3 /*limit per page*/
        );
        return $this->render('skills/index.html.twig', [
            'skills' => $skills,
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * @package App\Controller
     * @Route("/create" ,name="create_skills")
     */
    public function create(Request $request)
    {
        //controle d'acces
        if (!$this->isGranted("ROLE_ADMIN")){
            return $this->render('pages/404.html.twig');
        }

        $skills = new Skills();
        $form = $this->createForm(SkillsType::class, $skills);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $this->em->persist($skills);
            $this->em->flush();
            $this->addFlash('success' , 'skills crée avec succés!');
            return $this->redirectToRoute('skills_index');
        }
        return $this->render('skills/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param Skills $skills
     * @return Response
     * @package App\Controller
     * @Route("/edit/{id}" ,name="edit_skills")
     */
    public function edit(Request $request, Skills $skills)
    {

        //controle d'acces
        if (!$this->isGranted("ROLE_ADMIN")){
            return $this->render('pages/404.html.twig');
        }

        $form = $this->createForm(SkillsType::class, $skills);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $this->em->persist($skills);
            $this->em->flush();
            $this->addFlash('success' , 'skills modifié avec succés!');
            return $this->redirectToRoute('skills_index');
        }
        return $this->render('skills/edit.html.twig', [
            'skills' =>$skills,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete_skills")
     * @param Skills $skills
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Skills $skills)
    {
        //controle d'acces
        if (!$this->isGranted("ROLE_ADMIN")){
            return $this->render('pages/404.html.twig');
        }

        $this->em->remove($skills);
        $this->em->flush();
        $this->addFlash('success' , 'skills supprimé avec succés');
        return $this->redirectToRoute('skills_index');
    }

}
