<?php

class ProfileController extends Controller{
    public function work($parameters) : void{
        $personManager = new PersonManager(); 
        if(isset($parameters[0]) && is_numeric($parameters[0]))
            $person = $personManager->getPerson($parameters[0]);
        else
            $this->redirect("..");
            $this->head["title"] = $person["user"];
        $this->data["person"] = $person;
        
        $this->view="profile";
    }
}