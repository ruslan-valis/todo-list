# Account
> Simple REST API to manage tasks creation / visualization (like a TODO list)

## Install

1. Run docker containers
    ``` bash
   $ docker-compose up
   ``` 

2. Install dependencies
    ``` bash
    $ docker-compose exec app composer install
    ```
3. Update database with migrations and fixtures
    ``` bash
    $ docker-compose exec app php bin/console doctrine:migrations:migrate
    $ docker-compose exec app php bin/console doctrine:fixtures:load
    ```

5. Generate JWT keys and validate them
    ``` bash 
    $ bash bin/jwt/generate-keys.sh
    $ bash bin/jwt/validate-token.sh
    ```

## Usage

Execute symfony console command 
``` bash
docker-compose exec app php bin/console %ARGUMENTS%
```

## Testing

``` bash
$ docker-compose exec app php bin/phpunit
```

## Cleanup

``` bash
$ docker-compose down --rmi all
```

## Credits

- Ruslan Valis \<ruslan.valis@itomy.ch\>

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more
information.
