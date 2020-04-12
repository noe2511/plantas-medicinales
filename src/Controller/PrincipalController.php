<?php

namespace App\Controller;


use App\Entity\Planta;
use App\Entity\Colorflor;
use App\Entity\Parteutil;
use App\Entity\User;
use App\Entity\Usomedico;
use App\Form\PlantaType;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\PlantaRepository;
use PhpParser\Node\Expr\Cast\String_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\SubmitButtonBuilder;

class PrincipalController extends AbstractController
{


    /**
     * @Route("/", name="principal")
     *
     * @return void
     */
    /*public function listadoPlantas()
    {
        //Coger datos de la BD
        $em = $this->getDoctrine()->getManager();
        $plantas = $em->getRepository(Planta::class)->findAll();

        return $this->render("principal/index.html.twig", array('plantas' => $plantas));
    }*/

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
            5 /*limit per page*/
        );

        // parameters to template
        return $this->render('planta/listadoPaginado.html.twig', ['paginacion' => $paginacion]);
    }

    /**
     * @Route("plantas/filtros/{color}/{parteutil}/{and}")
     *
     * @param [type] $color
     * @return void
     */
    /* public function ProbarConsultas($color, $parteutil = null, $and = true)
    {
        $em = $this->getDoctrine()->getManager();
        $plantascolor = $em->getRepository(Planta::class)->getPlantasColorFlor($color);
        var_dump($plantascolor);
        return new Response("hola");
    }*/

    /**
     * @Route("/", name="principal")
     *
     * @return void
     */
    public function listadoPlantasPaginado(PaginatorInterface $paginator, Request $request, PlantaRepository $prepository)
    {
        $em = $this->getDoctrine()->getManager();

        if (isset($_GET["colorFiltrado"])) {
            if (isset($_GET["parteUtil"])) {
                if (sizeof($_GET["parteUtil"]) == 1 && !isset($_GET["usoMedico"])) {
                    $plantas = $prepository->getPlantasFiltros($_GET["colorFiltrado"], $_GET["parteUtil"][0]);
                } else if (isset($_GET["parteUtil"]) && isset($_GET["usoMedico"])) {
                    $plantas = $prepository->getPlantasTodosFiltros($_GET["colorFiltrado"], $_GET["parteUtil"], $_GET["usoMedico"]);
                } else {
                    $plantas = $prepository->getPlantasVariasPartesUtilesColor($_GET["colorFiltrado"], $_GET["parteUtil"]);
                }
            } else {
                $plantas = $prepository->getPlantasColorFlor($_GET["colorFiltrado"]);
            }
        } else if (isset($_GET["parteUtil"])) {
            if (sizeof($_GET["parteUtil"]) == 1 && isset($_GET["colorFiltrado"]) && !isset($_GET["usoMedico"])) {
                $plantas = $prepository->getPlantasFiltros($_GET["colorFiltrado"], $_GET["parteUtil"]);
            } else if (sizeof($_GET["parteUtil"]) > 1 && !isset($_GET["usoMedico"])) {
                $plantas = $prepository->getPlantasVariasPartesUtiles($_GET["parteUtil"]);
            } else if (!isset($_GET["usoMedico"])) {
                $plantas = $prepository->getPlantasParteUtil($_GET["parteUtil"]);
            } else if (!isset($_GET["colorFiltrado"]) && isset($_GET["usoMedico"])) {
                $plantas = $prepository->getPlantasParteUtilUsoMedico($_GET["parteUtil"], $_GET["usoMedico"]);
            }
        } else if (isset($_GET["usoMedico"])) {
            if (!isset($_GET["colorFiltrado"]) && !isset($_GET["parteUtil"])) {
                $plantas = $prepository->getPlantasUsoMedico($_GET["usoMedico"]);
            } else if (isset($_GET["colorFiltrado"]) && !isset($_GET["parteUtil"])) {
                $plantas = $prepository->getPlantasColorUsoMedico($_GET["colorFiltrado"], $_GET["usoMedico"]);
            }
        } else {
            $plantas = $em->getRepository(Planta::class)->findAll();
        }

        $colorflors = $this->getDoctrine()
            ->getRepository(Colorflor::class)
            ->findAll();

        $partesUtiles = $this->getDoctrine()
            ->getRepository(Parteutil::class)
            ->findAll();

        $usoMedico = $this->getDoctrine()
            ->getRepository(Usomedico::class)
            ->findAll();

        $paginacion = $paginator->paginate(
            $plantas, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            6 /*limit per page*/
        );

        //parameters to template
        return $this->render("principal/index.html.twig", [
            'paginacion' => $paginacion,
            'colorflors' => $colorflors,
            'partesUtiles' => $partesUtiles,
            'plantas' => $plantas,
            'usoMedico' => $usoMedico
        ]);
    }

    /**
     * @Route("usuario/crear", name="crearUsuario")
     *
     * @param UserPasswordEncoder $encoder
     * @return void
     */
    public function crearUsuario(UserPasswordEncoderInterface $encoder)
    {
        $usuario = new User();
        $contrasena = "1234";
        $encoded = $encoder->encodePassword($usuario, $contrasena);
        $usuario->setPassword($encoded);
        $usuario->setEmail("noe25111995@gmail.com");
        $usuario->setRoles(['ROLE_ADMIN']);
        $em = $this->getDoctrine()->getManager();
        $em->persist($usuario);
        $em->flush();

        return $this->redirectToRoute("principal");
    }
}
