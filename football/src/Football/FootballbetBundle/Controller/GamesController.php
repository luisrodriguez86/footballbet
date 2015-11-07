<?php

namespace Football\FootballbetBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Football\FootballbetBundle\Entity\Games;
use Football\FootballbetBundle\Form\GamesType;
use Symfony\Component\HttpFoundation\Response;

/**
 * Games controller.
 *
 */
class GamesController extends Controller
{

    /**
     * Lists all Games entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FootballbetBundle:Games')->findAll();
        $usuarioActual = $this->get('security.context')->getToken()->getUser();


        $consulta = $em->createQuery('SELECT g FROM FootballbetBundle:Grupo g
			JOIN g.usergrupos ug JOIN ug.user u
			WHERE u.id = :userId'
        );
        $consulta->setParameters(array(
            'userId'=>$usuarioActual->getId()
        ));
        $gruposUser = $consulta->getResult();

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->getRequest()->get('page', 1),
            10 );
        $grupos = $gruposUser;
        return $this->render('FootballbetBundle:Games:index.html.twig', compact(
            'pagination','grupos'  ));
    }

    /**
     * Lists all Games entities.
     *
     */
    public function betsgamesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $idGrupo = $request->request->get('idGrupo','');
        $grupo =  $em->getRepository('FootballbetBundle:Grupo')->find($idGrupo);
        if( $grupo == null )
            return new Response('Grupo no encontrado.');

        $league = $grupo->getLeague();

        if( $league == null )
            return new Response('League no encontrada.');

        $entities = $em->getRepository('FootballbetBundle:Games')->findActuales($league->getId());
        $usuarioActual = $this->get('security.context')->getToken()->getUser();

        $consulta = $em->createQuery('SELECT g FROM FootballbetBundle:Grupo g
			JOIN g.usergrupos ug JOIN ug.user u
			WHERE u.id = :userId'
        );
        $consulta->setParameters(array(
            'userId'=>$usuarioActual->getId()
        ));
        $gruposUser = $consulta->getResult();

        $datos = array(
            'entities' => $entities,
            'grupos'=>$gruposUser,
            'league'=>$league->getNombre()
        );

        if($idGrupo!=''){
            $apostadosYa = $em->getRepository('FootballbetBundle:Bet')->findGamesApostadosByIdUserAndIdGrupo($usuarioActual->getId(),$idGrupo);
            $datos['apostadosYa']=$apostadosYa;
        }

        return $this->render('FootballbetBundle:Games:betsgames.html.twig', $datos);
    }


    /**
     * Creates a new Games entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Games();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('games_show', array('id' => $entity->getId())));
        }

        return $this->render('FootballbetBundle:Games:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
    * Creates a form to create a Games entity.
    *
    * @param Games $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Games $entity)
    {
        $form = $this->createForm(new GamesType(), $entity, array(
            'action' => $this->generateUrl('games_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Games entity.
     *
     */
    public function newAction()
    {
        $entity = new Games();
        $form   = $this->createCreateForm($entity);

        return $this->render('FootballbetBundle:Games:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Games entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FootballbetBundle:Games')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Games entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FootballbetBundle:Games:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing Games entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FootballbetBundle:Games')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Games entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FootballbetBundle:Games:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Games entity.
    *
    * @param Games $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Games $entity)
    {
        $form = $this->createForm(new GamesType(), $entity, array(
            'action' => $this->generateUrl('games_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Games entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FootballbetBundle:Games')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Games entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('games_edit', array('id' => $id)));
        }

        return $this->render('FootballbetBundle:Games:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Games entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FootballbetBundle:Games')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Games entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('games'));
    }

    /**
     * Creates a form to delete a Games entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('games_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
