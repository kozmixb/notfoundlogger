# Not Found Logger

An image where you have a domain open to public but would like to hide its intention

When calling the website will say not found so can still use other ports online

This logger will send out an `UTP` message with `EITF` syslog format to the desired logger 
so can keep track of robot requests and visits

## Setup

Before building docker image make sure you create a `.env` file by
```
cp .env.example .env
```

After copy make sure you set the correct remote log server configs

```
LOG_URL="192.168.1.2"
LOG_PORT=514
```

App name will show up as `Host Name` in remote logging server

```
APP_NAME=app.name
```

After config is done you can build your docker image

```
docker build -t notfoundlogger .
docker save -o notfoundlogger.tar notfoundlogger
```
