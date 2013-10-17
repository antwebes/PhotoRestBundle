<?php 

namespace Ant\PhotoRestBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Chatea\UtilBundle\Controller\BaseRestController;

use JMS\SecurityExtraBundle\Annotation\SecureParam;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Chatea\ApiBundle\Entity\User;

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
    	
		$voteManager = $this->get('ant.photo_rest.manager.vote_manager');
		$existVote = $voteManager->findVoteByPhotoAndParticipant($photoId, $user->getId());
		
		if ($existVote){
			return $this->createError('You have already voted this photo ', '46', '409');
		}

		$vote = $voteManager->createVote();
		
		$form = $this->get('ant.photo_rest.form_factory.vote.default')->createForm();
		$form->setData($vote);
		
		$form->bind($dataRequest);
		
		if ($form->isValid()) {
						
			$vote->setParticipant($user);
			$voteManager->saveVote($vote, $photo);
			return $this->buildResourceView($vote, 200);
		}
		return $this->buildFormErrorsView($form);
	}
	/**
	 * Show my vote of a photo of the user logged
	 * @ApiDoc(
	 *  	description="show the vote of a photo of the user logged",
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
	public function showAction(User $user, $photo_id)
	{
		$result = $this->getPhotoAndVote($photo_id, $user);
		
		if (is_array($result)){
			return $this->buildResourceView($result['vote'], 200, 'vote_list');
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
	 * @QueryParam(name="limit", description="Max number of channels to be returned")
     * @QueryParam(name="offset", description="Number of records to skip")
	 */
	public function votesAction($id)
	{
		$participantManager = $this->get('ant.photo_rest.manager.participant_manager');
		$currentUser = $participantManager->findParticipantById($id);
		
		$votes = $this->get('ant.photo_rest.manager.vote_manager')->findAllVotesOfAnParticipant($currentUser);
		return $this->buildPagedView($votes, $currentUser, 'ant_photo_rest_vote_all_show', 200, 'vote_list');
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
	public function deleteAction(User $user, $photo_id)
	{
		$result = $this->getPhotoAndVote($photo_id);
		
		if (is_array($result)){
			$this->get('ant.photo_rest.manager.vote_manager')->deleteVote($result['vote'], $result['photo']);
			return $this->buildView('Vote deleted', 200);
		}
		else return $result;		
	}
	
	private function getPhotoAndVote($photoId, User $user)
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
	
	private function buildPagedView($collection, $entity, $route, $statusCode, $contextGroup = null)
	{
		$overrides = array(
								array(
									'rel' => 'self', 
									'definition' => array('route' => $route, 'parameters' => array('id'), 'rel' => 'self'), 
									'data' => $entity
								)
							);

		return $this->buildPagedResourcesView(
            $collection, 
            'Ant\PhotoBundle\Entity\Vote', 
            $statusCode, 
            $contextGroup, 
            array(), 
            $overrides
            );
	}
}
