<?php

namespace Football\FootballbetBundle\Controller;

use Football\FootballbetBundle\Entity\NotificacionForgot;
use Football\FootballbetBundle\Form\UserProfileType;
use Football\FootballbetBundle\Form\UserRegisterType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Football\FootballbetBundle\Entity\User;
use Football\FootballbetBundle\Form\UserType;

use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\ImageValidator;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * User controller.
 *
 */
class UserController extends Controller
{

    /**
     * Lists all User entities.
     *
     */
    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FootballbetBundle:User')->findAll();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->getRequest()->get('page', 1),
            10);
        return $this->render('FootballbetBundle:User:index.html.twig', compact(
            'pagination'
        ));
    }

    public function filtro_userAction()
    {
        if (!$this->getRequest()->isXmlHttpRequest()) return '';

        $nombre = $this->getRequest()->get('nombre', '');

        $email = $this->getRequest()->get('email', '');

        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FootballbetBundle:User')->findByNombreYEmail($nombre, $email);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->getRequest()->get('page', 1),
            10);
        return $this->render('FootballbetBundle:User:tableUsers.html.twig', compact(
            'pagination'));
    }

    /**
     * Creates a new User entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new User();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $encoder = $this->get('security.encoder_factory')->getEncoder($entity);
            $password = $encoder->encodePassword(
                $entity->getPassword(),
                $entity->getSalt()
            );
            $entity->setPassword($password);

            $entity->upload();

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('user_show', array('id' => $entity->getId())));
        }

        return $this->render('FootballbetBundle:User:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a User entity.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(User $entity)
    {
        $form = $this->createForm(new UserType(), $entity, array(
            'action' => $this->generateUrl('user_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Crear', 'attr' => array('class' => 'btn green')));

        return $form;
    }

    /**
     * Displays a form to create a new User entity.
     *
     */
    public function newAction()
    {
        $entity = new User();
        $form = $this->createCreateForm($entity);

        return $this->render('FootballbetBundle:User:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a User entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FootballbetBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FootballbetBundle:User:show.html.twig', array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),));
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FootballbetBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FootballbetBundle:User:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a User entity.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(User $entity)
    {
        $form = $this->createForm(new UserType(), $entity, array(
            'action' => $this->generateUrl('user_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Actualizar', 'attr' => array('class' => 'btn purple')));

        return $form;
    }

    /**
     * Edits an existing User entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FootballbetBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $encoder = $this->get('security.encoder_factory')->getEncoder($entity);
            $password = $encoder->encodePassword(
                $entity->getPassword(),
                $entity->getSalt()
            );
            $entity->setPassword($password);

            $entity->upload();
            $em->flush();

            return $this->redirect($this->generateUrl('user_edit', array('id' => $id)));
        }

        return $this->render('FootballbetBundle:User:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a User entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FootballbetBundle:User')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find User entity.');
            }

            $entity->removeUpload();

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('user'));
    }

    /**
     * Creates a form to delete a User entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('user_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Eliminar', 'attr' => array('class' => 'btn btn-xs red')))
            ->getForm();
    }

    public function login_registerAction(Request $request)
    {
        $entity = new User();
         $form = $this->createRegisterForm($entity);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($form->get('password')->getData() != $form->get('password_confirm')->getData())
                return $this->redirect($this->generateUrl('login_register'));

            $entity->upload();

            $encoder = $this->get('security.encoder_factory')->getEncoder($entity);
            $password = $encoder->encodePassword(
                $entity->getPassword(),
                $entity->getSalt()
            );
            $entity->setPassword($password);

            $em = $this->getDoctrine()->getManager();

            $grupoUser = $em->getRepository("FootballbetBundle:Group")->findByRole("ROLE_USER");

            $entity->addGroup($grupoUser[0]);

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl("home"));

        } else {

            return $this->render('FootballbetBundle:User:login-register.html.twig', array(
                'form' => $form->createView()
            ));

        }
    }

    public function createRegisterForm(User $entity)
    {

        $form = $this->createForm(new UserRegisterType(), $entity, array(
            'action' => $this->generateUrl('login_register'),
            'method' => 'POST'
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar', 'attr' => array('class' => 'btn green pull-right')));

        return $form;
    }

    public function forgot_passwordAction(Request $request)
    {
        $form = $this->createFormBuilder(null)
            ->add('email', 'email', array(
                'required' => false,
                'attr' => array('class' => 'form-control placeholder-no-fix valid', 'placeholder' => 'Email'),
                'constraints' => array(new Regex(array(
                    'pattern' => "/[a-zA-Z0-9\._-]+@[a-zA-Z0-9\._-]+$\.[a-z]/",
                    'match' => false,
                    'message' => 'Email incorrecto.'
                )))
            ))
            ->getForm();
        $form->handleRequest($request);


        if (strtolower($request->getMethod()) == 'post' && $form->isValid()) {
            $email = $form->get('email')->getData();

            if ($email != '') {
                $em = $this->getDoctrine()->getManager();
                $usuario = $em->getRepository('FootballbetBundle:User')->findByEmail($email);
                if (count($usuario) != 0) {

                    $usuario =$usuario[0];
                    $notificacionForgot = $em->getRepository('FootballbetBundle:NotificacionForgot')->findByUser($usuario->getId());

                    if(count($notificacionForgot) == 0) {
                        $notificacionForgot = new NotificacionForgot();
                        $notificacionForgot->setUser($usuario);
                        $notificacionForgot->setCodigo(md5($usuario->getId().$usuario->getEmail()));
                        $em->persist($notificacionForgot);
                        $em->flush();

                    }else
                        $notificacionForgot = $notificacionForgot[0];
                    $url = $this->generateUrl('forgotpassword_cambiarpass',array('codigo'=>$notificacionForgot->getCodigo()),true);

                    $this->emailTo($usuario,$url);

                    $envio = 'Se le ha enviado un email con las instrucciones para el cambio de clave.';

                    return $this->render('FootballbetBundle:User:forgot-password.html.twig', array('form' => $form->createView(),'envio'=>$envio));

                } else {
                    $form->get('email')->addError(new FormError('Email incorrecto.'));
                }
            }
        }


        return $this->render('FootballbetBundle:User:forgot-password.html.twig', array('form' => $form->createView()));


    }

    public function emailTo($usuario,$url)
    {
        $email = $usuario->getEmail();

        $nombreSitio = $this->container->getParameter('football.sitio.nombre');
        $correoAdmin = $this->container->getParameter('football.sitio.correo_admin');

        // Varios destinatarios
        $para = $email;

        // subject
        $subject = $nombreSitio.' - Solicitud Cambio de Clave';

        // message
        $mensaje = $this->renderView('FootballbetBundle::email_template_forgotpassword.html.twig', array('url' => $url,'usuario'=>$usuario));

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($correoAdmin)
            ->setTo($para)
            ->setBody($mensaje, 'text/html');

		return $this->get('mailer')->send($message);
    }

    public function cambiarpassAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        if(strtolower($request->getMethod())=='get') {
            $codigo = $request->query->get('codigo', '');
            $notificacionForgot = $em->getRepository('FootballbetBundle:NotificacionForgot')->findByCodigo($codigo);

            if (count($notificacionForgot) == 0) {
                $messageToastrText = 'Petici&oacute;n incorrecta.';

                $this->get('session')->getFlashBag()->add('info',$messageToastrText);
                return $this->redirect($this->generateUrl('home'));
            } else {

                $notificacionForgot = $notificacionForgot[0];

                $form = $this->createForgotForm($codigo);

                return $this->render('FootballbetBundle:User:forgot-password2.html.twig', array('form' => $form->createView()));
            }
        }else{

            $formData = $request->request->get('form');
            $codigo = $formData['codigo'];
            $form = $this->createForgotForm($codigo);

            if($codigo == '')
            {
                $error = "Email incorrecto.";
                return $this->render('FootballbetBundle:User:forgot-password2.html.twig', array('error' => $error,'form'=>$form->createView()));
            }else{

                $notificacionForgot =  $em->getRepository('FootballbetBundle:NotificacionForgot')->findByCodigo($codigo);
                if(count($notificacionForgot)!=0)
                {
                    $notificacionForgot =$notificacionForgot[0];
                }else
                {

                    $messageToastrText = 'Petici&oacute;n incorrecta.';

                    return $this->render('FootballbetBundle:User:forgot-password2.html.twig', array('error' => $messageToastrText,'form'=>$form->createView()));
                }

                $passwordData = $formData['password'];
                $password_confirmData = $formData['password_confirm'];

                if($passwordData != $password_confirmData){
                    $error = "Debe introducir la misma clave dos veces.";
                    return $this->render('FootballbetBundle:User:forgot-password2.html.twig', array('error' => $error,'form'=>$form->createView()));
                }
                $usuario = $notificacionForgot->getUser();

                $encoder = $this->get('security.encoder_factory')->getEncoder($usuario);
                $password = $encoder->encodePassword(
                    $passwordData,
                    $usuario->getSalt()
                );
                $usuario->setPassword($password);

                $em->persist($usuario);
                $em->remove($notificacionForgot);

                $em->flush();

                $error = 'Se ha cambiado la contraseña de su cuenta.';
                return $this->render('FootballbetBundle:User:forgot-password2.html.twig', array('error' => $error,'form'=>$form->createView()));
            }
        }
    }

    public function createForgotForm($codigo){
        $form = $this->createFormBuilder(null)
            ->add('password', 'password', array(
                'required' => false,
                'constraints' => array(new NotBlank(array('message' => 'Debe especificar una clave.')), new Length(array('min' => 6, 'minMessage' => 'Debe tener al menos 6 caracteres.'))),
                'attr' => array('class' => 'form-control placeholder-no-fix valid', 'placeholder' => 'Clave')))
            ->add('password_confirm', 'password', array(
                'required' => false,
                'mapped' => false,
                'constraints' => array(new NotBlank(array('message' => 'Debe especificar una clave.')), new Length(array('min' => 6, 'minMessage' => 'Debe tener al menos 6 caracteres.'))),
                'attr' => array('class' => 'form-control placeholder-no-fix valid', 'placeholder' => 'Clave')))
            ->add('codigo','hidden',array('data'=>$codigo))
        ;
        $resultado = $form->getForm();

        return $resultado;
    }
    public function user_profileAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $usuario = $this->get('security.context')->getToken()->getUser();

        $nombre = $usuario->getNombre();

        if (strtolower($request->getMethod()) == 'get') {
            $form = $this->createFormProfile(array('nombre' => $nombre, 'password' => ' '));

            $mis_apuestas = $em->getRepository('FootballbetBundle:Bet')->findByUserId($usuario->getId());

            $profile_form = $form->createView();
            $paginator = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $mis_apuestas,
                $this->getRequest()->get('page', 1),
                10);
            return $this->render('FootballbetBundle:User:user_profile.html.twig', compact(
                'profile_form', 'usuario', 'pagination'));
        }

        if (strtolower($request->getMethod()) == 'post') {

            $em = $this->getDoctrine()->getManager();

            $editForm = $this->createFormProfile(array());

            $editForm->handleRequest($request);

            if ($editForm->isValid()) {

                if ($editForm->get('password')->getData() != ' ') {
                    $encoder = $this->get('security.encoder_factory')->getEncoder($usuario);
                    $password = $encoder->encodePassword(
                        $editForm->get('password')->getData(),
                        $usuario->getSalt()
                    );
                    $usuario->setPassword($password);

                }

                $fileImage =$editForm->get('file')->getData();

                if ($fileImage != null && stripos($fileImage->getMimeType(),"image")!== false ) {
                    $usuario->setFile($editForm->get('file')->getData());
                    $usuario->upload();
                }

                $usuario->setNombre($editForm->get('nombre')->getData());

                $em->flush();

                return $this->redirect($this->generateUrl('user_profile'));

            }

        }
    }

    public function createFormProfile($valores)
    {
        $form = $this->createForm(new UserProfileType(), null, array(
            'action' => $this->generateUrl('user_profile'),
            'method' => 'POST',
            'valores' => $valores
        ));

        $form->add('submit', 'submit', array('label' => 'Actualizar', 'attr' => array('class' => 'btn green')));

        return $form;
    }
}
