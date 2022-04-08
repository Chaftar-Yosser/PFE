<?php
namespace App\Controller;


use App\Entity\Tasks;
use App\Repository\TasksRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $repository;

    public function __construct(UserRepository $userRepository)
    {
        $this->repository= $userRepository;
    }


    /**
     * @Route("/" , name="home_index")
     * @IsGranted("ROLE_USER")
     * @return Response
     */

    public function index(TasksRepository $tasksRepository , Request $request): Response
    {
//        dd($request->request->get('id'));
        $user = $this->getUser();
        if ($this->isGranted('ROLE_ADMIN')){
            $events = $tasksRepository->getTaskByDate();
        }else{
            $events = $tasksRepository->getTaskByDate($user);
        }
        // afficher les tâches sur une calendrier
        $tasks = [];
        /** @var Tasks $event */
        foreach($events as $event){
            switch ($event->getPriorite()){
                case "Elevée":
                    $color = "#B61702";
                    break;
                case "Normal":
                    $color = "#91E302";
                    break;
                case "Moyenne":
                    $color = "#FEC68B" ;
                    break;
                default:
                    $color = "#02BEF2";
                    break;
            }
            $tasks[] = [
                'id' => $event->getId(),
                'start' => $event->getDateDebut()->format('Y-m-d H:i:s'),
                'end' => $event->getDateFin()->format('Y-m-d H:i:s'),
                'title' => $event->getTaskName(),
                'status' => $event->getStatus(),
                'backgroundColor' => $color,
                'editUrl' => $this->generateUrl('edit_task', ['id'=>$event->getId()] )
            ];
        }
        $data = json_encode($tasks);
        $users = $this->repository->findAll();
        return $this->render('pages/home.html.twig', compact('data', 'events', 'users'));
    }

    public function notfound(): Response
    {
        return $this->render('pages/404.html.twig');
    }
}