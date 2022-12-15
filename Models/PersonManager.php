<?php
class personManager
{
    /** Přdá osobu do tabulky perosns.
     * @param array $person pole validovaných dat osoby.
     * @return int id vložené osoby
     * @throws UserException 40101 pokud PDO vyhodí vyjímku 
     * @throws UserException 40102 pokud počet vložených záznamů není jedna 
     */
    public function addPerson($person,  $userId, $addressId, $contactId): int
    {
        $person["user"] = $userId;
        $person["address"] = $addressId;
        $person["contact"] = $contactId;
        try {
            $countRows = Mysql::insert('persons', $person);
        } catch (Exception $error) {
            throw new UserException("Při vkládání osoby do databáze došlo k chybě!", 40101, $error);
        }
        if ($countRows == 1) {
            return Mysql::lastId();
        } else {
            Mysql::rollBack();
            throw new UserException("Do tabulky persons bylo vloženo $countRows řádků!", 40102);
        }
    }

    /** Odstraní osobu z tabulky persons.
     *  @param int id osoby
     *  @return true Při úspěchu;
     *  @throws UserException 40201 pokud PDO vyhodí výjimku 
     *  @throws UserException 40202 pokud počet smazaných záznamů není jedna 
     */
    public function deletePerson($personId): bool | int
    {
        try {
            $countRows = Mysql::delete("persons", array("persons_id" => $personId));
        } catch (Exception $error) {
            throw new UserException("Při mazání z tabulky persons došlo k chybě!", 40201, $error);
        }
        if ($countRows == 1) {
            return true;
        } else {
            Mysql::rollBack();
            throw new UserException("Z tabulky persons bylo odstraněno $countRows řádků!", 40202);
        }
    }

    /** 
     * Změní uživatele v tabulce users.
     * @param array $person Pole Jehož klíče jsou názvy sloupců a hodnoty jsou jejich nové hodnoty.
     * @throws UserException 40301 pokud PDO vyhodí výjimku 
     * @throws UserException 40302 pokud počet upravených záznamů není jedna
     */
    public function updatePerson(array $person, $personId): bool
    {

        try {
            $countRows = Mysql::update('persons', $person, "persons_id = ?", array($personId));
        } catch (Exception $error) {
            throw new UserException("Nepodařilo se změnit osobu kvůli chybě databáze!", 40301, $error);
        }
        return true;
    }

    public function updateAddress(array $address, $personId): int
    {
        $address = array_values($address);


        try {
            //Vytvoříme novou adresu pokud neexistuje.
            Mysql::edit("CALL insert_address(?, ?, ?, ?, @id);", $address);

            //Získáme id této adresy.
            $addressId = Mysql::oneValue("SELECT @id;");
            //Získáme id staré adresy.
            $oldAddressId = Mysql::oneValue("SELECT `address` FROM persons WHERE persons_id = ?", array($personId));
            //Pokud je nová adresa jiná než původní.
            if ($oldAddressId != $addressId) {
                //Novou adresu nastavýme osobě s id $personId.
                $countRows = Mysql::update(
                    'persons',
                    array('address' => $addressId),
                    'persons_id = ?',
                    array($personId)
                );
                //Zjistíme kolik osob žije na staré adrese.
                $countPersonsOnAddress = Mysql::oneValue(
                    '
                        SELECT count(*) 
                        FROM persons 
                        WHERE address = ?',
                    array($oldAddressId)
                );
                //Pokud na staré adrese už nikdo nežije, tak jí smažeme.
                if ($countPersonsOnAddress == 0) {
                    $addressManager = new AddressManager();
                    $addressManager->deleteAddress($oldAddressId);
                }
            } else {
                return $addressId;
            }
        } catch (Exception $error) {
            throw new UserException("Chyba databáze při úpravě adresy!", 40401, $error);
        }
        if ($countRows == 1) {
            return $addressId;
        } else {
            Mysql::rollBack();
            throw new UserException("V tabulce persons bylo upraveno $countRows řádků!", 40402);
        }
    }

    public function personValidation(Validator $validator): bool|array
    {
        //Proměnná ukazuje je li vše ok.
        $ok = true;
        $person = array(
            "first_name" => $validator->firstNameValidation(),
            "last_name" => $validator->lastNameValidation(),
            "date_of_birth" => $validator->dateOfBirthValidation(),
            "identity_card_number" => $validator->identityCardNumberValidation(),
            "national_id_number" => $validator->nationalIdNumberValidation(),
        );

        foreach ($person as $passed) {
            if (!$passed) {
                $ok = false;
            }
        }

        //když je vše ok vrátí pole připravené pro vložení do databáze.
        if ($ok) return $person;
        //jinak vrátí false
        else return false;
    }

    public function countPersons()
    {
        try {
            $count = Mysql::oneValue('
                    SELECT count(*)
                    FROM persons;
                ');
        } catch (Exception $error) {
            throw new UserException('Došlo k chybě databáze.', 40501);
        }
        return $count;
    }

    /**
     * Funkce která vrací všechny uživatele
     * @return array $users dvourozměrné pole kde v jednom rozměru jsou uživatele a v druhém jejich detaily. 
     * @throws UserExcepton pokud nebyl nalezen žádný uživatel.
     */
    public function getPersonsAll(int $from = 0, int $limit = 25): array
    {
        try {
            $users = Mysql::moreRows(
                'SELECT persons_id, first_name, last_name, user_name, `admin`, street_and_number, city, ZIP, national_id_number, identity_card_number
                FROM persons left JOIN users 
                ON persons.user = users.users_id 
                JOIN addresses
                ON addresses_id = persons.address
                LIMIT ?, ?;
            ', array($from, $limit), PDO::FETCH_ASSOC);
        } catch (Exception $error) {
            throw new UserException('Došlo k chybě databáze.', 40601);
        }
        
        return $users;
    }

    public function getPersons(int $from = 0, int $limit = 25): array
    {
        try {
            $users = Mysql::moreRows(
                'SELECT * 
                FROM persons 
                LIMIT ?, ?;
            ',
                array($from, $limit)
            );
        } catch (Exception $error) {
            throw new UserException('Došlo k chybě databáze.', 40701);
        }
        return $users;
    }

    public function getPersonAll(int $id): bool|array
    {
        try {
            $users = Mysql::oneRow(
                'SELECT *
                FROM persons JOIN users 
                ON user = users_id 
                JOIN addresses
                ON addresses_id = persons.address
                JOIN contact
                ON contact_id = contact
                WHERE persons_id = ?;
            ',
                array($id)
            );
        } catch (Exception $error) {
            throw new UserException('Došlo k chybě databáze.', 40801);
        }
        return $users;
    }

    public function getPerson(int $id, ...$columns) : ?array
    {
        $columns=implode(", ", $columns);
        try {
            $person = Mysql::oneRow('SELECT '.$columns.' FROM persons 
                WHERE persons_id = ?;',
                array($id), PDO::FETCH_ASSOC
            );
        } catch (Exception $error) {
            throw new UserException('Došlo k chybě databáze.', 40901);
        }
        
        return (empty($person)) ? null : $person;
    }
}
