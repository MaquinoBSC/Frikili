<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Form\PostsType;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class PostsController extends AbstractController
{
    /**
     * @Route("/registrar-posts", name="registrar-posts")
     */
    public function index(Request $request, SluggerInterface $slugger): Response
    {
        $posts= new Posts();
        $form= $this->createForm(PostsType::class, $posts);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $brochureFile = $form->get('foto')->getData();
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('photos_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                    throw new Exception("Upps ha ocurrido un error");
                }-

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $posts->setFoto($newFilename);
            }

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


    /**
     * @Route("/post/{id}", name="show-post")
     */
    public function ShowPost($id){
        $em= $this->getDoctrine()->getManager();
        $post= $em->getRepository(Posts::class)->find($id);

        return $this->render('posts/showPost.html.twig', [
            'post'=> $post
        ]);
    }


    /**
     * @Route("/mis-posts", name="mis-posts")
     */
    public function MisPost(){
        $em= $this->getDoctrine()->getManager();
        $user= $this->getUser();

        $posts= $em->getRepository(Posts::class)->findBy(['user'=> $user]);

        return $this->render('posts/misPosts.html.twig', [
            'misPosts'=> $posts
        ]);
    }
}
