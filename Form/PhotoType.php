<?php

namespace Ant\PhotoRestBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PhotoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('image')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(        	
            'data_class' => 'Ant\PhotoRestBundle\Entity\Photo',
        	'csrf_protection' => false
        ));
    }

    public function getName()
    {
        return 'photo';
    }
}