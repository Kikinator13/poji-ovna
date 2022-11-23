<?php 
    spl_autoload_register("autoloader");
    session_start();
    mb_internal_encoding("UTF-8");
    
    function autoloader(string $trida) : void
    {
        // Končí název třídy řetězcem "Kontroler" ?
        if (preg_match('/Controller$/', $trida))
            require("Controllers/" . $trida . ".php");
        else
            require("Models/" . $trida . ".php");
    }
    
    // Připojení k databázi
    Mysql::connect("localhost", "root", "", "insurance_company");
       
    $router = new RouterController();
    $router->work(array($_SERVER['REQUEST_URI']));
    $router->writeView();

