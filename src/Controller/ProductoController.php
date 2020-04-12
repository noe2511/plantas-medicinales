<?php

namespace App\Controller;

use App\Form\ProductoType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Producto;
use App\Entity\UsoMedico;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\ProductoRepository;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/producto")
 */
class ProductoController extends AbstractController
{
    /**
     * @Route("/", name="producto_index", methods={"GET"})
     */
    public function index(): Response
    {
        $productos = $this->getDoctrine()
            ->getRepository(Producto::class)
            ->findAll();

        return $this->render('producto/index.html.twig', [
            'productos' => $productos,
        ]);
    }

    /**
     * @Route("/producto/nuevoProducto", name="nuevoProducto", methods={"GET","POST"})
     *  @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request): Response
    {
        $producto = new Producto();
        $form = $this->createForm(ProductoType::class, $producto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ficheroimagen = $form['ficheroimagen']->getData();
            if ($ficheroimagen) {
                $nombrearchivo = $ficheroimagen->getClientOriginalName();
                $ficheroimagen->move(
                    $this->getParameter('directorio_imagenes'),
                    $nombrearchivo
                );
                $producto->setImagen($nombrearchivo);
            } else {
                $producto->setImagen("no_disponible.png");
            }


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($producto);
            $entityManager->flush();

            return $this->redirectToRoute('producto_index');
        }

        return $this->render('producto/nuevoProducto.html.twig', [
            'producto' => $producto,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idproducto}", name="producto_show", methods={"GET"})
     */
    public function show(Producto $producto): Response
    {
        return $this->render('producto/show.html.twig', [
            'producto' => $producto,
        ]);
    }

    /**
     * @Route("/producto/editarProducto/{idproducto}", name="editarProducto", methods={"GET","POST"})
     *  @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, Producto $producto): Response
    {
        $form = $this->createForm(ProductoType::class, $producto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('producto_index');
        }

        return $this->render('producto/editarProducto.html.twig', [
            'producto' => $producto,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/producto/borrarProducto/{idproducto}", name="borrarProducto", methods={"DELETE"})
     *  @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Producto $producto): Response
    {
        if ($this->isCsrfTokenValid('delete' . $producto->getIdproducto(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($producto);
            $entityManager->flush();
        }

        return $this->redirectToRoute('producto_index');
    }

    /**
     * @Route("/producto/tienda", name="tienda")
     *
     * @return void
     */
    public function tiendaPaginada(PaginatorInterface $paginator, Request $request, ProductoRepository $prepository)
    {
        $em = $this->getDoctrine()->getManager();
        var_dump($_GET);
        if (isset($_GET["usoMedico"])) {
            $productos = $prepository->getProductosUsoMedico($_GET["usoMedico"]);
        } else {
            $productos = $em->getRepository(Producto::class)->findAll();
        }

        $usoMedico = $this->getDoctrine()
            ->getRepository(Usomedico::class)
            ->findAll();

        $paginacion = $paginator->paginate(
            $productos, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            3 /*limit per page*/
        );

        return $this->render("producto/tienda.html.twig", [
            'paginacion' => $paginacion,
            'productos' => $productos,
            'usoMedico' => $usoMedico
        ]);
    }
}
