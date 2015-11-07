<?php

namespace Football\FootballbetBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Football\FootballbetBundle\Entity\Nomenclador;
use Football\FootballbetBundle\Form\NomencladorType;

/**
 * Nomenclador controller.
 *
 */
class NomencladorController extends Controller
{

    /**
     * Lists all Nomenclador entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FootballbetBundle:Nomenclador')->findAll();

        return $this->render('FootballbetBundle:Nomenclador:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Nomenclador entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Nomenclador();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('nomenclador_show', array('id' => $entity->getId())));
        }

        return $this->render('FootballbetBundle:Nomenclador:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
    * Creates a form to create a Nomenclador entity.
    *
    * @param Nomenclador $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Nomenclador $entity)
    {
        $form = $this->createForm(new NomencladorType(), $entity, array(
            'action' => $this->generateUrl('nomenclador_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Nomenclador entity.
     *
     */
    public function newAction()
    {
        $entity = new Nomenclador();
        $form   = $this->createCreateForm($entity);

        return $this->render('FootballbetBundle:Nomenclador:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Nomenclador entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FootballbetBundle:Nomenclador')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Nomenclador entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FootballbetBundle:Nomenclador:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing Nomenclador entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FootballbetBundle:Nomenclador')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Nomenclador entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FootballbetBundle:Nomenclador:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Nomenclador entity.
    *
    * @param Nomenclador $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Nomenclador $entity)
    {
        $form = $this->createForm(new NomencladorType(), $entity, array(
            'action' => $this->generateUrl('nomenclador_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Nomenclador entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FootballbetBundle:Nomenclador')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Nomenclador entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('nomenclador_edit', array('id' => $id)));
        }

        return $this->render('FootballbetBundle:Nomenclador:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Nomenclador entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FootballbetBundle:Nomenclador')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Nomenclador entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('nomenclador'));
    }

    /**
     * Creates a form to delete a Nomenclador entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('nomenclador_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
