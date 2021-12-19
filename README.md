# SimpleRouter-php

Install our package with composer:

Instala nuestro paquete con composer:

```txt
composer require easy-projects/simple-router
```

You must redirect all requests to your main file with this .htaccess or as you can see in the example folder "example".

Debes redireccionar toda solicitud hacia tu archivo principal con este .htaccess o como puedes visualizar en la carpeta de ejemplo "example".

```txt
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [QSA,L]
```

Example code:

Codigo de ejemplo:

```php
<?php
    //Include composer
    include_once __DIR__."/vendor/autoload.php";

    use EasyProjects\SimpleRouter\Router as Router;
    use EasyProjects\SimpleRouter\Request as Request;
    use EasyProjects\SimpleRouter\Response as Response;

    $api = new Router();
    
    //If you need allow petitions from javascript, use
    $api->cors();
    
    $api->get("/get/{idUser}", function(Request $req, Response $res){
        $res->status(200)->send($req->params->idUser);
    });
    
    $api->post("/add", function(Request $req, Response $res){
        //Get Files
        $res->status(200)->send($req->body->nameUser);
    });

    $api->put("/update/{idUser}", function(Request $req, Response $res){
        $res->status(200)->send($req->params->idUser." - ".$req->body->nameUser);
    });

    $api->delete("/delete/{idUser}", function(Request $req, Response $res){
        $res->status(200)->send($req->params->idUser." - ".$req->body->nameUser);
    });
    
    //REQUIRED
    $api->start();
```

Now if you need get Files Uploaded, use:

Si necesitas obtener archivos subidos usa:

```php
    $api->post("/upload/folder/{idFolder}", function(Request $req, Response $res){
        $res->status(200)->send($req->files->img->name." - ".$req->params->idFolder);
    });
```

![image](https://user-images.githubusercontent.com/86737117/144947334-5f09b150-5ec4-481c-9dfd-bc09592c7250.png)

# How does it work?

It consists of 3 classes, Router, Request, Response.

Router is the global class to be able to design the routes, you can put different methods: get, post, put, delete.
Request contains the data sent by the client's browser or the client in general, (url or form parameters, and headers).
Response contains two methods, status to send a response code (404, 500, 403), send () send a response to any object in a json.

# ¿Cómo funciona?

Se compone de 3 clases, Router, Request, Response.

Router es la clase global para poder diseñar las rutas, puedes poner distintos metodos: get, post, put, delete.
Request contiene los datos enviados por el navegador del ciente o el cliente en general, (Parametros de url o formulario, y los headers).
Response contiene dos metodos, status para enviar un codigo de respuesta (404, 500, 403), send() envia de respuesta cualquier objeto en un json.

