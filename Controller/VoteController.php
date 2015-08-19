<?php 

namespace Ant\PhotoRestBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Chatea\UtilBundle\Controller\BaseRestController;

use JMS\SecurityExtraBundle\Annotation\SecureParam;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Ant\PhotoRestBundle\Model\ParticipantInterface;

use FOS\RestBundle\Controller\Annotations\QueryParam;

/**
 * Vote controller.
 */
class VoteController extends BaseRestController
{
	/**
	 * Create a new vote entity
	 * @ApiDoc(
	 *  	description="create a vote",
	 *  	section="photo",
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
	public function createAction(ParticipantInterface $user, Request $request)
	{
		$sessionUser = $this->getUser();

        if($sessionUser != null && $sessionUser->getId() != $user->getId()){
            return $this->createError('You can not vote for other user', '32', '403');
        }

		$dataRequest = $request->request->get('vote');
		//get id of photo from request
		$photoId = $dataRequest['photo']; 
		$photoManager = $this->get('ant.photo_rest.entity_manager.photo_manager');
		$photo = $photoManager->findPhotoById($photoId);
		
		if (!$photo) {
			return $this->createError('Unable to find Photo entity', '42', '404');
    	}
    	
		$voteManager = $this->get('ant.photo_rest.manager.vote_manager');
		$existVote = $voteManager->findVoteByPhotoAndParticipant($photoId, $user->getId());
		//code error incorrect TODO
		if ($voteManager->isMePhoto($photo, $user)) return $this->serviceError('photo_rest.non_vote_own_photo', '409');;
		
		if ($existVote){
			return $this->serviceError('photo_rest.already_vote_photo', '409');
		}

		$vote = $voteManager->createVote();
		
		$form = $this->get('ant.photo_rest.form_factory.vote.default')->createForm();
		$form->setData($vote);
		
		$form->submit($dataRequest);
		
		if ($form->isValid()) {
						
			$vote->setParticipant($user);
			$voteManager->saveVote($vote, $photo);
			return $this->buildResourceView($vote, 200, "vote_show");
		}
		return $this->buildFormErrorsView($form);
	}
	/**
	 * Show my vote of a photo of the user
	 * @ApiDoc(
	 *  	description="show my vote of a photo of the user",
	 *  	section="photo",
	 *  	input="photo_id",
	 *  	output="Ant\PhotoRestBundle\Model\Vote",
	 *		statusCodes={
	 *         200="Returned when successful",
	 *         404="Unable to find Vote or Photo entity with code 42"
	 *     }
	 *  )
	 * @ParamConverter("user", class="ApiBundle:User", options={"error" = "user.entity.unable_find"})
	 * @SecureParam(name="user", permissions="OWNER,HAS_ROLE_ROLE_ADMIN")
	 */
	public function showAction(ParticipantInterface $user, $photo_id)
	{
		$result = $this->getPhotoAndVote($photo_id, $user);
		
		if (is_array($result)){
			return $this->buildResourceView($result['vote'], 200, 'vote_show');
		}
		else return $result;
	}
	/**
	 * Show all votes of an user
	 * @ApiDoc(
	 *  	description="show all votes of an user",
	 *  	section="photo",
	 *  	output="Ant\PhotoRestBundle\Model\Vote",
	 *		statusCodes={
	 *         200="Returned when successful",
	 *     }
	 *  )
	 * @QueryParam(name="limit", description="Max number of records to be returned")
     * @QueryParam(name="offset", description="Number of records to skip")
	 */
	public function votesAction($id)
	{
		$participantManager = $this->get('ant.photo_rest.manager.participant_manager');
		$currentUser = $participantManager->findParticipantById($id);
		$votes = $this->get('ant.photo_rest.manager.vote_manager')->findAllVotesOfAnParticipant($currentUser);
		$linkOverrides = array('route' => 'ant_photo_rest_vote_all_show', 'parameters' => array('id'), 'rel' => 'self', 'entity' => $currentUser);

		return $this->buildPagedResourcesView($votes, 'Ant\PhotoBundle\Entity\Vote', 200, 'vote_list', array(), $linkOverrides);
	}
	/**
	 * Delete a new vote entity
	 * @ApiDoc(
	 *  	description="delete a vote",
	 *  	section="photo",
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
	public function deleteAction(ParticipantInterface $user, $photo_id)
	{
		$result = $this->getPhotoAndVote($photo_id, $user);
		
		if (is_array($result)){
			$this->get('ant.photo_rest.manager.vote_manager')->deleteVote($result['vote'], $result['photo']);
			return $this->buildView('Vote deleted', 200);
		}
		else return $result;		
	}
	
	private function getPhotoAndVote($photoId, ParticipantInterface $user)
	{
		$photoManager = $this->get('ant.photo_rest.entity_manager.photo_manager');
		$photo = $photoManager->findPhotoById($photoId);
		
		if (!$photo) {
			return $this->createError('Unable to find Photo entity', '42', '404');
		}
		
		$voteManager = $this->get('ant.photo_rest.manager.vote_manager');
		$vote = $voteManager->findVoteByPhotoAndParticipant($photoId, $user->getId());
		
		if (!$vote) {
			return $this->createError('Unable to find Vote entity', '42', '404');
		}
		
		return array('vote' => $vote,
					'photo' => $photo);
	}
}
