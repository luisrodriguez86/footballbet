<?php

namespace Football\FootballbetBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GrupoType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $current_user = $options['current_user'];

        $builder
            ->add('nombre','text',array('label'=>'Nombre', 'attr'=> array('class'=>'form-control input-sm form-group')))
            ->add('league','entity',array(
                'multiple'=>false,
                'expanded'=>true,
                'class' => 'FootballbetBundle:League',
                'label'=>'Liga',
                'attr'=> array('class'=>'form-control form-group form-inline','style'=>'margin-left:10px;')

            ))
            ->add('users', 'entity',
                array(
                    'class' => 'FootballbetBundle:User',
                    'property'=>'nombre',
                    'required' => false,
                    'multiple' => true,
                    'label'=>'Invitar Usuarios',
                    'query_builder' => function ($repository) use ($current_user) {
                            $query = $repository->createQueryBuilder('user')
                                ->select('user')->where('user.id != :currentUserId')->setParameter('currentUserId',$current_user->getId());
                            return $query;
                        },
                    'mapped' =>false,
                    'attr'=> array('class'=>'form-control form-group')
                )
            );
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Football\FootballbetBundle\Entity\Grupo'
        ));

        $resolver->setRequired(array('current_user'));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'football_footballbetbundle_grupo';
    }
}
