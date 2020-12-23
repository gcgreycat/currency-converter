# Currency Converter

Simple json api service for converting currencies.

## Api

For sending data use header `Content-type: application/vnd.api+json`

### POST /convert

Request body

```json
{
    "data": {
        "from": "<string>",
        "to": "<string>",
        "amount": "<float>"
    }
}
```

Example

```json
{
    "data": {
        "from": "USD",
        "to": "EUR",
        "amount": 1.32
    }
}
```

`from` and `to` - currency code in ISO-4217 format (for example, `USD`, `EUR` etc)
`amount` - amount to convert

Response body

```json
{
    "data": {
        "result": "<float>"
    }
}
```

Example

```json
{
    "data": {
        "result": 1.2184
    }
}
```

### Errors

```json
{
    "errors": {
        "status": "<int>",
        "detail": "<string>"
    }
}
```

Example

```json
{
    "errors": {
        "status": 400,
        "detail": "from: This value is not a valid currency."
    }
}
```

## Run project

For simple run just use `make run-prod`. Project url is `localhost:8080`

## Development

`Toolkit` image is usefull for development. It contains `php`, `composer` and `symfony cli`. Scripts from `/tools` use this image. All scripts use `/application` as working directory. 

## Testing

For testing just use `make test`

## Commands

You may need to use `sudo` because of permission issue.

 - Build prod image
```Bash
make build
```

 - Build toolkit image
```Bash
make build-toolkit
```

 - Run prod cluster through docker-composer (prod image is used)
```Bash
make run-prod
```

 - Run dev cluster through docker-composer (use local folder)
```Bash
make run-dev
```

 - Run tests
```Bash
make test
```

 - Run composer install for project needed for development
```Bash
make composer-install
```