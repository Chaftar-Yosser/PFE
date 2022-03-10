<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Class UserController
 * @package App\Controller
 * @Route("/user")
 */

class UserController extends AbstractController
{
    private $em;
    private $repository;

    public function __construct(EntityManagerInterface $em, UserRepository $userRepository)
    {
        $this->em = $em;
        $this->repository= $userRepository;
    }

    /**
     * @Route("/" , name="user_index")
     * @return Response
     */
    public function index(): Response
    {
        $users = $this->repository->findAll();
        return $this->render('user/index.html.twig',[
            'users' => $users,
        ]);
    }

    /**
     * @param Request $request
     * @param $slugger
     * @return Response
     * @package App\Controller
     * @Route("/create" ,name="create_user")
     */
    public function createUser(Request $request, SluggerInterface $slugger)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $user = $form->getData();
            $image = $form->get('image')->getData();
            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();

                try {
                    $image->move(
                        $this->getParameter('image_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Image cannot be saved.');
                }
                $user->setImage($newFilename);
            }

            $this->em->persist($user);
            $this->em->flush();
            $this->addFlash('success' , 'User crée avec succés!');
            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/create.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * @param User $user
     * @param Request $request
     * @param $slugger
     * @return Response
     * @package App\Controller
     * @Route("/edit/{id}" ,name="edit_user")
     */
    public function edit(User $user , Request $request, SluggerInterface $slugger)
    {
        $originalImage = $user->getImage();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $image = $form->get('image')->getData();
            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();

                try {
                    $image->move(
                        $this->getParameter('image_directory'),
                        $newFilename
                    );
                    //delete old image
                    if($originalImage)
                        unlink($this->getParameter('image_directory')."/".$originalImage);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Image cannot be saved.');
                }
                $user->setImage($newFilename);
            }

            $this->em->persist($user);
            $this->em->flush();
            $this->addFlash('success' , 'User modifié avec succés');
            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' =>$user,
            'form' =>$form->createView()
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete_user")
     * @param User $user
     *
     */
    public function delete(User $user , Request $request)
    {
        $this->em->remove($user);
        $this->em->flush();
        $this->addFlash('success' , 'user supprimé avec succés');
        return $this->redirectToRoute('user_index');
    }
}
