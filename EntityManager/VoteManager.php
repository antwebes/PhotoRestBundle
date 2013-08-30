<?php

namespace Ant\PhotoRestBundle\EntityManager;

use Doctrine\ORM\EntityManager;
use Ant\PhotoRestBundle\ModelManager\VoteManager as BaseVoteManager;
use Ant\PhotoRestBundle\Model\VoteInterface;
use Ant\PhotoRestBundle\Model\ParticipantInterface;

class VoteManager extends BaseVoteManager
{
	/**
	 * @var Entity\PhotoManager
	 */
	protected $photoManager;
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
	public function __construct(PhotoManager $photoManager, EntityManager $em, $class)
	{
		$this->photoManager = $photoManager;
		$this->em = $em;
		$this->repository = $em->getRepository($class);
	
		$metadata = $em->getClassMetadata($class);
		$this->class = $metadata->name;
	}
	
	/**
	 * Saves a vote
	 *
	 * @param VoteInterface $vote
	 */
	protected function doSaveVote(VoteInterface $vote)
	{
		$this->em->persist($vote);
		$this->em->flush();
	}
	/**
	 * Finds one vote by the given criteria
	 *
	 * @param array $criteria
	 * @return VoteInterface
	 */
	public function findVoteBy(array $criteria)
	{
		return $this->repository->findOneBy($criteria);
	}
	/**
	 * Deletes a vote
	 *
	 * @param VoteInterface $vote the vote to delete
	 */
	public function doDeleteVote(VoteInterface $vote)
	{
		$this->em->remove($vote);
		$this->em->flush();
	}
	/**
	 * Finds all votes.
	 *
	 * @return array of VoteInterface
	 */
	public function findAllVotes()
	{
		return $this->repository->findAll();
	}
	/**
	 * Finds all votes of an user.
	 *
	 * @return array of VoteInterface
	 */
	public function findAllVotesOfAnParticipant(ParticipantInterface $participant)
	{
		
		return $this->repository->createQueryBuilder('v')
			->where('v.participant = :participant' )
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
