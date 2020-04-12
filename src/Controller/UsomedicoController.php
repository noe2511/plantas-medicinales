<?php

namespace App\Controller;

use App\Entity\Usomedico;
use App\Entity\Planta;
use App\Entity\Producto;
use App\Form\UsomedicoType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/usomedico")
 *  @IsGranted("ROLE_ADMIN")
 */
class UsomedicoController extends AbstractController
{
    /**
     * @Route("/", name="usomedico_index", methods={"GET"})
     */
    public function index(): Response
    {
        $usomedicos = $this->getDoctrine()
            ->getRepository(Usomedico::class)
            ->findAll();

        return $this->render('usomedico/index.html.twig', [
            'usomedicos' => $usomedicos,
        ]);
    }

    /**
     * @Route("/usomedico/nuevoUsoMedico", name="nuevoUsoMedico", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $usomedico = new Usomedico();
        $form = $this->createForm(UsomedicoType::class, $usomedico);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($usomedico);
            $entityManager->flush();

            return $this->redirectToRoute('usomedico_index');
        }

        return $this->render('usomedico/nuevoUsoMedico.html.twig', [
            'usomedico' => $usomedico,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/usomedico/show/{idusomedico}", name="usomedico_show", methods={"GET"})
     */
    public function show(Usomedico $usomedico): Response
    {
        return $this->render('usomedico/show.html.twig', [
            'usomedico' => $usomedico,
        ]);
    }

    /**
     * @Route("/usomedico/editarUsoMedico/{idusomedico}", name="editarUsoMedico", methods={"GET","POST"})
     */
    public function edit(Request $request, Usomedico $usomedico): Response
    {
        $form = $this->createForm(UsomedicoType::class, $usomedico);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('usomedico_index');
        }

        return $this->render('usomedico/editarUsoMedico.html.twig', [
            'usomedico' => $usomedico,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/usomedico/borrarUsoMedico/{idusomedico}", name="borrarUsoMedico", methods={"DELETE"})
     */
    public function delete(Request $request, Usomedico $usomedico): Response
    {
        $plantas = $this->getDoctrine()->getRepository(Planta::class)->getPlantasUnUsoMedico($usomedico);
        $productos = $this->getDoctrine()->getRepository(Producto::class)->getProductosUnUsoMedico($usomedico);

        if (($plantas != null || $plantas != "") && ($productos != null || $productos != "")) {
            return $this->render("usomedico/errorBorrar.html.twig");
        } else {
            if ($this->isCsrfTokenValid('borrar' . $usomedico->getIdusomedico(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($usomedico);
                $entityManager->flush();
            }
        }

        return $this->redirectToRoute('usomedico_index');
    }
}
