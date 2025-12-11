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
make init

make db-import

make up-build
```

The server will be listening on localhost:8080 and dabatase on localhost:8081.
