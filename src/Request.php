<?php
    namespace EasyProjects\SimpleRouter;

    class Request{
        public $params;
        public $body;
        public $files;
        public $headers;

        public function __construct($body, $params, $headers)
        {
            $this->params = json_decode(json_encode($this->decodeUrlData($params)));
            //$this->params = json_decode(json_encode($params));
            $this->body = json_decode(json_encode($body));
            $this->files = json_decode(json_encode(self::orderFiles($_FILES)));
            $this->headers = json_decode(json_encode($headers));
        }
        
        public static function orderFiles($files){
            foreach ($files as $keyField => $valueField) {
                if(is_array($valueField['name'])){
                    $arrayNuevo = [];
    
                    foreach ($valueField as $keyAttribute => $valueAttribute) {
                        foreach ($valueAttribute as $key => $value) {
                            $arrayNuevo[$key][$keyAttribute] = $value;
                        }
                    }
        
                    $files[$keyField] = $arrayNuevo;
                }else{
                    $files[$keyField] = $valueField;
                }
            }

            return $files;
        }

        private function decodeUrlData($params){
            $newArray = array();

            foreach ($params as $key => $value) {
                $newArray[$key] = urldecode($value);
            }

            return $newArray;
        }
    }
