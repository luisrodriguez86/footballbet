<?php

namespace Football\FootballbetBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre','text', array('label'=>'Nombre','attr'=> array('class'=>'form-control input-sm form-group')))
            //->add('salt')
            ->add('file','file', array('label'=>'Foto','attr'=> array('class'=>'form-group')))
            ->add('password','repeated',array(
                'first_name' => 'Clave',
                'second_name' => 'Repetir_clave',
                'type' => 'password',
                'attr'=> array('class'=>'form-control input-sm form-group')
            ))
            ->add('email','email', array('label'=>'Email','attr'=> array('class'=>'form-control input-sm form-group')))
            //->add('registerCode')
            //->add('isActive')
            ->add('groups', 'entity', array('multiple'=>true,'class'=>'FootballbetBundle:Group','property'=>'name','label'=>'Grupos', 'attr'=> array('class'=>'form-control form-group')))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Football\FootballbetBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'football_footballbetbundle_user';
    }
}
