<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\Quiz;
use App\Entity\Reponse;
use App\Entity\User;
use App\Form\QuizType;
use App\Form\ReponseType;
use App\Repository\QuizRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
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
     * @Route("/quiz-participate/{id}", name="quiz_participate")
     * @param Quiz $quiz
     * @param Request $request
     * @param Reponse $reponse
     * @param MailerInterface $mailer
     * @return Response
     */
    public function ParticipateQuiz(Quiz $quiz , Request $request , Reponse $reponse , MailerInterface $mailer): Response
    {
        $suggestedQuestions = $quiz->getQuestions();
        $score=0;
        if ($request->request->has('reponse')){
            foreach ($_POST["question"] as $questionId){
                if (isset($_POST["reponse"][$questionId])){
                    $questions =$_POST["reponse"];

                    foreach($questions as $key => $item){
                        // recuperer la question
                        $question  =$this->em->getRepository(Question::class)->find($key);

                        foreach($item as $k=>$value){
                            //  recuperer reponse correct de chaque question
                            $userResponse = $this->em->getRepository(Reponse::class)->find($k);
                            $isCorrect= $userResponse->getIsCorrect();
                            // if id reponse correcte de quesstion = id de réponse de user , calcul de score
                            if($isCorrect==true){
                                $score+=$question->getScore();
                            }
                        }
                    }
//                    dd($score);
                }
            }
        }
        return $this->render('quiz/quiz_participate.html.twig', [
            'quiz' => $quiz,
            'suggestedQuestions' => $suggestedQuestions,
        ]);
    }


    /**
     * @Route("/quiz-details/{id}", name="details")
     * @param Quiz $quiz
     * @return Response
     */
    public function showQuizDetail(Quiz $quiz): Response
    {
        $suggestedUsers = $this->em->getRepository(User::class)->getUsersByQuizSkills($quiz);
//        dd($suggestedUsers);
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
    public function create(Request $request ): Response
    {
        $quiz = new Quiz();
        $form = $this->createForm(QuizType::class,$quiz);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
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
     * @ParamConverter("quiz", options={"mapping": {"quizId": "id"}})
     * @Route("/edit/{quizId}" ,name="edit_quiz")
     */
    public function edit(Request $request, Quiz $quiz): Response
    {
        //controle d'acces
        if (!$this->isGranted("ROLE_ADMIN")){
            return $this->render('pages/404.html.twig');
        }

        $form = $this->createForm(QuizType::class, $quiz);
//        $nbquestion = $quiz->getNombrequestion();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            foreach ($quiz->getQuestions() as $quizQuestion){
                $quiz->removeQuestion($quizQuestion);
            }
            $questions = $this->em->getRepository(Question::class)->getQuestionByQuiz($quiz);
            foreach ($questions as $quizQuestion){
                $quiz->addQuestion($quizQuestion);
            }
            $this->em->persist($quiz);
            $this->em->flush();
            $this->addFlash('success' , 'Quiz modifié avec succés!');
            return $this->redirectToRoute('quiz_index');
        }
        return $this->render('quiz/edit.html.twig', [
            'quiz' =>$quiz,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Quiz $quiz
     * @return Response
     * @package App\Controller
     * @Route("/moreInfo/{id}" ,name="moreInfo_quiz")
     */
    public function moreInfo(Request $request, Quiz $quiz)
    {
        //controle d'acces
        if (!$this->isGranted("ROLE_ADMIN")){
            return $this->render('pages/404.html.twig');
        }
        $allQuestions = $this->em->getRepository(Question::class)->getAllQuestionByQuiz($quiz);
        return $this->render('quiz/more_Information.html.twig', [
            'quiz' =>$quiz,
            'allQuestions' => $allQuestions,
        ]);
    }

    /**
     * @param Quiz $quiz
     * @return Response
     * @package App\Controller
     * @Route("/quiz/{id}" , name="update_quiz")
     */
    public function update(Quiz $quiz): Response
    {
        foreach ($quiz->getQuestions() as $quizQuestion){
            $quiz->removeQuestion($quizQuestion);
        }

        $questions = $this->em->getRepository(Question::class)->getQuestionByQuiz($quiz);
        foreach ($questions as $quizQuestion){
            $quiz->addQuestion($quizQuestion);
        }
        $this->em->persist($quiz);
        $this->em->flush();
        $this->addFlash('success' , 'Quiz modifié avec succés!');
        return $this->redirectToRoute('quiz_index');
    }

    /**
     * @Route("/delete/{id}", name="delete_quiz")
     * @param Quiz $quiz
     * @return Response
     */
    public function delete(Quiz $quiz): Response
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
