<?php

namespace Football\FootballbetBundle\Entity;
use Doctrine\ORM\EntityRepository;


class GrupoRepository extends EntityRepository {

    public function cantidadGrupo()
    {
        $dql = 'SELECT COUNT(u.id) FROM FootballbetBundle:Grupo u ';

        $consulta = $this ->getEntityManager()->createQuery($dql);

        $result = $consulta->getSingleScalarResult();

        return $result;
    }
    public function usuariosGrupo($grupoId)
    {
        $dql = 'SELECT u FROM FootballbetBundle:User u JOIN u.usergrupos ug JOIN ug.grupo g WHERE g.id=:grupoId ORDER BY u.points DESC';

        $consulta = $this ->getEntityManager()->createQuery($dql);
        $consulta->setParameter('grupoId',$grupoId);
        $result = $consulta->getResult();

        return $result;
    }
    public function gruposUsuario($usuarioId)
    {
        $dql = 'SELECT g FROM FootballbetBundle:Grupo g JOIN g.usergrupos ug JOIN ug.user u WHERE u.id=:usuarioId';

        $consulta = $this ->getEntityManager()->createQuery($dql);
        $consulta->setParameter('usuarioId',$usuarioId);
        $result = $consulta->getResult();

        return $result;
    }

    public function findByGroupadmin($adminId)
    {
        $dql = 'SELECT g FROM FootballbetBundle:Grupo g JOIN g.group_admin u WHERE u.id=:adminId';

        $consulta = $this ->getEntityManager()->createQuery($dql);
        $consulta->setParameter('adminId',$adminId);
        $result = $consulta->getResult();

        return $result;
    }
}