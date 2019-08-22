IntonationGridBundle
====================

Data grids with pagination and filters for Symfony project based on the [prezent/grid-bundle](https://github.com/Prezent/prezent-grid-bundle).

Installation
------------

1. Open a command console, enter your project directory and execute:   

    ```console
    $ composer require intonation/grid-bundle
    ```

2. Since the service configuration of `prezent/grid-bundle` seemd not to work well with Symfony 4 add the following fix to `config/services.yaml` to register all `GridType` as public services: 

    ```yaml
    services:
        App\Grid\:
            resource: '../src/Grid/*'
            public: true
    ```

3. Add the Twig grid theme of the IntonationGridBundle to `config/packages/perzent_grid.yaml`:

    ```yaml
    prezent_grid:
        themes:
            - '@IntonationGrid/Grid/grid.html.twig'
    ```
