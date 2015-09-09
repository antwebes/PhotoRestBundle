<?php
/**
 * User: José Ramón Fernandez Leis
 * Email: jdeveloper.inxenio@gmail.com
 * Date: 9/09/15
 * Time: 15:37
 */

namespace Ant\PhotoRestBundle\Tests\FormType\Transformer;

use Ant\PhotoRestBundle\FormType\Transformer\IdToPhotoTransformer;
use Ant\PhotoRestBundle\Tests\FormType\Transformer\Fixtures\Photo;
use Symfony\Component\Form\Exception\TransformationFailedException;

class IdToUserTransformerTest extends \PHPUnit_Framework_TestCase
{
    private $photoManager;
    private $idToPhotoTransformer;

    protected function setUp()
    {
        $this->photoManager = $this->getMock('Ant\PhotoRestBundle\ModelManager\PhotoManagerInterface');
        $this->idToPhotoTransformer = new IdToPhotoTransformer($this->photoManager);
    }

    public function testTransformWithNull()
    {
        $this->assertEquals(0, $this->idToPhotoTransformer->transform(null));
    }

    public function testTransformWithPhoto()
    {
        $photo = new Photo();
        $photo->setId(20);

        $this->assertEquals(20, $this->idToPhotoTransformer->transform($photo));
    }

    public function testReverseTransform()
    {
        $photo = new Photo();
        $photo->setId(20);

        $this->photoManager
            ->expects($this->once())
            ->method('findPhotoById')
            ->with(20)
            ->will($this->returnValue($photo));

        $this->assertEquals($photo, $this->idToPhotoTransformer->reverseTransform(20));
    }

    public function testReverseTransformWithUnexitingPhoto()
    {
        $this->photoManager
            ->expects($this->once())
            ->method('findPhotoById')
            ->with(20)
            ->will($this->returnValue(null));

        try{
            $this->idToPhotoTransformer->reverseTransform(20);
        }catch(TransformationFailedException $e){
            return;
        }

        $this->fail('Expected TransformationFailedException');
    }
}