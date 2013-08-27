<?php 

namespace Ant\PhotoRestBundle\controller;

use Ant\PhotoRestBundle\Controller\BaseRestController;
use Symfony\Component\HttpFoundation\Request;
use Ant\PhotoRestBundle\Form\PhotoType;

/**
 * Foto controller.
 *
 */
class PhotoController extends BaseRestController
{
	
	public function createAction(Request $request)
	{
		$form = $this->createForm(new PhotoType(), array());
		
		if ($request->isMethod('POST')) {
			$form->bind($request);
			if ($form->isValid()) {
				$data = $form->getData();
				$url = $this->getPhotoUploader()->upload($data['photo']);
		
				return; // display a response or redirect
			}
		}
		
		return $this->render(
				'AntPhotoRestBundle:Photo:add.html.twig',
				array('form'  => $form->createView())
		);
	}
	/**
	 * @return Ant\PhotoRestBundle\Upload\PhotoUploader
	 */
	protected function getPhotoUploader()
	{
		return $this->get('ant.photo_rest.upload.photo_uploader');
	}
}