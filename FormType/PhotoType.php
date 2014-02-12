<?php

namespace Ant\PhotoRestBundle\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PhotoType extends AbstractType
{
	private $photoClass;
	
	public function __construct($photoClass)
	{
		$this->photoClass = $photoClass;
	}
	
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('image', 'file', array(
            		'label'=> 'Imagen'))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
    	parent::setDefaultOptions($resolver);
        $resolver->setDefaults(array(        	
            'data_class' => $this->photoClass,
        	'csrf_protection' => false
        ));
    }

    public function getName()
    {
        return '';
    }
}