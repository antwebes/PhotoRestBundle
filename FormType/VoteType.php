<?php

namespace Ant\PhotoRestBundle\FormType;

use Ant\PhotoRestBundle\FormType\Transformer\IdToPhotoTransformer;
use Ant\PhotoRestBundle\ModelManager\PhotoManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class VoteType extends AbstractType
{
	private $voteClass;
    private $photoManager;
	
	public function __construct($voteClass, PhotoManagerInterface $photoManager)
	{
		$this->voteClass = $voteClass;
        $this->photoManager = $photoManager;
	}
	
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new IdToPhotoTransformer($this->photoManager);
        $builder
            ->add(
                $builder->create('photo', 'text')
                    ->addModelTransformer($transformer)
            )
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