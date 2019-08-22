<?php

/*
 * This file is part of the IntonationGridBundle.
 *
 * (c) Wolfgang Drescher <drescher.wolfgang@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Intonation\GridBundle\Twig;

use Prezent\Grid\Twig\Node\RenderItemBlockNode;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class GridExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('grid_container', null, ['node_class' => RenderItemBlockNode::class, 'is_safe' => ['html']]),
        ];
    }
}
