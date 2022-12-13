<?php

class ProfileController extends Controller
{
    public PersonManager $personManager;
    public ContactManager $contactManager;
    public AddressManager $addressManager;
    public UserManager $userManager;
    public AreaCodeManager $areaCodeManager;
    public Validator $validator;
    public array $person;
    public StateManager $stateManager;

    public function work($parameters): void
    {
        $this->personManager = new PersonManager();
        $this->contactManager = new ContactManager();
        $this->areaCodeManager = new AreaCodeManager();
        $this->addressManager = new AddressManager();
        $this->stateManager = new StateManager();
        $this->userManager = new UserManager();
        $this->validator = new Validator();
        
        //Pokud URL obsahuje id. 
        
        if (isset($parameters[0]) && is_numeric($parameters[0])) {
            //Zjistíme id osoby. 
            $personId = $parameters[0];
            //Na profil má přístup jen admin a vlatník profilu.
            $this->userVerify($personId);
            //Získáme informace o osobě.
            $this->person = $this->getAllclientData($personId);
            //Pokud nemáme id přesměrujem na hlavní stránku.
        } else {
            $this->redirect("chyba");
        }
        //Pokud byl odeslán formulář.
        
        if ($_POST) {
            //Změníme adresu osoby s id $id.
            $this->edit($personId);
        }
        
        //Naformátujeme PSČ.(Uděláme za třetím číslem mezeru)
        
        
        //nastavýme hlavičku
        $this->head["title"] = "profil uživatele" . $this->person["user"];
        //Informace o osobě předáme šabloně.
        $this->data["person"] = $this->person;
        $this->data["states"] = $this->stateManager->getStates();
        $this->data["area_codes"] = $this->areaCodeManager->getAreaCodes();
        
        //Zjistíme aktuálně přihlášeného uživatele.
        $user=$this->userManager->getLoggedUser();
        //Zjistíme jestli je přihlášený uživatel admin.
        $admin = $user["admin"];
        //Pole určuje které položky profilu budou editovatelné uživatelem a které jen adminem.
        $this->data["edit"]=array(
            "first_name" => ($admin) ? "d-inline" : "d-none",
            "last_name" => ($admin) ? "d-inline" : "d-none",
            "user_name" => ($this->person["user_name"]=="Klient nemá účet.") ? "d-none" : "d-inline",
            "address" => "d-inline",
            "mail" => "d-inline",
            "phone" => "d-inline",
            "identity_card_number" => ($admin) ? "d-inline" : "d-none",
            "national_id_number" => ($admin) ? "d-inline" : "d-none",
        );
        
        
        
        //Nastavíme šablonu
        $this->view = "profile";
    }

    public function edit($personId)
    {
        /*
        //Načteme potřebné modely.
        $addressManager = $this->addressManager;
        $personManager = $this->personManager;
        $userManager = $this->userManager;
        $stateManager = $this->stateManager;
        $contactManager = $this->contactManager;
        $areaCodeManager = $this->areaCodeManager;
        $validator = $this->validator;
        /*
        //Zjistíme jestli byl použit ajax; 
        $ajax = (isset($_POST["ajax"])) ? true : false;
        //Ajax odstraníme z postu aby nepřekážel při vkládání do databáze.
        unset($_POST["ajax"]);
        //Ok je nazačátku true
        $ok = true;
        //Ciklus zvaliduje data a pokud je něco špatně změní $ok na false;
        foreach($_POST as $input => $value){
            $_POST[$input] = $validator->inputValidation($input);
            ($ok) ? $input : false;
        }
        if (!$ok){
            if ($ajax) {
                $response["errors"] = $validator->getFormMessages();
                echo json_encode($response);
                exit;
            }
            //Nastavíme zprávu.
            $this->addMessage("Změna se nezdařila!", TypeOfMessage::SUCCESS);
            //Přesměrujem.
            $this->redirect("profile/" . $personId);
            //předáme šabloně zprávy o chybných formulářových polích;
            $this->data["form_messages"] = $validator->getFormMessages();
        }
        //print_r($_POST);
        //exit;
        print_r(array_keys($_POST));
        $parameters = array_values($_POST);
        array_push($parameters,$personId);
        print_r($parameters);
        
        try {
            $query=Mysql::$connection->prepare(
            
            $query->execute($parameters);
            
           

        }catch (Exception $error) {
            if ($ajax) {
                echo '{errors: []}'.$error->getMessage();
                exit;
            }
            $this->addMessage("Změna se nezdařila!", TypeOfMessage::ERROR);
            $this->redirect("profile/" . $personId);
        }
        if($ajax){
            echo"aaa";
            echo json_encode($_POST);
            exit;
        }
        $this->addMessage("Změna proběhla úspěšně.", TypeOfMessage::SUCCESS);
        $this->redirect("profile/" . $personId);
        
    }*/
        
        if (isset($_POST["phone"])) {
            $this->phoneUpdate();
        }
        if (isset($_POST["mail"])) {
            $this->mailUpdate();
        }
        if (isset($_POST["first_name"])) {
            $this->firstNameUpdate();
        }
        if (isset($_POST["last_name"])) {
            $this->lastNameUpdate();
        }
        if (isset($_POST["user_name"])) {
            $this->userNameUpdate();
        }
        
        if (isset($_POST["street_and_number"])) {
            $this->addressUpdate();
        }
        if (isset($_POST["identity_card_number"])) {
            $this->identityCardNumberUpdate();
        }
        if (isset($_POST["national_id_number"])) {
            $this->nationalIdNumberUpdate();
        }
        
    }
    
    public function phoneUpdate()
    {
        $this->userVerify($this->person["persons_id"]);
        $phone = array(
            "phone" => $this->validator->phoneValidation(),
            "area_code" => $this->validator->areaCodeValidation()
        );
        if ($phone["phone"] && $phone["area_code"]) {
            try {
                $this->contactManager->updateContact($phone, $this->person["contact"]);
            } catch (UserException $error) {
                if (isset($_POST["ajax"])) {
                    echo '{errors:[]}';
                    exit;
                }
            }

            $phone = $this->contactManager->getContact($this->person["contact"], "phone", "area_code");

            $phone["phone"] = Formater::formatPhoneNumber($phone["phone"]);
            $areaCode = $this->areaCodeManager->getAreaCode($phone["area_code"], "area_code");
            $phone["area_code"] = $areaCode["area_code"];


            if (isset($_POST["ajax"])) {
                echo json_encode($phone);
                exit;
            }
            $this->addMessage("Telefonní číslo bylo změněno.", TypeOfMessage::SUCCESS);
            $this->redirect("profile/" . $this->person["personsId"]);
        } else {
            //Pokud je tento controler načítán ajaxem.
            if (isset($_POST["ajax"])) {
                $response["errors"] = $this->validator->getFormMessages();
                echo json_encode($response);
                exit;
            }
            //Nastavíme zprávu.
            $this->addMessage("Telefonní číslo se nepodařilo změnit!", TypeOfMessage::SUCCESS);
            //Přesměrujem.
            $this->redirect("profile/" . $this->person["personsId"]);
            //předáme šabloně zprávy o chybných formulářových polích;
            $this->data["form_messages"] = $this->validator->getFormMessages();
        }
    }
    public function mailUpdate()
    {
        $this->userVerify($this->person["persons_id"]);
        $mail = $this->validator->mailValidation();

        if ($mail) {
            try {
                $this->contactManager->updateContact(array("mail" => $mail), $this->person["contact"]);
            } catch (UserException $error) {
                if (isset($_POST["ajax"])) {
                    echo '{errors:[]}';
                    exit;
                }
            }
            $mail = $this->contactManager->getContact($this->person["contact"], "mail");
            if (isset($_POST["ajax"])) {
                echo json_encode($mail);
                exit;
            }
            $this->addMessage("E-mail byl změněn.", TypeOfMessage::SUCCESS);
            $this->redirect("profile/" . $this->person["personsId"]);
        } else {
            //Pokud je tento controler načítán ajaxem.
            if (isset($_POST["ajax"])) {
                $response["errors"] = $this->validator->getFormMessages();
                echo json_encode($response);
                exit;
            }
            //Nastavíme zprávu.
            $this->addMessage("E-mail se nepodařilo změnit!", TypeOfMessage::SUCCESS);
            //Přesměrujem.
            $this->redirect("profile/" . $this->person["personsId"]);
            //předáme šabloně zprávy o chybných formulářových polích;
            $this->data["form_messages"] = $this->validator->getFormMessages();
        }
    }
    public function firstNameUpdate()
    {
        $this->userVerify(true);
        $firstName = $this->validator->firstNameValidation();
        if ($firstName) {
            try {
                $this->personManager->updatePerson(array("first_name" => $firstName), $this->person["persons_id"]);
            } catch (UserException $error) {
                if (isset($_POST["ajax"])) {
                    echo '{errors:[]}';
                    exit;
                }
            }
            $firstName = $this->personManager->getPerson($this->person["persons_id"], "first_name");
            if (isset($_POST["ajax"])) {
                echo json_encode($firstName);
                exit;
            }
            $this->addMessage("Křestní jméno bylo změněno", TypeOfMessage::SUCCESS);
            $this->redirect("profile/" . $this->person["personsId"]);
        } else {
            //Pokud je tento controler načítán ajaxem.
            if (isset($_POST["ajax"])) {
                $response["errors"] = $this->validator->getFormMessages();
                echo json_encode($response);
                exit;
            }
            //Nastavíme zprávu.
            $this->addMessage("Křestní jméno se nepodařilo změnit!", TypeOfMessage::SUCCESS);
            //Přesměrujem.
            $this->redirect("profile/" . $this->person["personsId"]);
            //předáme šabloně zprávy o chybných formulářových polích;
            $this->data["form_messages"] = $this->validator->getFormMessages();
        }
    }
    public function lastNameUpdate()
    {
        $this->userVerify(true);
        $lastName = $this->validator->lastNameValidation();

        if ($lastName) {
            try {
                $this->personManager->updatePerson(array("last_name" => $lastName), $this->person["persons_id"]);
            } catch (UserException $error) {
                if (isset($_POST["ajax"])) {
                    echo '{errors:[]}';
                    exit;
                }
            }
            $lastName = $this->personManager->getPerson($this->person["persons_id"], "last_name");
            if (isset($_POST["ajax"])) {
                echo json_encode($lastName);
                exit;
            }
            $this->addMessage("Příjmení bylo změněno", TypeOfMessage::SUCCESS);
            $this->redirect("profile/" . $this->person["personsId"]);
        } else {
            //Pokud je tento controler načítán ajaxem.
            if (isset($_POST["ajax"])) {
                $response["errors"] = $this->validator->getFormMessages();
                echo json_encode($response);
                exit;
            }
            //Nastavíme zprávu.
            $this->addMessage("Příjmení se nepodařilo změnit!", TypeOfMessage::SUCCESS);
            //Přesměrujem.
            $this->redirect("profile/" . $this->person["personsId"]);
            //předáme šabloně zprávy o chybných formulářových polích;
            $this->data["form_messages"] = $this->validator->getFormMessages();
        }
    }
    public function userNameUpdate()
    {
        $this->userVerify($this->person["persons_id"]);
        $userName = $this->validator->userNameValidation();
        if ($userName) {
            try {
                $this->userManager->updateUser(array("user_name" => $userName), $this->person["user"]);
            } catch (UserException $error) {
                if (isset($_POST["ajax"])) {
                    echo '{errors:[]}';
                    exit;
                }
            }
            $userName = $this->userManager->getUser($this->person["user"], "user_name");
            if (isset($_POST["ajax"])) {
                echo json_encode($userName);
                exit;
            }
            $this->addMessage("Uživatelské jméno bylo změněno", TypeOfMessage::SUCCESS);
            $this->redirect("profile/" . $this->person["personsId"]);
        } else {
            //Pokud je tento controler načítán ajaxem.
            if (isset($_POST["ajax"])) {
                $response["errors"] = $this->validator->getFormMessages();
                echo json_encode($response);
                exit;
            }
            //Nastavíme zprávu.
            $this->addMessage("Uživatelské jméno se nepodařilo změnit!", TypeOfMessage::SUCCESS);
            //Přesměrujem.
            $this->redirect("profile/" . $this->person["personsId"]);
            //předáme šabloně zprávy o chybných formulářových polích;
            $this->data["form_messages"] = $this->validator->getFormMessages();
        }
    }
    public function nationalIdNumberUpdate()
    {
        $this->userVerify(true);
        $nationalIdNumber = $this->validator->nationalIdNumberValidation();
        if ($nationalIdNumber) {
            try {
                $this->personManager->updatePerson(array("national_id_number" => $nationalIdNumber), $this->person["persons_id"]);
            } catch (UserException $error) {

                if (isset($_POST["ajax"])) {
                    echo '{errors:[]}';
                    exit;
                }
            }
            $nationalIdNumber = $this->personManager->getPerson($this->person["persons_id"], "national_id_number");
            $nationalIdNumber["national_id_number"] = Formater::formatNationalIdNumber($nationalIdNumber["national_id_number"]);
            if (isset($_POST["ajax"])) {
                echo json_encode($nationalIdNumber);
                exit;
            }
            $this->addMessage("Rodné číslo bylo změněno.", TypeOfMessage::SUCCESS);
            $this->redirect("profile/" . $this->person["personsId"]);
        } else {
            //Pokud je tento controler načítán ajaxem.
            if (isset($_POST["ajax"])) {
                $response["errors"] = $this->validator->getFormMessages();
                echo json_encode($response);
                exit;
            }
            //Nastavíme zprávu.
            $this->addMessage("Rodné číslo se nepodařilo změnit!", TypeOfMessage::SUCCESS);
            //Přesměrujem.
            $this->redirect("profile/" . $this->person["personsId"]);
            //předáme šabloně zprávy o chybných formulářových polích;
            $this->data["form_messages"] = $this->validator->getFormMessages();
        }
    }
    public function identityCardNumberUpdate()
    {
        $this->userVerify(true);
        $identityCardNumber = $this->validator->identityCardNumberValidation();
        if ($identityCardNumber) {
            try {
                $this->personManager->updatePerson(array("identity_card_number" => $identityCardNumber), $this->person["persons_id"]);
            } catch (UserException $error) {

                if (isset($_POST["ajax"])) {
                    echo '{errors:[]}';
                    exit;
                }
            }
            $identityCardNumber = $this->personManager->getPerson($this->person["persons_id"], "identity_card_number");
            if (isset($_POST["ajax"])) {
                echo json_encode($identityCardNumber);
                exit;
            }
            $this->addMessage("Číslo občanského průkazu bylo změněno.", TypeOfMessage::SUCCESS);
            $this->redirect("profile/" . $this->person["personsId"]);
        } else {
            //Pokud je tento controler načítán ajaxem.
            if (isset($_POST["ajax"])) {
                $response["errors"] = $this->validator->getFormMessages();
                echo json_encode($response);
                exit;
            }
            //Nastavíme zprávu.
            $this->addMessage("Číslo občanského průkazu se nepodařilo změnit!", TypeOfMessage::SUCCESS);
            //Přesměrujem.
            $this->redirect("profile/" . $this->person["personsId"]);
            //předáme šabloně zprávy o chybných formulářových polích;
            $this->data["form_messages"] = $this->validator->getFormMessages();
        }
    }
    //Pokud editujeme adresu. (stačí zkontrolovat první pole, protože když tam je, jsou tam i ZIP a city)
    public function addressUpdate()
    {
        
        $this->userVerify($this->person["persons_id"]);
        $address = $this->addressManager->addressValidation($this->validator);
        if ($address) {
            try {
                $addressId = $this->personManager->updateAddress($address, $this->person["persons_id"]);
            } catch (UserException $error) {
                if (isset($_POST["ajax"])) {
                    echo '{errors:[]}';
                    exit;
                }
            }

            $address = $this->addressManager->getAddress($addressId, "street_and_number", "city", "ZIP", "state");
            $address["ZIP"] = Formater::formatZIP($address["ZIP"]);
            $address["state"] = $this->stateManager->getState($address["state"])["state_name"];
            //Pokud je tento controler načítán ajaxem.
            if (isset($_POST["ajax"])) {
                //Změněná adresa bude v json dostupná pod klíčem address.
                echo json_encode($address);
                exit;
            }
            $this->addMessage("Adresa byla změněna.", TypeOfMessage::SUCCESS);
            $this->redirect("profile/" . $this->person["personsId"]);
        } else {
            //Pokud je tento controler načítán ajaxem.
            if (isset($_POST["ajax"])) {
                $response["errors"] = $this->validator->getFormMessages();
                echo json_encode($response);
                exit;
            }
            //Nastavíme zprávu.
            $this->addMessage("Adresu se nepodařilo změnit!", TypeOfMessage::SUCCESS);
            //Přesměrujem.
            $this->redirect("profile/" . $this->person["personsId"]);
            //předáme šabloně zprávy o chybných formulářových polích;
            $this->data["form_messages"] = $this->validator->getFormMessages();
        }
    }


    /** vrátí všechna data */
    public function getAllclientData($personId): array
    {

        try {

            $person = $this->personManager->getPerson($personId, "*");
            $contact = $this->contactManager->getContact($person["contact"], "*");
            $areaCode = $this->areaCodeManager->getAreaCode($contact["area_code"], "*");
            $address = $this->addressManager->getAddress($person["address"], "*");
            $state = $this->stateManager->getState($address["state"]);
            $user = $this->userManager->getUser($person["user"], "*");
            if(!$user){
                $user=array("user_name"=>"Klient nemá účet.");
            }
            $address["ZIP"] = Formater::formatZIP($address["ZIP"]);
            $contact["phone"] = Formater::formatPhoneNumber($contact["phone"]);
            $contact["area_code"] = $contact["phone"];
            $person["national_id_number"] = Formater::formatNationalIdNumber($person["national_id_number"]);
            return array_merge($person, $contact, $areaCode, $address, $state, $user);
        } catch (Exception $error) {
            $this->addMessage($error->getCode() . "Nepodařilo se načíst data!", TypeOfMessage::ERROR);
            $this->redirect("chyba");
        }
    }
}
