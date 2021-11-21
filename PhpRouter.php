<?php
    class PhpRouter{
        private $status = false;

        public function start(){
            if($this->status == false){
                echo json_encode(array(
                    "code" => 0,
                    "message" => "Cannot {{".$_SERVER['REQUEST_METHOD']."}} in this root"
                ));
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

                $this->status = true;
                
                return $req;
        }

        private function verifyNumberParams($root, $uri){
            if(count(explode("/", $root)) == count(explode("/", $uri))){
                return true;
            }else{
                return false;
            }
        }

        public function post($root, $callback){
            $uri = $_SERVER['REQUEST_URI'];

            if($uri[strlen($uri)-1] != "/"){
                $uri .= "/";
            }

            if($root[strlen($root)-1] != "/"){
                $root .= "/";
            }

            $method = $_SERVER['REQUEST_METHOD']; 
            $rootTwo = str_replace("/", "\/", $root);
            $regex = preg_replace("/\{(.*?)\}/", "(.*?)", $rootTwo);

            if($method == "POST" && $this->verifyNumberParams($root, $uri) && preg_match("/^".$regex."$/im", $uri)){
                $req = $this->WriteRoot($root, $regex, $uri);
                $req['body'] = $_POST;
                $callback($req);
            }
        }

        public function get($root, $callback){
            $uri = $_SERVER['REQUEST_URI'];

            if($uri[strlen($uri)-1] != "/"){
                $uri .= "/";
            }

            if($root[strlen($root)-1] != "/"){
                $root .= "/";
            }
            $method = $_SERVER['REQUEST_METHOD']; 
            $rootTwo = str_replace("/", "\/", $root);
            $regex = preg_replace("/\{(.*?)\}/", "(.*?)", $rootTwo);

            if($method == "GET" && $this->verifyNumberParams($root, $uri) && preg_match("/".$regex."$/im", $_SERVER['REQUEST_URI'])){
                $req = $this->WriteRoot($root, $regex, $uri);
                $req['body'] = $_GET;
                $callback($req);
            }
        }

        public function put($root, $callback){
            $uri = $_SERVER['REQUEST_URI'];

            if($uri[strlen($uri)-1] != "/"){
                $uri .= "/";
            }

            if($root[strlen($root)-1] != "/"){
                $root .= "/";
            }
            $method = $_SERVER['REQUEST_METHOD']; 
            $rootTwo = str_replace("/", "\/", $root);
            $regex = preg_replace("/\{(.*?)\}/", "(.*?)", $rootTwo);

            if($method == "PUT" && $this->verifyNumberParams($root, $uri) && preg_match("/".$regex."$/im", $_SERVER['REQUEST_URI'])){
                parse_str(file_get_contents('php://input'), $_PUT);
                $req = $this->WriteRoot($root, $regex, $uri);
                $req['body'] = $_PUT;
                $callback($req);
            }
        }

        public function delete($root, $callback){
            $uri = $_SERVER['REQUEST_URI'];

            if($uri[strlen($uri)-1] != "/"){
                $uri .= "/";
            }

            if($root[strlen($root)-1] != "/"){
                $root .= "/";
            }

            $method = $_SERVER['REQUEST_METHOD']; 
            $rootTwo = str_replace("/", "\/", $root);
            $regex = preg_replace("/\{(.*?)\}/", "(.*?)", $rootTwo);

            if($method == "DELETE" && $this->verifyNumberParams($root, $uri) && preg_match("/".$regex."$/im", $_SERVER['REQUEST_URI'])){
                parse_str(file_get_contents('php://input'), $_DELETE);
                $req = $this->WriteRoot($root, $regex, $uri);
                $req['body'] = $_DELETE;
                $callback($req);
            }
        }

        public function WriteRootRegex($regex, $callback){
            if(preg_match($regex, $_SERVER['REQUEST_URI'], $matches)){
                $callback(false, $matches);
            }else{
                $callback(true, null);
            }
        }
    }