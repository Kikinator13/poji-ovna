<?php
    class AddressManager{
        /** Přdá adresu do tabulky addresses pokud už neexistje a vrátí její id ať už existuje nebo ne.
         * @param array $address pole validovaných dat adresy
         * @return int id vložené adresy
         * @throws UserException 30101 pokud PDO vyhodí vyjímku 
         * @throws UserException 30102 pokud počet vložených záznamů není jedna 
         */
        public function addAddress(array $address) : int
        {
            //Odstraníme klíče pole, protože funkce edit s nimi nepočítá.;
            $address=array_values($address);            
            try{
                //Zavoláme proceduru, která vloží adresu jen pokud ještě neexistuje a poskytne její id.
                $countRows=Mysql::edit("CALL insert_address(?, ?, ?, ?, ?, @id);", $address);   
            }catch(Exception $error){
                throw new UserException('Při vkládání adresy do databáze došlo k chybě!', 30101, $error);
                
            }
            if($countRows != 1){
                Mysql::rollBack();
                throw new UserException("Do tabulky addresses bylo vloženo $countRows řádků!", 30102);
            }
            //vrátíme id adresy;
            return Mysql::oneValue("SELECT @id;");
        }

        /** Odstraní adresu z tabulky address.
         *  @param int id adresy
         *  @return true Při úspěchu;
         *  @throws UserException 40201 pokud PDO vyhodí výjimku 
         *  @throws UserException 40202 pokud počet smazaných záznamů není jedna 
         */
        public function deleteAddress($addressId) : bool | int{
            try{
                $countRows = Mysql::delete("addresses", array("addresses_id"=>$addressId));
            }catch(Exception $error){
                throw new UserException ("Při mazání z tabulky addresses došlo k chybě!",30201, $error);
            }
            if($countRows == 1){
                 return true;
            }
            else {
                Mysql::rollBack();
                throw new UserException ("Z tabulky addresses bylo odstraněno $countRows řádků!",30202);
            }
        }
        
        /** Změní adresu v tabulce addresses např. Kdyby se přejmenovala ulice či město.
         *  Pro změnu adresy konkrétní osoby užíjte funkci updateAddress 
         *  ve třídě Persons.
         *  @param array $address Pole Jehož klíče jsou názvy sloupců a hodnoty jsou jejich nové hodnoty.
         *  @param int $addressId id adresy
         *  @throws UserException 40301 pokud PDO vyhodí výjimku 
         *  @throws UserException 40302 pokud počet upravených záznamů není jedna
         */
        public function updateAddress(array $address, $addressId) : bool
        {
            try{ 
                $countRows = Mysql::update('addresses', $address, "addresses_id = ?", array($addressId));
            }catch(Exception $error){
                throw new UserException("Nepodařilo se změnit adresu kvůli chybě databáze!", 30301, $error);
            }
                return true;
            
        }

        public function addressValidation(Validator $validator) : bool|array
        {
            //Proměnná ukazuje je li vše ok.
            $ok = true;
            
            $address = array(
                "street" => $validator->streetValidation(), 
                "building_identification_number" => $validator->buildingIdentificationNumberValidation(), 
                "house_number" => $validator->houseNumberValidation(), 
                "ZIP" => $validator->ZIPValidation(), 
                "city" => $validator->cityValidation() 
            );
            
            foreach ($address as $passed){
                if (!$passed){
                    $ok = false;
                }     
            }

            //když je vše ok vrátí pole připravené pro vložení do databáze.
            if($ok) return $address;  
            //jinak vrátí false
            else return false;            
        }
        
    }