<?php

namespace App\Controller;

use App\Entity\Colorflor;
use App\Form\ColorflorType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


/**
 * @Route("/colorflor")
 *  @IsGranted("ROLE_ADMIN")
 */
class ColorflorController extends AbstractController
{
    /**
     * @Route("/index", name="colorflor_index", methods={"GET"})
     */
    public function index(): Response
    {
        $colorflors = $this->getDoctrine()
            ->getRepository(Colorflor::class)
            ->findAll();

        return $this->render('colorflor/index.html.twig', [
            'colorflors' => $colorflors,
        ]);
    }

    /**
     * @Route("/colorflor/nuevoColor", name="nuevoColor", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $colorflor = new Colorflor();
        $form = $this->createForm(ColorflorType::class, $colorflor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($colorflor);
            $entityManager->flush();

            return $this->redirect($request->getUri());
        }

        return $this->render('colorflor/nuevoColor.html.twig', [
            'colorflor' => $colorflor,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("colorflor/show/{idcolorflor}", name="colorflor_show", methods={"GET"})
     */
    public function show(Colorflor $colorflor): Response
    {
        return $this->render('colorflor/show.html.twig', [
            'colorflor' => $colorflor,
        ]);
    }

    /**
     * @Route("/colorflor/editarColor/{idcolorflor}", name="editarColor", methods={"GET","POST"})
     */
    public function edit(Request $request, Colorflor $colorflor): Response
    {
        $form = $this->createForm(ColorflorType::class, $colorflor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('colorflor_index');
        }

        return $this->render('colorflor/editarColor.html.twig', [
            'colorflor' => $colorflor,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/colorflor/borrarColor/{idcolorflor}", name="borrarColor", methods={"DELETE"})
     */
    public function delete(Request $request, Colorflor $colorflor): Response
    {
        if ($this->isCsrfTokenValid('delete' . $colorflor->getIdcolorflor(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();

            try {
                $entityManager->remove($colorflor);
                $entityManager->flush();
            } catch (\Doctrine\DBAL\DBALException $e) {
                $exception_message = $e->getPrevious()->getCode();
                $colorflor = "color";
                return $this->render("colorflor/errorBorrar.html.twig");
            }
        }

        return $this->redirectToRoute('colorflor_index');
    }
}
