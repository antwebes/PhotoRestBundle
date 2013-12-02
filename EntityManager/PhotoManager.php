<?php

namespace Ant\PhotoRestBundle\EntityManager;

use Doctrine\ORM\EntityManager;
use Ant\PhotoRestBundle\ModelManager\PhotoManager as BasePhotoManager;
use Ant\PhotoRestBundle\Model\PhotoInterface;

class PhotoManager extends BasePhotoManager
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
	 * @param PhotoInterface $photo
	 */
	protected function doSavePhoto(PhotoInterface $photo)
	{
		$this->em->persist($photo);
		$this->em->flush();
	}
	/**
	 * Finds one photo by the given criteria
	 *
	 * @param array $criteria
	 * @return PhotoInterface
	 */
	public function findPhotoBy(array $criteria)
	{
		return $this->repository->findOneBy($criteria);
	}
	/**
	 * Deletes a photo
	 *
	 * @param PhotoInterface $photo the photo to delete
	 */
	public function doDeletePhoto(PhotoInterface $photo)
	{
		$this->em->remove($photo);
		$this->em->flush();
	}
	/**
	 * Returns the fully qualified photo class name
	 *
	 * @return string
	 **/
	public function getClass()
	{
		return $this->class;
	}
}
