# Ingresse Test for Backend Developer

### USAGE

```bash
PROJECT_DIR = /var/www/ingresse-test #put any dir that you want

git clone https://github.com/jmarcelocjr/ingresse-test $PROJECT_DIR

cd $PROJECT_DIR
composer install --ignore-platform-reqs

cd $PROJECT_DIR/docker
docker-compose up
```

### Accessing

The url to the API is [localhost:1010](http://localhost:1010 "localhost:1010")

### Routes

```
GET    /users
GET    /users/<id>
POST   /users #returns header Location with the url to get the user
PUT    /users/<id>
DELETE /users/<id>
```

To send requests to POST and PUT, you need to set the header `Content-type` with the value `application/json`

### Body to POST and PUT
```json
{
	"name": "Marcelo Cerqueira",
	"email": "jmarcelo.cjr@gmail.com",
	"login": "marceloCerqueira",
	"password": "CoffeS2"
}
```

### Tests
```bash
cd $PROJECT_DIR
composer test
```

