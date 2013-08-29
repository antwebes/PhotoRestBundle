<?php 

namespace Ant\PhotoRestBundle\controller;

use Chatea\ApiBundle\Controller\BaseRestController;

use Symfony\Component\HttpFoundation\Request;

use Ant\PhotoRestBundle\Entity\Photo;
use Ant\PhotoRestBundle\Util\ErrorResponse;
use Ant\PhotoRestBundle\Util;
use Ant\PhotoRestBundle\Event\AntPhotoRestEvents;
use Ant\PhotoRestBundle\Event\PhotoEvent;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use JMS\SecurityExtraBundle\Annotation\SecureParam;

/**
 * Foto controller.
 *
 */
class PhotoController extends BaseRestController
{
	/**
	 * Create a new photo entity
	 *  @ApiDoc(
	 *  	description="create a photo",
	 *  	input="Ant\PhotoRestBundle\FormType\PhotoType",
	 *  	output="Ant\PhotoRestBundle\Model\Photo",
	 *		statusCodes={
	 *         201="New entity created",
	 *         400="Bad request"
	 *     }
	 *  )
	 */
	public function createAction(Request $request)
	{
		$dataRequest = $request->request->all();
		
		$photoManager = $this->get('ant.photo_rest.entity_manager.photo_manager');
		$photo = $photoManager->createPhoto();
		
		$form = $this->get('ant.photo_rest.form_factory.photo.default')->createForm();
		$form->setData($photo);
		
		if ($request->isMethod('POST')) {
			
			$form->bind($dataRequest);
			
			if ($form->isValid()) {
				
				$currentUser = $this->get('security.context')->getToken()->getUser();
				$photo->setParticipant($currentUser);
				if ($request->files->get('image')) $image = $request->files->get('image');
				else $image = $form->getData()->getImage();
				
				$url = $this->getPhotoUploader()->upload($image);
				$photo->setPath($url);
				$photoManager->savePhoto($photo);
				
				return $this->buildView($photo, 200);
			}
			return $this->buildFormErrorsView($form);
		}		
		return $this->render(
				'AntPhotoRestBundle:Photo:add.html.twig',
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
	/**
	 * @return Ant\PhotoRestBundle\Upload\PhotoUploader
	 */
	protected function getPhotoUploader()
	{
		return $this->get('ant.photo_rest.upload.photo_uploader');
	}
}
