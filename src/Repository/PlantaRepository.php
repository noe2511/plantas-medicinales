<?php

namespace App\Repository;

use App\Entity\Planta;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class PlantaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Planta::class);
    }

    public function getPlantasColorFlor($color)
    {
        $em = $this->getEntityManager();
        $consulta = $em->createQuery(
            'select p from App\Entity\Planta p
            inner join p.colorflorIdcolorflor c
            where c.color = :color'
        )->setParameter(':color', $color);
        return $consulta->getResult();
    }


    public function getPlantasFiltros(string $color, string $parteutil = null, bool $and = true)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder('p')
            ->select('p')->from('App\Entity\Planta', 'p')
            ->innerJoin('p.colorflorIdcolorflor', 'c');

        if ($parteutil) {
            $qb->innerJoin('p.parteutilIdparteutil', 'pu');
            if ($and) {
                $qb->where('c.color = :color')
                    ->andWhere('pu.parte = :parte');
            } else {
                $qb->where('c.color = :color')
                    ->orWhere('pu.parte = :parte');
            }
        } else {
            $qb->where('c.color = :color');
        }

        if (!$parteutil) {
            $qb->setParameter('color', $color);
        } else {
            $qb->setParameters(['color' => $color, 'parte' => $parteutil]);
        }

        $consulta = $qb->getQuery();

        return $consulta->execute();
    }

    public function getPlantasParteUtil($parte)
    {
        $em = $this->getEntityManager();
        $consulta = $em->createQuery(
            'select p from App\Entity\Planta p
            inner join p.parteutilIdparteutil pu
            where pu.parte = :parte'
        )->setParameter(':parte', $parte);
        return $consulta->getResult();
    }

    public function getPlantasVariasPartesUtilesColor(string $color, array $parteutil)
    {
        $em = $this->getEntityManager();

        $em = $this->getEntityManager();
        $consulta = $em->createQueryBuilder('p')
            ->select('p')->from('App\Entity\Planta', 'p')
            ->innerJoin('p.colorflorIdcolorflor', 'c')
            ->innerJoin('p.parteutilIdparteutil', 'pu')
            ->where('pu.parte = :parte')
            ->setParameter(':parte', $parteutil[0]);

        for ($i = 1; $i < sizeof($parteutil); $i++) {
            $consulta->orWhere('pu.parte = :parte' . $i);
            $consulta->setParameter(":parte" . $i, $parteutil[$i]);
        }

        $consulta->andWhere('c.color = :color')
            ->setParameter('color', $color);

        $qb = $consulta->getQuery();

        return  $qb->execute();
    }

    public function getPlantasVariasPartesUtiles(array $parteutil)
    {
        $em = $this->getEntityManager();

        $consulta = $em->createQueryBuilder()
            ->select("p")->from('App\Entity\Planta', 'p')
            ->innerJoin('p.parteutilIdparteutil', 'pu')
            ->where('pu.parte = :parte')
            ->setParameter(':parte', $parteutil[0]);

        for ($i = 1; $i < sizeof($parteutil); $i++) {
            $consulta->orWhere('pu.parte = :parte' . $i);
            $consulta->setParameter(":parte" . $i, $parteutil[$i]);
        }
        $qb = $consulta->getQuery();

        return  $qb->execute();
    }

    public function getPlantasUsoMedico($usoMedico)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder('p')
            ->select('p')->from('App\Entity\Planta', 'p')
            ->setParameter(':uso', $usoMedico)
            ->innerJoin('p.usomedicoIdusomedico', 'um');

        for ($i = 0; $i < sizeof($usoMedico); $i++) {
            $qb->orWhere('um.uso = :uso' . $i);
            $qb->setParameter(":uso" . $i, $usoMedico[$i]);
        }
        $consulta = $qb->getQuery();

        return  $consulta->execute();
    }

    public function getPlantasColorUsoMedico($color, $usoMedico, $and = true)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder('p')
            ->select('p')->from('App\Entity\Planta', 'p')
            ->innerJoin('p.colorflorIdcolorflor', 'c');

        if ($usoMedico) {
            $qb->innerJoin('p.usomedicoIdusomedico', 'um');
            if ($and) {
                $qb->where('c.color = :color')
                    ->andWhere('um.uso = :uso');
            } else {
                $qb->where('c.color = :color')
                    ->orWhere('um.uso = :uso');
            }
        } else {
            $qb->where('c.color = :color');
        }

        if (!$usoMedico) {
            $qb->setParameter('color', $color);
        } else {
            $qb->setParameters(['color' => $color, 'usoMedico' => $usoMedico]);
        }

        $consulta = $qb->getQuery();

        return $consulta->execute();
    }

    public function getPlantasParteUtilUsoMedico($parteUtil, $usoMedico)
    {
        $em = $this->getEntityManager();

        $consulta = $em->createQueryBuilder()
            ->select("p")->from('App\Entity\Planta', 'p')
            ->innerJoin('p.parteutilIdparteutil', 'pu')
            ->where('pu.parte = :parte')
            ->setParameter(':parte', $parteUtil[0]);

        for ($i = 1; $i < sizeof($parteUtil); $i++) {
            $consulta->orWhere('pu.parte = :parte' . $i);
            $consulta->setParameter(":parte" . $i, $parteUtil[$i]);
        }

        $consulta->innerJoin('p.usomedicoIdusomedico', 'um');

        for ($i = 0; $i < sizeof($usoMedico); $i++) {
            $consulta->orWhere('um.uso = :uso' . $i);
            $consulta->setParameter(":uso" . $i, $usoMedico[$i]);
        }


        $qb = $consulta->getQuery();

        return  $qb->execute();
    }

    public function getPlantasTodosFiltros($color, $parteUtil, $usoMedico)
    {
        var_dump("hola");
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder('p')
            ->select('p')->from('App\Entity\Planta', 'p')
            ->innerJoin('p.colorflorIdcolorflor', 'c')
            ->where('c.color = :color')
            ->setParameter('color', $color)
            ->innerJoin('p.parteutilIdparteutil', 'pu');

        for ($i = 0; $i < sizeof($parteUtil); $i++) {
            $qb->orWhere('pu.parte = :parte' . $i);
            $qb->setParameter(":parte" . $i, $parteUtil[$i]);
            var_dump("hola2");
        }

        $qb->innerJoin('p.usomedicoIdusomedico', 'um');

        for ($i = 0; $i < sizeof($usoMedico); $i++) {
            $qb->orWhere('um.uso = :uso' . $i);
            $qb->setParameter(":uso" . $i, $usoMedico[$i]);
            var_dump($i);
        }

        $consulta = $qb->getQuery();

        return  $consulta->execute();
    }
}
