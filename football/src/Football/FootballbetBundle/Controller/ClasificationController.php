<?php

namespace Football\FootballbetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Request;

class ClasificationController extends Controller
{
    public function indexAction()
    {
        $ligaBbaUrl = $this->container->getParameter('footballbet.json.clasificacion.ligaBBA.url');
        $ligaSegundaDivisionUrl = $this->container->getParameter('footballbet.json.clasificacion.segundaDiv.url');

        $json_text = file_get_contents($ligaBbaUrl);
        $clasificacionLigaBBA = json_decode($json_text)->table;

        $json_text = file_get_contents($ligaSegundaDivisionUrl);
        $clasificacionSegundaDivision = json_decode($json_text)->table;

        return $this->render('FootballbetBundle:Clasification:index.html.twig',array(
            'liga1'=>$clasificacionLigaBBA,
            'liga2'=>$clasificacionSegundaDivision
        ));
    }

}
