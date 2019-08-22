IntonationGridBundle Documentation 
==================================

Data grids with pagination and filters for Symfony project based on the [prezent/grid-bundle](https://github.com/Prezent/prezent-grid-bundle).

For more informationen to the installation check out the [README.md](../../README.md) file.

GridType
--------

Create a reusable `GridType` for each entity as shown below. For more informations checkout the documentation of [prezent/prezent-grid](https://github.com/Prezent/prezent-grid/blob/master/doc/index.md) and [prezent/prezent-grid-bundle](https://github.com/Prezent/prezent-grid-bundle/blob/master/Resources/doc/index.md).

```php
// src/Grid/EventGridType.php

namespace App\Grid\Event;

use IntlDateFormatter;
use Prezent\Grid\BaseGridType;
use Prezent\Grid\Extension\Core\Type\ActionType;
use Prezent\Grid\Extension\Core\Type\BooleanType;
use Prezent\Grid\Extension\Core\Type\CollectionType;
use Prezent\Grid\Extension\Core\Type\DateTimeType;
use Prezent\Grid\Extension\Core\Type\StringType;
use Prezent\Grid\GridBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventGridType extends BaseGridType
{
    public function buildGrid(GridBuilder $builder, array $options = [])
    {
        $builder
            ->addColumn('id', StringType::class, [
                'label' => '',
                'sortable' => true,
                'route' => 'event',
                'route_parameters' => [
                    'id' => '{id}',
                ],
            ])
            ->addColumn('active', BooleanType::class)
            ->addColumn('name', StringType::class, [
                'sortable' => true,
            ])
            ->addColumn('description', StringType::class, [
                'sortable' => true,
                'truncate' => 32,
            ])
            ->addColumn('date', DateTimeType::class, [
                'sortable' => true,
                'date_format' => IntlDateFormatter::SHORT,
                'time_format' => IntlDateFormatter::NONE,
            ])
            ->addColumn('tags', CollectionType::class, [
                'item_property_path' => 'name',
                'item_separator' => ', ',
            ])
            ->addAction('edit', [
                'route' => 'event_edit',
                'route_parameters' => [
                    'id' => '{id}',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'attr' => [
                'class' => 'table table-striped',
            ],
        ]);
    }
}
```

GridService
-----------

Inject `IntonationGridBundle\GridBundle\Service\GridService` with dependency injection into a symfony controller.
Use it to get generate a `Grid` or to get the `GridBuilder`.
Get a paginated result of a QueryBuilder with `GridService::getPaginatedData(QueryBuilder, Request)`.

```php
// src/Controller/EventController.php

class EventController extends AbstractController
{
    /**
     * @Route("/event", name="event_index")
     */
    public function index(Request $request, EventRepository $eventRepository, GridService $gridService): Response
    {
        $grid = $gridService->createGrid(EventGridType::class);

        return $this->render('event/index.html.twig', [
            'grid' => $grid->createView(),
            'pagerfanta' => $gridService->getPaginatedData($eventRepository->createQueryBuilder('e'), $request),
        ]);
    }
}
```

Twig templates
--------------

Render a data grid as a table with pagination and filters with the Twig function `grid_container(grid, data)`.

```twig
{# templates/event/index.html.twig #}

{% extends 'base.html.twig' %}

{% block body %}
    <h1>Events</h1>
    {{ grid_container(grid, pagerfanta) }}
{% endblock %}
```
