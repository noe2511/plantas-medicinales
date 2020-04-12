<?php

namespace App\Controller;

use App\Entity\Planta;
use App\Form\PlantaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class PlantaController extends AbstractController
{
    /**
     * @Route("/planta/nueva", name="plantanueva")
     *  @IsGranted("ROLE_ADMIN")
     * 
     */
    public function nuevaPlanta(Request $request)
    {
        $nuevaPlanta = new Planta();
        $form = $this->createForm(PlantaType::class, $nuevaPlanta);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ficheroimagen = $form['ficheroimagen']->getData();
            if ($ficheroimagen) {
                $nombrearchivo = $ficheroimagen->getClientOriginalName();
                $ficheroimagen->move(
                    $this->getParameter('directorio_imagenes'),
                    $nombrearchivo
                );
                $nuevaPlanta->setImagen($nombrearchivo);
            } else {
                $nuevaPlanta->setImagen("no_disponible.png");
            }


            //Guardar en la base de datos
            $em = $this->getDoctrine()->getManager(); //em viene de entity manager
            $em->persist($nuevaPlanta); //persist actualiza la memoria
            $em->flush(); //flush guarda en la BD

            return $this->redirect($request->getUri());
        }

        return $this->render("planta/nueva.html.twig", ["form" => $form->createView()]);
    }

    /**
     * @Route("/planta/listadoPlantas", name="listadoPlantas")
     *  @IsGranted("ROLE_ADMIN")
     *
     * @return void
     */
    public function listadoPlantas()
    {
        //Coger datos de la BD
        $em = $this->getDoctrine()->getManager();
        $plantas = $em->getRepository(Planta::class)->findAll();

        return $this->render("planta/listadoPlantas.html.twig", array('plantas' => $plantas));
    }

    /**
     * @Route("planta/modificar/{id}", name="modifica_planta")
     *  @IsGranted("ROLE_ADMIN")
     *
     * @param integer $id
     * @return void
     */
    public function modificarPlanta(Planta $planta, Request $request)
    {
        $form = $this->createForm(PlantaType::class, $planta);
        $form->handleRequest($request); //Se encarga de recoger los datos del formulario

        if ($form->isSubmitted() && $form->isValid()) {

            $ficheroimagen = $form['ficheroimagen']->getData();
            if ($ficheroimagen) {
                $nombrearchivo = $ficheroimagen->getClientOriginalName();
                $ficheroimagen->move(
                    $this->getParameter('directorio_imagenes'),
                    $nombrearchivo
                );
                $planta->setImagen($nombrearchivo);
            }

            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute("listado_paginado");
        }

        return $this->render("planta/nueva.html.twig", ["form" => $form->createView()]);
    }

    /**
     * @Route("planta/borrar/{id}", name="borra_planta")
     *  @IsGranted("ROLE_ADMIN")
     *
     * @param Planta $planta
     * @return void
     */
    public function borrarPlanta(Planta $planta)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($planta);
        $em->flush();

        return $this->redirectToRoute("listado_paginado");
    }

    /**
     * @Route("/planta/detalle/{idplanta}", name="detallesPlanta")
     */
    public function show(Planta $planta)
    {
        return $this->render('planta/detalle.html.twig', [
            'planta' => $planta,
        ]);
    }
}
