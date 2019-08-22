<?php

namespace Intonation\GridBundle\Twig;

use Prezent\Grid\Twig\Node\RenderBlockNode;
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
