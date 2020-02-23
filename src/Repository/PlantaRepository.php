<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Entity\Planta;

class PlantaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Planta::class);
    }

    public function getPlantasColorFlor($color)
    {
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("select p.nombre, p.color from App\Entity\Planta p
                                    inner join p.colorflorIdcolorflor c 
                                    where c.color = :color")->setParameter(":color", $color);
        return $consulta->getResult();
    }

    /**
     * @Route("plantas/filtros/{color}/{parteutil}/{and})
     *
     * @param string $color
     * @param string $parteUtil
     * @param boolean $and
     * @return void
     */
    public function getPlantasFiltros(string $color, string $parteUtil = null, bool $and = true)
    {
        $em = $this->getEntityManager();
        $gb = $em->createQueryBuilder("p")->select("p")->from("App\Entity\Planta", "p")->innerJoin("p.colorflorIdcolorflor", "c");

        if ($parteUtil) {
            $gb->innerJoin("p.parteutilIdparteutil", "pu");
            if ($and) {
                $gb->where("c.color = :color")->andWhere("pu.parte = :parte");
            } else {
                $gb->where("c.color = :color")->orWhere("pu.parte = :parte");
            }
        } else {
            $gb->where("c.color = :color");
        }

        if (!$parteUtil) {
            $gb->setParameter("color", $color);
        } else {
            $gb->setParameters(["color" => $color, "parte" => $parteUtil]);
        }

        $consulta = $gb->getQuery();

        return $consulta->execute();
    }
}
