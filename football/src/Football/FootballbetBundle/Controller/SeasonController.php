<?php

namespace Football\FootballbetBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Football\FootballbetBundle\Entity\Season;
use Football\FootballbetBundle\Form\SeasonType;

/**
 * Season controller.
 *
 */
class SeasonController extends Controller
{

    /**
     * Lists all Season entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FootballbetBundle:Season')->findAll();
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->getRequest()->get('page', 1),
            10 );
        return $this->render('FootballbetBundle:Season:index.html.twig', compact('pagination'));
    }
    /**
     * Creates a new Season entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Season();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('season_show', array('id' => $entity->getId())));
        }

        return $this->render('FootballbetBundle:Season:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
    * Creates a form to create a Season entity.
    *
    * @param Season $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Season $entity)
    {
        $form = $this->createForm(new SeasonType(), $entity, array(
            'action' => $this->generateUrl('season_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Season entity.
     *
     */
    public function newAction()
    {
        $entity = new Season();
        $form   = $this->createCreateForm($entity);

        return $this->render('FootballbetBundle:Season:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Season entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FootballbetBundle:Season')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Season entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FootballbetBundle:Season:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing Season entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FootballbetBundle:Season')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Season entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FootballbetBundle:Season:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Season entity.
    *
    * @param Season $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Season $entity)
    {
        $form = $this->createForm(new SeasonType(), $entity, array(
            'action' => $this->generateUrl('season_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Season entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FootballbetBundle:Season')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Season entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('season_edit', array('id' => $id)));
        }

        return $this->render('FootballbetBundle:Season:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Season entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FootballbetBundle:Season')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Season entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('season'));
    }

    /**
     * Creates a form to delete a Season entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('season_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
