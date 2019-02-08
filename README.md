# cv

## Run composer to install dependencies

```
docker build -t cv . 
docker run -d \
--network quentinburgniard.com \
--restart always \
--name cv \
cv
```