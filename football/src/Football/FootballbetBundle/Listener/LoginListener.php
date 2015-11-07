<?php

namespace Ujc\AdministracionBundle\Listener;

use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\SecurityContext;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine; // for Symfony 2.1.0+
// use Symfony\Bundle\DoctrineBundle\Registry as Doctrine; // for Symfony 2.0.x
use Ujc\AuditoriaBundle\Entity\AuditLog;

/**
* Custom login listener.
*/
class LoginListener
{
	/** @var \Symfony\Component\Security\Core\SecurityContext */
	private $securityContext;
	
	/** @var \Doctrine\ORM\EntityManager */
	private $em;
	
	/**
	* Constructor
	*
	* @param SecurityContext $securityContext
	* @param Doctrine $doctrine
	*/
	public function __construct(SecurityContext $securityContext, Doctrine $doctrine)
	{
		$this->securityContext = $securityContext;
        $this->em = $this->getDoctrine()->getManager();
	}
	
	/**
	* Do the magic.
	*
	* @param InteractiveLoginEvent $event
	*/
	public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)   //cuando se loguee y sea exitoso el logueo 
	{		
		$user = $event->getAuthenticationToken()->getUser();
		$request = $event->getRequest();
		$ip = $request->getClientIp() == "::1" ? "127.0.0.1" : $request->getClientIp();  //si me devuelve ::1 pongo que me estoy conectando desde el localhost sino desde el ip de otra PC 
		$this->makeLogonLog($user,$ip);
	}
	
	public function makeLogonLog($user,$ip)    //inserta el log en la BD
	{
		$log = new AuditLog();		
		$log->setTypeId("0");
		$log->setType("Inicio de Sesión");
		$log->setDescription("El usuario ha iniciado sesión");	
		$time = new \DateTime();	
		$time->setTimezone(new \DateTimeZone('America/New_York'));
		$log->setEventTime($time);
		$log->setUser($user);
		$log->setIp($ip);
		$this->em->persist($log);
		$this->em->flush();
	}
} 