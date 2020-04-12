<?php

namespace App\Repository;

use App\Entity\Producto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class ProductoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Producto::class);
    }

    public function getProductosUsoMedico($usoMedico)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder('p')
            ->select('p')->from('App\Entity\Producto', 'p')
            ->innerJoin('p.usomedicoIdusomedico', 'um')
            ->where('um.uso = :uso')
            ->setParameter(':uso', $usoMedico[0]);

        for ($i = 1; $i < sizeof($usoMedico); $i++) {
            $qb->orWhere('um.uso = :uso' . $i);
            $qb->setParameter(":uso" . $i, $usoMedico[$i]);
        }
        $consulta = $qb->getQuery();

        return  $consulta->execute();
    }

    public function getProductosUnUsoMedico($usoMedico)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder('p')
            ->select('p')->from('App\Entity\Producto', 'p')
            ->innerJoin('p.usomedicoIdusomedico', 'um')
            ->where('um.uso = :uso')
            ->setParameter(':uso', $usoMedico);

        $consulta = $qb->getQuery();

        return  $consulta->execute();
    }
}
