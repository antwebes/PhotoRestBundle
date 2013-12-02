<?php

namespace Ant\PhotoRestBundle\EntityManager;

use Doctrine\ORM\EntityManager;
use Ant\PhotoRestBundle\ModelManager\AlbumManager as BaseAlbumManager;
use Ant\PhotoRestBundle\Model\ParticipantInterface;
use Ant\PhotoRestBundle\Model\AlbumInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

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
	public function doDelete(AlbumInterface $album)
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
        $query = $this->repository->createQueryBuilder('a')
            ->getQuery();

        return new Paginator($query);
	}
	/**
	 * Finds albums by the given criteria
	 *
	 * @param array $criteria
	 * @return array AlbumInterface
	 */
	public function findAlbumBy(array $criteria)
	{
        $qb = $this->repository->createQueryBuilder('a')->select('a');
        $whereConditions = array();


        foreach($criteria as $name => $value){
            $whereConditions[] = $qb->expr()->eq('a.'.$name, ":".$name);
            $qb->setParameter(":".$name, $value);
        }

        if(count($whereConditions) > 0){
            $whereSql = call_user_func_array(array($qb->expr(), 'andX'), $whereConditions);
            $qb->where($whereSql);
        }

        return new Paginator($qb);
	}
	/**
	 * Finds all albums of an user.
	 *
	 * @return array of AlbumInterface
	 */
	public function findAllAlbumsOfAnParticipant(ParticipantInterface $participant)
	{
		$query = $this->repository->createQueryBuilder('a')
			->where('a.participant = :participant' )
			->setParameter('participant', $participant)
			->getQuery();

        return new Paginator($query);
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
