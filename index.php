<?php 
    spl_autoload_register("autoloader");
    session_start();
    mb_internal_encoding("UTF-8");
    
    function autoloader(string $class) : void
    {
        // Končí název třídy řetězcem "Kontroler" ?
        if (preg_match('/Controller$/', $class)){  
            require("Controllers/" . $class . ".php");
        }else{
            require("Models/" . $class . ".php");
        }
    }
    
    // Připojení k databázi
    Mysql::connect("localhost", "root", "", "insurance_company");
       
    $router = new RouterController();
    $router->work(array($_SERVER['REQUEST_URI']));
    $router->writeView();

