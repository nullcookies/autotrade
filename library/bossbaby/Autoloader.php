<?php
// spl_autoload_register(function ($class_name) {
//     include $class_name . '.php';
// });

// function __autoload($name) {
// 	if ($exists = !class_exists($name) && file_exists($class = LIB_DIR . '/bossbaby/src/' . $name . '.php'))
//         require $class;
//     elseif (!$exists) {
//         eval('class ' . $name . ' extends Exception {}');
//         throw new $name('[__autoload] this file doesn\'t exists: ' . $class);
//     }
// }

// spl_autoload_register(function($className) {
//     $namespace = str_replace("\\", "/", __NAMESPACE__);
//     $className = str_replace("\\", "/", $className);
//     $class = LIB_DIR . DS . (empty($namespace) ? "" : $namespace . "/") . "{$className}.php";

//     if (!is_file($class) or !file_exists($class))
//     	$class = LIB_DIR . DS . (empty($namespace) ? "" : $namespace . "/") . "{$className}.php";
    
//     if (is_file($class) and file_exists($class))
//     	include_once($class);
// });

// class Autoloader
// {
//     static public function loader($className)
//     {
//         $filename = LIB_DIR . DS . 'bossbaby' . DS . str_replace("\\", '/', $className) . ".php";
        
//         // Could not load :(, trick this
//         $filename = str_replace('BossBaby/', '', $filename);
//         $className = str_replace('BossBaby\\', '', $className);
        
//         if (file_exists($filename)) {
//             include($filename);
//             var_dump($className);
//             var_dump(class_exists($className));
//             if (class_exists($className)) {
//                 return TRUE;
//             }
//         }
//         return FALSE;
//     }
// }
// spl_autoload_register('Autoloader::loader');

function __autoload($className) {
    $extensions = array(".php", ".class.php", ".inc");
    $paths = explode(PATH_SEPARATOR, get_include_path());
    $className = str_replace("_" , DIRECTORY_SEPARATOR, $className);
    foreach ($paths as $path) {
        $filename = $path . DIRECTORY_SEPARATOR . $className;
        var_dump($filename);
        foreach ($extensions as $ext) {
            if (is_readable($filename . $ext)) {
                require_once $filename . $ext;
                break;
           }
       }
    }
}