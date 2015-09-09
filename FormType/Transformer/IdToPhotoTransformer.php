<?php
namespace Ant\PhotoRestBundle\FormType\Transformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Ant\PhotoRestBundle\ModelManager\PhotoManagerInterface;

class IdToPhotoTransformer implements DataTransformerInterface
{
    /**
     * @var PhotoManagerInterface
     */
    private $photoManager;

    /**
     * @param PhotoManagerInterface $photoManager
     */
    public function __construct(PhotoManagerInterface $photoManager = null)
    {
        $this->photoManager = $photoManager;
    }

    /**
     * Transforms an object (photo) to a integer (id).
     *
     * @param  Photo|null $photo
     * @return integer
     */
    public function transform($photo = null)
    {
        if (null === $photo) {
            return 0;
        }

        return $photo->getId();
    }

    /**
     * Transforms a string (number) to an object (photo).
     *
     * @param  string $number
     *
     * @return Photo|null
     *
     * @throws TransformationFailedException if object (photo) is not found.
     */
    public function reverseTransform($id)
    {
        if (!$id) {
            return null;
        }

        $photo = $this->photoManager->findPhotoById($id);

        if (null === $photo) {
            throw new TransformationFailedException(sprintf(
                'An Photo with id "%s" does not exist!',
                $id
            ));
        }

        return $photo;
    }
}