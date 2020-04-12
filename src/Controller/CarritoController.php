<?php

namespace App\Controller;

use App\Entity\Producto;
use Spipu\Html2Pdf\Html2Pdf;
use Swift_Attachment;
use Swift_Mailer;
use Swift_SmtpTransport;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

//header("Content-Disposition: attachment; filename=pdf_generado.pdf");

/**
 * @Route("/carrito")
 */
class CarritoController extends AbstractController
{
    /**
     * @Route("/carrito", name="carrito")
     */
    public function mostrarCarrito(SessionInterface $sesion)
    {
        $productos = [];
        $carrito = $sesion->get("carrito");

        if (is_null($carrito)) {
            $carrito = array();
            $sesion->set("carrito", $carrito);
        }

        foreach ($carrito as $codigo => $cantidad) {
            $producto = $this->getDoctrine()->getRepository(Producto::class)->find((int) $codigo);
            $elem = [];
            $elem["codProd"] = $producto->getIdproducto();
            $elem["nombre"] = $producto->getNombre();
            $elem["descripcion"] = $producto->getDescripcion();
            $elem["unidades"] = implode($cantidad);
            $productos[] = $elem;
        }
        return $this->render("carrito/carrito.html.twig", array("productos" => $productos));
    }

    /**
     * @Route("/aniadir", name="aniadir")
     */
    public function aniadir(SessionInterface $session)
    {
        var_dump($_POST);
        $id = $_POST["cod"];
        $unidades = $_POST["unidades"];
        $carrito = $session->get("carrito");

        if (is_null($carrito)) {
            $carrito = array();
        }

        if (isset($carrito[$id])) {
            $carrito[$id]["unidades"] += intval($unidades);
        } else {
            $carrito[$id]["unidades"] = intval($unidades);
        }
        $session->set("carrito", $carrito);

        return $this->redirectToRoute("carrito");
    }

    /**
     * @Route("/eliminar", name="eliminar")
     */
    public function eliminar(SessionInterface $session)
    {
        $id = $_POST["cod"];
        $unidades = $_POST["unidades"];
        $carrito = $session->get("carrito");

        if (is_null($carrito)) {
            $carrito = array();
        }

        if (isset($carrito[$id])) {
            $carrito[$id]["unidades"] -= intval($unidades);
            if ($carrito[$id]["unidades"] <= 0) {
                unset($carrito[$id]);
            }
        }

        $session->set("carrito", $carrito);
        return $this->redirectToRoute("carrito");
    }

    /**
     * @Route("/realizarPedido", name="realizarPedido")
     */
    public function realizarPedido(SessionInterface $session, \Swift_Mailer $mailer)
    {
        $em = $this->getDoctrine()->getManager();
        $carrito = $session->get("carrito");

        if (is_null($carrito) || count($carrito) == 0) {
            return $this->render(
                "carrito/carrito.html.twig",
                array("error" => 1)
            );
        } else {
            foreach ($carrito as $codigo => $cantidad) {
                $producto = $this->getDoctrine()->getRepository(Producto::class)->find($codigo);

                $elem = [];
                $elem["codProd"] = $producto->getIdproducto();
                $elem["nombre"] = $producto->getNombre();
                $elem["descripcion"] = $producto->getDescripcion();
                $elem["unidades"] = implode($cantidad);

                $productos[] = $elem;
            }
            $session->set("carrito", array());
            $transport = (new Swift_SmtpTransport('smtp.gmail.com', 587, 'tls'))
                ->setUsername('noe25111995@gmail.com')
                ->setPassword('Admin_0404');

            $mailer = new Swift_Mailer($transport);

            $message = (new \Swift_Message())
                ->setFrom(["noe25111995@gmail.com" => "Sistema de pedidos"])
                ->setTo($_GET["correo"])
                ->setSubject("pedido confirmado")
                ->setBody(
                    $this->renderView(
                        "carrito/correo.html.twig",
                        ["productos" => $productos]
                    ),
                    "text/html"
                );



            $html2pdf = new Html2Pdf();
            $html2pdf->writeHTML(
                $this->renderView(
                    "carrito/correo.html.twig",
                    ["productos" => $productos]
                ),
                "text/html"
            );

            $html2pdf->Output('document_name.pdf', 'D');


            $mailer->send($message);



            return $this->render("carrito/pedido.html.twig", array("error" => 0, "productos" => $productos));
        }
    }
}
