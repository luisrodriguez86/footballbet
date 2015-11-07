<?php

namespace Football\FootballbetBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Football\FootballbetBundle\Entity\User;
use Football\FootballbetBundle\Form\UserType;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Usuario controller.
 *
 */
class SecurityController extends Controller
{

    public function loginAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContextInterface::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContextInterface::AUTHENTICATION_ERROR);
        }

        return $this->render('FootballbetBundle:Security:login.html.twig',array(
            'last_username' => $session->get(SecurityContextInterface::LAST_USERNAME),
            'error' => $error
        ));
    }
}
