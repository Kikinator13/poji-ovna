<?php
class ContactManager
{
    /** 
     * Přidá kontakt do tabulky contact.
     * @param array $contact zvalidované pole kontaktů
     * @return int id vloženého kontaktu
     * @throws UserException 50101 pokud PDO vyhodí vyjímku 
     * @throws UserException 50102 pokud počet vložených záznamů není jedna   
     */
    public function addContact($contact, Validator $validator): int
    {
        try {
            //Vložíme kontakty.
            $countRows = Mysql::insert('contact', $contact);
        }
        //Pokud je vyhozena vyjímka...
        catch (Exception $error) {
            //Pokud dojde k jiné chybě...    
            throw new UserException('Při vkládání uživatele do databáze došlo k chybě!', 50101, $error);
        }
        if ($countRows != 1) {
            Mysql::rollBack();
            throw new UserException("Do tabulky contact bylo vloženo $countRows řádků!", 50102);
        } else {
            //Vrátíme id.
            return Mysql::lastId();
        }
    }

    /** Odstraní kontakt z tabulky contact.
     *  @param int id kontaktu
     *  @return true Při úspěchu;
     *  @throws UserException 50201 pokud PDO vyhodí výjimku 
     *  @throws UserException 50202 pokud počet smazaných záznamů není jedna 
     */
    public function deleteContact($contactId): bool | int
    {
        try {
            $countRows = Mysql::delete("contact", array("contact_id" => $contactId));
        } catch (Exception $error) {
            throw new UserException("Při mazání z tabulky contact došlo k chybě!", 50201, $error);
        }
        if ($countRows == 1) {
            return true;
        } else {
            Mysql::rollBack();
            throw new UserException("Z tabulky contact bylo odstraněno $countRows řádků!", 50202);
        }
    }

    /** Změní kontakt v tabulce contact např. 
     *  @param array $contact Pole Jehož klíče jsou názvy sloupců a hodnoty jsou jejich nové hodnoty.
     *  @param int $contactId id kontaktu
     *  @throws UserException 50301 pokud PDO vyhodí výjimku 
     *  @throws UserException 50302 pokud počet upravených záznamů není jedna
     */
    public function updateContact(array $contact, $contactId) : int|bool
    {
        
        try {
            $countRows = Mysql::update('contact', $contact, "contact_id = ?", array($contactId));
        } catch (Exception $error) {
            throw new UserException("Nepodařilo se změnit kontakt kvůli chybě databáze!", 50301, $error);
        }
        return true;
    }

    public function getContact($id, ...$columns) 
    {
        $columns = implode(", ", $columns);
        return Mysql::oneRow("SELECT ". $columns ." FROM contact WHERE contact_id = ?", 
        array($id), PDO::FETCH_ASSOC);
    }
    public function contactValidation(Validator $validator): ?array
    {
        //Proměnná ukazuje je li vše ok.
        $ok = true;

        $contact = array(
            "area_code" => $validator->areaCodeValidation(),
            "phone" => $validator->phoneValidation(),
            "mail" => $validator->mailValidation()
        );

        foreach ($contact as $passed) {
            if (!$passed) {
                $ok = false;
            }
        }

        //když je vše ok vrátí pole připravené pro vložení do databáze.
        if ($ok) return $contact;
        //jinak vrátí false
        else return null;
    }
}
