<?php

/*
 * This file is part of the IntonationGridBundle.
 *
 * (c) Wolfgang Drescher <drescher.wolfgang@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Intonation\GridBundle\Service;

use Doctrine\ORM\QueryBuilder;
use Prezent\Grid\DefaultGridFactory;
use Prezent\Grid\Extension\Core\GridType;
use Prezent\Grid\Grid;
use Prezent\Grid\GridBuilder;
use Symfony\Component\HttpFoundation\Request;

class GridService
{
    private $gridFactory;

    public function __construct(DefaultGridFactory $gridFactory)
    {
        $this->gridFactory = $gridFactory;
    }

    /**
     * @see DefaultGridFactory::createBuilder()
     */
    public function createBuilder($type = GridType::class, array $options = []): GridBuilder
    {
        return $this->gridFactory->createBuilder($type, $options);
    }

    /**
     * @see DefaultGridFactory::createGrid()
     */
    public function createGrid($type = GridType::class, array $options = []): Grid
    {
        return $this->gridFactory->createGrid($type, $options);
    }

    public function getAllData(QueryBuilder $queryBuilder): iterable
    {
        return $queryBuilder->getQuery()->getResult();
    }
}
