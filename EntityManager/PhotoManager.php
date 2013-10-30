<?php

namespace Ant\PhotoRestBundle\EntityManager;

use Ant\PhotoRestBundle\Model\ParticipantInterface;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Pagination\Paginator;
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
		$this->class = $metadata->getName();
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
	 * Finds photos by the given criteria
	 *
	 * @param array $criteria
	 * @return array PhotoInterface
	 */
	public function findPhotoBy(array $criteria)
	{
		$qb = $this->repository->createQueryBuilder('p')->select('p');
		$whereConditions = array();
		
		
		foreach($criteria as $name => $value){
		  $whereConditions[] = $qb->expr()->eq('p.'.$name, ":".$name);
		  $qb->setParameter(":".$name, $value);
		}
		
		if(count($whereConditions) > 0){
		  $whereSql = call_user_func_array(array($qb->expr(), 'andX'), $whereConditions);
		  $qb->where($whereSql);
		}
		
		return new Paginator($qb);
	}
	/**
	 * Finds one photo by the given criteria
	 *
	 * @param array $criteria
	 * @return PhotoInterface
	 */
	public function findOnePhotoBy(array $criteria)
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
	 * Returns the fully qualified comment thread class name
	 *
	 * @return string
	 **/
	public function getClass()
	{
		return $this->class;
	}
}
