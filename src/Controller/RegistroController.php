<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegistroController extends AbstractController
{
    /**
     * @Route("/registro", name="registro")
     */
    public function index(Request $request): Response
    {
        $user= new User();
        $user->setBaneado(false);
        $user->setRoles(['ROLE_USER']);//Asignar Rol, tambien tenemos ROLE_ADMIN
        $form= $this->createForm(UserType::class, $user);

        //Determinamos si el formulario fue enviado
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            //Manejador de entidades
            $entityManager= $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('exito', "Se ha registrado exitosamente");
            return $this->redirectToRoute('registro');
        }

        //render apunta directamente a la carpeta template
        return $this->render('registro/index.html.twig', [
            'controller_name' => 'RegistroController',
            'registerForm'=> $form->createView()
        ]);
    }
}
