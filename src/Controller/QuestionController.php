<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\Search;
use App\Entity\Skills;
use App\Form\QuestionQuizType;
use App\Form\QuestionType;
use App\Form\SearchType;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class UserController
 * @package App\Controller
 * @Route("/question")
 */

class QuestionController extends AbstractController
{
    private $em;
    private $repository;

    public function __construct(EntityManagerInterface $em, QuestionRepository $questionQuizRepository)
    {
        $this->em = $em;
        $this->repository= $questionQuizRepository;
    }

    #[Route('/', name: 'question_index')]
    public function index(PaginatorInterface $paginator , Request $request): Response
    {
        // filter
        $search = new Search();
        $form = $this->createForm(SearchType::class,$search);
        $form->handleRequest($request);

        $question = $paginator->paginate(
            $this->repository->getQuestions($search), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            3 /*limit per page*/
        );

        return $this->render('question/index.html.twig', [
            'question' => $question,
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * @package App\Controller
     * @Route("/create" ,name="create_question")
     */
    public function createQuestion(Request $request)
    {
        $question = new Question();
//        $question->setSkills($skills);
        $form = $this->createForm(QuestionType::class,$question);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->em->persist($question);
            $this->em->flush();
            $this->addFlash('success' , 'Question crée avec succés!');
            return $this->redirectToRoute('question_index');
        }

        return $this->render('question/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Question $question
     * @return Response
     * @package App\Controller
     * @Route("/edit/{id}" ,name="edit_question")
     */
    public function edit(Request $request, Question $question)
    {
        //controle d'acces
        if (!$this->isGranted("ROLE_ADMIN")){
            return $this->render('pages/404.html.twig');
        }

        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $this->em->persist($question);
            $this->em->flush();
            $this->addFlash('success' , 'Question modifié avec succés!');
            return $this->redirectToRoute('question_index');
        }
        return $this->render('question/edit.html.twig', [
            'question' =>$question,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete_question")
     * @param Question $question
     */
    public function delete(Question $question)
    {
        //controle d'acces
        if (!$this->isGranted("ROLE_ADMIN")){
            return $this->render('pages/404.html.twig');
        }

        $this->em->remove($question);
        $this->em->flush();
        $this->addFlash('success' , 'Question supprimé avec succés');
        return $this->redirectToRoute('question_index');
    }


}
