# apollo

## usage
```shell script
docker build -t apollo -f .docker/Dockerfile .
docker run -v "$PWD/public_html":/var/www/html -p 8082:80 -it apollo 
```
