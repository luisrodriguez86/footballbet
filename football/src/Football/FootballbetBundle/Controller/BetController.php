<?php

namespace Football\FootballbetBundle\Controller;

use Football\FootballbetBundle\Entity\UserGrupo;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Football\FootballbetBundle\Entity\Bet;
use Football\FootballbetBundle\Form\BetType;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bet controller.
 *
 */
class BetController extends Controller
{

    /**
     * Lists all Bet entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $current_user = $this->container->get('security.context')->getToken()->getUser();

        $entities='';
        $entities = $em->getRepository('FootballbetBundle:Bet')->findByIdAdminGrupo($current_user->getId());

        if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN'))
        {
            $entities = $em->getRepository('FootballbetBundle:Bet')->findAll();
        }
        $grupos = $em->getRepository('FootballbetBundle:Grupo')->findAll();
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->getRequest()->get('page', 1),
            10 );
        return $this->render('FootballbetBundle:Bet:index.html.twig', compact('pagination','grupos'));
    }

    public function cambiarBetAction($idBet,$betChoice){

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FootballbetBundle:Bet')->find($idBet);

        $entity->setLocalwin(false);
        $entity->setAwaywin(false);
        $entity->setDraft(false);

        switch ($betChoice) {
            case -1:
            {
                $entity->setLocalwin(true);
                break;
            }
            case 0:
            {
                $entity->setDraft(true);
                break;
            }
            case 1:
            {
                $entity->setAwaywin(true);
                break;
            }
        }
        $em->persist($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('bet'));
    }

    public function filtro_betAction()
    {
        if (!$this->getRequest()->isXmlHttpRequest()) return '';

        $idGrupo = $this->getRequest()->get('idGrupo', '');
        if($idGrupo !='')
            $idGrupo = (int)$idGrupo;

        $em = $this->getDoctrine()->getManager();

        $current_user = $this->container->get('security.context')->getToken()->getUser();

        $entities='';

        if ($this->get('security.context')->isGranted('ROLE_GROUP_ADMIN'))
          $entities = $em->getRepository('FootballbetBundle:Bet')->findByIdGrupo($idGrupo,$current_user->getId());

        if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN'))
         $entities = $em->getRepository('FootballbetBundle:Bet')->findByIdGrupo($idGrupo,'');


        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->getRequest()->get('page', 1),
            10 );
        return $this->render('FootballbetBundle:Bet:admin_table.html.twig', compact('pagination'));
    }

    /**
     * Creates a new Bet entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Bet();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('bet_show', array('id' => $entity->getId())));
        }

        return $this->render('FootballbetBundle:Bet:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Bet entity.
     *
     * @param Bet $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Bet $entity)
    {
        $form = $this->createForm(new BetType(), $entity, array(
            'action' => $this->generateUrl('bet_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Bet entity.
     *
     */
    public function newAction()
    {
        $entity = new Bet();
        $form = $this->createCreateForm($entity);

        return $this->render('FootballbetBundle:Bet:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Bet entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FootballbetBundle:Bet')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Bet entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FootballbetBundle:Bet:show.html.twig', array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),));
    }

    /**
     * Displays a form to edit an existing Bet entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FootballbetBundle:Bet')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Bet entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FootballbetBundle:Bet:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Bet entity.
     *
     * @param Bet $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Bet $entity)
    {
        $form = $this->createForm(new BetType(), $entity, array(
            'action' => $this->generateUrl('bet_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Bet entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FootballbetBundle:Bet')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Bet entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('bet_edit', array('id' => $id)));
        }

        return $this->render('FootballbetBundle:Bet:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Bet entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {

            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FootballbetBundle:Bet')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Bet entity.');
            }

            $em->remove($entity);
            $em->flush();


        return $this->redirect($this->generateUrl('bet'));
    }

    /**
     * Creates a form to delete a Bet entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('bet_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }

    public function createFromGameAction(Request $request)
    {
        $idGame = $request->get('idGame');
        $betChoice = $request->get('bet');
        $grupos = $request->get('grupos');

        $em = $this->getDoctrine()->getManager();
        $game = $em->getRepository("FootballbetBundle:Games")->find($idGame);


        if (!is_numeric($idGame) || !is_numeric($betChoice) || ($betChoice < -1 || $betChoice > 1) || count($grupos) == 0 || $game == null)
            return new JsonResponse("error");

        $entity = new Bet();

        $usuarioActual = $this->get('security.context')->getToken()->getUser();

        foreach ($grupos as $grupo) {
            $consulta = $em->createQuery('SELECT ug FROM FootballbetBundle:UserGrupo ug
                JOIN ug.grupo g JOIN ug.user u
                WHERE u.id = :userId AND g.id = :grupoid'
            );
            $consulta->setParameters(array(
                'userId' => $usuarioActual->getId(),
                'grupoid' => $grupo
            ));
            $userGrupos = $consulta->getResult();

            $grupoOBJ = $em->getRepository("FootballbetBundle:Grupo")->find($grupo);

            $userGrupo = '';

            if (count($userGrupos) == 0) {
                return new JsonResponse("error");
            }

            $userGrupo = $userGrupos[0];
            $usergrupoId = $userGrupo->getId();

            $bet = $em->getRepository("FootballbetBundle:Bet")->findByUserGrupoIdAndGameId($usergrupoId, $idGame);

            if ($bet != null) {
                $entity = $bet[0];
            } else {

                $entity->setUsergrupo($userGrupo);
                $entity->setGame($game);
            }

            $entity->setLocalwin(false);
            $entity->setAwaywin(false);
            $entity->setDraft(false);

            switch ($betChoice) {
                case -1:
                {
                    $entity->setLocalwin(true);
                    break;
                }
                case 0:
                {
                    $entity->setDraft(true);
                    break;
                }
                case 1:
                {
                    $entity->setAwaywin(true);
                    break;
                }
            }
            $em->persist($entity);
            $em->flush();

        }
        return new JsonResponse("ok");
    }
}
