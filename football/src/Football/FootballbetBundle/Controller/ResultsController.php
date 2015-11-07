<?php

namespace Football\FootballbetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Request;

class ResultsController extends Controller
{
    public function indexAction()
    {
        $annoActual = date("Y");

        $url = $this->container->getParameter('footballbet.json.resultados.ligaBBA.url');
        $url = str_replace("ANNOACTUAL", $annoActual, $url);

        $json_text = file_get_contents($url);

        $resultadosLigaBBA = json_decode($json_text)->match;

        $url = $this->container->getParameter('footballbet.json.resultados.segundaDiv.url');
        $url = str_replace("ANNOACTUAL", $annoActual, $url);

        $json_text = file_get_contents($url);

        $resultadosSegundaDiv = json_decode($json_text)->match;

        /*echo '<pre>';
        echo print_r($resultadosLigaBBA);
        echo '</pre>';
        die;*/

        return $this->render('FootballbetBundle:Results:index.html.twig',array(
            'liga1'=>$resultadosLigaBBA,
            'liga2'=>$resultadosSegundaDiv,
        ));
    }

}
