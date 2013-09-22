<?php 

namespace Ant\PhotoRestBundle\Controller;

use Ant\PhotoRestBundle\Controller\BaseRestController;

use Symfony\Component\HttpFoundation\Request;

use Ant\PhotoRestBundle\Entity\Photo;
use Ant\PhotoRestBundle\Event\AntPhotoRestEvents;
use Ant\PhotoRestBundle\Event\PhotoEvent;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use JMS\SecurityExtraBundle\Annotation\SecureParam;
use JMS\SecurityExtraBundle\Security\Authorization\Expression\Expression;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Chatea\ApiBundle\Entity\User;

use Pagerfanta\Exception\OutOfRangeCurrentPageException;

/**
 * Foto controller.
 *
 */
class PhotoController extends BaseRestController
{
	/**
	 * Create a new photo entity
	 * @ApiDoc(
	 *  	description="create a photo",
	 *		section="photo",
	 *  	input="Ant\PhotoRestBundle\FormType\PhotoType",
	 *  	output="Ant\PhotoRestBundle\Model\Photo",
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
		$dataRequest = $request->request->all();
		
		$photoManager = $this->get('ant.photo_rest.entity_manager.photo_manager');
		$photo = $photoManager->createPhoto();
		
		$form = $this->get('ant.photo_rest.form_factory.photo.default')->createForm();
		$form->setData($photo);
		
		if ($request->isMethod('POST')) {
			
			$form->bind($dataRequest);
			
			if ($form->isValid()) {
				
// 				$currentUser = $this->get('ant.photo_rest.manager.participant_manager')->findParticipantById($id);
// 				$currentUser = $this->get('security.context')->getToken()->getUser();
				$photo->setParticipant($user);
				if ($request->files->get('image')) $image = $request->files->get('image');
				else $image = $form->getData()->getImage();
				
				$url = $this->getPhotoUploader()->upload($image);
				$photo->setPath($url);
				$photoManager->savePhoto($photo);
				return $this->buildResourceView($photo, 200);
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
	 * @ApiDoc(
	 *  	description="show a photo",
	 *  	section="photo",
	 *  	output="Ant\PhotoRestBundle\Model\Photo",
	 *		statusCodes={
	 *         200="Returned when successful",
	 *         404="Unable to find Photo entity with code 42"
	 *     }
	 *  )
	 */
	public function getShowAction($id)
	{
		$photoManager = $this->get('ant.photo_rest.entity_manager.photo_manager');
		$photo = $photoManager->findPhotoById($id);
		
		if (null === $photo) {
			return $this->createError('Unable to find Photo entity', '42', '404');
		}
		return $this->buildResourceView($photo, 200, 'vote_list');
		
	}
	/**
	 * List all Photo entities of an user.
	 * @ApiDoc(
	 *  	description="List all photos of an user",
	 *  	section="photo",
	 *  	output="Ant\PhotoRestBundle\Model\Photo",
	 *		statusCodes={
	 *         200="Returned when successful"
	 *     }
	 *  )
	 */
	public function photosUserAction($id)
	{
		$participantManager = $this->get('ant.photo_rest.manager.participant_manager');
		$participant = $participantManager->findParticipantById($id);
		
		if (null === $participant) {
			return $this->createError('Unable to find User entity', '32', '404');
		}
	
		$photoManager = $this->get('ant.photo_rest.entity_manager.photo_manager');
		$entities = $photoManager->findAllMePhotos($participant);		
		
		return $this->buildPagedView($entities, $participant, 'ant_photo_rest_show_user_all', 200, 'photo_list');
	}
	/**
	 * Delete a photo entity
	 * @ApiDoc(
	 *  	description="delete a photo",
	 *  	section="photo",
	 *  	output="Ant\PhotoRestBundle\Model\Photo",
	 *		statusCodes={
	 *         200="Returned when successful",
	 *         403="Access denied",
	 *         404="Unable to find Photo entity with code 42"
	 *     }
	 *  )
	 */
	public function deleteAction($photo_id)
	{
		$photoManager = $this->get('ant.photo_rest.entity_manager.photo_manager');
		$photo = $photoManager->findPhotoById($photo_id);
		
		if (null === $photo) {
			return $this->createError('Unable to find Photo entity', '42', '404');
		}
		
		$securityContext = $this->container->get('security.context');
		
		$user = $securityContext->getToken()->getUser();
		//if user is not owner or has not Role Admin or application
		if ( !($photoManager->isOwner($user, $photo) or $securityContext->isGranted(array(new Expression('hasRole("ROLE_ADMIN") or hasRole("ROLE_APPLICATION")')))))
			return $this->createError('This user has no permission for this action', '32', '403');
				
		$path = $photo->getPath();
		$photo = $photoManager->deletePhoto($photo);
		
		$dispatcher = $this->container->get('event_dispatcher');
		$dispatcher->dispatch(AntPhotoRestEvents::PHOTO_DELETED, new PhotoEvent($path));
		
		return $this->buildView('Photo deleted', 200);
		
	}
	
	/**
	 * Insert a photo entity into album id
	 * @ApiDoc(
	 *  	description="Insert a photo entity into album id",
	 *		section="photo",
	 *  	output="message confirmation",
	 *		statusCodes={
	 *         200="photo inserted",
	 *         400="Bad request"
	 *     }
	 *  )
	 *  @SecureParam(name="user", permissions="OWNER,HAS_ROLE_ROLE_ADMIN,HAS_ROLE_APPLICATION")
	 *  @ParamConverter("user", class="ApiBundle:User", options={"error" = "user.entity.unable_find", "id" = "user_id"})
	 */
	public function insertToAlbumAction(User $user, $photo_id, $album_id)
	{
		$photoManager = $this->get('ant.photo_rest.entity_manager.photo_manager');
		
		$photo = $photoManager->findPhotoById($photo_id);		
		if (!$photo) return $this->createError('Unable to find Photo entity', '42', '404');
		
		$album = $this->get('ant.photo_rest.manager.album_manager')->findAlbumById($album_id);		
		if (!$album) return $this->createError('Unable to find Album entity', '42', '404');
		
		ldd($album->getPhotos()->getValues());
		$securityContext = $this->container->get('security.context');
		
		if ( !($photoManager->isOwner($user, $photo) or $this->get('ant.photo_rest.manager.album_manager')->isOwner($user, $album)
				or $securityContext->isGranted(array(new Expression('hasRole("ROLE_ADMIN") or hasRole("ROLE_APPLICATION")')))
				 )) return $this->createError('This user has no permission for this action', '32', '403');
			
		$photoManager->insertToAlbum($photo, $album);
		
		return $this->buildView('Photo inserted', 200);
	}
	
	/**
	 * Delete a photo entity of an album id
	 * @ApiDoc(
	 *  	description="Delete a photo entity of an album id",
	 *		section="photo",
	 *  	output="message confirmation",
	 *		statusCodes={
	 *         200="photo deleted of an album",
	 *         400="Bad request"
	 *     }
	 *  )
	 *  @SecureParam(name="user", permissions="OWNER,HAS_ROLE_ROLE_ADMIN,HAS_ROLE_APPLICATION")
	 *  @ParamConverter("user", class="ApiBundle:User", options={"error" = "user.entity.unable_find", "id" = "user_id"})
	 */
	public function deleteOfAlbumAction(User $user, $photo_id)
	{
		$photoManager = $this->get('ant.photo_rest.entity_manager.photo_manager');
	
		$photo = $photoManager->findPhotoById($photo_id);
		if (!$photo) return $this->createError('Unable to find Photo entity', '42', '404');
	
		$securityContext = $this->container->get('security.context');
		
		if ( !($photoManager->isOwner($user, $photo)
				or $securityContext->isGranted(array(new Expression('hasRole("ROLE_ADMIN") or hasRole("ROLE_APPLICATION")')))
		)) return $this->createError('This user has no permission for this action', '32', '403');
		
		$album = $photo->getAlbum();
			
		$photoManager->deleteOfAlbum($photo, $album);
	
		return $this->buildView('Photo deleted of Album', 200);
	}
	
	/**
	 * @return Ant\PhotoRestBundle\Upload\PhotoUploader
	 */
	
	protected function getPhotoUploader()
	{
		return $this->get('ant.photo_rest.upload.photo_uploader');
	}
	
	private function buildPagedView($collection, $entity, $route, $statusCode, $contextGroup = null)
	{
		$resourceBuilder = $this->get('hateoas.resource_builder');
		$paginator = $this->get('paginator');
		$page = $this->getPage();
	
		try {
			$collection = $paginator->paginate($collection, $page);
			$resource = $resourceBuilder->createCollection($collection, 'Ant\PhotoBundle\Entity\Photo',
									array(),
									array(
									  array(
										'rel' => 'self', 
										'definition' => array('route' => $route, 'parameters' => array('id'), 'rel' => 'self'), 
										'data' => $entity
										)
									  ) 
									);
			
			return $this->buildView($resource, $statusCode, $contextGroup);
		}catch(OutOfRangeCurrentPageException $e){
			return $this->customError404('page.not_found');
		}
	}
	
	private function buildResourceView($entity, $statusCode, $contextGroup = null)
	{
		$resourceBuilder = $this->get('hateoas.resource_builder');
		$resource = $resourceBuilder->create($entity);
	
		return $this->buildView($resource, $statusCode, $contextGroup);
	}
	
	private function getPage()
	{
		return (int)$this->getRequest()->get('page', 1);
	}
}
