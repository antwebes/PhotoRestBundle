<?php 

namespace Ant\PhotoRestBundle\controller;

use Chatea\ApiBundle\Controller\BaseRestController;
use Symfony\Component\HttpFoundation\Request;
use Chatea\ApiBundle\Util\Util;

use Ant\PhotoRestBundle\Entity\Photo;

/**
 * Foto controller.
 *
 */
class PhotoController extends BaseRestController
{
	
	public function createAction(Request $request)
	{
		$photo = new Photo();
		$form = $this->createFormBuilder($photo)
			->add('image', 'file', array('label'  => 'Imagen'))
			->add('title', 'text', array('required'=>false))->getForm();
		
		if ($request->isMethod('POST')) {
			
			$form->bind($request);
			
			if ($form->isValid()) {
				
				$data = $form->getData();
				$url = $this->getPhotoUploader()->upload($data->getImage());
		
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