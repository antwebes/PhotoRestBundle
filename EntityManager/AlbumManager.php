<?php

namespace Ant\PhotoRestBundle\EntityManager;

use Doctrine\ORM\EntityManager;
use Ant\PhotoRestBundle\ModelManager\AlbumManager as BaseAlbumManager;

use Ant\PhotoRestBundle\Model\AlbumInterface;

class AlbumManager extends BaseAlbumManager
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
	 * Saves a album
	 *
	 * @param AlbumInterface $album
	 */
	protected function doSaveAlbum(AlbumInterface $album)
	{
		$this->em->persist($album);
		$this->em->flush();
	}
	/**
	 * Finds one album by the given criteria
	 *
	 * @param array $criteria
	 * @return AlbumInterface
	 */
	public function findOneAlbumBy(array $criteria)
	{
		return $this->repository->findOneBy($criteria);
	}
	/**
	 * Deletes a album
	 *
	 * @param AlbumInterface $album the album to delete
	 */
	public function doDeleteAlbum(AlbumInterface $album)
	{
		$this->em->remove($album);
		$this->em->flush();
	}
	/**
	 * Finds all albums.
	 *
	 * @return array of AlbumInterface
	 */
	public function findAllAlbums()
	{
		return $this->repository->findAll();
	}
	/**
	 * Finds all albums of an user.
	 *
	 * @return array of AlbumInterface
	 */
	public function findAllAlbumsOfAnParticipant(ParticipantInterface $participant)
	{
		return $this->repository->createQueryBuilder('a')
			->where('a.participant = :participant' )
			->setParameter('participant', $participant)
			->getQuery()
			->execute();
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
