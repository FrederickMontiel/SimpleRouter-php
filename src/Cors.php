<?php
namespace EasyProjects\SimpleRouter;

class Cors{
    /**
     * @param string ...$origins
     * @return bool|Cors
     */
    public function setAllowedOrigins(string ...$origins): bool | Cors {
        try{
            if(count($origins) == 0){
                header("Access-Control-Allow-Origin: *");
            }else{
                $httpOrigin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : null;

                if($httpOrigin){
                    $contains = function($origin, $origins){
                        foreach($origins as $o){
                            if(strpos($origin, $o) !== false){
                                return true;
                            }
                        }
                        return false;
                    };
    
                    if($contains($httpOrigin, $origins)){
                        header("Access-Control-Allow-Origin: " . $httpOrigin);
                    }
                }else{
                    (new Response())->status(403)->send("Origin not Allowed");
                }

            }

            return new Cors();
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

            return new Cors();
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

            return new Cors();
        }catch(\Exception $e){
            return false;
        }
    }
}
