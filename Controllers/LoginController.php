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

            if ($userManager->getUser())
                $this->redirect('admin');
            // Hlavička stránky
            $this->head['title'] = 'Přihlášení';
            if ($_POST)
            {
                try
                {
                    $userManager->login($_POST['name'], $_POST['password']);
                    $this->addMessage('Byl jste úspěšně přihlášen.', TypeOfMessage::SUCCESS);
                    $this->redirect('profil/'.$_SESSION["users_id"]);
                }
                catch (UserException $error)
                {
                    $this->addMessage($error->getMessage(), TypeOfMessage::ERROR);
                }
            }
            // Nastavení šablony
            $this->view = 'login';
        }
    }