<?php
    namespace EasyProjects\SimpleRouter;

    class Render{
        private static $strict = null;

        public function __construct()
        {
            
        }

        public static function view($view, $data = []){
            if(self::$strict){
                echo file_get_contents($view.".php");
            }else{
                self::render(file_get_contents($view.".php"), (array)$data);
            }
        }

        private static function render($view, $data){
            $view = preg_replace("/<!--.*?-->/ms", "", $view);

            $view = self::helperAssets($view, $data);
            $view = self::helperForEachss($view, $data);
            $view = self::helperVars($view, $data);

            echo $view;
        }

        private static function getValueByArray($values, $data){
            $newValue = null;

            foreach ($values as $key => $value) {
                if($key == 0){
                    $newValue = (array)$data[$value];
                }else{
                    $newValue = (array)$newValue[$value];
                }
            }
            
            return $newValue[0]??"";
        }

        private static function helperVars($view, $data){
            preg_match_all('/\$\{\{(.*?)\}\}/', $view, $matches);

            foreach ($matches[1] as $key => $value) {
                $newValue = explode(".", $value);
                $view = str_replace("\${{".$value."}}", 
                    self::getValueByArray($newValue, $data)
                , $view);
            }

            return $view;
        }

        private static function helperAssets($view, $data){
            $url = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);
            
            preg_match_all("/\[href\]=[\"\'](.*?)[\"\']/im", $view, $hrefs);
            foreach ($hrefs[1] as $key => $value) {
                $view = str_replace("[href]=\"".$value."\"", 
                    "href=\"".$url."/".$value."\"", 
                $view);
            }

            preg_match_all("/\[src\]=[\"\'](.*?)[\"\']/im", $view, $srcs);
            foreach ($srcs[1] as $key => $value) {
                $view = str_replace("[src]=\"".$value."\"", 
                    "src=\"".$url."/".$value."\"", 
                $view);
            }

            return $view;
        }

        private static function helperForEachss($view, $data){
            return $view;
        }
    }