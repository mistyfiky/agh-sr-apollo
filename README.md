# apollo

## usage
```shell script
docker build -t apollo -f .docker/Dockerfile .
docker run -p 8084:80 -d --name apollo apollo 
```

## command with volume mount for development
```shell script
docker run -p 8084:80 -d -v "$PWD/public_html":/var/www/html apollo
```
