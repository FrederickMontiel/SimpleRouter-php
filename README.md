# SimpleRouter-php

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
    //Include composer
    include_once __DIR__."/vendor/autoload.php";

    use EasyProjects\SimpleRouter\Router as Router;
    use EasyProjects\SimpleRouter\Request as Request;
    use EasyProjects\SimpleRouter\Response as Response;

    $router = new Router();
    
    //If you need allow petitions from javascript, use

    $router->cors()->setAllowedOrigins("easyprojects.tech", "localhost");
    $router->cors()->setAllowedMethods("GET", "POST", "PUT", "DELETE");
    $router->cors()->setAllowedHeaders("Content-Type", "Authorization");


    /*
        Require all files from folder to final subfolder
        
        Then if you have:
        app
            controllers
                - UsersController.php
                Implicits
                    - UserImplicit.php
            models
                - UsersModel.php
            roots
                - UsersRoot.php
        
        Require all files php from controllers, implicits, models and roots
    */
    $router->importAll("./app"); //If you dont use namespaces you need to use this

    /*
        Require all files from folder
        
        If you have:
        app
            controllers
                - UsersController.php
                Implicits
                    - UserImplicit.php
            models
                - UsersModel.php
            roots
                - UsersRoot.php
            - home.php
        
        Require all files php in app, only home.php
    */
    $router->import("./configs"); //If you dont use namespaces you need to use this
    
    /* New Autoload! */
    $router->autoload(); 
    /*
        Import only the packages and classes that you are using.

        Improves application loading performance.

        For this you need to use "namespace".
    */
    
    $router->get("/get/{idUser}", function(Request $req, Response $res){
        $res->status(200)->send($req->params->idUser);
    });
    
    $router->post("/add", function(Request $req, Response $res){
        //Get Files
        $res->status(200)->send($req->body->nameUser);
    });

    $router->put("/update/{idUser}", function(Request $req, Response $res){
        $res->status(200)->send($req->params->idUser." - ".$req->body->nameUser);
    });

    $router->delete("/delete/{idUser}", function(Request $req, Response $res){
        $res->status(200)->send($req->params->idUser." - ".$req->body->nameUser);
    });
    
    //DEPRECATED
    //$api->start();
```

Now if you need get Files Uploaded, use:

```php
    $router->post("/upload/folder/{idFolder}", function(Request $req, Response $res){
        $res->status(200)->send($req->files->img->name." - ".$req->params->idFolder);
    });
```

![image](https://user-images.githubusercontent.com/86737117/144947334-5f09b150-5ec4-481c-9dfd-bc09592c7250.png)

# How does it work?

It consists of 3 classes, Router, Request, Response.

Router is the global class to be able to design the routes, you can put different methods: get, post, put, delete.
Request contains the data sent by the client's browser or the client in general, (url or form parameters, and headers).
Response contains two methods, status to send a response code (404, 500, 403), send () send a response to any object in a json.


