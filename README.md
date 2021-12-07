# SimpleRouter-php

Instala nuestro paquete con composer:
```txt
composer require easy-projects/simple-router
```
Debes redireccionar toda solicitud hacia tu archivo principal con este .htaccess o como puedes visualizar en la carpeta de ejemplo "example".

```txt
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [QSA,L]
```

Codigo de ejemplo:

```php
<?php
    //Incluye el autoload de composer
    include_once __DIR__."/vendor/autoload.php";

    use EasyProjects\SimpleRouter\Router as Router;
    use EasyProjects\SimpleRouter\Request as Request;
    use EasyProjects\SimpleRouter\Response as Response;

    $api = new Router();
    $api->get("/imprimir", function(Request $req, Response $res){
        $res->status(200)->send($req);
    });
    
    $api->post("/imprimir", function(Request $req, Response $res){
        $res->status(200)->send($req);
    });

    $api->put("/imprimir", function(Request $req, Response $res){
        $res->status(200)->send($req);
    });

    $api->delete("/imprimir", function(Request $req, Response $res){
        $res->status(200)->send($req);
    });
    
    //Verifica si se realizó una peticion a alguna ruta. (Esto es requerido)
    $api->start();
```

![image](https://user-images.githubusercontent.com/86737117/144947334-5f09b150-5ec4-481c-9dfd-bc09592c7250.png)

# ¿Cómo funciona?

Se compone de 3 clases, Router, Request, Response.

Router es la clase global para poder diseñar las rutas, puedes poner distintos metodos: get, post, put, delete.
Request contiene los datos enviados por el navegador del ciente o el cliente en general, (Parametros de url o formulario, y los headers).
Response contiene dos metodos, status para enviar un codigo de respuesta (404, 500, 403), send() envia de respuesta cualquier objeto en un json.
