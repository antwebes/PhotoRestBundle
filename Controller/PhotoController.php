<?php 

namespace Ant\PhotoRestBundle\Controller;

use Ant\PhotoRestBundle\Controller\BaseRestController;

use Symfony\Component\HttpFoundation\Request;

use Ant\PhotoRestBundle\Entity\Photo;
use Ant\PhotoRestBundle\Event\AntPhotoRestEvents;
use Ant\PhotoRestBundle\Event\PhotoEvent;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

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
	 * Show a photo entity
	 *  @ApiDoc(
	 *  	description="show a photo",
	 *  	output="Ant\PhotoRestBundle\Model\Photo",
	 *		statusCodes={
	 *         200="Returned when successful",
	 *         404="Unable to find Photo entity with code 42"
	 *     }
	 *  )
	 */
	public function showAction($id)
	{
		$photoManager = $this->get('ant.photo_rest.entity_manager.photo_manager');
		$photo = $photoManager->findPhotoById($id);
		
		if (null === $photo) {
			return $this->createError('Unable to find Photo entity', '42', '404');
		}
		return $this->buildView($photo, 200);
		
	}
	/**
	 * Delete a new photo entity
	 *  @ApiDoc(
	 *  	description="delete a photo",
	 *  	output="Ant\PhotoRestBundle\Model\Photo",
	 *		statusCodes={
	 *         200="Returned when successful",
	 *         403="Access denied",
	 *         404="Unable to find Photo entity with code 42"
	 *     }
	 *  )
	 */
	public function deleteAction($id)
	{
		$photoManager = $this->get('ant.photo_rest.entity_manager.photo_manager');
		$photo = $photoManager->findPhotoById($id);
		
		if (null === $photo) {
			return $this->createError('Unable to find Photo entity', '42', '404');
		}
		if ($photo->getParticipant() == $this->get('security.context')->getToken()->getUser() ){
			$path = $photo->getPath();
			$photo = $photoManager->deletePhoto($photo);
			$dispatcher = $this->container->get('event_dispatcher');
			$dispatcher->dispatch(AntPhotoRestEvents::PHOTO_DELETED, new PhotoEvent($path));
		} else{
			return $this->createError('Access denied', '44', '403');
		}
		return $this->buildView('Photo deleted', 200);
		
	}
	/**
	 * @return Ant\PhotoRestBundle\Upload\PhotoUploader
	 */
	protected function getPhotoUploader()
	{
		return $this->get('ant.photo_rest.upload.photo_uploader');
	}
}
