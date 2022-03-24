<?php
namespace App\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/" , name="home_index")
     * @IsGranted("ROLE_USER")
     * @return Response
     */

    public function index(): Response
    {
        return $this->render('pages/home.html.twig');
    }

    public function notfound(): Response
    {
        return $this->render('pages/404.html.twig');
    }
}