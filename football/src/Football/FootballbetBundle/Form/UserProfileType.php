<?php

namespace Football\FootballbetBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserProfileType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $nombre = (isset($options['valores']['nombre']))?$options['valores']['nombre']:'';
        $password = (isset($options['valores']['password']))?$options['valores']['password']:'';
        $builder
            ->add('nombre','text', array('label'=>'Nombre','attr'=> array('value'=>$nombre,'class'=>'form-control input-sm form-group'),
                'constraints' => array(
                    new NotBlank(array('message' => 'Nombre incorrecto.')))))
            ->add('password', 'repeated', array('required'=>false,'first_name'=>'Clave','first_options'=>array('attr'=>array('value'=>$password)), 'second_name'=>'Confirmar','second_options'=>array('attr'=>array('value'=>$password)),'type'=>'password','constraints' => array(new NotBlank(array('message' => 'Debe especificar una clave.')) ),))
            ->add('file','file', array('required'=>false,'label'=>'Foto','attr'=> array('class'=>'upload'),));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
           /* 'data_class' => 'Football\FootballbetBundle\Entity\User'*/
        ));

        $resolver->setRequired(array('valores'));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'football_footballbetbundle_user_profile';
    }
}
