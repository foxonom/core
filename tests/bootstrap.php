<?php
set_include_path(
  __DIR__ . "/../src" .
  PATH_SEPARATOR .
  get_include_path()
);

if (file_exists(__DIR__ . "/../vendor/autoload.php")) {
    require(__DIR__ . "/../vendor/autoload.php");
}
spl_autoload_register(function($class_name)
  {
    $class_name = ltrim($class_name, "\\");
    $file_name  = "";
    if ($pos_last = strrpos($class_name, "\\")) {
      $namespace  = substr($class_name, 0, $pos_last);
      $class_name = substr($class_name, $pos_last + 1);
      $file_name  = str_replace("\\", DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        
        if (strpos($file_name, "Headzoo\\Core\\Tests\\") === 0) {
            $file_name = str_replace("\\Tests", "", $file_name);
            $file_name = __DIR__ . "\\{$file_name}";
        } else if ('Exceptions\\' === $file_name) {
            $file_name = __DIR__ . "\\Headzoo\\Core\\Exceptions\\";
        }
    }
    $file_name .= str_replace("_", DIRECTORY_SEPARATOR, $class_name) . ".php";

    require($file_name);
  });