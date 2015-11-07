<?php

namespace Football\FootballbetBundle\Controller;

use Doctrine\DBAL\DBALException;
use Football\FootballbetBundle\Entity\Notificacion;
use Football\FootballbetBundle\Entity\UserGrupo;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\Common\Collections\ArrayCollection;

use Football\FootballbetBundle\Entity\Grupo;
use Football\FootballbetBundle\Form\GrupoType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Grupo controller.
 *
 */
class GrupoController extends Controller
{

    /**
     * Lists all Grupo entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $current_user = $this->currentUser();

        if (!$this->get('security.context')->isGranted('ROLE_USER'))
            throw new AccessDeniedHttpException("Acceso denegado");

        $entities = '';

        // DESCOMENTAR PARA QUE NADA MAS SE MUESTREN LOS GRUPOS EN LOS QUE EL USER ACTUAL ES EL ADMIN
        if ( $this->get('security.context')->isGranted('ROLE_SUPER_ADMIN'))
        $entities = $em->getRepository('FootballbetBundle:Grupo')->findAll();
        else
        $entities = $em->getRepository('FootballbetBundle:Grupo')->findByGroupadmin($current_user->getId());

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->getRequest()->get('page', 1),
            10 );

        return $this->render('FootballbetBundle:Grupo:index.html.twig', compact('pagination'));
    }

    /**
     * Creates a new Grupo entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Grupo();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $current_user = $this->currentUser();

            if ($current_user != null)
                $entity->setGroupAdmin($current_user);

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            // creando los usergrupos
                // creando la relacion con el admin del grupo como si fuera un miembro mas del grupo
                    $grupoUser = new UserGrupo();
                    $grupoUser->setUser($current_user);
                    $grupoUser->setGrupo($entity);
                    $em->persist($grupoUser);
                    $em->flush();
                // creando la relacion con el admin del grupo como si fuera un miembro mas del grupo
            $usuariosAnotificar = $form->get("users")->getData()->toArray();
            $enviados = 0;
            // notificar a los usuarios
            foreach ($usuariosAnotificar as $usuario) {
                if($usuario->getId() != $current_user->getId())
                {
                    $notificacion = new Notificacion();
                    $notificacion->setUser($usuario);
                    $notificacion->setGrupo($entity);
                    $notificacion->setCodigo(md5(uniqid($usuario->getId().$entity->getId())));

                    $em->persist($notificacion);
                    $em->flush();

                    $envioResult = $this->emailTo($notificacion);

                    $enviados+=count($envioResult);
                }
            }

            $messageToastrText = 'Se ha invitado a '.($enviados).' usuarios mediante email  para participar en el Grupo: '.$entity->getNombre();

            $this->get('session')->getFlashBag()->add('info',$messageToastrText);

            return $this->redirect($this->generateUrl('grupo_show', array('id' => $entity->getId())));
        }

        return $this->render('FootballbetBundle:Grupo:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    public function activarParticipacionAction($codigo){

        $em = $this->getDoctrine()->getManager();

        $notificacion = $em->getRepository('FootballbetBundle:Notificacion')->findByCodigo($codigo);

        if(count($notificacion)!=0){
            $notificacion=$notificacion[0];

            $usuario = $em->createQuery('SELECT u,gu FROM FootballbetBundle:User u JOIN u.groups gu WHERE u.id=:userId')->setParameter('userId',$notificacion->getUser()->getId())->getResult();
            $usuario=$usuario[0];

            $grupo = $em->createQuery('
              SELECT g,l,ga
              FROM FootballbetBundle:Grupo g
                  JOIN g.league l
                  JOIN g.group_admin ga
              WHERE g.id=:idGrupo')->setParameter('idGrupo',$notificacion->getGrupo()->getId())->getResult();
            $grupo=$grupo[0];

            $grupoUser = new UserGrupo();
            $grupoUser->setUser($usuario);
            $grupoUser->setGrupo($grupo);
            $em->persist($grupoUser);


            //$token = new UsernamePasswordToken($usuario,$usuario->getPassword(),'frontend',$usuario->getRoles());

            //$this->get('security.context')->setToken($token);

            $em->remove($notificacion);
            $em->flush();

            $this->get('session')->getFlashBag()->add('info',
                '¡Enhorabuena! Has activado tu participación en el Grupo: '.$notificacion->getGrupo()->getNombre()
            );
        }
        return $this->redirect($this->generateUrl('home'));
    }

    /**
     * Creates a form to create a Grupo entity.
     *
     * @param Grupo $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Grupo $entity)
    {
        $form = $this->createForm(new GrupoType(), $entity, array(
            'action' => $this->generateUrl('grupo_create'),
            'method' => 'POST',
            'current_user'=>$this->currentUser()
        ));

        $form->add('submit', 'submit', array('label' => 'Crear', 'attr'=> array('class'=>'btn green')));

        return $form;
    }

    /**
     * Displays a form to create a new Grupo entity.
     *
     */
    public function newAction()
    {
        $entity = new Grupo();
        $form = $this->createCreateForm($entity);

        return $this->render('FootballbetBundle:Grupo:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Grupo entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FootballbetBundle:Grupo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Grupo entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        $consulta = $em->createQuery('SELECT us FROM FootballbetBundle:User us
			JOIN us.usergrupos ug JOIN ug.grupo g
			WHERE g.id = :grupoid');
        $consulta->setParameters(array(
            'grupoid' => $entity->getId()
        ));

        $usuarios = $consulta->getResult();


        return $this->render('FootballbetBundle:Grupo:show.html.twig', array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
            'usuarios' => $usuarios
        ));
    }

    /**
     * Displays a form to edit an existing Grupo entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $current_user = $this->currentUser();

        $entity = $em->getRepository('FootballbetBundle:Grupo')->find($id);

        if ($current_user->getId() == $entity->getGroupAdmin()->getId() || ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN'))) {
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Grupo entity.');
            }

            $editForm = $this->createEditForm($entity);
            $deleteForm = $this->createDeleteForm($id);

            return $this->render('FootballbetBundle:Grupo:edit.html.twig', array(
                'entity' => $entity,
                'edit_form' => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
            ));
        } else {

            throw new AccessDeniedHttpException("No puede editar este grupo.");

        }
    }

    /**
     * Creates a form to edit a Grupo entity.
     *
     * @param Grupo $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Grupo $entity)
    {
        $form = $this->createForm(new GrupoType(), $entity, array(
            'action' => $this->generateUrl('grupo_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'current_user'=>$this->currentUser()
        ));

        $em = $this->getDoctrine()->getManager();
        $resultado = $em->getRepository('FootballbetBundle:Grupo')->usuariosGrupo($entity);
        $collection = new ArrayCollection($resultado);

        $form->get('users')->setData($collection);

        $form->add('submit', 'submit', array('label' => 'Actualizar', 'attr'=> array('class'=>'btn purple')));

        return $form;
    }

    /**
     * Edits an existing Grupo entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FootballbetBundle:Grupo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Grupo entity.');
        }

        $current_user = $this->currentUser();

        if ($current_user->getId() != $entity->getGroupAdmin()->getId())
            throw new AccessDeniedHttpException("No puede editar este grupo.");

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $usuariosAnotificar = $editForm->get("users")->getData()->toArray();
            $enviados = 0;
            // notificar a los usuarios
            foreach ($usuariosAnotificar as $usuario) {

                $consulta = $em->createQuery('SELECT ug FROM FootballbetBundle:UserGrupo ug
			JOIN ug.grupo g JOIN ug.user u
			WHERE u.id = :userId AND g.id = :grupoid'
                );
                $consulta->setParameters(array(
                    'userId' => $usuario->getId(),
                    'grupoid' => $entity->getId()
                ));
                $grupoUser = $consulta->getResult();

                if (count($grupoUser) == 0) {

                    $consulta = $em->createQuery('SELECT n FROM FootballbetBundle:Notificacion n
			JOIN n.user u JOIN n.grupo g
			WHERE u.id = :userId AND g.id = :grupoid'
                    );
                    $consulta->setParameters(array(
                        'userId' => $usuario->getId(),
                        'grupoid' => $entity->getId()
                    ));
                    $notificaciones = $consulta->getResult();
                    $notificacion = '';
                    if(count($notificaciones)==0){
                    $notificacion = new Notificacion();
                    $notificacion->setUser($usuario);
                    $notificacion->setGrupo($entity);
                    $notificacion->setCodigo(md5($usuario->getId().$entity->getId()));

                        $em->persist($notificacion);

                    }else
                    {
                        $notificacion=$notificaciones[0];
                    }

                    $envioResult = $this->emailTo($notificacion);

                    $enviados+=count($envioResult);
                }
            }

                $messageToastrText = 'Se ha invitado a '.($enviados).' usuarios mediante email  para participar en el Grupo: '.$entity->getNombre();

                $this->get('session')->getFlashBag()->add('info',$messageToastrText);

                 $em->persist($entity);
                $em->flush();

            /*return $this->render('FootballbetBundle::email_template_notificacion.html.twig',array('notificacion'=>$notificacion));*/


            return $this->redirect($this->generateUrl('grupo_edit', array('id' => $id)));
        }

        return $this->render('FootballbetBundle:Grupo:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    public function emailTo(\Football\FootballbetBundle\Entity\Notificacion $notificacion)
    {
        $nombreSitio = $this->container->getParameter('football.sitio.nombre');
        $correoAdmin = $this->container->getParameter('football.sitio.correo_admin');

        $nombreGrupo = $notificacion->getGrupo()->getNombre();
        $email = $notificacion->getUser()->getEmail();
        $emailAdmin = $notificacion->getGrupo()->getGroupAdmin()->getEmail();//'administrador@aldea.cu';

        $nombreGrupoAdmin = $notificacion->getGrupo()->getGroupAdmin()->getNombre();

        $ruta = $this->generateUrl("grupo_show", array('id' => $notificacion->getGrupo()->getId()));


        // Varios destinatarios
        $para = $email;

        // subject
        $subject = $nombreSitio.' - Invitacion al grupo ' . $nombreGrupo;

        // message
        $mensaje =  $this->renderView('FootballbetBundle::email_template_notificacion.html.twig',array('notificacion'=>$notificacion));
 
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($correoAdmin)
            ->setTo($para)
            ->setBody($mensaje,'text/html');
		return $this->get('mailer')->send($message);
    }

    /**
     * Deletes a Grupo entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FootballbetBundle:Grupo')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Grupo entity.');
            }

            try{
            $em->remove($entity);
            $em->flush();

            }catch ( DBALException $dbal){

                return new Response($dbal->getMessage());

            }
        }

        return $this->redirect($this->generateUrl('grupo'));
    }

    /**
     * Creates a form to delete a Grupo entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('grupo_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Eliminar', 'attr'=> array('class'=>'btn btn-sm red')))
            ->getForm();
    }

    public function groupAction()
    {
        $em = $this->getDoctrine()->getManager();

        $usuarioActual = $this->currentUser();

        // USUARIOS AMIGOS ORDENADOS POR SUS PUNTOS
        $userGrupos = $usuarioActual->getUsergrupos();
        $grupos = array();

        foreach( $userGrupos as $userGrupo ){
            $idGrupo = $userGrupo->getGrupo()->getId();

            $grupos[$idGrupo]=array(

                'grupo'=>$userGrupo->getGrupo(),
                'clasificacion'=>$em->getRepository('FootballbetBundle:User')->clasificacionGrupo($idGrupo)
            );

        }

        return $this->render('FootballbetBundle:Grupo:group.html.twig',array(

            'grupos'=>$grupos

        ));
    }

    public function currentUser()
    {

        return $this->get('security.context')->getToken()->getUser();

    }

    public function crearGrupoFrontEndAction(Request $request)
    {
        $grupo = new Grupo();

        if (strtolower($request->getMethod()) == 'get') {
            $formulario = $this->createCreateFormFront($grupo);

            return $this->render('FootballbetBundle:Grupo:crear_grupo_front.html.twig', array(
                'form_grupo' => $formulario->createView()
            ));
        }

        if (strtolower($request->getMethod()) == 'post') {

            $entity = new Grupo();
            $formulario = $this->createCreateFormFront($entity);

            $formulario->handleRequest($request);

            if ($formulario->isValid()) {

                $current_user = $this->currentUser();

                if ($current_user != null){
                    $entity->setGroupAdmin($current_user);

                    $em = $this->getDoctrine()->getManager();
                    if(!$this->get('security.context')->isGranted('ROLE_GROUP_ADMIN')) {
                        $rolGrupoAdmin = $em->getRepository("FootballbetBundle:Group")->findByName("ROLE_GROUP_ADMIN");
                        if (count($rolGrupoAdmin) != 0) {
                            $rolGrupoAdmin = $rolGrupoAdmin[0];
                            $current_user->addGroup($rolGrupoAdmin);
                            $em->persist($current_user);
                            $em->flush();
                        }
                    }
                }

                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();

                // creando los usergrupos
                    // creando la relacion con el admin del grupo como si fuera un miembro mas del grupo
                        $grupoUser = new UserGrupo();
                        $grupoUser->setUser($current_user);
                        $grupoUser->setGrupo($entity);
                        $em->persist($grupoUser);
                        $em->flush();
                // creando la relacion con el admin del grupo como si fuera un miembro mas del grupo

                $usuariosAnotificar = $formulario->get("users")->getData()->toArray();
                $enviados = 0;
                // notificar a los usuarios
                foreach ($usuariosAnotificar as $usuario) {
                    if($usuario->getId() != $current_user->getId())
                    {
                        $notificacion = new Notificacion();
                        $notificacion->setUser($usuario);
                        $notificacion->setGrupo($entity);
                        $notificacion->setCodigo(md5(uniqid($usuario->getId().$entity->getId())));

                        $em->persist($notificacion);
                        $em->flush();

                        $envioResult = $this->emailTo($notificacion);

                        $enviados+=count($envioResult);
                    }
                }

                $messageToastrText = 'Se han invitado a '.($enviados).' usuarios mediante email  para participar en el Grupo: '.$entity->getNombre();

                $this->get('session')->getFlashBag()->add('info',$messageToastrText);

                //return new Response(json_encode('ok'));
                return $this->redirect($this->generateUrl('user_profile'));
            }

            $this->get('session')->getFlashBag()->add('error',
                'Error al crear el grupo ');

            return $this->redirect($this->generateUrl('home'));

        }
    }

    private function createCreateFormFront(Grupo $entity)
    {
          /*$formulario = $this->createFormBuilder($entity)
              ->add('nombre','text',array('label'=>'Nombre', 'attr'=> array('class'=>'form-control input-sm form-group')))
              ->add('tipo','entity',array(
                  'multiple'=>false,
                  'expanded'=>true,
                  'class' => 'FootballbetBundle:Nomenclador',
                  'label'=>'Tipo',
                  'attr'=> array('class'=>'form-control form-group form-inline','style'=>'margin-left:10px;')

              ))
              ->add('users', 'entity',
                  array(
                      'class' => 'FootballbetBundle:User',
                      'required' => false,
                      'multiple' => true,
                      'label'=>'Usuarios',
                      'query_builder' => function ($repository) {
                              $query = $repository->createQueryBuilder('user')
                                  ->select('user');
                              return $query;
                          },
                      'mapped' =>false,
                      'attr'=> array('class'=>'form-control form-group')
                  )
              )
              ->add('submit', 'submit', array('label' => 'Crear', 'attr'=> array('class'=>'btn green')))
          ;
        $formulario = $formulario->setAction($this->generateUrl('crear_grupo_frontend'))->setMethod('POST');

        return $formulario->getForm();*/

        $form = $this->createForm(new GrupoType(), $entity, array(
            'action' => $this->generateUrl('crear_grupo_frontend'),
            'method' => 'POST',
            'current_user'=>$this->currentUser()
        ));

        $form->add('submit', 'submit', array('label' => 'Crear', 'attr'=> array('class'=>'btn green')));

        return $form;
    }
}
