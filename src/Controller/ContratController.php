<?php

namespace App\Controller;

use App\Entity\Contrat;
use App\Entity\Search;
use App\Entity\User;
use App\Form\ContratType;
use App\Form\SearchType;
use App\Repository\ContratRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class UserController
 * @package App\Controller
 * @Route("/contrat")
 */

class ContratController extends AbstractController
{
    private $em;
    private $repository;

    public function __construct(EntityManagerInterface $em, ContratRepository $contratRepository)
    {
        $this->em = $em;
        $this->repository= $contratRepository;
    }

    /**
     * @Route("/" , name="contrat_index")
     * @return Response
     */
    public function index(PaginatorInterface $paginator , Request $request): Response
    {
        // Filter
        $search = new Search();
        $form = $this->createForm(SearchType::class,$search);
        $form->handleRequest($request);

        // affichage des contrats pour l'utilisateur courant
        $user = $this->getUser();
        if ($this->isGranted('ROLE_ADMIN')){
            $contrat = $this->repository->getContrats($search);
        }else{
            $contrat = $this->repository->findBy(["user" => $user]);
        }
        // pagination
        $contrat = $paginator->paginate(
            $contrat, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            3 /*limit per page*/
        );
        return $this->render('contrat/index.html.twig',[
            'contrats' => $contrat,
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * @package App\Controller
     * @Route("/create" ,name="create_contrat")
     */
    public function createContrat(Request $request)
    {
        //controle d'acces
        if (!$this->isGranted("ROLE_RH")){
//            throw new AccessDeniedException('Need ROLE_RH!');
            return $this->render('pages/404.html.twig');
        }

        $contrat = new Contrat();
        $form = $this->createForm(ContratType::class, $contrat, [
            'create' =>true
        ]);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $this->em->persist($contrat);
            $this->em->flush();
            $this->addFlash('success' , 'Contrat crée avec succés!');
            return $this->redirectToRoute('contrat_index');
        }

        return $this->render('contrat/create.html.twig', [
            'form' => $form->createView()
        ]);

    }

    /**
     * @param Request $request
     * @param Contrat $contrat
     * @return Response
     * @package App\Controller
     * @Route("/edit/{id}" ,name="edit_contrat")
     */
    public function edit(Request $request, Contrat $contrat)
    {
        //controle d'acces
        if (!$this->isGranted("ROLE_RH")){
            return $this->render('pages/404.html.twig');
        }

        $form = $this->createForm(ContratType::class, $contrat, [
            'create' => false
        ]);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $this->em->persist($contrat);
            $this->em->flush();
            $this->addFlash('success' , 'Contrat modifié avec succés!');
            return $this->redirectToRoute('contrat_index');
        }
        return $this->render('contrat/edit.html.twig', [
            'contrat' =>$contrat,
            'form' => $form->createView(),
        ]);

    }

    /**
     * @Route("/delete/{id}", name="delete_contrat")
     * @param Contrat $contrat
     *
     */
    public function delete(Contrat $contrat)
    {
        //controle d'acces
        if (!$this->isGranted("ROLE_RH")){
            return $this->render('pages/404.html.twig');
        }

        $this->em->remove($contrat);
        $this->em->flush();
        $this->addFlash('success' , 'contrat supprimé avec succés');
        return $this->redirectToRoute('contrat_index');
    }

}
