<?php
    // Správce uživatelů.
    class UserManager
    {
        private array $validations=array();            

        /**
         * Vrátí otisk hesla
         * @return string otisk hesla
        */
        public function getHash(string $heslo) : string
        {
            return password_hash($heslo, PASSWORD_DEFAULT);
        }
        
        /**
         * Registruje nového uživatele do systému
         * @param string $name uživatelské jméno 
         * @param string $password heslo
         * @param string $passwordAgain heslo znovu pro kontrolu 
         * @param string $year antispam
         * @throws UserException Pokud je některý s parametrů chybný.
         */
        public function register(array $user, array $address, array $person) : void
        {
                        
            try
            {
                //Zahájení transakce aby byly ovlivněny všechny tabulky nebo žádná.
                Mysql::startTransaction();
                //Vložíme uživatele.
                Mysql::insert('users', $user);
                //Uložíme si jeho id kvůli vložení do tabulky persons.
                $user_id = Mysql::lastId();
            }
            //Pokud je vyhozena vyjímka...
            catch (PDOException $error)
            {
                //Pokud uživatel již existuje...
                if($error->getCode() == 23000)
                    throw new UserException('Uživatel s tímto jménem je již zaregistrovaný.',112);
                //Pokud dojde k jiné chybě...    
                else    
                    throw new UserException('Registrace se nezdařila.', 113);
            }
            
            $address=array_values($address);
            
            $rows=Mysql::edit("CALL insert_address(?, ?, ?, ?, ?, @id);", $address);
            if($rows != 1)
                throw new UserException('Nepodařilo se vložit adresu.', 114);
            $address_id=Mysql::oneValue("SELECT @id;");
            echo $address_id." ".$rows;
            $person["address"]=$address_id;
            $person["user"]=$user_id;
            
            Mysql::insert('persons', $person);
            Mysql::commit();
            
        }
        public function clearForm(){
            $this->addFormMessage("user", "", TypeOfFormMessage::EMPTY);
            $this->addFormMessage("password", "", TypeOfFormMessage::EMPTY);
            $this->addFormMessage('password_again','', TypeOfFormMessage::EMPTY);               
            $this->addFormMessage("street", "", TypeOfFormMessage::EMPTY);
            $this->addFormMessage("building_identification_number", "", TypeOfFormMessage::EMPTY);
            $this->addFormMessage("house_number", "", TypeOfFormMessage::EMPTY); 
            $this->addFormMessage("ZIP", "", TypeOfFormMessage::EMPTY);
            $this->addFormMessage("city", "", TypeOfFormMessage::EMPTY);
            $this->addFormMessage("first_name", "", TypeOfFormMessage::EMPTY); 
            $this->addFormMessage("last_name", "", TypeOfFormMessage::EMPTY); 
            $this->addFormMessage("date_of_birth", "", TypeOfFormMessage::EMPTY); 
            $this->addFormMessage("national_id_number", "", TypeOfFormMessage::EMPTY); 
            $this->addFormMessage("identity_card_number", "", TypeOfFormMessage::EMPTY); 
            $this->addFormMessage("phone", "", TypeOfFormMessage::EMPTY);
            $this->addFormMessage("mail", "", TypeOfFormMessage::EMPTY);            
            $this->addFormMessage("captcha", "", TypeOfFormMessage::EMPTY);
            $this->addFormMessage("agreement", "", TypeOfFormMessage::EMPTY);
            $_POST["user"] = "";
            $_POST["password"] = "";
            $_POST['password_again'] = "";
            $_POST["street"] = "";
            $_POST["building_identification_number"] = "";
            $_POST["house_number"] = "";
            $_POST["ZIP"] = "";
            $_POST["city"] = "";
            $_POST["first_name"] = "";
            $_POST["last_name"] = "";
            $_POST["date_of_birth"] = "";
            $_POST["national_id_number"] = "";
            $_POST["identity_card_number"] = "";
            $_POST["phone"] = "";
            $_POST["mail"] = "";
            $_POST["captcha"] = "";
            $_POST["agreement"] = false;

        }
        
        public function addFormMessage(string $nameOfInput, string $text, TypeOfFormMessage $type) : void
        {
            $this->validations[$nameOfInput] = array(
                "text" => $text,
                "type" => $type->value
            );
        }   

        public function getFormMessages() : array
        {
            return $this->validations;
        }
        public function captchaValidation() : bool
        {   
           
            if ($_POST["captcha"] != date('Y')){
                $this->addFormMessage('captcha','Špatně vyplněný antispam!', TypeOfFormMessage::INVALID);
                return false;
            }
            $this->addFormMessage('captcha','Ok', TypeOfFormMessage::VALID);
            return true;
        }   

        /** funkce zjistí zda byli všechny údaje do tabulky uživatelé zadané správně,
         *  a případně je upravý tak aby byly.
         * @param array $user pole údajů o uživateli;
         * @return array $user pole upravené pro vložení. 
         */
        public function userValidation() : bool | array
        {   
            //Proměnná ukazuje je li vše ok.
            $good = true;
            $user = array(
                "user" => trim($_POST['user']), 
                "password" => trim($_POST['password']), 
                "password_again" => trim($_POST['password_again'])
            );
            //validace pole user       
            if (mb_strlen($user["user"])>40){  
                $this->addFormMessage( "user", 'Uživatelské jméno nesmí být delší než 40 znaků!', TypeOfFormMessage::INVALID);
                $good = false;
            }else if ($user["user"]==""){
                $this->addFormMessage( "user", 'Uživatelské jméno je povinný údaj!', TypeOfFormMessage::INVALID);                
                $good = false;
            }else{
                $this->addFormMessage( "user", 'Ok', TypeOfFormMessage::VALID);
            }
            //validace hesla
            if(preg_match("/^\G(?=(.*[0-9])).{8,}$/x", $user["password"])){
                $this->addFormMessage('password','Ok', TypeOfFormMessage::VALID);
                if ($user["password"] != $user["password_again"]){
                    $this->addFormMessage('password_again','Hesla nesouhlasí!', TypeOfFormMessage::INVALID);
                    $good = false;
                }else{
                    $this->addFormMessage('password_again','Ok', TypeOfFormMessage::VALID);
                }    
            }else{
                $this->addFormMessage('password','Heslo musí být dlouhé alespoň 8 znaků a musí obsahovat číslici.', TypeOfFormMessage::INVALID);
                $this->addFormMessage('password_again','', TypeOfFormMessage::INVALID);

            }
            
            //Heslo potřebujeme v dotazu jen jednou, proto bude odstraněn index password_again.
            unset($user["password_again"]);
            //Zahashujeme heslo
            $user["password"] = $this->getHash($user["password"]);
            
            //když je vše ok vrátí pole připravené pro vložení do databáze.
            if($good) return $user;  
            //jinak vrátí else
            else return false;
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
        public function addressValidation() : bool|array
        {
            //Proměnná ukazuje je li vše ok.
            $good = true;
            
            $address = array(
                "street" => trim($_POST['street']), 
                "building_identification_number" => (int)($_POST['building_identification_number']), 
                "house_number" => trim($_POST['house_number']), 
                "ZIP" => (int)($_POST['ZIP']), 
                "city" => trim($_POST['city']) 
            );
            //Název ulice nesmí být delší než 255 znaků.
            if (mb_strlen($address["street"])>255){
                $this->addFormMessage('street', 'Název ulice nesmí být delší než 255 znaků!', TypeOfFormMessage::INVALID);
                $good = false;
            //Název ulice musí být vyplněný.
            }else if ($address["street"]==""){
                $this->addFormMessage('street', 'Název ulice nesmí být delší než 255 znaků!', TypeOfFormMessage::INVALID);
                $good = false;
            }else if($address["street"]==""){
                $this->addFormMessage('street', 'Ulice je povinný údaj!', TypeOfFormMessage::INVALID);
                $good = false;
            }else{
                $this->addFormMessage('street', 'Ok', TypeOfFormMessage::VALID);
            }
            //Číslo popisné musí být číslo mezi 1 a 9999.
            if (!is_numeric($address["building_identification_number"]) || $address["building_identification_number"]>=10000 || $address["building_identification_number"]<=0){
                $this->addFormMessage("building_identification_number",'Špatný formát čísla popisného!', TypeOfFormMessage::INVALID);
                $good = false;
            }else{
                $this->addFormMessage("building_identification_number",'Ok', TypeOfFormMessage::VALID);
            }
            //Číslo orientační musí být číslo mezi 1 a 9999.
            if (preg_match('/^[0-9]{1,4}[A-Z]?$/i', $address["house_number"])){
                $this->addFormMessage('house_number', 'Ok', TypeOfFormMessage::VALID);
            }else{
                $this->addFormMessage('house_number', 'Špatný formát čísla orientačního!', TypeOfFormMessage::INVALID);
                $good = false;
            }
            //Odstranění mezery v PSČ.
            $address["ZIP"] = str_replace(" ", "", $address["ZIP"]);
            //PSČ musí být číslo mezi 1 a 99999.
            if (!is_numeric($address["ZIP"]) || $address["ZIP"]>=100000 || $address["ZIP"]<=10000){
                $this->addFormMessage('ZIP', 'Špatný formát PSČ!', TypeOfFormMessage::INVALID);
                $good = false;  
            }else{
                $this->addFormMessage('ZIP', 'Ok', TypeOfFormMessage::VALID);
            }
            //Název města nesmí být delší než 255 znaků
            if (mb_strlen($address["city"])>255){
                $this->addFormMessage('city', 'Název města nesmí být delší než 255 znaků!', TypeOfFormMessage::INVALID);
                $good = false;
            //Název města musí být vyplněný
            }else if ($address["city"]==""){
                $this->addFormMessage('city', 'Název města nesmí být delší než 255 znaků!', TypeOfFormMessage::INVALID);
                $good = false;
            }else
                $this->addFormMessage('city', 'Ok', TypeOfFormMessage::VALID);
            
            //když je vše ok vrátí pole připravené pro vložení do databáze.
            if($good) return $address;  
            //jinak vrátí else
            else return false;            
        }
            
        public function personValidation() : bool|array
        {
            //Proměnná ukazuje je li vše ok.
            $good = true;
            $person = array(
                "first_name" => trim($_POST['first_name']), 
                "last_name" => trim($_POST['last_name']), 
                "date_of_birth" => trim($_POST['date_of_birth']), 
                "identity_card_number" => trim($_POST['identity_card_number']),
                "national_id_number" => trim($_POST['national_id_number']),
                "phone" => trim($_POST['phone']),
                "mail" => trim($_POST['mail'])
            );
            if(mb_strlen($person["first_name"])>40){
                $this->addFormMessage('first_name', 'Jméno nesmí být delší než 40 znaků', TypeOfFormMessage::INVALID);
                $good = false;
            }else if ($person["first_name"]==""){
                $this->addFormMessage('first_name', 'Jméno je pvinný údaj!', TypeOfFormMessage::INVALID);
                $good = false;
            }else{
                $this->addFormMessage('first_name', 'Ok', TypeOfFormMessage::VALID);
            }
            if(mb_strlen($person["last_name"])>40){
                $this->addFormMessage('last_name', 'Příjmení nesmí být delší než 40 znaků', TypeOfFormMessage::INVALID);
                $good = false;
            }else if ($person["last_name"]==""){
                $this->addFormMessage('last_name', 'Příjmení je pvinný údaj!', TypeOfFormMessage::INVALID);
                $good = false;
            }else{
                $this->addFormMessage('last_name', 'Ok', TypeOfFormMessage::VALID);
            }
            //validace emailu
            if(filter_var($person["mail"], FILTER_VALIDATE_EMAIL)){
                $this->addFormMessage('mail', 'Ok', TypeOfFormMessage::VALID);
            }else{
                $this->addFormMessage('mail', 'Špatný formát mailové adresy!', TypeOfFormMessage::INVALID);
                $good = false;
            }
            //K telefonnímu číslu přidáme předvolbu a odstraníme mezery.
            $person["phone"]="+420".str_replace(" ", "", $person["phone"]);
            //validace telefonního číala
            if(preg_match('/^(\+|#|00)[0-9]{11,12}$/i', $person["phone"])){
                $this->addFormMessage('phone', 'Ok', TypeOfFormMessage::VALID);
            }else{
                $this->addFormMessage('phone', 'Špatný formát telefonního čísla!', TypeOfFormMessage::INVALID);
                $good = false;
            }
            if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/i', $person["date_of_birth"])){
                $this->addFormMessage('date_of_birth', 'Ok', TypeOfFormMessage::VALID);
            }else{
                $this->addFormMessage('date_of_birth', 'Špatný formát data!', TypeOfFormMessage::INVALID);
                $good = false;
            }
            if(preg_match('/^([1-9][0-9]{8}|[0-9]{6}[A-Z]{2}[0-9]{0,2}|[A-Z]{0,2}[0-9]{0,2}[0-9]{6})$/i', $person["identity_card_number"],$pole)){
                $this->addFormMessage('identity_card_number', 'Ok', TypeOfFormMessage::VALID);
            }else{
                $this->addFormMessage('identity_card_number', 'Špatný formát čísla občanského průkazu!', TypeOfFormMessage::INVALID);
                $good = false;
            }
            echo print_r($pole);
            $person["national_id_number"]=str_replace(" ", "", $person["national_id_number"]);
            $person["national_id_number"]=str_replace("/", "", $person["national_id_number"]);
            if(preg_match('/^[0-9]{2}[0156][0-9][0-3][0-9][0-9]{4}$/i', $person["national_id_number"])){
                $this->addFormMessage('national_id_number', 'Ok', TypeOfFormMessage::VALID);
            }else{
                $this->addFormMessage('national_id_number', 'Špatný formát rodného čísla!', TypeOfFormMessage::INVALID);
                $good = false;
            }
            //když je vše ok vrátí pole připravené pro vložení do databáze.
            if($good) return $person;  
            //jinak vrátí else
            else return false;
        }
    

        /**
         * Přihlásí uživatele do systému
         * @throws UserException pokud se jméno a heslo neshodují u žádného uživatele.
         */
        public function logIn(string $name, string $password) : void
        {
            $user = Mysql::oneRow('
                SELECT users_id, user, password, admin
                FROM users
                WHERE user = ?
            ', array($name));
            if (!$user || !password_verify($password,$user['password']))
                throw new UserException('Neplatné jméno nebo heslo.',141);
            $_SESSION['user'] = $user;
        }

        /**
         * Odhlásí uživatele
         */
        public function logOut() : void
        {
            unset($_SESSION['user']);
        }

        /**
         * Vrátí aktuálně přihlášeného uživatele
         */
        public function getUser() : ?array
        {
            if (isset($_SESSION['user']))
                return $_SESSION['user'];
            return null;
        }

        /**
         * Funkce která vrací všechny uživatele
         * @return array $users dvourozměrné pole kde v jednom rozměru jsou uživatele a v druhém jejich detaily. 
         * @throws UserExcepton pokud nebyl nalezen žádný uživatel.
         */
        public function getPersons(int $from = 0, int $limit = 25) : array
        {
            try{
            $users = Mysql::moreRows('
                SELECT persons_id, first_name, last_name, users.user, admin, street, house_number, building_identification_number, city, ZIP, national_id_number, identity_card_number
                FROM persons JOIN users 
                ON persons.user = users.users_id 
                JOIN addresses
                ON addresses_id = persons.address
                LIMIT ?, ?;
            ', array($from,$limit));
            }catch(TypeOfMessage $error){
                throw new UserException('Došlo k chybě databáze.', 171);
            }
            return $users;       
        }
        public function countPersons(){
            try{
                $count = Mysql::oneValue('
                    SELECT count(*)
                    FROM persons;
                ');
            }catch(TypeOfMessage $error){
                throw new UserException('Došlo k chybě databáze.', 171);
            }
            return $count;
        }

    }