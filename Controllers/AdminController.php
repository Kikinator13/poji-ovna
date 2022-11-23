<?php
    class AdminController extends Controller
    {
        public function work(array $parameters) : void
        {
            // Do administrace mají přístup jen přihlášení uživatelé s právy admina.
            //$this->userVerify(true);
            // Hlavička stránky
            $this->hlavicka['titulek'] = 'Administrace';
            // Získání dat o přihlášeném uživateli
            $userManager = new UserManager();
            
            try{
                $persons = $userManager->getPersons();
            }catch(UserException $error){
                $this->addMessage($error->getMessage(), TypeOfMessage::ERROR);
            }
        
            $this->data['persons'] = $persons;
            // Nastavení šablony
            $this->view = 'admin';
            
        }
    }