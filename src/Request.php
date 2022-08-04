<?php
    namespace EasyProjects\SimpleRouter;

    class Request{
        public static $params;
        public static $body;
        public static $files;
        public static $headers;

        public function __construct($body, $params, $headers)
        {
            //$this->params = json_decode(json_encode($this->decodeUrlData($params)));
            $this->params = json_decode(json_encode($params));
            $this->body = json_decode(json_encode($body));
            $this->files = json_decode(json_encode($_FILES));
            $this->headers = json_decode(json_encode($headers));
        }

        /*private function decodeUrlData($params){
            $newArray = array();

            foreach ($params as $key => $value) {
                $newArray[$key] = urldecode($value);
            }

            return $newArray;
        }*/
    }
