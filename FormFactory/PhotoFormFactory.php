<?php

/**
 * This file is part of the AntPhotoRestBundle package.
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ant\PhotoRestBundle\FormFactory;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * PhotoForm factory class.
 *
 * @author Chrysweel
 */
class PhotoFormFactory implements PhotoFormFactoryInterface
{
    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $name;

    /**
     * Constructor.
     *
     * @param FormFactoryInterface $formFactory
     * @param string               $type
     * @param string               $name
     */
    public function __construct(FormFactoryInterface $formFactory, $type, $name)
    {
        $this->formFactory = $formFactory;
        $this->type        = $type;
        $this->name        = $name;
    }

    /**
     * Creates a new form.
     *
     * @return FormInterface
     */
    public function createForm()
    {
        $builder = $this->formFactory->createNamedBuilder($this->name, $this->type);

        return $builder->getForm();
    }
}
