<?php

/**
 * This file is part of the AntPhotoRestBundle package.
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ant\PhotoRestBundle\FormFactory;

use Symfony\Component\Form\FormInterface;

/**
 * Album form creator
 *
 * @author Chrysweel
 */
interface AlbumFormFactoryInterface
{
    /**
     * Creates a comment form
     *
     * @return FormInterface
     */
    public function createForm();
}
