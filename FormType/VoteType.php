<?php

namespace Ant\PhotoRestBundle\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class VoteType extends AbstractType
{
	private $voteClass;
	
	public function __construct($voteClass)
	{
		$this->voteClass = $voteClass;
	}
	
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('photo')
            ->add('score')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
    	parent::setDefaultOptions($resolver);
        $resolver->setDefaults(array(        	
            'data_class' => $this->voteClass,
        	'csrf_protection' => false
        ));
    }

    public function getName()
    {
        return "vote";
    }
}