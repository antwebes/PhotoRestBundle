<?php

namespace Ant\PhotoRestBundle\EntityManager;

use Doctrine\ORM\EntityManager;
use Ant\PhotoRestBundle\ModelManager\ParticipantManager as BasePhotoManager;
use Ant\PhotoRestBundle\Model\ParticipantInterface;

class ParticipantManager extends BasePhotoManager
{
	/**
	 * @var EntityManager
	 */
	protected $em;
	
	/**
	 * @var EntityRepository
	 */
	protected $repository;
	
	/**
	 * @var string
	 */
	protected $class;
	
	/**
	 * Constructor.
	 *
	 * @param \Doctrine\ORM\EntityManager                  $em
	 * @param string                                       $class
	 */
	public function __construct(EntityManager $em, $class)
	{
		$this->em = $em;
		$this->repository = $em->getRepository($class);
	
		$metadata = $em->getClassMetadata($class);
		$this->class = $metadata->name;
	}
	
	
	/**
	 * Finds one participant by the given criteria
	 *
	 * @param array $criteria
	 * @return PhotoInterface
	 */
	public function findParticipantBy(array $criteria)
	{
		return $this->repository->findOneBy($criteria);
	}
	
	/**
	 * Returns the fully qualified comment thread class name
	 *
	 * @return string
	 **/
	public function getClass()
	{
		return $this->class;
	}
}
