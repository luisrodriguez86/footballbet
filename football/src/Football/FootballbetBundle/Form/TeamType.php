<?php

namespace Football\FootballbetBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TeamType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', 'text', array('label'=>'Nombre','attr'=>array('class'=>'form-control form-group')))
            ->add('logo', 'file', array('data_class'=> null,'label'=>'Logo', 'attr'=> array('class'=>'form-group')))
            ->add('league', 'entity', array('class'=>'FootballbetBundle:League','property'=>'nombre','label'=>'Liga', 'attr'=> array('class'=>'form-control form-group')))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Football\FootballbetBundle\Entity\Team'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'football_footballbetbundle_team';
    }
}
