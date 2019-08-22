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
use Intonation\GridBundle\Utils\ElementTypeGuesserInterface;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Prezent\Grid\DefaultGridFactory;
use Prezent\Grid\Extension\Core\GridType;
use Prezent\Grid\Grid;
use Prezent\Grid\GridBuilder;
use ReflectionClass;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;

class GridService
{
    public const PROPERTY_SELECT_MODE_EXCLUSIVE = 'exclusive';
    public const PROPERTY_SELECT_MODE_EXCLUDE = 'exclude';
    private const PAGINATION_DEFAULT_ITEMS_PER_PAGE = 15;

    private $gridFactory;
    private $parameterBag;
    private $elementTypeGuesser;
    private $paginationItemsPerPage;

    public function __construct(DefaultGridFactory $gridFactory, ParameterBagInterface $parameterBag, ElementTypeGuesserInterface $elementTypeGuesser)
    {
        $this->gridFactory = $gridFactory;
        $this->parameterBag = $parameterBag;
        $this->elementTypeGuesser = $elementTypeGuesser;
        $this->paginationItemsPerPage = self::PAGINATION_DEFAULT_ITEMS_PER_PAGE;
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

    public function getPaginatedData(QueryBuilder $queryBuilder, Request $request): Pagerfanta
    {
        $alias = current($queryBuilder->getDQLPart('from'))->getAlias();
        $sortField = $request->query->get($this->parameterBag->get('prezent_grid.sort_field_parameter'));
        $sortOrder = $request->query->get($this->parameterBag->get('prezent_grid.sort_order_parameter'));
        $currentPageParam = $request->query->get('page');
        $limitParam = $request->query->get('limit');

        if ($sortField) {
            $queryBuilder->orderBy("$alias.{$this->sanitizeQueryParam($sortField)}", 'DESC' === mb_strtoupper($sortOrder) ? 'DESC' : 'ASC');
        }

        $adapter = new DoctrineORMAdapter($queryBuilder);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(max('all' === $limitParam ? \count($pagerfanta) : ((int) $limitParam ?: $this->paginationItemsPerPage), 5));
        $pagerfanta->setCurrentPage(max((int) $currentPageParam, 1));

        return $pagerfanta;
    }

    /**
     * Removes all non-alphanumeric characters except whitespaces.
     */
    private function sanitizeQueryParam(string $string): string
    {
        return preg_replace('/[^a-z0-9.]+/i', '', $string);
    }

    public function createGridFromEntity(string $className, ?array $properties = [], string $propertySelectMode = self::PROPERTY_SELECT_MODE_EXCLUSIVE)
    {
        $gridBuilder = $this->createBuilder();
        $reflectionClass = new ReflectionClass($className);

        foreach ($reflectionClass->getProperties() as $property) {
            if (self::PROPERTY_SELECT_MODE_EXCLUSIVE === $propertySelectMode and !\in_array($property->getName(), $properties, true)) {
                continue;
            }
            if (self::PROPERTY_SELECT_MODE_EXCLUDE === $propertySelectMode and \in_array($property->getName(), $properties, true)) {
                continue;
            }
            $guessType = $this->elementTypeGuesser->guessType($className, $property->getName());
            $gridBuilder->addColumn($property->getName(), $guessType->getType(), $guessType->getOptions());
        }

        return $gridBuilder->getGrid();
    }
}
