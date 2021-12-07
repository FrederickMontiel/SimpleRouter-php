<?php
    namespace EasyProjects\SimpleRouter;

    class Request{
        public $params;
        public $body;
        public $files;
        public $headers;

        public function __construct($body, $params, $headers)
        {
            $this->params = $params;
            $this->body = $body;
            $this->files = $_FILES;
            $this->headers = $headers;
        }
    }
