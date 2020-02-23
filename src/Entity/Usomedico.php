<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Usomedico
 *
 * @ORM\Table(name="usomedico")
 * @ORM\Entity
 */
class Usomedico
{
    /**
     * @var int
     *
     * @ORM\Column(name="idUsoMedico", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idusomedico;

    /**
     * @var string
     *
     * @ORM\Column(name="Uso", type="string", length=150, nullable=false)
     */
    private $uso;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Planta", mappedBy="usomedicoIdusomedico")
     */
    private $plantaIdplanta;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Producto", inversedBy="usomedicoIdusomedico")
     * @ORM\JoinTable(name="usomedico_has_producto",
     *   joinColumns={
     *     @ORM\JoinColumn(name="UsoMedico_idUsoMedico", referencedColumnName="idUsoMedico")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="Producto_idProducto", referencedColumnName="idProducto")
     *   }
     * )
     */
    private $productoIdproducto;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->plantaIdplanta = new \Doctrine\Common\Collections\ArrayCollection();
        $this->productoIdproducto = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getIdusomedico(): ?int
    {
        return $this->idusomedico;
    }

    public function getUso(): ?string
    {
        return $this->uso;
    }

    public function setUso(string $uso): self
    {
        $this->uso = $uso;

        return $this;
    }

    /**
     * @return Collection|Planta[]
     */
    public function getPlantaIdplanta(): Collection
    {
        return $this->plantaIdplanta;
    }

    public function addPlantaIdplantum(Planta $plantaIdplantum): self
    {
        if (!$this->plantaIdplanta->contains($plantaIdplantum)) {
            $this->plantaIdplanta[] = $plantaIdplantum;
            $plantaIdplantum->addUsomedicoIdusomedico($this);
        }

        return $this;
    }

    public function removePlantaIdplantum(Planta $plantaIdplantum): self
    {
        if ($this->plantaIdplanta->contains($plantaIdplantum)) {
            $this->plantaIdplanta->removeElement($plantaIdplantum);
            $plantaIdplantum->removeUsomedicoIdusomedico($this);
        }

        return $this;
    }

    /**
     * @return Collection|Producto[]
     */
    public function getProductoIdproducto(): Collection
    {
        return $this->productoIdproducto;
    }

    public function addProductoIdproducto(Producto $productoIdproducto): self
    {
        if (!$this->productoIdproducto->contains($productoIdproducto)) {
            $this->productoIdproducto[] = $productoIdproducto;
        }

        return $this;
    }

    public function removeProductoIdproducto(Producto $productoIdproducto): self
    {
        if ($this->productoIdproducto->contains($productoIdproducto)) {
            $this->productoIdproducto->removeElement($productoIdproducto);
        }

        return $this;
    }

    public function __toString()
    {
        return $this->uso;
    }
}
