<?php
class AdminController extends Controller
{
    public function work(array $parameters): void
    {        
        // Do administrace mají přístup jen přihlášení uživatelé s právy admina.
        $this->userVerify(true);
        // Hlavička stránky
        $this->hlavicka['titulek'] = 'Administrace';
        //Volání personManagera.
        $personManager = new PersonManager();
        
        if(isset($parameters[1]) && $parameters[1] != "") {
            $from = $parameters[0];
            $limit = $parameters[1];
        
        }else if(isset($parameters[0]) && $parameters[0] != ""){    
            $from = 0;
            $limit = $parameters[0];
       
        }else{
            $from = 0;
            $limit = 10;
          
        }
        $countPerson=$personManager->countPersons();
        
        //Zavoláme paginátor.
        $paginator = new Paginator("admin" ,$countPerson, $from, $limit);     
        try {
            $persons = $personManager->getPersonsAll($from, $limit);
              
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
