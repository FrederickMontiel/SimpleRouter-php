# SimpleRouter-php

Te permitirá crear un routing de la forma mas fácil posible, recibiendo todo lo enviado por un usuario a través de un callback.

Primero necesitas redireccionar cualquier petición con nuestro archivo de configuración .htaccess de ejemplo:

```txt
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [QSA,L]
```

Importa la libreria.

```php
  require "./PhpRouter.php";
```
Crea una nueva instancia de la clase

```php
  $api = new PhpRouter();
```
Ahora puedes usar la api, llamando a la variable donde guardaste la instancia y usar uno de sus metodos:

post, get, put, delete.

teniendo el archivo principal dentro de una carpeta dentro de htdocs.

```php
    $api->post("/ejemplo/{numero}", function($req){
        echo json_encode($req);
    });
```

Al final de tu aplicación necesitas usar este metodo para la verificación de la utilización de alguna ruta.

```php
     $api->start();
```

Codigo de ejemplo:

```php
  <?php
    require "./PhpRouter.php";
    header("Content-Type: application/json");

    $api = new PhpRouter();
    $api->post("/ejemplo/{numero}", function($req){
        echo json_encode($req);
    });

    $api->start();
```

Respuesta en la variable $req
![image](https://user-images.githubusercontent.com/86737117/142752088-0e94f513-8db0-4156-8049-eee29a2c2f2a.png)
