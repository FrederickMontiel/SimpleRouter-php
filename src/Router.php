<?php
    namespace EasyProjects\SimpleRouter;

    use Exception;

    class Router{
        private $status = false;
        private $input = false;

        public function start(){
            if($this->status == false){
                $res = new Response();
                $res->status(404)->send(array(
                    "code" => 0,
                    "message" => "The petition {{".$_SERVER['REQUEST_METHOD']."}} in this root not exists"
                ));
            }
        }

        public function cors($domains = "*", $methods = "*", $headers = "*"){
            $isCLI = (php_sapi_name() == 'cli');

            if(!$isCLI){
                if(is_array($domains)){
                    $domains = implode(", ", $domains);
                }
    
                if(is_array($methods)){
                    $methods = implode(", ", $methods);
                }
    
                if(is_array($headers)){
                    $headers = implode(", ", $headers);
                }
    
                header("Access-Control-Allow-Origin: ".$domains);
                header("Access-Control-Allow-Methods: ".$methods);
                header("Access-Control-Allow-Headers: ".$headers);
            }
        }

        private function WriteRoot($root, $regex, $uri){
            $req = array();
            preg_match_all("/\{(.*?)\}/", $root, $params);
            $paramsNames = $params[1];

            preg_match_all("/".$regex."/", $uri, $matches);

            $params = array();

            for ($i=1; $i < count($matches); $i++) { 
                
                $params[$paramsNames[$i-1]] = $matches[$i][0];
            }

            $req['params'] = $params;
            $req['headers'] = getallheaders();

            if(isset($req['headers']['Content-Type']) == "application/json"){
                try{
                    $this->input = json_decode(file_get_contents("php://input"), true);
                }catch(Exception $e){}
            }

            $this->status = true;
            
            return $req;
        }

        private function sanitizeRoots($metodo, $root){
            $isCLI = (php_sapi_name() == 'cli');

            if(!$isCLI){
                $directory = explode("/", $_SERVER['PHP_SELF']);
                unset($directory[count($directory) - 1]);
                
                $directory = implode("/", $directory);
                $root = $directory.$root;
                $uri = $_SERVER['REQUEST_URI'];
                $uriDos = $_SERVER['REQUEST_URI'];
                $method = $_SERVER['REQUEST_METHOD'];
    
                if($uri[strlen($uri)-1] != "/"){
                    $uri .= "/";
                }
    
                if($uriDos[strlen($uriDos)-1] != "/"){
                    $uriDos .= "/";
                }
    
                if($root[strlen($root)-1] != "/"){
                    $root .= "/";
                }
    
                $regex = preg_replace("/\{(.*?)\}/", "(.*?)", str_replace("/", "\/", $root));
    
                if($method == $metodo && count(explode("/", $root)) == count(explode("/", $uri)) && preg_match("/".$regex."$/im", $uriDos)){
                    
                    return array("regex" => $regex, "uri" => $uriDos);
                }else{
                    return false;
                }
            }
        }

        public function get($root, $callback){
            $sanitized = $this->sanitizeRoots("GET", $root);
            if($sanitized != false){
                $req = $this->WriteRoot($root, $sanitized['regex'], $sanitized['uri']);
                if($this->input == false){
                    $callback(new Request($_GET, $req['params'], $req['headers']), new Response);
                }else{
                    $callback(new Request($this->input, $req['params'], $req['headers']), new Response);
                }
            }
        }

        public function post($root, $callback){
            $sanitized = $this->sanitizeRoots("POST", $root);
            if($sanitized != false){
                $req = $this->WriteRoot($root, $sanitized['regex'], $sanitized['uri']);
                $req['body'] = $_POST;

                if($this->input == false){
                    $callback(new Request($_POST, $req['params'], $req['headers']), new Response);
                }else{
                    $callback(new Request($this->input, $req['params'], $req['headers']), new Response);
                }
            }
        }

        public function put($root, $callback){
            $sanitized = $this->sanitizeRoots("PUT", $root);
            if($sanitized != false){
                $req = $this->WriteRoot($root, $sanitized['regex'], $sanitized['uri']);
                if($this->input == false){
                    parse_str(file_get_contents('php://input'), $_PUT);
                    $callback(new Request($_PUT, $req['params'], $req['headers']), new Response);
                }else{
                    $callback(new Request($this->input, $req['params'], $req['headers']), new Response);
                }
            }
        }

        public function patch($root, $callback){
            $sanitized = $this->sanitizeRoots("PATCH", $root);
            if($sanitized != false){
                $req = $this->WriteRoot($root, $sanitized['regex'], $sanitized['uri']);
                if($this->input == false){
                    parse_str(file_get_contents('php://input'), $_PATCH);
                    $callback(new Request($_PATCH, $req['params'], $req['headers']), new Response);
                }else{
                    $callback(new Request($this->input, $req['params'], $req['headers']), new Response);
                }
            }
        }

        public function delete($root, $callback){
            $sanitized = $this->sanitizeRoots("DELETE", $root);
            if($sanitized != false){
                $req = $this->WriteRoot($root, $sanitized['regex'], $sanitized['uri']);

                if($this->input == false){
                    parse_str(file_get_contents('php://input'), $_DELETE);
                    $callback(new Request($_DELETE, $req['params'], $req['headers']), new Response);
                }else{
                    $callback(new Request($this->input, $req['params'], $req['headers']), new Response);
                }
            }
        }

        public function WriteRootRegex($regex, $callback){
            if(preg_match($regex, $_SERVER['REQUEST_URI'], $matches)){
                $callback(false, $matches);
            }else{
                $callback(true, null);
            }
        }

        public function listFolderFiles($dir){
            if(file_exists($dir)){
                $ffs = scandir($dir);
            
                unset($ffs[array_search('.', $ffs, true)]);
                unset($ffs[array_search('..', $ffs, true)]);
            
                if (count($ffs) < 1)
                    return;

                foreach($ffs as $ff){
                    if(is_dir($dir.'/'.$ff)){
                        $this->listFolderFiles($dir.'/'.$ff);
                    }else{
                        $fileExt = explode(".", $ff);
                        
                        if(end($fileExt) == "php"){
                            require_once $dir.'/'.$ff;
                        }
                    }
                }
            }
        }

        public function listFiles($dir){
            if(file_exists($dir)){
                $ffs = scandir($dir);
            
                unset($ffs[array_search('.', $ffs, true)]);
                unset($ffs[array_search('..', $ffs, true)]);
            
                if (count($ffs) < 1)
                    return;

                foreach($ffs as $ff){
                    if(is_dir($dir.'/'.$ff)){
                        //$this->listFiles($dir.'/'.$ff);
                    }else{
                        $fileExt = explode(".", $ff);
                        
                        if(end($fileExt) == "php"){
                            require_once $dir.'/'.$ff;
                        }
                    }
                }
            }
        }

        public function import($path){
            $this->listFiles($path);
        }

        public function importAll($path){
            $this->listFolderFiles($path);
        }
        
        public function autoload(){
            spl_autoload_register(function($class){
                $url = str_replace("\\", "/", $class.".php");
                require_once $url;
            });
        }
    }
