<?php
    abstract class Controller
    {
        protected array $data = array();
        protected string $view = "";
        protected array $head = array('title' => '', 'keyWords' => '', 'description' => '');

        abstract function work(array $parameters) : void;
        
        public function writeView() : void
        {
            if ($this->view)
            {
                //Rozbalí pole do proměnných a ošetří html entity.
                extract($this->treat($this->data));
                //Rozbalí proměnné neošetřéné. Jejich názvy začínají podtržítkem.
                extract($this->data, EXTR_PREFIX_ALL, "");
                require("views/" . $this->view . ".phtml");

            }
        }

        public function redirect(string $url) : never
        {
            header("Location: /$url");
            header("Connection: close");
            exit;
        }
        public function treat($data = null)
        {
            if (!isset($data))
                return null;
            elseif (is_string($data))
                return htmlspecialchars($data, ENT_QUOTES);
            elseif (is_array($data))
            {
                foreach($data as $key => $item)
                {
                    $data[$key] = $this->treat($item);
                }
                return $data;
            }
            else
                return $data;
        }
        /**Přidá zprávu do session pole se zprávamy.
         * Pokud je vyplněn parametr $form name
         */
        public function addMessage(string $text, TypeOfMessage $type=TypeOfMessage::ERROR) : void
        {
            $message = array(
                "type" => $type,
                "text" => $text
            );
            if (isset($_SESSION['messages']))
                $_SESSION['messages'][] = $message; 
            else
                $_SESSION['messages'] = array($message);
        }
        public function getMessages() : array
        {
            if (isset($_SESSION['messages']))
            {
                $messages = $_SESSION['messages'];
                unset($_SESSION['messages']);
                return $messages;
            }
            else
                return array();
        }
        
        
        public function userVerify(bool $admin = false) : void
        {
            $userManager = new UserManager();
            $user = $userManager->getUser();
            if (!$user || ($admin && !$user['admin']))
            {
                $this->addMessage('Nedostatečná oprávnění.');
                $this->redirect('login');
            }
        }
    }