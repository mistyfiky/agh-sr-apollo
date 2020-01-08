# apollo

## usage
```shell script
docker build -t apollo .
docker run --rm --name apollo -p 8084:80\
 -e NEO4J_URI='bolt://delphi'\
 -e SENTRY_DSN='https://apiKey@sentry.io/projectId'\
 -d apollo 
```

## command with volume mount for development
```shell script
docker run --rm --name apollo -p 8084:80\
 -e NEO4J_URI='bolt://delphi'\
 -e SENTRY_DSN='https://apiKey@sentry.io/projectId'\
 -v "$(pwd)/public":/var/www/html\
 -d apollo
```
