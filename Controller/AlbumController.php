<?php 

namespace Ant\PhotoRestBundle\Controller;

use Ant\PhotoRestBundle\Controller\BaseRestController;

use Symfony\Component\HttpFoundation\Request;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use JMS\SecurityExtraBundle\Annotation\SecureParam;
use JMS\SecurityExtraBundle\Security\Authorization\Expression\Expression;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Chatea\ApiBundle\Entity\User;
use Chatea\FotoBundle\Entity\Album;
/**
 * Album controller.
 *
 */
class AlbumController extends BaseRestController
{
	/**
	 * Create a new album entity
	 * @ApiDoc(
	 *  	description="create an album",
	 *		section="photo",
	 *  	input="Ant\PhotoRestBundle\FormType\AlbumType",
	 *  	output="Ant\PhotoRestBundle\Model\Album",
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
		$albumManager = $this->get('ant.photo_rest.manager.album_manager');
		$album = $albumManager->createAlbum();
		
		$form = $this->get('ant.photo_rest.form_factory.album.default')->createForm();
		$form->setData($album);
		
		$form->bind($request);
		
		if ($form->isValid()) {
			
			$album->setParticipant($user);
			
			$this->get('ant.photo_rest.manager.album_manager')->save($album);
		
			return $this->buildView($album, 201);
		}
		return $this->buildFormErrorsView($form);
	}
	
	/**
	 * Deletes an Album entity.
	 * @ApiDoc(
	 *  	description="Delete an album",
	 *  	section="photo",
	 *  	output="Chatea\ApiBundle\Entity\Album",
	 *		statusCodes={
	 *         200="Returned when successful",
	 *         403="Access denied",
	 *         404="Unable to find Channel entity with code 32"
	 *     }
	 *  )
	 *  @SecureParam(name="user", permissions="OWNER,HAS_ROLE_ROLE_ADMIN,HAS_ROLE_APPLICATION")
	 *  @ParamConverter("user", class="ApiBundle:User", options={"error" = "user.entity.unable_find"}, options={"id" = "user_id"})
	 * 
	 */
	public function deleteAction(User $user, $album_id)
	{
		$album = $this->get('ant.photo_rest.manager.album_manager')->findAlbumById($album_id);
		
		if (!$album) return $this->createError('Unable to find Album entity', '42', '404');
		
		$securityContext = $this->container->get('security.context');
		if (!($this->get('ant.photo_rest.manager.album_manager')->isOwner($user, $album) or $securityContext->isGranted(array(new Expression('hasRole("ROLE_ADMIN") or hasRole("ROLE_APPLICATION")'))))){
			return $this->createError('This user has no permission for this action', '32', '403');
		}
		
		$this->get('ant.photo_rest.manager.album_manager')->delete($album);
			
		return $this->buildView('Album deleted', 200);
	}
	/**
	 * List the Albums of an user.
	 * @ApiDoc(
	 *  	description="List the Albums of an user.",
	 *  	section="photo",
	 *  	output="Chatea\ApiBundle\Entity\Album",
	 *		statusCodes={
	 *         200="Returned when successful",
	 *         404="Unable to find User entity with code 32"
	 *     }
	 *  )
	 *  @ParamConverter("user", class="ApiBundle:User", options={"error" = "user.entity.unable_find"}, options={"id" = "user_id"})
	 *
	 */
	public function listAction(User $user)
	{
		$albums = $this->get('ant.photo_rest.manager.album_manager')->findAllMeAlbums($user);
		
		return $this->buildView($albums, 200, 'photo_list');
	}
}
