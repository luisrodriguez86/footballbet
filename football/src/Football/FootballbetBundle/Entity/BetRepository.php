<?php

namespace Football\FootballbetBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;


class BetRepository extends EntityRepository {

    public function findByUserGrupoIdAndGameId($idUserGrupo,$idGame)
    {
        $em = $this->getEntityManager();

        $consulta = $em->createQuery("select b from FootballbetBundle:Bet b JOIN
                                b.usergrupo ug JOIN b.game g
                                where ug.id = :usergrupoid AND g.id = :gameid");
        $consulta->setParameter('usergrupoid',$idUserGrupo);
        $consulta->setParameter('gameid',$idGame);
        $bet = $consulta->getResult();

        return (count($bet)!=0)?$bet:null;
    }

    public function cantidadBet()
    {
        $dql = 'SELECT COUNT(u.id) FROM FootballbetBundle:Bet u ';

        $consulta = $this ->getEntityManager()->createQuery($dql);

        $result = $consulta->getSingleScalarResult();

        return $result;
    }

    public function comportamientoAnno()
    {
        $sql='SELECT DISTINCT YEAR(bet.date) as anno FROM bet';
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult( 'anno', 'anno');
        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $annos = $query->getResult();

        $apuestasPorAno = array();

        foreach($annos as $annoTemp)
        {
            $anno = $annoTemp['anno'];

        /*$sql='SELECT MONTH(bet.date) as mes, COUNT(bet.id) as total FROM bet WHERE YEAR(bet.date)='.$anno.' group by mes';
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult( 'mes', 'mes');
        $rsm->addScalarResult('total', 'total');
        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $meses = $query->getResult();

            $apuestasPorAno[$anno]= array();
            $apuestasPorAno[$anno]['anno']=$anno;

            foreach($meses as $mesTemp)
            {
                $mes = (\DateTime::createFromFormat('n',$mesTemp['mes'])->format('F')) ;

                $apuestasPorAno[$anno][$mes]['mes']= $mes;
                $apuestasPorAno[$anno][$mes]['total']= $mesTemp['total'];
            }*/




            $sql='SELECT DISTINCT MONTH(bet.date) as mes FROM bet WHERE YEAR(bet.date)='.$anno;
            $rsm = new ResultSetMapping();
            $rsm->addScalarResult( 'mes', 'mes');
            $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
            $meses = $query->getResult();

            $apuestasPorAno[$anno]= array();
            $apuestasPorAno[$anno]['anno']=$anno;
            $apuestasPorAno[$anno]['bets']=array();
            foreach($meses as $mesTemp)
            {
                $mes = (\DateTime::createFromFormat('n',$mesTemp['mes'])->format('F')) ;

                $sql='SELECT COUNT(bet.id) as total FROM bet WHERE YEAR(bet.date)='.$anno.' AND MONTH(bet.date)='.$mesTemp['mes'];
                $rsm = new ResultSetMapping();
                $rsm->addScalarResult( 'total', 'total');
                $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
                $total = $query->getResult();

                $apuestasPorAno[$anno]['bets'][]= array($mes,(int)$total[0]['total']);
            }

           /* echo '<pre>';
        print_r($apuestasPorAno[$anno]['bets']);
        echo '</pre>';
        die();*/

             $apuestasPorAno[$anno]['bets'] = json_encode($apuestasPorAno[$anno]['bets']);
        }
        return $apuestasPorAno;
    }

    public function findByNombreYEmail($nombre,$email)
    {
        $dql = 'SELECT b,ug,u FROM FootballbetBundle:Bet b JOIN b.usergrupo ug JOIN ug.user u ';

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
        $bets = $consulta->getResult();

        return $bets;
    }

    public function findByUserId($usuarioId)
    {
        $em = $this ->getEntityManager();

        $dql = 'SELECT g FROM FootballbetBundle:Grupo g JOIN g.usergrupos ug JOIN ug.bets b JOIN ug.user u WHERE u.id = :usuarioId GROUP BY g.id ';

        $filtros = array(
            'usuarioId'=>$usuarioId
        );

        $consulta = $em->createQuery($dql);
        $consulta->setParameters($filtros);
        $grupos = $consulta->getResult();

        $resultado = array();

        foreach($grupos as $grupo)
        {
            $usuariosDelGrupo = $em->getRepository('FootballbetBundle:Grupo')->usuariosGrupo($grupo->getId());
            $apuestasMias = $em->getRepository('FootballbetBundle:Bet')->findByIdUserYIdGrupo($usuarioId,$grupo->getId());

            $resultadoTemp=array(
                'grupo'=>$grupo->getNombre(),
                'usuarios'=>$usuariosDelGrupo,
                'apuestas'=>$apuestasMias
            );
            $resultado[]=$resultadoTemp;

        }
        return $resultado;
    }

    public function findByIdUserYIdGrupo($idUser,$idGrupo)
    {
        $dql = 'SELECT b,ug,u,g,game FROM FootballbetBundle:Bet b JOIN b.usergrupo ug JOIN ug.user u JOIN ug.grupo g JOIN b.game game WHERE u.id = :idUser AND g.id = :idGrupo';

        $filtros = array(
            'idUser'=>$idUser,
            'idGrupo'=>$idGrupo
        );

        $consulta = $this ->getEntityManager()->createQuery($dql);

            $consulta->setParameters($filtros);
        $bets = $consulta->getResult();

        return $bets;
    }

    public function findByIdGrupo($idGrupo,$idUser)
    {
        $dql = 'SELECT b FROM FootballbetBundle:Bet b JOIN b.usergrupo ug JOIN ug.user u JOIN ug.grupo g';

        $filtros = array(
        );

        if($idGrupo != '' || $idUser != '')
        {    $dql.=' WHERE ';
            $and = '';
            if($idGrupo !=''){
                $dql.=' g.id = :idGrupo';
                $filtros['idGrupo'] = $idGrupo;
                $and = ' AND ';
            }

            if($idUser!=''){
                $dql.= $and." g.group_admin =:idUser ";
                $filtros['idUser']=$idUser;
            }
        }
        $consulta = $this ->getEntityManager()->createQuery($dql);
        $consulta->setParameters($filtros);
        $bets = $consulta->getResult();

        return $bets;
    }

    public function findByIdAdminGrupo($usuarioId)
    {
        $dql = 'SELECT b FROM FootballbetBundle:Bet b JOIN b.usergrupo ug JOIN ug.user u JOIN ug.grupo g JOIN g.group_admin ga WHERE ga.id = :usuarioId ';

        $filtros = array(
            'usuarioId'=>$usuarioId
        );

        $consulta = $this ->getEntityManager()->createQuery($dql);
        $consulta->setParameters($filtros);
        $bets = $consulta->getResult();

        return $bets;
    }

    public function findGamesApostadosByIdUserAndIdGrupo($usuarioId,$idGrupo)
    {
        $dql = 'SELECT DISTINCT g.id FROM FootballbetBundle:Bet b JOIN b.usergrupo ug JOIN ug.user u JOIN b.game g JOIN ug.grupo gru WHERE u.id = :usuarioId AND gru.id = :idGrupo';

        $filtros = array(
            'usuarioId'=>$usuarioId,
            'idGrupo'=>$idGrupo
        );

        $consulta = $this ->getEntityManager()->createQuery($dql);
        $consulta->setParameters($filtros);
        $gamesIds = $consulta->getResult();

        return $gamesIds;
    }
}