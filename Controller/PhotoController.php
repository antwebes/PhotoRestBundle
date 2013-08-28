<?php 

namespace Ant\PhotoRestBundle\controller;

use Chatea\ApiBundle\Controller\BaseRestController;
use Symfony\Component\HttpFoundation\Request;
use Chatea\ApiBundle\Util\Util;

use Ant\PhotoRestBundle\Entity\Photo;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
/**
 * Foto controller.
 *
 */
class PhotoController extends BaseRestController
{
	/**
	 * Create a new photo entity
	 * @Rest\View(statusCode=204)
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