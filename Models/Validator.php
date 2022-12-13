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
        
        public function inputValidation($input){
            $function = $input."Validation";
            return $this->$function();
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

        public function streetAndNumberValidation() : ?string{
            $street=trim($_POST["street_and_number"]);
            //Název ulice nesmí být delší než 255 znaků.
            if (mb_strlen($street)>255){
                $this->addFormMessage('street', 'Název ulice nesmí být delší než 255 znaků!', TypeOfFormMessage::INVALID);
                return null;
            //Název ulice musí být vyplněný.
            }else if($street==""){
                $this->addFormMessage('street_and_number', 'Ulice je povinný údaj!', TypeOfFormMessage::INVALID);
                return null;
            }else{
                $this->addFormMessage('street_and_number', 'Ok', TypeOfFormMessage::VALID);
                return $street;
            }
        }

        
        

        public function ZIPValidation() : ?string{
            $ZIP = trim($_POST["ZIP"]);
            //Odstranění mezery v PSČ.
            $ZIP = str_replace(" ", "", $ZIP);
            //PSČ musí být 5 žísel.
            if (!preg_match('/^[0-9]{5}$/', $ZIP)){
                $this->addFormMessage('ZIP', 'Špatný formát PSČ!', TypeOfFormMessage::INVALID);
                return null;  
            }else{
                
                $this->addFormMessage('ZIP', 'Ok', TypeOfFormMessage::VALID);
                return $ZIP;
            }
        }
        
        public function cityValidation() : ?string{
            $city = trim($_POST["city"]);
            //Název města nesmí být delší než 255 znaků
            if (mb_strlen($city) > 255){
                $this->addFormMessage('city', 'Název města nesmí být delší než 255 znaků!', TypeOfFormMessage::INVALID);
                return null;
            //Název města musí být vyplněný
            }else if ($city == ""){
                $this->addFormMessage('city', 'Název města nesmí být delší než 255 znaků!', TypeOfFormMessage::INVALID);
                return null;
            }else{
                $this->addFormMessage('city', 'Ok', TypeOfFormMessage::VALID);
                return $city;
            }
        }

        public function firstNameValidation() : ?String
        {
            $first_name = trim($_POST["first_name"]);
            //Křestní jméno nesmí být delší než 40 znaků.
            if(mb_strlen($first_name)>40){
                $this->addFormMessage('first_name', 'Jméno nesmí být delší než 40 znaků', TypeOfFormMessage::INVALID);
                return null;
            //Křestní jméno nesmí být prázdné.
            }else if ($first_name==""){
                $this->addFormMessage('first_name', 'Jméno je pvinný údaj!', TypeOfFormMessage::INVALID);
                return null;
            }else{
                $this->addFormMessage('first_name', 'Ok', TypeOfFormMessage::VALID);
                return $first_name;
            }
        }

        public function lastNameValidation() : ?string{
            //Příjmení nesmí být delší než 40 znaků.
            $last_name = trim($_POST["last_name"]);
            if(mb_strlen($last_name)>40){
                $this->addFormMessage('last_name', 'Příjmení nesmí být delší než 40 znaků', TypeOfFormMessage::INVALID);
                return null;
            //Příjmení nesmí být prázdné.
            }else if ($last_name==""){
                $this->addFormMessage('last_name', 'Příjmení je pvinný údaj!', TypeOfFormMessage::INVALID);
                return null;
            }else{
                $this->addFormMessage('last_name', 'Ok', TypeOfFormMessage::VALID);
                return $last_name;
            }
        }

        public function mailValidation() : ?string
        {
            $mail = trim($_POST["mail"]);
            //Pokud je zadaná platná adresa.
            if($mail == ""){
                $this->addFormMessage('mail', 'E-mailová adresa je povinný údaj!', TypeOfFormMessage::INVALID);
                return null;

            }else if(!filter_var($mail, FILTER_VALIDATE_EMAIL)){
                $this->addFormMessage('mail', 'Špatný formát mailové adresy!', TypeOfFormMessage::INVALID);
                return null;
            }else{
                $this->addFormMessage('mail', 'Ok', TypeOfFormMessage::VALID);
                return $mail;
            }
        }
        public function stateValidation(): ?int{
            try{
                $id=Mysql::oneValue("SELECT state_id from `state` WHERE state_id = ?", array($_POST["state"]));
                $this->addFormMessage('state', 'Ok', TypeOfFormMessage::VALID);
                
                return $id;
                if(is_numeric($id)){
                    $this->addFormMessage('state', 'Tento stát není podporován!', TypeOfFormMessage::INVALID);
                    return null;
                }
            }catch (UserException $error){
                $this->addFormMessage('state', 'Došo k chybě databáze!', TypeOfFormMessage::INVALID);
                return null;
            }

        }
        public function contactValidation() : ?int{
            try{
                $id=Mysql::oneValue("SELECT contact_id from contact WHERE contact_id = ?", array($_POST["contact"]));
                $this->addFormMessage('contact', 'Ok', TypeOfFormMessage::VALID);
                return $id;
                if(is_numeric($id)){
                    $this->addFormMessage('contact', 'Nepodařilo se načíst kontakty!', TypeOfFormMessage::INVALID);
                    return null;

                }
            }catch (UserException $error){
                $this->addFormMessage('state', 'Došo k chybě databáze!', TypeOfFormMessage::INVALID);
                return null;
            }

        }
        public function areaCodeValidation() : ?int{
            try{
                $id=Mysql::oneValue("SELECT area_code_id from area_code WHERE area_code_id = ?", array($_POST["area_code"]));
                $this->addFormMessage('area_code', 'Ok', TypeOfFormMessage::VALID);
                return $id;
                if(is_numeric($id)){
                    $this->addFormMessage('area_code', 'Tato předvolba není podporována!', TypeOfFormMessage::INVALID);
                    return null;
                }
            }catch (UserException $error){
                $this->addFormMessage('area_code', 'Došo k chybě databáze!', TypeOfFormMessage::INVALID);
                return null;
            }
        }
        public function phoneValidation() : ?string
        {
            $phone = trim($_POST["phone"]);
            //K telefonnímu číslu přidáme předvolbu a odstraníme mezery.
            $phone = str_replace(" ", "", $phone);
            //validace telefonního číala
            if(preg_match('/^[0-9]{8,9}$/', $phone)){
                $this->addFormMessage('phone', 'Ok', TypeOfFormMessage::VALID);
                return $phone;
            }else{
                $this->addFormMessage('phone', 'Špatný formát telefonního čísla!', TypeOfFormMessage::INVALID);
                return null;
            }
        }

        public function dateOfBirthValidation() : ?string
        {
            $dateOfBirth = trim($_POST["date_of_birth"]);
            if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $dateOfBirth)){
                $this->addFormMessage('date_of_birth', 'Ok', TypeOfFormMessage::VALID);
                return $dateOfBirth;
            }else{
                $this->addFormMessage('date_of_birth', 'Špatný formát data!', TypeOfFormMessage::INVALID);
                return null;
            }
        }

        public function identityCardNumberValidation() : ?string
        {
            $identityCardNumber = trim($_POST["identity_card_number"]);
            
            if($identityCardNumber == ""){
                $this->addFormMessage('identity_card_number', 'Číslo občanského průkazu je povinnný údaj!', TypeOfFormMessage::INVALID);
                return null;
            }else if(!preg_match('/^([1-9][0-9]{8}|[0-9]{6}[A-Z]{2}[0-9]{0,2}|[A-Z]{0,2}[0-9]{0,2}[0-9]{6})$/i', $identityCardNumber)){
                $this->addFormMessage('identity_card_number', 'Špatný formát čísla občanského průkazu!', TypeOfFormMessage::INVALID);
                return null;
            }else{
                $this->addFormMessage('identity_card_number', 'Ok', TypeOfFormMessage::VALID);
                return $identityCardNumber;
            }
        }

        public function nationalIdNumberValidation() : ?string
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
                return null;
            }
        }
        
        public function userNameValidation() : ?string{
            $user = trim($_POST["user_name"]);
            //validace pole user       
            if (mb_strlen($user)>40){  
                $this->addFormMessage( "user_name", 'Uživatelské jméno nesmí být delší než 40 znaků!', TypeOfFormMessage::INVALID);
                return null;
            }else if ($user==""){
                $this->addFormMessage( "user_name", 'Uživatelské jméno je povinný údaj!', TypeOfFormMessage::INVALID);                
                return null;
            }else{
                $this->addFormMessage( "user_name", 'Ok', TypeOfFormMessage::VALID);
                return $user;
            }
        }
        public function passwordValidation() : ?string
        {
            $password = trim($_POST["password"]);
            //Pokud heslo je dlouhé alespoň 8 znaků a obsahuje číslici.
            if(preg_match("/^\G(?=(.*[0-9])).{8,}$/x", $password)){
                $this->addFormMessage('password','Ok', TypeOfFormMessage::VALID);
                
                return $password;
            }else{
                $this->addFormMessage('password','Heslo musí být dlouhé alespoň 8 znaků a musí obsahovat číslici.', TypeOfFormMessage::INVALID);
                $this->addFormMessage('password_again','', TypeOfFormMessage::INVALID);
                return null;
            }
        }
        public function passwordAgainValidation($password) : ?string
        {
            $password_again = trim($_POST["password_again"]);
            //Je kontrolní pole shodné s polem heslo?
            if ($password_again === $password){
                $this->addFormMessage('password_again','Ok', TypeOfFormMessage::VALID);
                return $password_again;
            }else{
                $this->addFormMessage('password_again','Hesla nesouhlasí!', TypeOfFormMessage::INVALID);
                return null;
            }
        }         
        
        public function typeValidation() : ?string
        {
            $type = trim($_POST["type"]);
            $options = array(
                "úrazové pojištění",
                "pojištění nemovitosti",
                "pojištění osobních věcí a karet"
            );
            if(in_array($type, $options)){
                $this->addFormMessage('type','Ok', TypeOfFormMessage::VALID);
                return $type;
            }else{
                $this->addFormMessage('type','Tento druh pojištění není v naší nabídce!', TypeOfFormMessage::INVALID);
                return null;
            }
        }
        public function variantValidation() : ?string
        {
            $variant = trim($_POST["variant"]);
            $options = array(
                "STANDARD",
                "PREMIUM",
                "GOLD"
            );
            if(in_array($variant, $options)){
                $this->addFormMessage('variant','Ok', TypeOfFormMessage::VALID);
                return $variant;
            }else{
                $this->addFormMessage('variant','Tato varianta není v naší nabídce!', TypeOfFormMessage::INVALID);
                return null;
            }
        }

        public function policyHolderValidation() : ?int
        {
            $policyHolder = trim($_POST["policy_holder"]);
            
            $personManager=new PersonManager();
            $personId=$personManager->getPerson($policyHolder, "person_id");
            if(is_numeric($policyHolder)){
                $this->addFormMessage('policy_holder','Ok', TypeOfFormMessage::VALID);
                return $policyHolder;
            }else{
                $this->addFormMessage('policy_holder','Pojistník nenalezen!', TypeOfFormMessage::INVALID);
                return null;
            }
        }
        public function insuredValidation() : ?int
        {
            $insured = trim($_POST["insured"]);
            
            $personManager=new PersonManager();
            $personId=$personManager->getPerson($insured, "person_id");
            if(is_numeric($personId)){
                $this->addFormMessage('insured','Ok', TypeOfFormMessage::VALID);
                return $insured;
            }else{
                $this->addFormMessage('insured','Pojištěný nenalezen nenalezen!', TypeOfFormMessage::INVALID);
                return null;
            }
        }
        public function policyValueValidation() : ?int{
            $policyValue = trim($_POST["policy_value"]);
            if($policyValue > 0 && $policyValue < 2147483647){
                $this->addFormMessage('policy_value','Ok', TypeOfFormMessage::VALID);
                return $policyValue;
                
            }else{
                $this->addFormMessage('policy_value','Pojistná částka je mimo povolený rozsah!', TypeOfFormMessage::INVALID);
                return null;
            }
        }
        public function insuranceRateValidation(): ?int{
            $insuranceRate = trim($_POST["insurance_rate"]);
            if($insuranceRate > 0 && $insuranceRate < 2147483647){
                $this->addFormMessage('insurance_rate','Ok', TypeOfFormMessage::VALID);
                return $insuranceRate;
                
            }else{
                $this->addFormMessage('policy_value','Pojistné je mimo povolený rozsah!', TypeOfFormMessage::INVALID);
                return null;
            }
        }
    }