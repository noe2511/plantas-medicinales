<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Producto
 *
 * @ORM\Table(name="producto")
 * @ORM\Entity
 */
class Producto
{
    /**
     * @var int
     *
     * @ORM\Column(name="idProducto", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idproducto;

    /**
     * @var string
     *
     * @ORM\Column(name="Nombre", type="string", length=100, nullable=false)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="Descripcion", type="string", length=500, nullable=false)
     */
    private $descripcion;

    /**
     * @var float
     *
     * @ORM\Column(name="Precio", type="float", precision=10, scale=0, nullable=false)
     */
    private $precio;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Usomedico", mappedBy="productoIdproducto")
     */
    private $usomedicoIdusomedico;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->usomedicoIdusomedico = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getIdproducto(): ?int
    {
        return $this->idproducto;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getPrecio(): ?float
    {
        return $this->precio;
    }

    public function setPrecio(float $precio): self
    {
        $this->precio = $precio;

        return $this;
    }

    /**
     * @return Collection|Usomedico[]
     */
    public function getUsomedicoIdusomedico(): Collection
    {
        return $this->usomedicoIdusomedico;
    }

    public function addUsomedicoIdusomedico(Usomedico $usomedicoIdusomedico): self
    {
        if (!$this->usomedicoIdusomedico->contains($usomedicoIdusomedico)) {
            $this->usomedicoIdusomedico[] = $usomedicoIdusomedico;
            $usomedicoIdusomedico->addProductoIdproducto($this);
        }

        return $this;
    }

    public function removeUsomedicoIdusomedico(Usomedico $usomedicoIdusomedico): self
    {
        if ($this->usomedicoIdusomedico->contains($usomedicoIdusomedico)) {
            $this->usomedicoIdusomedico->removeElement($usomedicoIdusomedico);
            $usomedicoIdusomedico->removeProductoIdproducto($this);
        }

        return $this;
    }

    public function __toString()
    {
        return $this->nombre;
    }
}
