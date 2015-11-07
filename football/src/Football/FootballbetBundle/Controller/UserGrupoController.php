<?php

namespace Football\FootballbetBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Football\FootballbetBundle\Entity\UserGrupo;
use Football\FootballbetBundle\Form\UserGrupoType;

/**
 * UserGrupo controller.
 *
 */
class UserGrupoController extends Controller
{

    /**
     * Lists all UserGrupo entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FootballbetBundle:UserGrupo')->findAll();

        return $this->render('FootballbetBundle:UserGrupo:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new UserGrupo entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new UserGrupo();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('usergrupo_show', array('id' => $entity->getId())));
        }

        return $this->render('FootballbetBundle:UserGrupo:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
    * Creates a form to create a UserGrupo entity.
    *
    * @param UserGrupo $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(UserGrupo $entity)
    {
        $form = $this->createForm(new UserGrupoType(), $entity, array(
            'action' => $this->generateUrl('usergrupo_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new UserGrupo entity.
     *
     */
    public function newAction()
    {
        $entity = new UserGrupo();
        $form   = $this->createCreateForm($entity);

        return $this->render('FootballbetBundle:UserGrupo:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a UserGrupo entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FootballbetBundle:UserGrupo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UserGrupo entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FootballbetBundle:UserGrupo:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing UserGrupo entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FootballbetBundle:UserGrupo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UserGrupo entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FootballbetBundle:UserGrupo:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a UserGrupo entity.
    *
    * @param UserGrupo $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(UserGrupo $entity)
    {
        $form = $this->createForm(new UserGrupoType(), $entity, array(
            'action' => $this->generateUrl('usergrupo_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing UserGrupo entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FootballbetBundle:UserGrupo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UserGrupo entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('usergrupo_edit', array('id' => $id)));
        }

        return $this->render('FootballbetBundle:UserGrupo:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a UserGrupo entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FootballbetBundle:UserGrupo')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find UserGrupo entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('usergrupo'));
    }

    /**
     * Creates a form to delete a UserGrupo entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('usergrupo_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
