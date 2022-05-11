<?php

namespace App\Controller;

use App\Entity\Leave;
use App\Entity\Search;
use App\Entity\User;
use App\Form\SearchType;
use App\Repository\LeaveRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class UserController
 * @package App\Controller
 * @Route("/leave")
 */
class LeaveController extends AbstractController
{
    private $em;
    private $repository;

    public function __construct(EntityManagerInterface $em, LeaveRepository $leaveRepository)
    {
        $this->em = $em;
        $this->repository= $leaveRepository;
    }

    #[Route('/', name: 'leave_index')]
    public function index(PaginatorInterface $paginator , Request $request): Response
    {
        // filter
        $search = new Search();
        $form = $this->createForm(SearchType::class,$search);
        $form->handleRequest($request);

//        $user = $this->getUser();
////        if ($this->isGranted('ROLE_ADMIN')){
////            $leave = $this->repository->getLeave($search);
////        }else{
////            $leave = $user->getLeaves();
////        }

        $leave = $paginator->paginate(
            $leave = $this->repository->getLeave($search),
            /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            3 /*limit per page*/
        );
        return $this->render('leave/index.html.twig', [
            'leaves' => $leave,
            'form' => $form->createView()
        ]);
    }


    /**
     * @param Request $request
     * @return Response
     * @package App\Controller
     * @Route("/create" ,name="create_leave")
     */
    public function createLeave(Request $request)
    {
        $leave = new Leave();
        $form = $this->createForm(\App\Form\LeaveType::class, $leave);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $this->em->persist($leave);
            $this->em->flush();
            $this->addFlash('success' , 'demande crée avec succés!');
            return $this->redirectToRoute('leave_index');
        }

        return $this->render('leave/create.html.twig', [
            'leave' =>$leave,
            'form' => $form->createView()
        ]);

    }

    /**
     * @param Request $request
     * @param Leave $leave
     * @return Response
     * @throws TransportExceptionInterface
     * @package App\Controller
     * @Route("/update/{id}" ,name="update_leave")
     */
    public function UpdateLeave(Request $request, Leave $leave, MailerInterface $mailer )
    {
        $form = $this->createForm(\App\Form\LeaveType::class, $leave, ['update'=>true]);
//        $form->remove(startDate);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->em->persist($leave);
            $this->em->flush();
            // envoie de mail pour noitifier user de l'etat de sa demande
            if ($leave->getStatus() == Leave::STATUS_ACCEPTER || $leave->getStatus() == Leave::STATUS_REFUSER){
                $email = (new Email())
                    ->from($leave->getUserTo()->getEmail())
                    ->to($leave->getUserFrom()->getEmail())
                    ->subject('Réponse à la demande ')
                    ->text('Sending emails is fun again!')
                    ->html('Votre demande a été' . $leave->getStatus())
                ;
                $mailer->send($email);
            }
            $this->addFlash('success' , 'demande modifié avec succés!');
            return $this->redirectToRoute('leave_index');
        }
        return $this->render('leave/update.html.twig', [
            'leave' =>$leave,
            'form' => $form->createView(),
        ]);

    }

    /**
     * @param Request $request
     * @param Leave $leave
     * @param MailerInterface $mailer
     * @return Response
     * @package App\Controller
     * @Route("/edit/{id}" ,name="edit_leave")
     */
    public function editLeave(Request $request, Leave $leave , MailerInterface $mailer )
    {
        $form = $this->createForm(\App\Form\LeaveType::class, $leave );
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $this->em->persist($leave);
            $this->em->flush();

            $email = (new Email())
                ->from($leave->getUserFrom()->getEmail())
                ->to($leave->getUserTo()->getEmail())
                ->subject('Modification de la demande ')
                ->text('Sending emails is fun again!')
                ->html(' Cette demande a été modifié  ' . '<br>'
                    .' date début : '  . $leave->getStartDate()->format('d-m-Y')
                    . '<br>'.' date fin : ' . $leave->getEndDate()->format('d-m-Y')
                )
            ;
            $mailer->send($email);

            $this->addFlash('success' , 'demande modifié avec succés!');
            return $this->redirectToRoute('leave_index');
        }
        return $this->render('leave/edit.html.twig', [
            'leave' =>$leave,
            'form' => $form->createView(),
        ]);

    }

    /**
     * @Route("/delete/{id}", name="delete_leave")
     * @param Leave $leave
     * @return Response
     */
    public function deleteLeave(Leave $leave)
    {
        $this->em->remove($leave);
        $this->em->flush();
        $this->addFlash('success' , 'demande supprimé avec succés');
        return $this->redirectToRoute('leave_index');
    }
}
