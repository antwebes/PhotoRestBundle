<?php

namespace Ant\PhotoRestBundle\EntityManager;

use Doctrine\ORM\EntityManager;
use Ant\PhotoRestBundle\ModelManager\VoteManager as BaseVoteManager;
use Ant\PhotoRestBundle\Model\VoteInterface;

class VoteManager extends BaseVoteManager
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
	 * Saves a photo
	 *
	 * @param VoteInterface $photo
	 */
	protected function doSaveVote(VoteInterface $photo)
	{
		$this->em->persist($photo);
		$this->em->flush();
	}
	/**
	 * Finds one photo by the given criteria
	 *
	 * @param array $criteria
	 * @return VoteInterface
	 */
	public function findVoteBy(array $criteria)
	{
		return $this->repository->findOneBy($criteria);
	}
	/**
	 * Deletes a photo
	 *
	 * @param VoteInterface $photo the photo to delete
	 */
	public function doDeleteVote(VoteInterface $photo)
	{
		$this->em->remove($photo);
		$this->em->flush();
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
