```
$ docker build -t apollo -f .docker/Dockerfile .
$ docker run -v "$PWD/public_html":/var/www/html -p 80:80 -it apollo 
