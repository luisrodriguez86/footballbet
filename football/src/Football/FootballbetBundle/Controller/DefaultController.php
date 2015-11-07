<?php

namespace Football\FootballbetBundle\Controller;

use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $today = $this->getLocalDate();
        //$em = $this->get('doctrine')->getEntityManager();
        $em = $this->getDoctrine()->getManager();
        $publicidades = $em->getRepository('FootballbetBundle:Publicity')->getFrontalPublicity($today,3);
        return $this->render('FootballbetBundle:Default:index.html.twig',
            array(
                'publicidades'=> $publicidades
            ));
    }

    public function getLocalDate()
    {
        $date = getdate();
        return $date['year'].'-'.$date['mon'].'-'.$date['mday'];
    }

    public function admin_beginAction()
    {
        /*$em = $this->getDoctrine()->getManager();
        $cantidadUser = $em->getRepository('FootballbetBundle:User')->cantidadUsuarios();
        $cantidadBet = $em->getRepository('FootballbetBundle:Bet')->cantidadBet();
        $cantidadGrupos = $em->getRepository('FootballbetBundle:Grupo')->cantidadGrupo();

        $usuariosCincoMasPuntos = $em->getRepository('FootballbetBundle:User')->findCincoMasPuntos();
        $comportamientoBetAnnoTemp = $em->getRepository('FootballbetBundle:Bet')->comportamientoAnno();

        $datos = array(
            'cantidadUser'=>$cantidadUser,
            'cantidadBet'=>$cantidadBet,
            'cantidadGrupo'=>$cantidadGrupos,
            'cincoMasPoints'=>$usuariosCincoMasPuntos,
           'comportamientoBetAnno'=>$comportamientoBetAnnoTemp
        );
        /*echo '<pre>';
        print_r($comportamientoBetAnnoTemp);
        echo '</pre>';
        die();

        return $this->render('FootballbetBundle:Default:admin-begin.html.twig',array(
            'datos'=>$datos
        )); */

        return $this->redirect($this->generateUrl('grupo'));
    }
}
