<?php
class AdminController extends Controller
{
    public function work(array $parameters): void
    {        
        // Do administrace mají přístup jen přihlášení uživatelé s právy admina.
        //$this->userVerify(true);
        // Hlavička stránky
        $this->hlavicka['titulek'] = 'Administrace';
        // Získání dat o přihlášeném uživateli
        $userManager = new UserManager();
        if(isset($parameters[1]) && $parameters[1] != "") {
            $from = $parameters[0];
            $to = $parameters[1];
        
        }else if(isset($parameters[0]) && $parameters[0] != ""){    
            $from = 0;
            $to = ($parameters[0]==0)?0:$parameters[0]-1;
       
        }else{
            $from = 0;
            $to = 9;
          
        }
        $countPerson=$userManager->countPersons();
        
        //Zavoláme paginátor.
        $paginator = new Paginator($countPerson, $from, $to);
        
        $limit = $paginator->getLimit();
        
        //aktuální stránka
        $from=$paginator->getFrom();
        //echo $from . " " . $to . " " . $limit;
        //exit;
        try {
            $persons = $userManager->getPersons($from, $limit);
        } catch (UserException $error) {
            $this->addMessage($error->getMessage(), TypeOfMessage::ERROR);
        }
        $this->data["pages"] = $paginator->GeneratePagination(2);
        
        //Předání dat o osobě viewu.
        $this->data['persons'] = $persons;
        // Nastavení šablony
        $this->view = 'admin';
    }
}
