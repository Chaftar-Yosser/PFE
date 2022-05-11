<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\Quiz;
use App\Entity\User;
use App\Form\QuizType;
use App\Repository\QuizRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;



/**
 * Class UserController
 * @package App\Controller
 * @Route("/quiz")
 */
class QuizController extends AbstractController
{
    private $em;
    private $repository;

    public function __construct(EntityManagerInterface $em, QuizRepository $quizRepository)
    {
        $this->em = $em;
        $this->repository= $quizRepository;
    }

    #[Route('/', name: 'quiz_index')]
    public function index(PaginatorInterface $paginator , Request $request): Response
    {
        // pagination
        $quiz = $paginator->paginate(
            $this->repository->findAll(), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            3 /*limit per page*/
        );
        return $this->render('quiz/index.html.twig', [
            'quiz' => $quiz,
        ]);
    }

    /**
     * @Route("/quiz-details/{id}", name="details")
     * @return Response
     */
    public function showProjectDetail(Quiz $quiz): Response
    {
        $suggestedUsers = $this->em->getRepository(User::class)->getUsersByQuizSkills($quiz);
        return $this->render('quiz/details.html.twig', [
            'quiz' => $quiz,
            'suggestedUsers' => $suggestedUsers,
        ]);
    }

    /**
     * @Route("/quiz/{id}/addUser/{userId}" , name="affect_user")
     * @param Quiz $quiz
     * @param $userId
     * @param MailerInterface $mailer
     * @return Response
     */
    public function addUserToQuiz(Quiz $quiz, $userId , MailerInterface $mailer): Response
    {

        $user =$this->em->getRepository(User::class)->find($userId);
        $quiz->addUser($user);
        $this->em->persist($quiz);
        $this->em->flush();
//      envoie de mail pour notifier user à quelle quiz il est affecté
        $email = (new Email())
            ->from($this->getParameter("mailer_sender")) // toujours l'envoie avec le mail déclarer dans le service yaml
            ->to($user->getEmail())
            ->subject('Affectation au quiz ')
            ->html('Vous pouvez participer au quiz  ' . $quiz->getName())
        ;
        $mailer->send($email);

        $this->addFlash('success' , 'User affecter au projet avec succés!');
        return $this->redirectToRoute('details', ['id' => $quiz->getId()]);
    }

    /**
     * @param Request $request
     * @return Response
     * @package App\Controller
     * @Route("/create" ,name="create_quiz")
     */
    public function create(Request $request )
    {
        $quiz = new Quiz();
        $form = $this->createForm(QuizType::class,$quiz);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
//            $skills = $form->get("skills")->getData();

            $questions = $this->em->getRepository(Question::class)->getQuestionByQuiz($quiz);
            foreach ($questions as $quizQuestion){
                $quiz->addQuestion($quizQuestion);
            }
            $this->em->persist($quiz);
            $this->em->flush();
            $this->em->refresh($quiz);
            $this->addFlash('success' , 'Quiz crée avec succés!');
            return $this->redirectToRoute('quiz_index');
        }

        return $this->render('quiz/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Quiz $quiz
     * @return Response
     * @package App\Controller
     * @Route("/edit/{id}" ,name="edit_quiz")
     */
    public function edit(Request $request, Quiz $quiz)
    {
        //controle d'acces
        if (!$this->isGranted("ROLE_ADMIN")){
            return $this->render('pages/404.html.twig');
        }

        $form = $this->createForm(QuizType::class, $quiz);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $questions = $this->em->getRepository(Question::class)->getQuestionByQuiz($quiz);
            foreach ($questions as $quizQuestion){
                $quiz->removeQuestion($quizQuestion);
//                $quiz->addQuestion($quizQuestion);
            }

            $this->em->persist($quiz);
            $this->em->flush();
            $this->addFlash('success' , 'Quiz modifié avec succés!');
            return $this->redirectToRoute('quiz_index' ,);
        }
        return $this->render('quiz/edit.html.twig', [
            'quiz' =>$quiz,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete_quiz")
     * @param Quiz $quiz
     * @return Response
     */
    public function delete(Quiz $quiz)
    {
        //controle d'acces
        if (!$this->isGranted("ROLE_ADMIN")){
            return $this->render('pages/404.html.twig');
        }

        $this->em->remove($quiz);
        $this->em->flush();
        $this->addFlash('success' , 'Quiz supprimé avec succés');
        return $this->redirectToRoute('quiz_index');
    }
}
