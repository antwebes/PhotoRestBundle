<?php

namespace Ant\PhotoRestBundle\ModelManager;

use Ant\PhotoRestBundle\Model\VoteInterface;

/**
 * With this class you can create a entity Vote, without class final and independent of ORM
 * @author Chrysweel
 *
 */

abstract class VoteManager
{
	public function saveVote(VoteInterface $vote)
	{
		$this->doSaveVote($vote);
	}
	
	public function deleteVote(VoteInterface $vote)
	{
		$this->doDeleteVote($vote);
	}
	
	public function createVote()
	{
		$class = $this->getClass();
		$vote = new $class;
		
		return $vote;
	}
	/**
	 * @param string $id
	 * @return VoteInterface
	 */
	public function findVoteById($id)
	{
		return $this->findVoteBy(array('id' => $id));
	}
}