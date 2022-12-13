<?php
    class AddressManager{
        /** Přdá pojištění do tabulky insurance 
         * @param array $insurance pole validovaných dat pojištění
         * @return int id vkládaného pojištění
         * @throws UserException 70101 pokud PDO vyhodí výjimku 
         * @throws UserException 70102 pokud počet vložených záznamů není jedna 
         */
        public function addInsurance(array $insurance) : int
        {
            try {
                //Vložíme pojištění.
                $countRows = Mysql::insert('insurance', $insurance);
            }
            //Pokud je vyhozena výjimka...
            catch (Exception $error) {
                //vyhodíme výjimku    
                throw new UserException('Při vkládání uživatele do databáze došlo k chybě!', 70101, $error);
            }
            if ($countRows != 1) {
                Mysql::rollBack();
                throw new UserException("Do tabulky contact bylo vloženo $countRows řádků!", 70102);
            } else {
                //Vrátíme id.
                return Mysql::lastId();
            }
        }

        /** Odstraní pojištění z tabulky insurance.
         *  @param int $insuranceId id pojištění
         *  @return true Při úspěchu;
         *  @throws UserException 70201 pokud PDO vyhodí výjimku 
         *  @throws UserException 70202 pokud počet smazaných záznamů není jedna 
         */
        public function deleteInsurance($insuranceId) : bool | int{
            try{
                $countRows = Mysql::delete("insurance", array("insurance_id"=>$addressId));
            }catch(Exception $error){
                throw new UserException ("Při mazání z tabulky insurance došlo k chybě!",70201, $error);
            }
            if($countRows == 1){
                 return true;
            }
            else {
                Mysql::rollBack();
                throw new UserException ("Z tabulky addresses bylo odstraněno $countRows řádků!",70202);
            }
        }
        
        /** Změní pojištění v tabulce insurance 
         *  @param array $insurance poole jehož klíče jsou názvy sloupců a hodnoty jsou jejich nové hodnoty.
         *  @param int $insuranceId id pojištění
         *  @throws UserException 70301 pokud PDO vyhodí výjimku 
         *  @throws UserException 70302 pokud počet upravených záznamů není jedna
         */
        public function updateInsurance(array $insurance, $insuranceId) : bool
        {
            try{ 
                $countRows = Mysql::update('insurance', $insurance, "insurance_id = ?", array($insuranceId));
            }catch(Exception $error){
                throw new UserException("Nepodařilo se změnit adresu kvůli chybě databáze!", 70301, $error);
            }
                return true;
            
        }

        public function getInsurance($id, ...$columns ){ 
            $columns=implode(", ", $columns);
            return Mysql::oneRow(
                "SELECT ".$columns." FROM insurance 
            WHERE insurance_id = ?", 
            array($id),
            PDO::FETCH_ASSOC
        );
        }
        public function insuranceValidation(Validator $validator) : ?array
        {
            //Proměnná ukazuje je li vše ok.
            $ok = true;
            
            $insurance = array(
                "type" => $validator->typeValidation(), 
                "variant" => $validator->variantValidation(), 
                "policy_holder" => $validator->policyHolderValidation(),
                "insured" => $validator->insuredValidation(),
                "policy_value" => $validator->policyValueValidation(),
                "insurance_rate" => $validator->insuranceRateValidation()
            );
            
            foreach ($insurance as $validation){
                if ($validation === null){
                    return false;
                }     
            }
            //když je vše ok vrátí pole připravené pro vložení do databáze.
            return $insurance;  
            
                        
        }
        
    }