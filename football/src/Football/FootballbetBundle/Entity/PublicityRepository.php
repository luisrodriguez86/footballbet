<?php

namespace Football\FootballbetBundle\Entity;
use Doctrine\ORM\EntityRepository;


class PublicityRepository extends EntityRepository {

    public function getFrontalPublicity($today, $cant)
    {
        //$em = $this->getDoctrine()->getManager();
        return $this->getEntityManager()->createQuery("select p from FootballbetBundle:Publicity p where p.date <= :hoy and p.expiredate >= :hoy")
                                        ->setParameter('hoy',$today)
                                        ->setMaxResults($cant)
                                        ->getResult();
    }
}