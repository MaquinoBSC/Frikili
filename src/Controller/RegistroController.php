<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistroController extends AbstractController
{
    /**
     * @Route("/registro", name="registro")
     */
    public function index(Request $request, UserPasswordEncoderInterface $passwordEncoder, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user= new User();
        $form= $this->createForm(UserType::class, $user);
        
        //Determinamos si el formulario fue enviado
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            //Manejador de entidades
            $entityManager= $this->getDoctrine()->getManager();
            
            //$user->setPassword($passwordEncoder->encodePassword($user, $form['password']->getData()));
            $user->setPassword($passwordHasher->hashPassword($user, $form['password']->getData()));
            
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('exito', User::REGISTRO_EXITOSO);
            return $this->redirectToRoute('registro');
        }

        //render apunta directamente a la carpeta template
        return $this->render('registro/index.html.twig', [
            'controller_name' => 'RegistroController',
            'registerForm'=> $form->createView()
        ]);
    }
}
