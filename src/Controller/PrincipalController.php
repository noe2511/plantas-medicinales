<?php

namespace App\Controller;

use App\Entity\Planta;
use App\Form\PlantaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;

class PrincipalController extends AbstractController
{


    /**
     * @Route("/", name="principal")
     *
     * @return void
     */
    public function listadoPlantas()
    {
        //Coger datos de la BD
        $em = $this->getDoctrine()->getManager();
        $plantas = $em->getRepository(Planta::class)->findAll();

        return $this->render("principal/index.html.twig", array('plantas' => $plantas));
    }

    /**
     * @Route("/saludo",name="saludo")
     *
     * @return void
     */
    public function saludo()
    {
        return $this->render('principal/saludo.html.twig');
    }

    public function crear_menu_izquierdo()
    {
        $colores = ["Rojo", "Blanco", "Amarillo"];
        return $this->render('shared/menu_izquierdo.html.twig', ["colores" => $colores]);
    }

    /**
     * @Route("/crearformulario", name="crear_formulario")
     *
     * @return void
     */
    public function crear_formulario(Request $request)
    {
        //Valores iniciales
        /*$valores_iniciales = ["Nombre" => "Pepe"];

        $formulario = $this->createFormBuilder($valores_iniciales)
            ->add("Nombre", TextType::class, ["constraint" => [new NotBlank(["message"
            => "No puede estar vacío"])]])->add("Edad", NumberType::class)->getForm();*/

        $tomillo = new Planta();
        $tomillo->setNombre("Tomillo");
        $formulario = $this->createForm(PlantaType::class);

        //Recoger datos si submit
        $formulario->handleRequest($request);

        if ($formulario->isSubmitted() && $formulario->isValid()) {
            $datos = $formulario->getData();
            var_dump($datos);
        }

        return $this->render("principal/formulario.html.twig", ["formulario" => $formulario->createView()]);
    }

    /**
     * @Route("/listado/paginado", name="listado_paginado")
     *
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return void
     */
    public function listAction(PaginatorInterface $paginator, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $plantas = $em->getRepository(Planta::class)->findAll();
        $paginacion = $paginator->paginate(
            $plantas, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            3 /*limit per page*/
        );

        // parameters to template
        return $this->render('planta/listadoPaginado.html.twig', ['paginacion' => $paginacion]);
    }



    /**
     * @Route("plantas/filtros/{color}/{parteutil}/{and}")
     *
     * @param string $color
     * @return void
     */
    /* public function ProbarConsultas($color, $parteUtil, $and)
    {
        $em = $this->getDoctrine()->getManager();
        $plantasColor = $em->getRepository(Planta::class)->getPlantaFiltros($color);
        var_dump($plantasColor);
        return new Response("hola");
    }*/
}
