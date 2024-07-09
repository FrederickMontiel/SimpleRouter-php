<?php
    namespace EasyProjects\SimpleRouter;

    use Exception;

    class Router{
        private static $status = false;
        private $input = false;

        public static Request $request;
        public static Response $response;

        public function __construct(
            private ?Cors $cors = new Cors(),
        ){}

        public function start(){
            if(self::$status == false && php_sapi_name() != 'cli'){
                $res = new Response();
                $res->status(404)->send([
                    "message" => "The petition {{".$_SERVER['REQUEST_METHOD']."}} in this root not exists"
                ]);
            }
        }

        public function cors() : Cors{
            return $this->cors;
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

            self::$status = true;
            
            return $req;
        }

        private function sanitizeRoots($metodo, $root){
            $isCLI = (php_sapi_name() == 'cli');

            if(!$isCLI){
                $directory = explode("/", $_SERVER['PHP_SELF']);

                //echo json_encode($directory);

                if(!strpos($directory[count($directory) - 1], ".php")){
                    $directory = ["index.php"];
                }

                unset($directory[count($directory) - 1]);
                
                $directory = implode("/", $directory);
                $root = $directory.$root;
                $uri = preg_replace("/\?.*/", "", urldecode($_SERVER['REQUEST_URI']));
                $uriDos = $uri;
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

        public function get($root, callable ...$callbacks){
            $sanitized = $this->sanitizeRoots("GET", $root);
            if($sanitized != false){
                $req = $this->WriteRoot($root, $sanitized['regex'], $sanitized['uri']);

                Router::$response = new Response;

                if($this->input == false){
                    Router::$request = new Request($_GET, $req['params'], $req['headers'], $_REQUEST);
                }else{
                    Router::$request = new Request($this->input, $req['params'], $req['headers'], $_REQUEST);
                }

                foreach ($callbacks as $callback) {
                    $callback(Router::$request, Router::$response);
                }
            }
        }

        public function post($root, callable ...$callbacks){
            $sanitized = $this->sanitizeRoots("POST", $root);
            if($sanitized != false){
                $req = $this->WriteRoot($root, $sanitized['regex'], $sanitized['uri']);
                $req['body'] = $_POST;

                Router::$response = new Response;

                if($this->input == false){
                    Router::$request = new Request($_POST, $req['params'], $req['headers'], $_REQUEST);
                }else{
                    Router::$request = new Request($this->input, $req['params'], $req['headers'], $_REQUEST);
                }

                foreach ($callbacks as $callback) {
                    $callback(Router::$request, Router::$response);
                }
            }
        }

        public function put($root, callable ...$callbacks){
            $sanitized = $this->sanitizeRoots("PUT", $root);
            if($sanitized != false){
                $req = $this->WriteRoot($root, $sanitized['regex'], $sanitized['uri']);
                Router::$response = new Response;

                if($this->input == false){
                    parse_str(file_get_contents('php://input'), $_PUT);
                    Router::$request = new Request($_PUT, $req['params'], $req['headers'], $_REQUEST);
                }else{
                    Router::$request = new Request($this->input, $req['params'], $req['headers'], $_REQUEST);
                }

                foreach ($callbacks as $callback) {
                    $callback(Router::$request, Router::$response);
                }
            }
        }

        public function delete($root, callable ...$callbacks){
            $sanitized = $this->sanitizeRoots("DELETE", $root);
            if($sanitized != false){
                $req = $this->WriteRoot($root, $sanitized['regex'], $sanitized['uri']);

                Router::$response = new Response;
                
                if($this->input == false){
                    parse_str(file_get_contents('php://input'), $_DELETE);
                    Router::$request = new Request($_DELETE, $req['params'], $req['headers'], $_REQUEST);
                }else{
                    Router::$request = new Request($this->input, $req['params'], $req['headers'], $_REQUEST);
                }

                foreach ($callbacks as $callback) {
                    $callback(Router::$request, Router::$response);
                }
            }
        }

        public function patch($root, callable ...$callbacks){
            $sanitized = $this->sanitizeRoots("PATCH", $root);
            if($sanitized != false){
                $req = $this->WriteRoot($root, $sanitized['regex'], $sanitized['uri']);

                Router::$response = new Response;

                if($this->input == false){
                    parse_str(file_get_contents('php://input'), $_PATCH);
                    Router::$request = new Request($_PATCH, $req['params'], $req['headers'], $_REQUEST);
                }else{
                    Router::$request = new Request($this->input, $req['params'], $req['headers'], $_REQUEST);
                }

                foreach ($callbacks as $callback) {
                    $callback(Router::$request, Router::$response);
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
                if(file_exists($url)){
                    require_once $url;
                }
            });
        }

        public function streamFile($path, $contentType = 'application/octet-stream') {
            if (file_exists($path)) {
                header('Content-Type: ' . $contentType);
                header('Content-Length: ' . filesize($path));
                header('Content-Disposition: attachment; filename="' . basename($path) . '"');
                readfile($path);
                exit;
            } else {
                http_response_code(404);
                die('File not found');
            }
        }

        //Coming Soon
        public function prepareAssets($path="./assets"){
            $path .= "/";
            if(preg_match_all("/\/assets\/(.*)/im", $_SERVER['REQUEST_URI'], $values)){
                //var_dump($values);
                
                self::$status = true;
                //filetype($path.$values[1][0])


                //echo $secondType;

                if(file_exists($path.$values[1][0])){
                    /*$firstType = explode("/", mime_content_type($path.$values[1][0]))[0];
                    $secondType = explode(".", $values[1][0]);
                    $secondType = end($secondType);*/

                    //. mime_content_type($path.$values[1][0])
                    header('Content-Type: '.mime_content_type($path.$values[1][0]) );
                    header('Content-Length: ' . filesize($path.$values[1][0]));
                    header('Content-Disposition: attachment; filename="' . basename($path.$values[1][0]) . '"');
                    readfile($path.$values[1][0]);
                    exit;

                    /*if($secondType == "js"){
                        $firstType = "text";
                        $secondType = "javascript";
                        
                        header("Content-type: application/javascript");
                    
                    }else{
                        header("Content-type: ".$firstType."/".$secondType);
                    }

                    echo file_get_contents($path.$values[1][0]);

                    exit;*/
                }else{
                    $response = new Response();
                    $response->status(404)->send(["error" => "File not found."]);
                }
            }
        }
    }
