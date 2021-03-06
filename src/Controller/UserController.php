<?php

namespace App\Controller;

use App\Entity\Search;
use App\Entity\User;
use App\Form\SearchType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
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
     *
     * @return Response
     */

    //@IsGranted("ROLE_ADMIN")
    public function index(  PaginatorInterface $paginator , Request $request): Response
    {
        //Filter
        $search = new Search();
        $form = $this->createForm(SearchType::class,$search);
        $form->handleRequest($request);

        //pagination
        $users = $paginator->paginate(
            $this->repository->getUser($search), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            3 /*limit per page*/
        );
        return $this->render('user/index.html.twig',[
            'users' => $users,
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param SluggerInterface $slugger
     * @param UserPasswordHasherInterface $passwordHasher
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     * @package App\Controller
     * @Route("/create" ,name="create_user")
     */
    public function createUser(Request $request, SluggerInterface $slugger, UserPasswordHasherInterface $passwordHasher)
    {
        //controle d'acces
        if (!$this->isGranted("ROLE_ADMIN")){
            return $this->render('pages/404.html.twig');
        }

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            //set password hasher
            $hashedPassword = $passwordHasher->hashPassword($user,$user->getPassword());
            $user->setPassword($hashedPassword);

            $user = $form->getData();
            $image = $form->get('image')->getData();
            if ($image) {
                //set image
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
            $this->addFlash('success' , 'User cr??e avec succ??s!');
            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/create.html.twig', [
            'user' =>$user,
            'form' => $form->createView()
        ]);
    }


    /**
     * @param User $user
     * @param Request $request
     * @param SluggerInterface $slugger
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     * @package App\Controller
     * @Route("/edit/{id}" ,name="edit_user")
     */
    public function edit(User $user , Request $request, SluggerInterface $slugger ,  UserPasswordHasherInterface $passwordHasher)
    {
        //controle d'acces
        if (!$this->isGranted("ROLE_ADMIN")){
            return $this->render('pages/404.html.twig');
        }

        $originalImage = $user->getImage();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        $oldImage = $user->getImage();
        if($form->isSubmitted() && $form->isValid())
        {
            $hashedPassword = $passwordHasher->hashPassword($user,$user->getPassword());
            $user->setPassword($hashedPassword);

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
                    if($originalImage && file_exists($this->getParameter('image_directory')."/".$originalImage))
                        unlink($this->getParameter('image_directory')."/".$originalImage);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Image cannot be saved.');
                }
                $user->setImage($newFilename);
            }

            $this->em->persist($user);
            $this->em->flush();
            $this->addFlash('success' , 'User modifi?? avec succ??s');
            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' =>$user,
            'form' =>$form->createView(),
            'oldImage' =>$oldImage,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete_user")
     * @param User $user
     * @IsGranted("ROLE_ADMIN")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(User $user)
    {
        $this->em->remove($user);
        $this->em->flush();
        $this->addFlash('success' , 'user supprim?? avec succ??s');
        return $this->redirectToRoute('user_index');
    }
}
