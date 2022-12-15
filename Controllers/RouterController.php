<?php

    class RouterController extends Controller
    {

        protected Controller $controller;

        public function work(array $parameters) : void
        {
            $devidedPath = $this->parseURL($parameters[0]);
            if (empty($devidedPath[0]))
                $this->redirect('login');
            $classOfController = $this->dashesToCamelCase(array_shift($devidedPath)) . 'Controller';
            if (file_exists('Controllers/' . $classOfController . '.php'))
                $this->controller = new $classOfController;
            else
                $this->redirect('error');
            $this->controller->work($devidedPath);
            $this->data['title'] = $this->controller->head['title'];
            $this->data['description'] = $this->controller->head['description'];
            $this->data['keyWords'] = $this->controller->head['keyWords'];
            $this->data['messages'] = $this->getMessages();
            $userManager = new UserManager();
            $this->data['user'] = $userManager->getLoggedUser();
            
            $this->view = 'index';

        }

        private function parseURL(string $url) : array
        {
            $parsedURL = parse_url($url);
            $parsedURL["path"] = ltrim($parsedURL["path"], "/");
            $parsedURL["path"] = trim($parsedURL["path"]);
            $devidedPath = explode("/", $parsedURL["path"]);
            return $devidedPath;
        }
        
        private function dashesToCamelCase(string $text) : string
        {
            $sentence = str_replace('-', ' ', $text);
            $sentence = ucwords($sentence);
            $sentence = str_replace(' ', '', $sentence);
            return $sentence;
        }


    }