<?php
    class Validator{
        //Sem se ukládají zprávy o chybách či úspěších jednotlivých inputů.
        private array $formMessages;
        
        /** Přidá zprávu. 
         * @return void;
        */
        public function addFormMessage(string $nameOfInput, string $text, TypeOfFormMessage $type) : void
        {
            $this->formMessages[$nameOfInput] = array(
                "text" => $text,
                "type" => $type->value
            );
        } 

        public function getFormMessages() : array
        {
            return $this->formMessages;
        }

        public function captchaValidation() : bool
        {   
            $captcha = trim($_POST["captcha"]);
            if ($captcha != date('Y')){
                $this->addFormMessage('captcha','Špatně vyplněný antispam!', TypeOfFormMessage::INVALID);
                return false;
            }
            $this->addFormMessage('captcha','Ok', TypeOfFormMessage::VALID);
            return true;
        }
        
        public function agreementValidation() : bool
        {
            if(isset($_POST["agreement"])){
                $this->addFormMessage('agreement','Ok', TypeOfFormMessage::VALID);
                $_POST["agreement"]=true;
                return true;
            }
            $this->addFormMessage('agreement','Bez souhlasu se registrovat nelze.', TypeOfFormMessage::INVALID);
            $_POST["agreement"]=false;
            return false;
        }

        public function streetValidation() : bool | string{
            $street=trim($_POST["street"]);
            //Název ulice nesmí být delší než 255 znaků.
            if (mb_strlen($street)>255){
                $this->addFormMessage('street', 'Název ulice nesmí být delší než 255 znaků!', TypeOfFormMessage::INVALID);
                return false;
            //Název ulice musí být vyplněný.
            }else if($street==""){
                $this->addFormMessage('street', 'Ulice je povinný údaj!', TypeOfFormMessage::INVALID);
                return false;
            }else{
                $this->addFormMessage('street', 'Ok', TypeOfFormMessage::VALID);
                return $street;
            }
        }

        public function buildingIdentificationNumberValidation() : bool | string{
            $buildingIdentificationNumber = trim($_POST["building_identification_number"]);
            //Číslo popisné musí být číslo mezi 1 a 9999.
            if (!is_numeric($buildingIdentificationNumber) || $buildingIdentificationNumber>=10000 || $buildingIdentificationNumber<=0){
                $this->addFormMessage("building_identification_number",'Špatný formát čísla popisného!', TypeOfFormMessage::INVALID);
                return false;
            }else{
                $this->addFormMessage("building_identification_number",'Ok', TypeOfFormMessage::VALID);
                return $buildingIdentificationNumber;
            }
        }
        public function houseNumberValidation() : bool | string{
            $houseNumber=trim($_POST["house_number"]);
            //Číslo orientační musí být číslo mezi 1 a 9999.
            if (preg_match('/^[0-9]{1,4}[A-Z]?$/i', $houseNumber)){
                $this->addFormMessage('house_number', 'Ok', TypeOfFormMessage::VALID);
                return $houseNumber;
            }else{
                $this->addFormMessage('house_number', 'Špatný formát čísla orientačního!', TypeOfFormMessage::INVALID);
                return false;
            }
        }

        public function ZIPValidation() : bool | string{
            $ZIP = trim($_POST["ZIP"]);
            //Odstranění mezery v PSČ.
            $ZIP = str_replace(" ", "", $ZIP);
            //PSČ musí být číslo mezi 1 a 99999.
            if (!is_numeric($ZIP) || $ZIP>=100000 || $ZIP<=10000){
                $this->addFormMessage('ZIP', 'Špatný formát PSČ!', TypeOfFormMessage::INVALID);
                return false;  
            }else{
                $this->addFormMessage('ZIP', 'Ok', TypeOfFormMessage::VALID);
                return $ZIP;
            }
        }
        
        public function cityValidation() : bool | string{
            $city = trim($_POST["city"]);
            //Název města nesmí být delší než 255 znaků
            if (mb_strlen($city) > 255){
                $this->addFormMessage('city', 'Název města nesmí být delší než 255 znaků!', TypeOfFormMessage::INVALID);
                return false;
            //Název města musí být vyplněný
            }else if ($city == ""){
                $this->addFormMessage('city', 'Název města nesmí být delší než 255 znaků!', TypeOfFormMessage::INVALID);
                return false;
            }else{
                $this->addFormMessage('city', 'Ok', TypeOfFormMessage::VALID);
                return $city;
            }
        }

        public function firstNameValidation() : bool | String
        {
            $first_name = trim($_POST["first_name"]);
            //Křestní jméno nesmí být delší než 40 znaků.
            if(mb_strlen($first_name)>40){
                $this->addFormMessage('first_name', 'Jméno nesmí být delší než 40 znaků', TypeOfFormMessage::INVALID);
                return false;
            //Křestní jméno nesmí být prázdné.
            }else if ($first_name==""){
                $this->addFormMessage('first_name', 'Jméno je pvinný údaj!', TypeOfFormMessage::INVALID);
                return false;
            }else{
                $this->addFormMessage('first_name', 'Ok', TypeOfFormMessage::VALID);
                return $first_name;
            }
        }

        public function lastNameValidation() : bool | string{
            //Příjmení nesmí být delší než 40 znaků.
            $last_name = trim($_POST["last_name"]);
            if(mb_strlen($last_name)>40){
                $this->addFormMessage('last_name', 'Příjmení nesmí být delší než 40 znaků', TypeOfFormMessage::INVALID);
                return false;
            //Příjmení nesmí být prázdné.
            }else if ($last_name==""){
                $this->addFormMessage('last_name', 'Příjmení je pvinný údaj!', TypeOfFormMessage::INVALID);
                return false;
            }else{
                $this->addFormMessage('last_name', 'Ok', TypeOfFormMessage::VALID);
                return $last_name;
            }
        }

        public function mailValidation() : bool | string
        {
            $mail = trim($_POST["mail"]);
            //Pokud je zadaná platná adresa.
            if($mail == ""){
                $this->addFormMessage('mail', 'E-mailová adresa je povinný údaj!', TypeOfFormMessage::INVALID);
                return false;

            }else if(!filter_var($mail, FILTER_VALIDATE_EMAIL)){
                $this->addFormMessage('mail', 'Špatný formát mailové adresy!', TypeOfFormMessage::INVALID);
                return false;
            }else{
                $this->addFormMessage('mail', 'Ok', TypeOfFormMessage::VALID);
                return $mail;
            }
        }

        public function phoneValidation() : bool | string
        {
            $phone = trim($_POST["phone"]);
            //K telefonnímu číslu přidáme předvolbu a odstraníme mezery.
            $phone = "+420".str_replace(" ", "", $phone);
            //validace telefonního číala
            if(preg_match('/^(\+|#|00)[0-9]{11,12}$/i', $phone)){
                $this->addFormMessage('phone', 'Ok', TypeOfFormMessage::VALID);
                return $phone;
            }else{
                $this->addFormMessage('phone', 'Špatný formát telefonního čísla!', TypeOfFormMessage::INVALID);
                return false;
            }
        }

        public function dateOfBirthValidation() : bool | string
        {
            $dateOfBirth = trim($_POST["date_of_birth"]);
            if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/i', $dateOfBirth)){
                $this->addFormMessage('date_of_birth', 'Ok', TypeOfFormMessage::VALID);
                return $dateOfBirth;
            }else{
                $this->addFormMessage('date_of_birth', 'Špatný formát data!', TypeOfFormMessage::INVALID);
                return false;
            }
        }

        public function identityCardNumberValidation() : bool | string
        {
            $identityCardNumber = trim($_POST["identity_card_number"]);
            
            if($identityCardNumber == ""){
                $this->addFormMessage('identity_card_number', 'Číslo občanského průkazu je povinnný údaj!', TypeOfFormMessage::INVALID);
                return false;
            }else if(!preg_match('/^([1-9][0-9]{8}|[0-9]{6}[A-Z]{2}[0-9]{0,2}|[A-Z]{0,2}[0-9]{0,2}[0-9]{6})$/i', $identityCardNumber)){
                $this->addFormMessage('identity_card_number', 'Špatný formát čísla občanského průkazu!', TypeOfFormMessage::INVALID);
                return false;
            }else{
                $this->addFormMessage('identity_card_number', 'Ok', TypeOfFormMessage::VALID);
                return $identityCardNumber;
            }
        }

        public function nationalIdNumberValidation() : bool | string
        {
            $nationalIdNumber = trim($_POST["national_id_number"]);
            //Odstranění mezer
            $nationalIdNumber=str_replace(" ", "", $nationalIdNumber);
            //Odstranění lomítka
            $nationalIdNumber=str_replace("/", "", $nationalIdNumber);
            //Pokud rodné číslo splňuje tvar rodného čísla
            if(preg_match('/^[0-9]{2}[0156][0-9][0-3][0-9][0-9]{4}$/i', $nationalIdNumber)){
                $this->addFormMessage('national_id_number', 'Ok', TypeOfFormMessage::VALID);
                return $nationalIdNumber;
            }else{
                $this->addFormMessage('national_id_number', 'Špatný formát rodného čísla!', TypeOfFormMessage::INVALID);
                return false;
            }
        }
        
        public function userValidation() : bool | string{
            $user = trim($_POST["user"]);
            //validace pole user       
            if (mb_strlen($user)>40){  
                $this->addFormMessage( "user", 'Uživatelské jméno nesmí být delší než 40 znaků!', TypeOfFormMessage::INVALID);
                return false;
            }else if ($user==""){
                $this->addFormMessage( "user", 'Uživatelské jméno je povinný údaj!', TypeOfFormMessage::INVALID);                
                return false;
            }else{
                $this->addFormMessage( "user", 'Ok', TypeOfFormMessage::VALID);
                return $user;
            }
        }
        public function passwordValidation() : bool | string
        {
            $password = trim($_POST["password"]);
            //Pokud heslo je dlouhé alespoň 8 znaků a obsahuje číslici.
            if(preg_match("/^\G(?=(.*[0-9])).{8,}$/x", $password)){
                $this->addFormMessage('password','Ok', TypeOfFormMessage::VALID);
                return $password;
            }else{
                $this->addFormMessage('password','Heslo musí být dlouhé alespoň 8 znaků a musí obsahovat číslici.', TypeOfFormMessage::INVALID);
                $this->addFormMessage('password_again','', TypeOfFormMessage::INVALID);
                return false;
            }
        }
        public function passwordAgainValidation($password) : bool | string
        {
            $password_again = trim($_POST["password_again"]);
            //Je kontrolní pole shodné s polem heslo?
            if ($password_again === $password){
                $this->addFormMessage('password_again','Ok', TypeOfFormMessage::VALID);
                return $password_again;
            }else{
                $this->addFormMessage('password_again','Hesla nesouhlasí!', TypeOfFormMessage::INVALID);
                return false;
            }
        }         
    }