<?php

namespace Football\FootballbetBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;


class GamesRepository extends EntityRepository {

    public function findActuales($league)
    {
        $fechaActual = new \DateTime();
        $fechaActual->setTime(23,59,59);

        $dql = 'SELECT g FROM FootballbetBundle:Games g JOIN g.league l WHERE g.date>:fechaActual AND l.id=:league';

        $consulta = $this ->getEntityManager()->createQuery($dql);

        $consulta->setParameter('league',$league);
        $consulta->setParameter('fechaActual',$fechaActual);

        $result = $consulta->getResult();

        return $result;
    }
}