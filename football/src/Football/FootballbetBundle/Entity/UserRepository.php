<?php

namespace Football\FootballbetBundle\Entity;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class UserRepository extends EntityRepository implements UserProviderInterface
{

    public function loadUserByUsername($username)
    {
        $q = $this
            ->createQueryBuilder('u')
            ->where('u.email = :email')
            ->setParameter('email', $username)
            ->getQuery()
        ;
        $user='';
        try {
// The Query::getSingleResult() method throws an exception
// if there is no record matching the criteria.
            $user = $q->getSingleResult();
        } catch (NoResultException $e) {
            $message = sprintf(
                'Revise sus datos.',
                $username
            );
            throw new Exception($message,0,$e);
            //throw new UsernameNotFoundException($message,, 0, $e);
        }
        return $user;
    }


    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(
                sprintf(
                    'Instances of "%s" are not supported.',
                    $class
                )
            );
        }
        return $this->find($user->getId());
    }
    public function supportsClass($class)
    {
        return $this->getEntityName() === $class
        || is_subclass_of($class, $this->getEntityName());
    }

    public function findByNombreYEmail($nombre,$email)
    {
        $dql = 'SELECT u FROM FootballbetBundle:User u ';

        if($nombre!=""||$email!="")
            $dql.='WHERE ';

        $filtros = array();
        $and = '';

        if($nombre!=''){
            $dql.= "u.nombre LIKE :nombre ";
            $filtros['nombre']="%$nombre%";
            $and = ' AND ';
        }

        if($email!=''){
            $dql.= $and." u.nombre LIKE :email ";
            $filtros['email']="%$email%";
        }

        $consulta = $this ->getEntityManager()->createQuery($dql);
        if($nombre!=''||$email!='')
        $consulta->setParameters($filtros);
        $usuarios = $consulta->getResult();

        return $usuarios;
    }

    public function findCincoMasPuntos()
    {
        $dql = 'SELECT u FROM FootballbetBundle:User u ORDER BY u.points DESC';
        $consulta = $this ->getEntityManager()->createQuery($dql);
        $consulta->setMaxResults(5);
        $result = $consulta->getResult();

        return $result;
    }

    public function cantidadUsuarios()
    {
        $dql = 'SELECT COUNT(u.id) FROM FootballbetBundle:User u ';

        $consulta = $this ->getEntityManager()->createQuery($dql);

        $result = $consulta->getSingleScalarResult();

        return $result;
    }

    public function clasificacionGrupo($idGrupo)
    {
        $em = $this->getEntityManager();
        $dql = 'select u FROM FootballbetBundle:User u JOIN u.usergrupos ug JOIN ug.grupo g WHERE g.id = :idGrupo
            order by u.points DESC';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('idGrupo',$idGrupo);
        $result = $consulta->getResult();

        return $result;
    }
}