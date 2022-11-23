<?php
    class ErrorController extends Controller
    {
        public function work(array $parameters) : void
        {
        // Hlavička požadavku
        header("HTTP/1.0 404 Not Found");
        // Hlavička stránky
        $this->hlavicka['title'] = 'Chyba 404';
        // Nastavení šablony
        $this->view = 'error';
        }
    }