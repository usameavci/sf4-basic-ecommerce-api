# Symfony 4 Basic E-Commerce API

# Setup Docker
Create a `.env`
```sh
$ cp .env.dist .env
```

Build and run container
```sh
$ docker-compose build
$ docker-compose up -d
```

Access project
```sh
http://localhost:1180
```

# If you have local nginx
``` nginx
server {
    listen      80;
    listen      [::]:80;
    server_name sf4-basic-ecommerce-api.test;

    error_log /var/log/nginx/sf4-basic-ecommerce-api.test-error.log;
    access_log off;

    location / {
        proxy_pass http://127.0.0.1:1180;
        proxy_set_header Host 'sf4-basic-ecommerce-api.test';

        proxy_buffer_size          128k;
        proxy_buffers              4 256k;
        proxy_busy_buffers_size    256k;
    }

    location ~ /\.ht {
        deny all;
    }

    location /.well-known/acme-challenge/ {
        root /var/www/letsencrypt/;
        log_not_found off;
    }
}
```

# Setup API
Create schema
```sh
php bin/console doctrine:schema:create
```

# Setup Postman
Download and import the following files;
- [x] [Environment File](https://github.com/usameavci/sf4-basic-ecommerce-api/blob/master/postman/Local.postman_environment.json)
- [x] [Collection File](https://github.com/usameavci/sf4-basic-ecommerce-api/blob/master/postman/API%20Collection.postman_collection.json)

# Create OAuth Client
Open the Postman and expand `Setup` folder. Run `Setup Example Data` request. This request creates;
- 1 User (Admin)
- 1 User (Company Admin)
- 3 User (Customer)
- 1 Company
- 1 Product

When response return `CLIENT_ID` and `CLIENT_SECRET` will be set automatically.

You can login with the user you want using `Login As ...` requests under the user folder.

# Fin
