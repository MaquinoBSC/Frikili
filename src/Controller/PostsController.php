<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Form\PostsType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostsController extends AbstractController
{
    /**
     * @Route("/registrar-posts", name="registrar-posts")
     */
    public function index(Request $request): Response
    {
        $posts= new Posts();
        $form= $this->createForm(PostsType::class, $posts);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $user= $this->getUser();//Obtenemos el usuario actualmente logeado
            $posts->setUser($user);
            $em= $this->getDoctrine()->getManager();
            $em->persist($posts);
            $em->flush();

            return $this->redirectToRoute('dashboard');
        }
        return $this->render('posts/index.html.twig', [
            'controller_name' => 'PostsController',
            'postsForm'=> $form->createView()
        ]);
    }
}
