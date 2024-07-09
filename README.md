# SimpleRouter

See full example project:
[https://github.com/FrederickMontiel/CRUD-SimpleRouter-php
](https://github.com/FrederickMontiel/CRUD-SimpleRouter-php)

Install our package with composer:

```txt
composer require easy-projects/simple-router
```

You must redirect all requests to your main file with this .htaccess

```txt
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [QSA,L]
```

Example code:

```php
<?php
    include_once __DIR__."/vendor/autoload.php";

    use EasyProjects\SimpleRouter\Router as Router;

    $router = new Router();

     /*
        Import only the packages and classes that you are using.

        Improves application loading performance.

        For this you need to gestione your project by packages.
    */
    $router->autoload(); 

    //Cors rules for the browser
    $router->cors()->setAllowedOrigins("easyprojects.tech", "localhost");
    $router->cors()->setAllowedMethods("GET", "POST", "PUT", "DELETE");
    $router->cors()->setAllowedHeaders("Content-Type", "Authorization");

    //Routes
    $router->get("/get/{idUser}", 
        //Middlewares for example
        fn () => Middleware::auth(),
        //Controller
        fn () => Router::$response->status(200)->send(Router::$request->params->idUser)
    );
    
    $router->post("/add", 
        fn () => Router::$response->status(200)->send(Router::$request->body->nameUser)
    );

    $router->put("/update/{idUser}", 
        fn () => Router::$response->status(200)->send(Router::$request->params->idUser." - ".Router::$request->body->nameUser)
    );

    $router->delete("/delete/{idUser}", 
        fn () => Router::$response->status(200)->send(Router::$request->params->idUser." - ".Router::$request->body->nameUser)
    );
    
    //Use only in development
    $api->start();
```

Now if you need get Files Uploaded, use:

```php
    $router->post("/upload/folder/{idFolder}", 
        fn () => Router::$response->status(200)->send(Router::$request->files->img->name." - ".Router::$request->params->idFolder)
    );
```

![image](https://user-images.githubusercontent.com/86737117/144947334-5f09b150-5ec4-481c-9dfd-bc09592c7250.png)

# How does it work?

It consists of 3 classes, Router, Request, Response.

Router is the global class to be able to design the routes, you can put different methods: get, post, put, delete.
Request contains the data sent by the client's browser or the client in general, (url or form parameters, and headers).
Response contains two methods, status to send a response code (404, 500, 403), send () send a response to any object in a json.


