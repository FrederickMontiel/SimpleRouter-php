<?php
namespace EasyProjects\SimpleRouter;

class Cors{
    /**
     * @param string ...$origins
     * @return bool|Cors
     */
    public function setAllowedOrigins(string ...$origins): bool | Cors {
        try{
            if(count($origins) == 0)
                header("Access-Control-Allow-Origin: *");
            else
                header("Access-Control-Allow-Origin: ".implode(", ", $origins));

            return self::class;
        }catch(\Exception $e){
            return false;
        }
    }

    public function setAllowedMethods(string ...$methods): bool | Cors {
        try{
            if(count($methods) == 0)
                header("Access-Control-Allow-Methods: *");
            else
                header("Access-Control-Allow-Methods: ".implode(", ", $methods));

            return self::class;
        }catch(\Exception $e){
            return false;
        }
    }


    public function setAllowedHeaders(string ...$headers): bool | Cors {
        try{
            if(count($headers) == 0)
                header("Access-Control-Allow-Headers: *");
            else
                header("Access-Control-Allow-Headers: ".implode(", ", $headers));

            return self::class;
        }catch(\Exception $e){
            return false;
        }
    }
}