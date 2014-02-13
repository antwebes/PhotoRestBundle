<?php

namespace Ant\PhotoRestBundle\Handler;

use Ant\PhotoRestBundle\ModelManager\PhotoManager;

class PhotoHandler
{
	/**
	 * @var ObjectManager
	 */
	private $om;
	private $photoManager;
	private $entityClass;
	private $formFactory;
	private $securityContext;
	
	/**
	 * @param ObjectManager $om
	 */
	public function __construct(PhotoManager $photoManager, $entityClass, FormFactoryInterface $formFactory, SecurityContext $securityContext)
	{
		$this->photoManager = $photoManager;
		
		$this->om = $om;
		$this->entityClass = $entityClass;
		$this->formFactory = $formFactory;
		$this->securityContext = $securityContext;
	}
	
	/**
	 * Create a new Photo.
	 *
	 * @param array $parameters
	 *
	 * @return Photo	 
	 */
	public function post(array $parameters)
	{
		$photo = $this->photoManager->createPhoto();
	
		return $this->processForm($photo, $parameters, 'POST');
	}
}