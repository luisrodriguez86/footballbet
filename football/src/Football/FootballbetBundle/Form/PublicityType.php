<?php

namespace Football\FootballbetBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PublicityType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name','text', array('label'=>'Nombre','attr'=> array('class'=>'form-control input-sm form-group')))
            ->add('file','file', array('data_class'=> null, 'label'=>'Foto','attr'=> array('class'=>'form-group')))
            // ->add('date','hidden')
            ->add('expiredate','date', array('label'=>'Fecha Expiracion','attr'=> array('class'=>'form-control input-sm form-group')))

        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Football\FootballbetBundle\Entity\Publicity'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'football_footballbetbundle_publicity';
    }
}
