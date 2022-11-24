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
            
                $from = (!empty($parameters[0]))?$parameters[0]:0;
                $number = (!empty($parameters[1]))?$parameters[1]:10;
            try{
                $persons = $userManager->getPersons($from, $number);
            }catch(UserException $error){
                $this->addMessage($error->getMessage(), TypeOfMessage::ERROR);
            }
        
            $this->data['persons'] = $persons;
            // Nastavení šablony
            $this->view = 'admin';
            
        }
    }