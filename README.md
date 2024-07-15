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

Example easy code:
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
    $router->cors()->setAllowedOrigins("https://easyprojects.tech/", "localhost");
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
    $router->start();
```
For files you can do:

```php
    $router->post("/upload/folder/{idFolder}", 
        fn () => Router::$response->status(200)->send(Router::$request->files->img->name." - ".Router::$request->params->idFolder)
    );
```

# How does it work?

It consists of 3 classes, Router, Request, Response.

Router is the global class to be able to design the routes, you can put different methods: get, post, put, delete.
Request contains the data sent by the client's browser or the client in general, (url or form parameters, and headers).
Response contains two methods, status to send a response code (404, 500, 403), send () send a response to any object in a json.

## $router = new Router();
Es solo una instancia de la clase `Router`, que permite contener toda la información de las rutas y evaluarlas para la función `$router->start();`.

## $router->start();
Al contrario de como se cree, la clase `$router->start();` sirve solo para responder un 404 en caso, no se haya encontrado ningun match en las rutas.

## $router->autoload();
El metodo `$router->autoload();` es para proyectos basados en paquetes.

![image](https://github.com/FrederickMontiel/SimpleRouter-php/assets/86737117/0ace69c5-db11-435a-821a-628bcc106d3d)

Mira el namespace en la siguiente imagen:

![image](https://github.com/FrederickMontiel/SimpleRouter-php/assets/86737117/9fb64760-8b72-49c7-a108-03213e93684d)

Ahora mira la estructura de las carpetas.

![image](https://github.com/FrederickMontiel/SimpleRouter-php/assets/86737117/598bf0ac-f760-4e97-b871-173fb2b75200)

Debes tomar en cuenta que el mismo nombre que se usa en la clase debe ser el mismo que se usa en el archivo, es decir que si tu clase es `ExampleController` el archivo debe ser `ExampleController.php`, dentro de la estructura del paquete en el namespace, por lo que el archivo debe estar en el siguiente path: `App/Controllers/ExampleController.php` desde donde esté tu index.

Ahora si importas los archivos con `require` o con `include` no es necesario usar esta funcionalidad aunque hay que tomar en cuenta que no estará optimizado el código y será mucho más lento porque que se cargan todas las clases usadas en el proyecto y no mientras se vayan requiriendo.

## $router->cors();
¿Qué es cors? Es una capa de seguridad, dejame darte un ejemplo:

Imagina que alguien tiene intenciones maliciosas y trata de hacer peticiones a tu api en php desde una pagina web que el atacante hizo, supongamos que tu tienes el dominio `mydomain.com` y el atacante tiene `domainatacker.com`, imaginemos aún más, supongamos que tu tienes mucha información sobre pokemones como `pokeapi` que reflejas con tu ApiREST, ahora imagina que el atacante puede usar la información que responde tu api en su sitio web, esto consume recursos tuyos y te hace perder clientes porque alguien mas está usando la información que tienes almacenada devuelta por la api y los clientes obtienen tu servicio a un menor costo o incluso gratis, esto se traduce en perdidas para la empresa. 

(Esto aplica solo para navegadores que ejecuten javascript para hacer peticiones http, es decir que si hacen peticiones localmente desde algun servicio local como `curl` pueden hacer peticiones sin problema)

Pues las Reglas Cors(Cors rules) previene esto mismo,

### $router->cors()->setAllowedOrigins();
El metodo `$router->cors()->setAllowedOrigins` permite decirle al navegador del cliente, "Si estan enviando una petición desde estos dominios, dejalos pasar y sigue ejecutando todo lo demás.".

La cantidad de parametros son infinitos, asi que puedes agregar infinitos dominios.

`$router->cors()->setAllowedOrigins("https://easyprojects.tech/", "localhost");`

Toma en cuenta que si usas el metodo sin nigun parametro le estas diciendo al navegador que, acepte las peticiones de cualquier dominio: 

`$router->cors()->setAllowedOrigins();` 

Algo mas a tomar en cuenta es que ejemplo, si agregas solo `$router->cors()->setAllowedOrigins("localhost");` estas diciendo que cualquier puerto de localhost puede acceder a los recursos de la api. Es decir que, `"localhost:80"`, `"localhost:3000"`, `"localhost:4200"` y todos los demas `"localhost:any"` y sin importar si es con el protocolo `http` o `https` podrán hacer peticiones a tu api.

Con esto me refiero a que tengas cuidado cuando hagas: `$router->cors()->setAllowedOrigins("easyprojects.tech");` porque si alguien crea un nombre de dominio con cualquier texto antes de "easyprojects.tech" es decir que si alguien crea un nombre de dominio `aeasyprojects.tech` podrá acceder a un tus apis, de la misma manera con el dominio supongamos que leiste la recomentacion anterior y agregaste el protocolo `http` o `https` de la siguiente manera `$router->cors()->setAllowedOrigins("https://easyprojects.tech");`, si alguien crea un dominio 'easyprojects.techa' puede acceder porque se evalua que el origen contenga `easyprojects.tech`, por lo que te aconsejo completamente evaluar la ruta completa de esta manera: `$router->cors()->setAllowedOrigins("https://easyprojects.tech/");`

### $router->cors()->setAllowedMethods();
Este metodo puede aceptar parametros para metodos http, es decir que se puede decir al navegador que no bloquee ninguna solicitud que se envie con el metodo tal:

`$router->cors()->setAllowedMethods("GET", "POST", "PUT", "DELETE");` 

Aqui se le indica al navegador, deja pasar todas las peticiones enviadas con GET, POST, PUT y DELETE.

### $router->cors()->setAllowedMethods();
Hay algunos headers que están bloqueados desde el mismo navegador, es decir, por defecto el navegador no permite cambiar el origen de la peticion, sin embargo si se agrega aqui, de modo que si le permitimos el header `Access-Control-Allow-Origin`, aceptará que se pueda cambiar el origen. 

//Nada recomentable hacer esto.
`$router->cors()->setAllowedHeaders("Access-Control-Allow-Origin");`

## Metodos
Los metodos son formas de recibir datos desde el lado de php, datos enviados desde la computadora del cliente de una forma especifica y los mas usuales son:

GET, POST, PUT, DELETE.


### GET
`https://easyprojects.tech/Categories?id=1&param=eeeeee`

Solo servirá para devolver información.

`$router->body` estará vacío siempre ya que por defecto GET, no tiene ningun cuerpo de solicitud, ni los navegadores ni las librerias permiten enviar peticiones http GET con cuerpo de solicitud.

### POST
`https://easyprojects.tech/Category`

Solo servirá para agregar información a la base de datos y subir archivos al servidor o algun otro servicio que se esté usando.

Puede obtener archivos con el siguiente `Router::$request->files`, se puede usar `Router::$request->files->archivo` para obtener el archivo subido con la key archivo y asi secuencialmente. 

### PUT
`https://easyprojects.tech/Category`

Solo servirá para actualizar información (No se puede subir archivos).

### DELETE
Solo servirá para eliminar información.

Todos los metodos pueden usar `Router::$request->headers`,  `Router::$request->query`,  `Router::$request->body` y `Router::$request->params`.
Solo el metodo `POST` tendrá información en `Router::$request->files`.


### $router->get(); $router->post(); $router->put(); $router->delete();
Cualquiera de estos metodos sirven para registrar una ruta dentro del router para que se evalue, el metodo consiste de un parametro que requiere la ruta a registrar y parametros infinitos de metodos a ejecutar continuamente, es decir que:

`Parametro 1` -> `/Categories`

`Parametro (2 o más hasta el infinito)` -> `fn() => MethodToExecute()`.

Ejemplo:
```php
$router->get(
    "/Categories", //<-- Primer parametro
    fn () => MethodOne(), //<-- Segundo parametro
    fn () => MethodTwo(), //<-- Tercer parametro
    //... hacia el infinito
    fn () => MethodInfinite(), //<-- Ultimo parámetro parametro
);
```

El mismo principio lo usa post, put, delete.

Cualquier duda o consulta siempre puedes escribirme a `fmontiel@easyprojects.tech`



 



















