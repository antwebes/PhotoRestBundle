<?php

namespace Ant\PhotoRestBundle\EntityManager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Ant\PhotoRestBundle\ModelManager\VoteManager as BaseVoteManager;
use Ant\PhotoRestBundle\Model\VoteInterface;
use Ant\PhotoRestBundle\Model\ParticipantInterface;

class VoteManager extends BaseVoteManager
{
	const DATE_FORMAT = 'Y-m-d H:i:s';
	
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
		$query = $this->repository->createQueryBuilder('v')->select('v')
			->where('v.participant = :participant' )
			->setParameter('participant', $participant->getId())
			->getQuery();
		//ldd($query);
			
		return new Paginator($query, false);
		
	}
	
	
	/**
	 * Number of votes created in a date range, grouped by day
	 *
	 * @param string $dateTimeInit the date init
	 * @param string $dateTimeEnd the date end
	 * @return array | null
	 */
	public function countVotesCreatedBetweenDatesGroupByDates(\DateTime $dateTimeInit, \DateTime $dateTimeEnd)
	{
		$queryBuilder = $this->em->createQueryBuilder();
		$queryBuilder->select('DATE(v.publicatedAt) AS DateCreatedAt, COUNT(v.publicatedAt) AS Votes, COUNT(DISTINCT v.participant) AS Users')
		->from('FotoBundle:Vote','v')
		->where($queryBuilder->expr()->between('v.publicatedAt', ':dateTimeInit', ':dateTimeEnd'))
		->addGroupBy('DateCreatedAt')
		->addOrderBy('DateCreatedAt');
	
	
		$queryBuilder->setParameter('dateTimeInit',$dateTimeInit->format(self::DATE_FORMAT));
		$queryBuilder->setParameter('dateTimeEnd',$dateTimeEnd->format(self::DATE_FORMAT));
	
		$result = $queryBuilder->getQuery()->getScalarResult();
	
		return $result;
	}
	
	/**
	 * Number of votes created in a date range, in a date range, grouped by weeks
	 *
	 * @param string $dateTimeInit the date init
	 * @param string $dateTimeEnd the date end
	 * @return array | null
	 */
	public function countVotesCreatedBetweenDatesGroupByWeeks(\DateTime $dateTimeInit, \DateTime $dateTimeEnd)
	{
		$queryBuilder = $this->em->createQueryBuilder();
		$queryBuilder->select('YEAR(v.publicatedAt) AS YEAR, WEEK(v.publicatedAt) AS WEEK, COUNT(v.publicatedAt) AS Votes , COUNT(DISTINCT v.participant) AS Users')
		->from('FotoBundle:Vote','v')
		->where($queryBuilder->expr()->between('v.publicatedAt', ':dateTimeInit', ':dateTimeEnd'))
		->groupBy('YEAR')
		->addGroupBy('WEEK')
		->OrderBy('YEAR','DESC')
		->addOrderBy('WEEK');
	
	
		$queryBuilder->setParameter('dateTimeInit',$dateTimeInit->format(self::DATE_FORMAT));
		$queryBuilder->setParameter('dateTimeEnd',$dateTimeEnd->format(self::DATE_FORMAT));
	
		$result = $queryBuilder->getQuery()->getScalarResult();
	
		return $result;
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
