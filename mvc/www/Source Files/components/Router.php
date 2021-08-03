<?php

class Router
{
  private $routes;

  public function __construct()
  {
    $routesPath=ROOT.'/config/routes.php';
    $this->routes = include($routesPath);
  }
  // Mетод возвращает строку
  private function getURI()
  {
    if (!empty($_SERVER['REQUEST_URI'])) {
      return trim($_SERVER['REQUEST_URI'], '/');
    }
  }
  public function run()
  {
    //Получить строку запроса
    $uri = $this->getURI();

    // Проверить наличие такого запроса в routes.php
    foreach ($this->routes as $uriPattern => $path) {

      // Сравниваем $uriPattern и $url

      if (preg_match("~$uriPattern~", $uri)) {

        // Eсли есть совпаденииеб определить какой коетроллер
        // и action обрабатывает запрос

        // Получаем внутренний путь из внешнего согласно правилу
        $internalRoute = preg_replace("~$uriPattern~", $path, $uri);

        $segments = explode('/', $internalRoute);

        unset($segments[0]);
        unset($segments[1]);
        
        $controllerName = array_shift($segments).'Controller';
        $controllerName = ucfirst($controllerName);

        $actionName = 'action'.ucfirst(array_shift($segments));
        $parametrs = $segments;

        // Подключить файл класса-контроллера
        $controllerFile = ROOT.'/controllers/'.
                $controllerName.'.php';

        if (file_exists($controllerFile)) {
          include_once($controllerFile);
        }

        // Создать обЪект, вызват метод (т.е. action)
        $controllerObject = new $controllerName;

        $result = call_user_func_array(array($controllerObject, $actionName), $parametrs);

        if ($result != null) {
          break;
        }
      }
    }


  }
}






 ?>
