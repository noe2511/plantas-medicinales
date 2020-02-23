<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

class UsuarioController extends AbstractController
{
    /**
     * @Route("/usuario/crear", name="crear_usuario")
     *
     * @param UserPasswordEncoder $encoder
     * @return void
     */
    public function crearUsuario(UserPasswordEncoder $encoder)
    {
        $usuario = new User();
        $contrasena = "1234";
        $encoded = $encoder->encodePassword($usuario, $contrasena);
        $usuario->setPassword($encoded);
        $usuario->setEmail("aiv@gmail.com");

        $em = $this->getDoctrine()->getManager();
        $em->persist($usuario);
        $em->flush();

        return $this->redirectToRoute("principal");
    }
}
