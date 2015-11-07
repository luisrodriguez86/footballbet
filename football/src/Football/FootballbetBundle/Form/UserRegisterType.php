<?php

namespace Football\FootballbetBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Image;


class UserRegisterType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', 'text', array(
                'required' => false,
                'constraints' => array(new NotBlank(array('message' => 'Nombre incorrecto.'))),
                'attr' => array('class' => 'form-control placeholder-no-fix valid', 'placeholder' => 'Nombre Completo')))
            ->add('email', 'email', array(
                'required' => false,
                'constraints' => array(
                    new NotBlank(array('message' => 'Email incorrecto.'))),
                'attr' => array('class' => 'form-control placeholder-no-fix valid', 'placeholder' => 'Email')))
            ->add('password', 'password', array(
                'required' => false,
                'constraints' => array(new NotBlank(array('message' => 'Debe especificar una clave.')), new Length(array('min' => 6, 'minMessage' => 'Debe tener al menos 6 caracteres.'))),
                'attr' => array('class' => 'form-control placeholder-no-fix valid', 'placeholder' => 'Clave')))
            ->add('password_confirm', 'password', array(
                'required' => false,
                'mapped' => false,
                'constraints' => array(new NotBlank(array('message' => 'Debe especificar una clave.')), new Length(array('min' => 6, 'minMessage' => 'Debe tener al menos 6 caracteres.'))),
                'attr' => array('class' => 'form-control placeholder-no-fix valid', 'placeholder' => 'Clave')))
            ->add('file', 'file', array(
                'required' => false,
                'attr' => array('class' => 'form-control placeholder-no-fix valid')));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            //'data_class' => 'Football\FootballbetBundle\Entity\User',
            /*'validation_constraint' => array(
                'nombre'=>new NotBlank(array('message' => 'Nombre incorrecto.')),
                'email' => new Email(array('message' => 'Email no válido.')),
                'email' => new NotBlank(array('message' => 'Email incorrecto.')),
                'password' => new NotBlank(array('message' => 'Debe especificar una clave.')),
                'password' => new Length(array('min' => 6, 'minMessage' => 'Debe tener al menos 6 caracteres.')),
                'password_confirm' => new NotBlank(array('message' => 'Debe especificar una clave.')),
                'password_confirm' => new Length(array('min' => 6, 'minMessage' => 'Debe tener al menos 6 caracteres.')),
                'file' => new NotBlank(array('message' => 'Debe especificar un avatar.')),
                'file' => new Image(array('mimeTypesMessage' => 'Debe selecionar una imagen.'))
            ),*/
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
        ));
    }


    /**
     * @return string
     */
    public function getName()
    {
        return 'football_footballbetbundle_user_register';
    }
}
