# Account
> Simple REST API to manage tasks creation / visualization (like a TODO list)

## Install

``` bash
$ docker-compose up
$ docker-compose exec app composer install
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
