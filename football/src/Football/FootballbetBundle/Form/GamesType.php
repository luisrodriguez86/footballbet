<?php

namespace Football\FootballbetBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GamesType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('localGoals')
            ->add('awayGoals')
            ->add('date')
            ->add('local')
            ->add('away')
            ->add('season')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Football\FootballbetBundle\Entity\Games'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'football_footballbetbundle_games';
    }
}
