<?php

namespace Football\FootballbetBundle\Controller;

use Football\FootballbetBundle\Entity\Games;
use Football\FootballbetBundle\Entity\Season;
use Football\FootballbetBundle\Entity\Team;
use Football\FootballbetBundle\Tests\Controller\GamesControllerTest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Football\FootballbetBundle\Entity\League;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\False;

/**
 * UpdateResult controller.
 *
 */
class ResultController extends Controller
{

    private $results = '';

    private $leaguesIds = '';

    /**
     * Actualiza todos los resutlados, leagues, teams, games
     *
     */
    public function updateResultAction()
    {

        set_time_limit(300);
        $this->results = array();
        $this->leaguesIds = array();

        try {

            //$this->updateLeague();
            //$this->updateTeams();
            $this->updateGames();

            return new JsonResponse("ok");

        } catch (Exception $e) {

            return new Response($e->getMessage());

        }
    }

    /*
     * $aActualizar =>  "games" || "leagues"  || "teams"
     * */
    private function loadUpdateResults($aActualizar, $traceo)
    {
        $inicio = 0;

        $this->results = array(
            1=>array(),
            2=>array(),
        );

        $url = $this->container->getParameter("footballbet.json.$aActualizar.url");

        switch ($aActualizar) {
            case "games":
            {

                $annoActual = date("Y");

                $url = str_replace("ANNOACTUAL", $annoActual, $url);

                $temp = "";

                do {
                    $temp = array();
                    $url = str_replace("INICIO", $inicio, $url);
                    $url = str_replace("LIGA", 1, $url);
                    $temp = "";

                    $updatedContent = file_get_contents($url);
                    $temp = json_decode($updatedContent);
                    $temp = $temp->match;
                    $cantidad = count($temp);
                    $this->results[1] = array_merge($this->results[1], $temp);
                    $inicio = $cantidad - 1;

                    break;

                } while ($cantidad != 0);

                $inicio = 0;
                $url = $this->container->getParameter("footballbet.json.$aActualizar.url");
                $url = str_replace("ANNOACTUAL", $annoActual, $url);
                
                do {
                    $temp = array();
                    $url = str_replace("INICIO", $inicio, $url);
                    $url = str_replace("LIGA", 2, $url);
                    $temp = "";

                    $updatedContent = file_get_contents($url);
                    $temp = json_decode($updatedContent);
                    $temp = $temp->match;
                    $cantidad = count($temp);
                    $this->results[2] = array_merge($this->results[2], $temp);
                    $inicio = $cantidad - 1;
                    break;

                } while ($cantidad != 0);

                break;
            }

        }
        if ($traceo) {
            echo "<pre>";
            print_r($this->results);
            echo "</pre>";
            die();
        }

    }

    public function updateGames()
    {
        $nombreLigaBBVA = "Liga BBVA";
        $nombreSegundaDivision = "Segunda División";

        $this->loadUpdateResults("games", false);
        $em = $this->getDoctrine()->getManager();
        $ligaBBVA = $em->getRepository('FootballbetBundle:League')->findByNombre($nombreLigaBBVA);

        if (count($ligaBBVA) == 0) {
            $ligaBBVA = new League();
            $ligaBBVA->setNombre($nombreLigaBBVA);
            $this->saveEntity($ligaBBVA);
        } else
            $ligaBBVA = $ligaBBVA[0];

        $ligaSegundaDivision = $em->getRepository('FootballbetBundle:League')->findByNombre($nombreSegundaDivision);

        if (count($ligaSegundaDivision) == 0) {
            $ligaSegundaDivision = new League();
            $ligaSegundaDivision->setNombre($nombreSegundaDivision);
            $this->saveEntity($ligaSegundaDivision);
        } else
            $ligaSegundaDivision = $ligaSegundaDivision[0];


        foreach ($this->results as $clave => $valor) {

            foreach ($valor as $clave2=>$gameObj) {
                $awayName = $gameObj->visitor;
                $localName = $gameObj->local;
                //"2015-02-11 22:00:00"
                $date = \DateTime::createFromFormat("Y-m-d H:i:s", utf8_decode($gameObj->schedule));
                $localGoals = utf8_decode($gameObj->local_goals);
                $awayGoals = utf8_decode($gameObj->visitor_goals);

                $em = $this->getDoctrine()->getManager();

                $awayTeam = $em->getRepository('FootballbetBundle:Team')->findByNombre($awayName);
                $localTeam = $em->getRepository('FootballbetBundle:Team')->findByNombre($localName);

                //$ligaNombre = $gameObj->competition_name;
                $game = $this->checkDoNotExistGame($awayName, $localName, $date);

                $liga = null;

                if ($clave == 1)
                    $liga = $ligaBBVA;
                else
                    $liga = $ligaSegundaDivision;

                if (count($awayTeam) == 0) {
                    $team = new Team();
                    $team->setLogo($gameObj->visitor_shield);
                    $team->setNombre($awayName);
                    $team->setLeague($liga);
                    $idTeam = $this->saveEntity($team);
                    $awayTeam = $team;

                } else {
                    $awayTeam = $awayTeam[0];
                }


                if (count($localTeam) == 0) {
                    $team = new Team();
                    $team->setLogo($gameObj->local_shield);
                    $team->setNombre($localName);
                    $team->setLeague($liga);
                    $idTeam = $this->saveEntity($team);
                    $localTeam = $team;
                } else {
                    $localTeam = $localTeam[0];
                }
                if (count($game) == 0) {
                    $game = new Games();
                    $game->setAway($awayTeam);
                    $game->setLocal($localTeam);
                    $game->setLeague($liga);
                    $game->setAwayGoals($awayGoals);
                    $game->setLocalGoals($localGoals);
                    $game->setDate($date);

                    $idGame = $this->saveEntity($game);

                    $fechaActual = new \DateTime();
                    if ($fechaActual > $date && $game->getUpdated() == false) {
                        $game->setUpdated(true);
                        $idGame = $this->saveEntity($game);
                        $this->updateBetsResults($game);
                    }
                    //$game->setSeason();

                } else {

                    $game = $game[0];
                    $fechaActual = new \DateTime();
                    $fechaJuego = $game->getDate();

                    if ($game->getUpdated() == false && $fechaActual > $fechaJuego) {
                        $game->setAwayGoals($awayGoals);
                        $game->setLocalGoals($localGoals);
                        $game->setUpdated(true);
                        //$game->setSeason();
                        $idGame = $this->saveEntity($game);
                        $this->updateBetsResults($game);
                    }

                }

                $gameObj='';
            }
        }
    }

    public function updateBetsResults($game)
    {

        $em = $this->getDoctrine()->getManager();

        $consulta = $em->createQuery('SELECT b,ug,u,g FROM FootballbetBundle:Bet b
			JOIN b.usergrupo ug JOIN ug.user u JOIN b.game g
			WHERE g.id = :gameid'
        );
        $consulta->setParameters(array(
            'gameid' => $game->getId()
        ));
        $bets = $consulta->getResult();

        $localwin = $game->getLocalGoals() > $game->getAwayGoals();
        $awaywin = $game->getAwayGoals() > $game->getLocalGoals();
        $draft = $game->getAwayGoals() == $game->getLocalGoals();


        foreach ($bets as $bet) {
            $puntosAganar = 2;

            $sumado = false;

            if ($bet->getLocalwin() == $localwin && $localwin == true && $sumado == false) {
                $bet->setPoints($puntosAganar);

                $sumado = true;
            }

            if ($bet->getDraft() == $draft && $draft == true && $sumado == false) {
                $bet->setPoints($puntosAganar);

                $sumado = true;
            }

            if ($bet->getAwaywin() == $awaywin && $awaywin == true && $sumado == false) {
                $bet->setPoints($puntosAganar);

                $sumado = true;
            }

            $this->saveEntity($bet);

            if ($sumado) {
                $usuario = $bet->getUsergrupo()->getUser();
                $usuario->setPoints($usuario->getPoints() + $puntosAganar);

                $userGrupo = $bet->getUsergrupo();
                $userGrupo->setPoints($userGrupo->getPoints() + $puntosAganar);

                $this->saveEntity($usuario);
                $this->saveEntity($userGrupo);
            }
        }


    }

    public function saveEntity($objEntity)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($objEntity);
        $em->flush();

        return $objEntity->getId();
    }

    /*
     * devuelve true si no existe
     *
     */
    public
    function checkDoNotExistLeague($nombre, $logo)
    {
        $em = $this->getDoctrine()->getManager();
        $parameters = array(
            "nombre" => $nombre,
            "logo" => $logo,
        );
        $entities = $em->getRepository('FootballbetBundle:League')->findBy($parameters);
        return count($entities) == 0;

    }

    public
    function checkDoNotExistTeam($nombre, $logo)
    {
        $em = $this->getDoctrine()->getManager();
        $parameters = array(
            "nombre" => $nombre,
            "logo" => $logo,
        );
        $entities = $em->getRepository('FootballbetBundle:Team')->findBy($parameters);
        return count($entities) == 0;

    }

    public
    function checkDoNotExistSeason($title, $year)
    {
        $em = $this->getDoctrine()->getManager();
        $parameters = array(
            "title" => $title,
            "year" => $year,
        );
        $entities = $em->getRepository('FootballbetBundle:Season')->findBy($parameters);
        return count($entities) == 0;

    }

    function checkDoNotExistGame($awayName, $localName, $date)
    {
        $em = $this->getDoctrine()->getManager();

        $consulta = $em->createQuery('SELECT g,a,l FROM FootballbetBundle:Games g JOIN g.away a JOIN g.local l
			WHERE a.nombre = :awayName AND l.nombre = :localName
			AND g.date = :date'
        );
        $consulta->setParameters(array(
            'awayName' => $awayName,
            'localName' => $localName,
            'date' => $date
        ));
        $entities = $consulta->getResult();

        if (count($entities) == 0)
            return null;
        return $entities;

    }
}
