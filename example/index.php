<?php
    include_once __DIR__."/../vendor/autoload.php";

    use EasyProjects\SimpleRouter\Router as Router;
    use EasyProjects\SimpleRouter\Request as Request;
    use EasyProjects\SimpleRouter\Response as Response;

    $api = new Router();
    $api->get("/a", function(Request $req, Response $res){
        $res->status(200)->send($req);
    });