# Simple PHP Shopcart backend

This project includes a React backend that implements
- user login
- adding/removing items to/from a shopping cart
- ADMIN user changing product availability

The frontend implementation can be found at https://github.com/Exiv-k/shopcart-react

### Prerequisites

- Docker

### Installation
```bash
make init
make db-import
```
After all the dependencies are installed, run
```bash
make restart
```
The server will be listening on localhost:8080 and dabatase on localhost:8081.
There is a test admin account with

username: admin1

password: 654321

or you can create new admins using phpMyAdmin on localhost:8081
