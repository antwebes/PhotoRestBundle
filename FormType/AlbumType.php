<?php

namespace Ant\PhotoRestBundle\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AlbumType extends AbstractType
{
	private $albumClass;
	
	public function __construct($albumClass)
	{
		$this->albumClass = $albumClass;
	}
	
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('description')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
    	parent::setDefaultOptions($resolver);
        $resolver->setDefaults(array(        	
            'data_class' => $this->albumClass,
        	'csrf_protection' => false
        ));
    }

    public function getName()
    {
        return "ant_photo_album";
    }
}