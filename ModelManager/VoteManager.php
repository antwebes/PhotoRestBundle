<?php

namespace Ant\PhotoRestBundle\ModelManager;

use Ant\PhotoRestBundle\Model\ParticipantInterface;

use Ant\PhotoRestBundle\Model\PhotoInterface;

use Ant\PhotoRestBundle\Model\VoteInterface;

/**
 * With this class you can create a entity Vote, without class final and independent of ORM
 * @author Chrysweel
 *
 */

abstract class VoteManager
{
	/**
	 * @var PhotoManager
	 */
	protected $photoManager;
	
	/**
	 * Constructor.
	 *
	 * @param \Doctrine\ORM\EntityManager                  $em
	 * @param string                                       $class
	 */
	public function __construct(PhotoManager $photoManager)
	{
		$this->photoManager = $photoManager;
	}
	
	
	public function saveVote(VoteInterface $vote, PhotoInterface $photo)
	{
		$this->doSaveVote($vote);
		$this->photoManager->updateScore($photo, $vote->getScore(), '+');
	}
	
	public function deleteVote(VoteInterface $vote, PhotoInterface $photo)
	{
		$this->photoManager->updateScore($photo, $vote->getScore(), '-');
		$this->doDeleteVote($vote);
	}
	
	public function createVote()
	{
		$class = $this->getClass();
		$vote = new $class;
		
		return $vote;
	}
	
	public function findAllVotesOfAnParticipant(ParticipantInterface $participant)
	{
		return $this->findAllVotesOfAnParticipant($participant);
	}
	/**
	 * @param string $id
	 * @return VoteInterface
	 */
	public function findVoteById($id)
	{
		return $this->findVoteBy(array('id' => $id));
	}
	
	public function findVoteByPhotoAndParticipant($photoId, $participantId)
	{
		return $this->findVoteBy(array('photo' => $photoId, 'participant' => $participantId));
	}
}