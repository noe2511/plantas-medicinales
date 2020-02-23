<?php

namespace App\Controller;

use App\Entity\Planta;
use App\Form\PlantaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class PlantaController extends AbstractController
{
    /**
     * @Route("/planta/nueva", name="plantanueva")
     *
     * @return void
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
            }


            //Guardar en la base de datos
            $em = $this->getDoctrine()->getManager(); //em viene de entity manager
            $em->persist($nuevaPlanta); //persist actualiza la memoria
            $em->flush(); //flush guarda en la BD
        }

        return $this->render("planta/nueva.html.twig", ["form" => $form->createView()]);
    }

    /**
     * @Route("/planta/listadoPlantas", name="listadoPlantas")
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
     *
     * @param integer $id
     * @return void
     */
    public function modificarPlanta(int $id, Request $request) //id tiene que ser el mismo nombre que el de la plantilla
    {
        $em = $this->getDoctrine()->getManager();
        $planta = $em->getRepository(Planta::class)->find($id);

        $form = $this->createForm(PlantaType::class, $planta);
        $form->handleRequest($request); //Se encarga de recoger los datos del formulario

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute("listadoPlantas");
        }
        return $this->render("planta/nueva.html.twig", ["form" => $form->createView()]);
    }

    /**
     * @Route("planta/borrar/{id}", name="borra_planta")
     *
     * @param Planta $planta
     * @return void
     */
    public function borrarPlanta(Planta $planta)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($planta);
        $em->flush();

        return $this->redirectToRoute("listadoPlantas");
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
