<?php
    namespace EasyProjects\SimpleRouter;

    class Response{
        public function status($status){
            if(is_int($status)){

            }else{
                intval($status);
            }

            http_response_code($status);

            return new Response();
        }

        public function send($object){
            header("Content-Type: application/json");
            echo json_encode($object);
            exit();
        }
    }
