<?php 

namespace Ant\PhotoRestBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Vote controller.
 *
 */
class VoteController
{
	/**
	 * Create a new vote entity
	 *  @ApiDoc(
	 *  	description="create a vote",
	 *  	input="Ant\PhotoRestBundle\FormType\VoteType",
	 *  	output="Ant\PhotoRestBundle\Model\Vote",
	 *		statusCodes={
	 *         201="New entity created",
	 *         400="Bad request"
	 *     }
	 *  )
	 */
	public function createAction(Request $request, $photoId)
	{		
		$photoManager = $this->get('ant.photo_rest.entity_manager.photo_manager');
		$photo = $photoManager->findPhotoById($photoId);
		
		$voteManager = $this->get('ant.photo_rest.manager.vote_manager');
		$vote = $voteManager->createVote();
		
		$form = $this->get('ant.photo_rest.form_factory.photo.default')->createForm();
		$form->setData($vote);
		
		if ($request->isMethod('POST')) {
			
			$form->bind($dataRequest);
			
			if ($form->isValid()) {
				
				$currentUser = $this->get('security.context')->getToken()->getUser();
				$vote->setParticipant($currentUser);
				
				$voteManager->saveVote($vote);
				//TODO
				$photoManager->incrementVote();
				$photoManager->updateScore($form->getData()->getScore());
				return $this->buildView($vote, 200);
			}
			return $this->buildFormErrorsView($form);
		}		
		return $this->render(
				'AntPhotoRestBundle:Vote:add.html.twig',
				array('form'  => $form->createView())
		);
	}
	/**
	 * Delete a new photo entity
	 *  @ApiDoc(
	 *  	description="delete a photo",
	 *  	output="Ant\PhotoRestBundle\Model\Photo",
	 *		statusCodes={
	 *         200="Returned when successful",
	 *         403="Access denied",
	 *         404="Unable to find Photo entity with code 32"
	 *     }
	 *  )
	 */
	public function deleteAction($id)
	{
		$photoManager = $this->get('ant.photo_rest.entity_manager.photo_manager');
		$photo = $photoManager->findPhotoById($id);
		
		if (null === $photo) {
			$errorResponse = ErrorResponse::createResponse('Unable to find Photo entity', '34');
			return $this->buildView($errorResponse, 404);
		}
		if ($photo->getParticipant() == $this->get('security.context')->getToken()->getUser() ){
			$path = $photo->getPath();
			$photo = $photoManager->deleteBadge($photo);
			$dispatcher = $this->container->get('event_dispatcher');
			$dispatcher->dispatch(AntPhotoRestEvents::PHOTO_DELETED, new PhotoEvent($path));
		} else{
			$errorResponse = ErrorResponse::createResponse('Access denied', 'xxxx');
		}
		
		
		return $this->buildView('Photo deleted', 200);
		
	}
	private function createFormErrorsView($form, $statusCode = 400)
	{
		$errors = Util::getAllFormErrorMessages($form);
		$r = $this->get('api.servicio.error_response')->createResponse($errors, $this->container->getParameter('channel.form.register'));
		$view = $this->view($r, $statusCode);
		$view->setFormat('json');
		return $view;
	}
	
	private function buildFormErrorsView($form)
	{
		$view = $this->createFormErrorsView($form);
		return $this->handleView($view);
	}
}
