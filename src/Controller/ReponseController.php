<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\Reponse;
use App\Form\ReponseType;
use App\Repository\ReponseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class UserController
 * @package App\Controller
 * @Route("/reponse")
 */

class ReponseController extends AbstractController
{
    private EntityManagerInterface $em;
    private ReponseRepository $repository;

    public function __construct(EntityManagerInterface $em, ReponseRepository $reponseRepository)
    {
        $this->em = $em;
        $this->repository= $reponseRepository;
    }

    #[Route('/question-reponse/{id}', name: 'reponse_index')]
    public function index(Question $question , PaginatorInterface $paginator , Request $request ): Response
    {
//        dd($this->repository->findByQuestion($question));
        $reponse = $paginator->paginate(
            $this->repository->findByQuestion($question), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            4 /*limit per page*/
        );
        return $this->render('reponse/index.html.twig', [
            'reponse' => $reponse,
            'question' => $question,
        ]);
    }

    /**
     * @param Request $request
     * @param Question $question
     * @return Response
     * @package App\Controller
     * @Route("/question/{id}/create" ,name="create_reponse")
     */
    public function create(Request $request , Question $question): Response
    {
        $reponse = new Reponse();
        $reponse->setQuestion($question);
        $form = $this->createForm(ReponseType::class,$reponse);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->em->persist($reponse);
            $this->em->flush();
            $this->addFlash('success' , 'Question crée avec succés!');
            return $this->redirectToRoute('reponse_index' , ["id" => $reponse->getQuestion()->getId()]);
        }

        return $this->render('reponse/create.html.twig', [
            'form' => $form->createView(),
            'question' => $question,
        ]);
    }

    /**
     * @param Request $request
     * @param Reponse $reponse
     * @return Response
     * @package App\Controller
     * @ParamConverter("question", options={"mapping": {"questionId": "id"}})
     * @ParamConverter("reponse", options={"mapping": {"id": "id"}})
     * @Route("/question/{questionId}/edit/{id}" ,name="edit_reponse")
     */
    public function edit(Request $request, Reponse $reponse): Response
    {
        //controle d'acces
        if (!$this->isGranted("ROLE_ADMIN")){
            return $this->render('pages/404.html.twig');
        }

        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $this->em->persist($reponse);
            $this->em->flush();
            $this->addFlash('success' , 'Question modifié avec succés!');
            return $this->redirectToRoute('reponse_index' , ["id" => $reponse->getQuestion()->getId()]);
        }
        return $this->render('reponse/edit.html.twig', [
            'reponse' =>$reponse,
            'question' => $reponse->getQuestion(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @ParamConverter("question", options={"mapping": {"questionId": "id"}})
     * @ParamConverter("reponse", options={"mapping": {"id": "id"}})
     * @Route("/question/{questionId}/delete/{id}", name="delete_reponse")
     * @param Reponse $reponse
     * @return Response
     */
    public function delete(Reponse $reponse):Response
    {
        //controle d'acces
        if (!$this->isGranted("ROLE_ADMIN")){
            return $this->render('pages/404.html.twig');
        }

        $this->em->remove($reponse);
        $this->em->flush();
        $this->addFlash('success' , 'Réponse supprimé avec succés');
        return $this->redirectToRoute('reponse_index' , ["id" => $reponse->getQuestion()->getId()]);
    }
}
