<?php 

namespace Ant\PhotoRestBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Ant\PhotoRestBundle\Controller\BaseRestController;

use JMS\SecurityExtraBundle\Annotation\SecureParam;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Chatea\ApiBundle\Entity\User;

/**
 * Vote controller.
 */
class VoteController extends BaseRestController
{
	/**
	 * Create a new vote entity
	 *  @ApiDoc(
	 *  	description="create a vote",
	 *  	section="propietario",
	 *  	input="Ant\PhotoRestBundle\FormType\VoteType",
	 *  	output="Ant\PhotoRestBundle\Model\Vote",
	 *		statusCodes={
	 *         201="New entity created",
	 *         400="Bad request"
	 *     }
	 *  )
	 *  @SecureParam(name="user", permissions="OWNER,HAS_ROLE_ROLE_ADMIN,HAS_ROLE_APPLICATION")
	 *  @ParamConverter("user", class="ApiBundle:User", options={"error" = "user.entity.unable_find"})
	 */
	public function createAction(User $user, Request $request)
	{
		$dataRequest = $request->request->get('vote');
		//get id of photo from request
		$photoId = $dataRequest['photo']; 
		$photoManager = $this->get('ant.photo_rest.entity_manager.photo_manager');
		$photo = $photoManager->findPhotoById($photoId);
		
		if (!$photo) {
			return $this->createError('Unable to find Photo entity', '42', '404');
    	}    	
    	
    	$participantManager = $this->get('ant.photo_rest.manager.participant_manager');
    	$currentUser = $participantManager->findParticipantById($id);
    	
		$voteManager = $this->get('ant.photo_rest.manager.vote_manager');
		$existVote = $voteManager->findVoteByPhotoAndParticipant($photoId, $currentUser->getId());
		
		if ($existVote){
			return $this->createError('You have already voted this photo ', '46', '409');
		}

		$vote = $voteManager->createVote();
		
		$form = $this->get('ant.photo_rest.form_factory.vote.default')->createForm();
		$form->setData($vote);
		
		$form->bind($dataRequest);
		
		if ($form->isValid()) {
						
			$vote->setParticipant($currentUser);
			$voteManager->saveVote($vote, $photo);
			return $this->buildView($vote, 200);
		}
		return $this->buildFormErrorsView($form);
	}
	/**
	 * Show the vote of a photo
	 *  @ApiDoc(
	 *  	description="show a vote of a photo",
	 *  	section="photo",
	 *  	input="photo_id",
	 *  	output="Ant\PhotoRestBundle\Model\Vote",
	 *		statusCodes={
	 *         200="Returned when successful",
	 *         404="Unable to find Vote or Photo entity with code 42"
	 *     }
	 *  )
	 */
	public function showAction($id)
	{
		$result = $this->getPhotoAndVote($id);
		
		if (is_array($result)){
			return $this->buildView($result['vote'], 200);
		}
		else return $result;
	}
	/**
	 * Show all votes of an user
	 *  @ApiDoc(
	 *  	description="show all votes of an user",
	 *  	section="propietario",
	 *  	output="Ant\PhotoRestBundle\Model\Vote",
	 *		statusCodes={
	 *         200="Returned when successful",
	 *     }
	 *  )
	 */
	public function votesAction($id)
	{
		$participantManager = $this->get('ant.photo_rest.manager.participant_manager');
		$currentUser = $participantManager->findParticipantById($id);
		
		$votes = $this->get('ant.photo_rest.manager.vote_manager')->findAllVotesOfAnParticipant($currentUser);
		return $this->buildView($votes, 200);
	}
	/**
	 * Delete a new vote entity
	 *  @ApiDoc(
	 *  	description="delete a vote",
	 *  	section="vote",
	 *      input="Photo_id",
	 *  	output="Ant\PhotoRestBundle\Model\Vote",
	 *		statusCodes={
	 *         200="Returned when successful",
	 *         403="Access denied",
	 *         404="Unable to find Vote or Photo entity with code 42"
	 *     }
	 *  )
	 *  @SecureParam(name="user", permissions="OWNER,HAS_ROLE_ROLE_ADMIN,HAS_ROLE_APPLICATION")
	 *  @ParamConverter("user", class="ApiBundle:User", options={"error" = "user.entity.unable_find"})
	 */
	public function deleteAction(User $user, $photo_id)
	{
		$result = $this->getPhotoAndVote($photo_id);
		
		if (is_array($result)){
			$this->get('ant.photo_rest.manager.vote_manager')->deleteVote($result['vote'], $result['photo']);
			return $this->buildView('Vote deleted', 200);
		}
		else return $result;		
	}
	
	private function getPhotoAndVote($photoId)
	{
		$photoManager = $this->get('ant.photo_rest.entity_manager.photo_manager');
		$photo = $photoManager->findPhotoById($photoId);
		
		if (!$photo) {
			return $this->createError('Unable to find Photo entity', '42', '404');
		}
		$currentUser = $this->get('security.context')->getToken()->getUser();
		
		$voteManager = $this->get('ant.photo_rest.manager.vote_manager');
		$vote = $voteManager->findVoteByPhotoAndParticipant($photoId, $currentUser->getId());
		
		if (!$vote) {
			return $this->createError('Unable to find Vote entity', '42', '404');
		}
		
		return array('vote' => $vote,
					'photo' => $photo);
	}
}
