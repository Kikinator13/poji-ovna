<?php
    // Správce uživatelů.
    class UserManager
    {       
        /**
         * Vrátí otisk hesla
         * @return string otisk hesla
         */
        public function getHash(string $heslo) : string
        {
            return password_hash($heslo, PASSWORD_DEFAULT);
        }
        
        /** 
         * Přidá uživatele do tabulky users.
         * @param array $user zvalidované pole dat uživatele
         * @return int id vloženého uživatele
         * @throws UserException 20101 pokud PDO vyhodí vyjímku 
         * @throws UserException 20102 pokud počet vložených záznamů není jedna 
         * @throws UserException 20103 pokud uživatel již existuje  
         */  
        public function addUser($user, Validator $validator) : int
        {
            try
            {
                //Vložíme uživatele.
                $countRows=Mysql::insert('users', $user);
            }
            //Pokud je vyhozena vyjímka...
            catch (Exception $error)
            {
                //Pokud uživatel již existuje...
                if($error->getCode() == 23000){
                    $validator->addFormMessage("user", "Uživatel s tímto jménem již existuje!", TypeOfFormMessage::INVALID);
                    throw new UserException('Uživatel s tímto jménem je již zaregistrovaný!', 20103, $error);
                //Pokud dojde k jiné chybě...    
                }else{    
                    throw new UserException('Při vkládání uživatele do databáze došlo k chybě!', 20101, $error);
                }
            }
            if($countRows != 1){
                Mysql::rollBack();
                throw new UserException("Do tabulky users bylo vloženo $countRows řádků!", 20102);
            }else{
                //Vrátíme id.
                return Mysql::lastId();
            }
        }
        
        /** 
         * Odstraní uživatele z tabulky users.
         * @param int id uživatele
         * @return true Při úspěchu;
         * @throws UserException 20201 pokud PDO vyhodí výjimku 
         * @throws UserException 20202 pokud počet smazaných záznamů není jedna 
         */
        public function deleteUser($userId) : bool | int{
            try{
                $countRows = Mysql::delete("users", array("users_id"=>$userId));
            }catch(Exception $error){
                throw new UserException ("Při mazání z tabulky users došlo k chybě!",20201, $error);
            }
            if($countRows == 1){
                 return true;
            }
            else {
                Mysql::rollBack();
                throw new UserException ("Z tabulky users bylo odstraněno $countRows řádků!",20202);
            }
        }
        
        /** 
         * Změní uživatele v tabulce users.
         * @param array $user Pole Jehož klíče jsou názvy sloupců a hodnoty jsou jejich nové hodnoty.
         * @throws UserException 20301 pokud PDO vyhodí výjimku 
         * @throws UserException 20302 pokud počet upravených záznamů není jedna
         */
        public function updateUser(array $user) : bool
        {
            
            try{ 
                $countRows = Mysql::update('users', $user, "users_id = ?", array($user["users_id"]));
            }catch(Exception $error){
                throw new UserException("Nepodařilo se změnit uživatele kvůli chybě databáze!", 20301, $error);
            }
                return true;
        }


        /** 
         * funkce zjistí zda byli všechny údaje do tabulky uživatelé zadané správně,
         * a případně je upravý tak aby byly.
         * @param array $user pole údajů o uživateli;
         * @return array $user pole upravené pro vložení do databáze. 
         */
        public function userValidation(Validator $validator, bool $update = false) : bool | array
        {   
            //Proměnná ukazuje je li vše ok.
            $ok = true;
            //Zvalidujeme uživatelské jméno a uložíme do pole.
            $user["user"] = $validator->userValidation(); 
            //Pokud se nejedná o update uživatele, kde je heslo prázdné(nechceme ho měnit).
            if(!($update && $_POST["password"] == "")){
                $user["password"] = $validator->passwordValidation();
                $validator->passwordAgainValidation($user["password"]);
                //Zahashujeme heslo
                $user["password"] = $this->getHash($user["password"]);
            }else{
                $validator->addFormMessage("password", "", TypeOfFormMessage::EMPTY);
                $validator->addFormMessage("password_again", "", TypeOfFormMessage::EMPTY);
            }
            
            //Ciklus projde pole uživatel a pokud narazí na false(tedy chybně zvalidováno) změní $good na false; 
            foreach($user as $passed)
            {
                if (!$passed){
                    $ok = false;
                }
            }
            //když je vše ok vrátí pole připravené pro vložení do databáze.
            
            
            if($ok) return $user;  
            
            //jinak vrátí false;
            else return false;
        }

        /**
         * Přihlásí uživatele do systému
         * @throws UserException pokud se jméno a heslo neshodují u žádného uživatele.
         */
        public function logIn(string $name, string $password) : void
        {
            try{
            $user = Mysql::oneRow('
                SELECT users_id, users.user, password, admin, persons_id
                FROM persons JOIN users
                ON persons.user = users_id
                WHERE users.user = ?
            ', array($name));
            }catch(Exception $error){
                throw new UserException('Neplatné jméno nebo heslo.',141);
            }
            if (!$user || !password_verify($password,$user['password']))
                throw new UserException('Neplatné jméno nebo heslo.',142);
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

        
        
    }