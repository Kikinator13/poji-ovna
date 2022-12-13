<?php
    class LoginController extends Controller
    {
        public function work(array $parameters) : void
        {
            
            
            $userManager = new UserManager();
            //Pokud je v prvním parametru logout odhlásíme uživatele.
            if (!empty($parameters[0]) && $parameters[0] == 'logout')
            {
                $userManager->logOut();
                $this->redirect('login');
            }
            // Hlavička stránky
            $this->head['title'] = 'Přihlášení';
            //Zjískáme data o přihlášeném užívately.
            $user=$userManager->getLoggedUser();
            //Pokud je uživatel již přihlášen, přesměrujeme ho na jeho profil.
            if ($user)
                $this->redirect('profile/'.$user["persons_id"]);
            if ($_POST)
            {
                try
                {
                    $userManager->login($_POST['user_name'], $_POST['password']);
                    $this->addMessage('Byl jste úspěšně přihlášen.', TypeOfMessage::SUCCESS);
                    //Zjískáme data o nově přihlášeném užívately.
                    $user=$userManager->getLoggedUser();
                    $this->redirect('profile/'.$user["persons_id"]);
                }
                catch (UserException $error)
                {
                    $this->addMessage($error->getMessage(), TypeOfMessage::ERROR);
                }
            }else{
                $_POST["user_name"]="";
            }
            // Nastavení šablony
            $this->view = 'login';
        }
    }