# Simple PHP Shopcart backend

This project includes a React backend that implements
- user login
- adding/removing items to/from a shopping cart
- ADMIN user modifying product availability

The frontend implementation can be found at https://github.com/Exiv-k/shopcart-react

### Prerequisites

- Docker

### Installation
```bash
docker compose up --build
```
In php bash:
```bash
composer install
```

### Configure Database
You can import backup.sql via phpMyAdmin or
```bash
docker exec <db-container> mysqldump -u root -prootpass shopping > backup.sql
```
where ```<db-container>``` is the container id of mysql database.

The server will be listening on localhost:8080 and dabatase on localhost:8081.
