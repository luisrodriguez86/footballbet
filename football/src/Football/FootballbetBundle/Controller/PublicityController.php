<?php

namespace Football\FootballbetBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Football\FootballbetBundle\Entity\Publicity;
use Football\FootballbetBundle\Form\PublicityType;

/**
 * Publicity controller.
 *
 */
class PublicityController extends Controller
{

    /**
     * Lists all Publicity entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FootballbetBundle:Publicity')->findAll();
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->getRequest()->get('page', 1),
            10 );
        return $this->render('FootballbetBundle:Publicity:index.html.twig', compact(
            'pagination'));
    }
    /**
     * Creates a new Publicity entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Publicity();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->upload();
            $entity->setDate(new \DateTime("now"));
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('publicity_show', array('id' => $entity->getId())));
        }

        return $this->render('FootballbetBundle:Publicity:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
    * Creates a form to create a Publicity entity.
    *
    * @param Publicity $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Publicity $entity)
    {
        $form = $this->createForm(new PublicityType(), $entity, array(
            'action' => $this->generateUrl('publicity_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Crear','attr'=> array('class'=>'btn green')));

        return $form;
    }

    /**
     * Displays a form to create a new Publicity entity.
     *
     */
    public function newAction()
    {
        $entity = new Publicity();
        $form   = $this->createCreateForm($entity);

        return $this->render('FootballbetBundle:Publicity:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Publicity entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FootballbetBundle:Publicity')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Publicity entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FootballbetBundle:Publicity:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing Publicity entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FootballbetBundle:Publicity')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Publicity entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FootballbetBundle:Publicity:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Publicity entity.
    *
    * @param Publicity $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Publicity $entity)
    {
        $form = $this->createForm(new PublicityType(), $entity, array(
            'action' => $this->generateUrl('publicity_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Actualizar','attr'=> array('class'=>'btn purple')));

        return $form;
    }
    /**
     * Edits an existing Publicity entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FootballbetBundle:Publicity')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Publicity entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entity->removeUpload();
            $entity->upload();
            $entity->setDate(new \DateTime("now"));
            $em->flush();

            return $this->redirect($this->generateUrl('publicity'));
        }

        return $this->render('FootballbetBundle:Publicity:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Publicity entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FootballbetBundle:Publicity')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Publicity entity.');
            }

            $entity->removeUpload();

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('publicity'));
    }

    /**
     * Creates a form to delete a Publicity entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('publicity_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Eliminar','attr'=> array('class'=>'btn btn-xs red')))
            ->getForm()
        ;
    }
}
