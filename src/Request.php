<?php
    namespace EasyProjects\SimpleRouter;

    class Request{
        public $body;
        public $params;
        public $headers;

        public function __construct($body, $params, $headers)
        {
            $this->body = $body;
            $this->params = $params;
            $this->headers = $headers;
        }
    }