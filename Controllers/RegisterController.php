<?php
class RegisterController extends Controller
{
    private Validator $validator;
    private UserManager $userManager;
    private PersonManager $personManager;
    private AddressManager $addressManager;
    private ContactManager $contactManager;
    private StateManager $stateManager;
    private AreaCodeManager $areaCodeManager;

    public function work(array $parameters): void
    {
        //Vytvoříme instance manažerů.
        $this->userManager = new UserManager();
        $this->personManager = new PersonManager();
        $this->addressManager = new AddressManager();
        $this->contactManager = new ContactManager();
        $this->stateManager = new StateManager();
        $this->areaCodeManager = new AreaCodeManager();
        $this->validator = new Validator();
        if ($_POST) //Pokud byl formulář již odeslán
        {
            //Připravíme si zvalidovaná data.
            //Pokud registrujeme nového uživatele.
            if (empty($_POST["person_id"])) {
                //Při nastavení formType na Registrace bude ve formuláři captcha a souhlas s podmínkama.
                $this->data["formType"] = "Registrace";
                $captcha = $this->validator->captchaValidation();
                $agreement = $this->validator->agreementValidation();
                //Pokud chce uživatel vytvořit i účet
                if (isset($_POST["want_account"])) {
                    $user = $this->userManager->userValidation($this->validator);
                } else {
                    $user = true;
                }
                //Pokud updatujeme uživatele.
            } else {
                //Na updatovacím formuláři nebude captcha a zaškrtávadlo souhlasu s podmínkami.
                $this->data["formType"] = "Editace osoby";
                //Captch a asouhlas nastavýme na true aby prošli kontrolou. 
                //Ve formuláři je totiž mít ebudeme protože update provádí jedině přihlášený admin.
                $captcha = true;
                $agreement = true;
                //Pokud je zaškrtnuto že chceme něco dělat z uživatelským účtem.
                if (isset($_POST["want_account"])) {
                    //Zjistíme jestli uživatel existuje. Buď dostaneme jeho číslo nebo null.
                    $person=$this->personManager->getPerson($_POST["person_id"], "user");
                    //Pokud editovaná osoba již má účet nemusí být vyplněno heslo (Nemusí ho chtít měnit. To zajití druhý parametr nastavený na true).
                    if($person["user"]){
                        //Díky druhému parametru bude funkce ignorovat, když je prázdné heslo, protože ho měnit nechceme.
                        $user = $this->userManager->userValidation($this->validator, true);
                    //Pokud uživatel neexistuje.
                    } else {
                        //Zvalidujeme i s heslem(druhý parametr je nastaven defaultně na false).
                        $user = $this->userManager->userValidation($this->validator);
                    }
                //Pokud s uživatelským účtem nic dělat nechceme.
                }else{
                    //Nastavíme $user na true aby prošel kontrolou poté ho musíme změnit na false.
                    $user=true;
                }
            }
            $contact = $this->contactManager->contactValidation($this->validator);
            $address = $this->addressManager->addressValidation($this->validator);
            $person = $this->personManager->personValidation($this->validator);

            //Pokud jsou všechna data v pořádku.
            if ($captcha && $agreement && $user && $address && $person && $contact) {
                //Pokud z účetem nic neděláme vyčistíme všechny data o uživateli.
                if (!isset($_POST["want_account"])) {
                    //$user nastavíme na false a zároveň všechny proměnné zobrazované ve formuláři vyprázdníme.
                    $user = $this->userClear();
                }

                //Pokud updatujeme.
                if (!empty($_POST["person_id"])) {
                    $this->data["formType"] = "Editace osoby";
                    //Do pole persons, které připravila funkce personValidation přidáme id.

                    $person = $this->update($user, $address, $person, $contact);

                    //Pokud registrujeme.
                } else {
                    $this->data["formType"] = "Registrace";
                    $this->register($user, $address, $person, $contact);
                }
            } else {
                $this->addMessage("Jedno nebo více polí nejsou vyplněna správně!", TypeOfMessage::ERROR);
            }

            $this->data['form_messages'] = $this->validator->getFormMessages();
            //Pokud formulář nebyl ještě odeslán.
        } else {
            //Pokud v URL není zadaná operace budeme registrovat. Jinak nastavýme operaci dle URL.
            $operation = (empty($parameters[0])) ? "registration" : $parameters[0];
            //Pokud potřebujeme id (updatujeme nebo mažeme, neregistrujeme).
            if ($operation != "registration") {
                //Pokud je v URL zadané id a je to číslo.
                if (isset($parameters[1]) && is_numeric($parameters[1])) {
                    $id = $parameters[1];
                } else {
                    $this->addMessage("Byl zadán nevalidní parametr!", TypeOfMessage::ERROR);
                    $this->redirect("pojistovna");
                }
                //Pokud registrujeme.
            } else {
                $id = null;
            }
            $this->prepareForm($operation, $id);
        }
        //Předáme šabloňě státy a předvolby aby byli k dispozici v selectech.
        $this->data["area_codes"] = $this->areaCodeManager->getAreaCodes();
        $this->data["states"] = $this->stateManager->getStates();

        // Nastavení šablony
        $this->view = 'registration';
    }



    /**
     * Registruje nového uživatele do systému
     * @param string $name uživatelské jméno 
     * @param string $password heslo
     * @param string $passwordAgain heslo znovu pro kontrolu 
     * @param string $year antispam
     * @throws UserException 20103 Pokud uživatel se zadaným názvem již existuje.
     * @throws UserException 10101 Pokud došlo k jiné chybě.
     */
    public function register(array|bool $user, array $address, array $person, array $contact): void
    {

        $userManager = $this->userManager;
        $addressManager = $this->addressManager;
        $personManager = $this->personManager;
        $contactManager = $this->contactManager;
        $validator = $this->validator;
        try {
            //Zahájení transakce aby byly ovlivněny všechny tabulky nebo žádná.
            Mysql::startTransaction();
            //pokud chceme vytvořit i uživatelský účet, vložíme uživatele do databáze.

            if ($user) {
                $userId = $userManager->addUser($user, $validator);
            }
            //Vložíme kontakty do databáze.
            $contactId = $contactManager->addContact($contact, $validator);
            //Vložíme adresu.
            $addressId = $addressManager->addAddress($address);
            //Vložíme osobu.
            $personManager->addPerson($person, $userId, $addressId, $contactId);
            //Comitneme.
            Mysql::commit();
            $this->addMessage('Byl jste úspěšně zaregistrován.', TypeOfMessage::SUCCESS);
            //Pokud byl vytvářen uživatelský účet přihlásíme se s ním
            if ($user)
                $this->userManager->logIn($_POST['user_name'], $_POST['password']);
            //Když nebyl vytvořen účet přesměrujeme na index.
            else
                $this->redirect("index");
        }
        //Pokud je vyhozena vyjímka...
        catch (Exception $error) {

            //Pokud uživatelské jméno již existuje.
            if ($error->getCode() == 20103) {
                $this->addMessage($error->getmessage(), TypeOfMessage::ERROR);
            } else {
                $this->addMessage($error->getMessage(), TypeOfMessage::ERROR);
            }
        }
        //Zjistíme nově přihlášeného uživatele.
        $user = $userManager->getLoggedUser();
        $this->redirect('profile/' . $user["persons_id"]);
    }

    /** Odstraní osobu i s jejím uživatelským účtem.
     *  @param int $personId id osoby
     *  @return array $Person informace o smazané osobě
     *  @throws UserException 10101 při neúspěchu.
     */
    public function delete(int $personId): array
    {

        $this->userVerify(true);
        $userManager = $this->userManager;
        $personManager = $this->personManager;
        $contactManager = $this->contactManager;
        $addressManager = $this->addressManager;
        try {
            //Zahájíme transakci
            Mysql::startTransaction();
            //Zjistíme přezdívku, jméno příjmení a id uživatelského účtu.
            $person = Mysql::oneRow("SELECT first_name, last_name, user, contact, `address`
                    FROM persons WHERE persons_id = ?", array($personId));

            $personManager->deletePerson($personId);
            if (isset($person["user"]))
                $userManager->deleteUser($person["user"]);

            $contactManager->deleteContact($person["contact"]);
            $personOnAddress = MYSQL::oneValue("SELECT count(*) FROM addresses WHERE addresses_id = ?", array($person["address"]));
            if ($personOnAddress == 1)
                $addressManager->deleteAddress($person["address"]);


            Mysql::commit();
        } catch (Exception $error) {
            $this->addMessage($error->getMessage() . "Nepodařilo se odstranit záznam! Chyba je pravděpodobně na naší straně.", TypeOfMessage::ERROR);
            $this->redirect("admin");
        }
        $this->addmessage("Uživatel " . $person["first_name"] . " " . $person["last_name"] . " byl úspěšně smazán", TypeOfMessage::SUCCESS);
        $this->redirect("admin");
    }


    public function update(array|bool $user, array $address, array $person, $contact): array
    {
        $this->userVerify(true);
        $userManager = $this->userManager;
        $personManager = $this->personManager;
        $contactManager = $this->contactManager;
        try {
            //Zahájíme transakci
            Mysql::startTransaction();
            //Zjistíme id uživatele osoby a kontaktu.
            $id = Mysql::oneRow(
                "SELECT user, `address`, contact 
                FROM persons WHERE persons_id = ?",
                array(
                    $_POST["person_id"]
                )
            );

            //Pokud bylo zaškrtnuto, že chceme vytvořit účet.
            if ($user) {
                //Pokud už existuje updatujeme ho
                if ($id["user"]) {
                    $userManager->updateUser($user, $id["user"]);
                //Když neexistuje Zaregistrujeme ho
                } else {
                        $userManager->addUser($user, $this->validator);
                        $person["user"] = MYSQL::lastId();
                }
            //Pokud účet existuje ale tlačítko účtu je odškrtnuté účet smažeme.
            } else if ($id["user"]) {
                $userManager->deleteUser($id["user"]);
            }
            //updatujeme osobu
            $personManager->updatePerson($person, $_POST["person_id"]);
            //Přidáme id uživatele
            //$user["users_id"] = $id["user"];
            $contactManager->updateContact($contact, $id["contact"]);

            $personManager->updateAddress($address, $_POST["person_id"]);
            //Uživatel se updatuje pouze má-li editovná osoba uživatelský účet. 

            Mysql::commit();
        } catch (Exception $error) {
            $this->addMessage("Nepodařilo se upravit záznam! Chyba je pravděpodobně na naší straně.  ", TypeOfMessage::ERROR);
            $this->redirect("admin");
        }
        $this->addmessage("Záznam byl upraven!", TypeOfMessage::SUCCESS);
        $this->redirect("admin");
    }
    public function prepareForm(string $operation, ?int $id = null): void
    {
        switch ($operation) {
            case "delete":
                $this->userVerify(true);
                $person = $this->delete($id);
                break;

                //Pokud $parametr 0 = update.
            case 'update':
                $this->userVerify(true);
                //Pokud 
                //Předáme šabloně veškerá data o klientovi.
                try {
                    $_POST = Mysql::oneRow(
                        "SELECT * 
                                    FROM persons 
                                    left JOIN users ON persons.user = users_id
                                    JOIN addresses ON `address` = addresses_id
                                    JOIN contact ON `contact` = contact_id
                                    WHERE persons_id = ?",
                        array($id)
                    );
                    $this->data["switch"] = ($_POST["user"]) ? "" : "";
                    $_POST["want_account"] = ($_POST["user"]) ? true : false;


                    //změna hlavičky
                    $this->data["formType"] = "Registrace";
                    $this->head['title'] = 'Registrace';
                } catch (Exception $error) {
                    $this->addMessage("Nepodařilo se načíst data o klientovi!", TypeOfMessage::ERROR);
                    $this->redirect("admin");
                }
                //Heslo chceme ponechat prázdné protože hasch je nám zde na nic.
                $_POST["password"] = "";
                $_POST["password_again"] = "";

                $this->data["area_codes"] = $this->areaCodeManager->getAreaCodes();
                $this->data["states"] = $this->stateManager->getStates();

                //změna hlavičky
                $this->data["formType"] = "Editace osoby";
                $this->head['title'] = 'Změna klienta';
                $this->clearFormMessages();
                $_POST["person_id"] = $id;
                $this->data['form_messages'] = $this->validator->getFormMessages();

                break;
                //Pokud registrujeme nového uživatele.
            case "registration":

                // Hlavička stránky
                //Nastavýme prázdné řetězce na proměnné, které vypisuje formulář. (Validační hlášky a zadaná data)
                $this->clearFormField();
                $this->clearFormMessages();
                $this->data["area_codes"] = $this->areaCodeManager->getAreaCodes();
                $this->data["states"] = $this->stateManager->getStates();
                $this->head['title'] = 'Registrace';

                $this->data["formType"] = "Registrace";
                $this->data['form_messages'] = $this->validator->getFormMessages();
                break;
        }
    }
    /** Funkce vyčistí formulářové zprávy.
     * @return void
     */
    public function clearFormMessages(): void
    {
        $this->validator->addFormMessage("user_name", "", TypeOfFormMessage::EMPTY);
        $this->validator->addFormMessage("password", "", TypeOfFormMessage::EMPTY);
        $this->validator->addFormMessage('password_again', '', TypeOfFormMessage::EMPTY);
        $this->validator->addFormMessage("street_and_number", "", TypeOfFormMessage::EMPTY);
        $this->validator->addFormMessage("ZIP", "", TypeOfFormMessage::EMPTY);
        $this->validator->addFormMessage("city", "", TypeOfFormMessage::EMPTY);
        $this->validator->addFormMessage("first_name", "", TypeOfFormMessage::EMPTY);
        $this->validator->addFormMessage("last_name", "", TypeOfFormMessage::EMPTY);
        $this->validator->addFormMessage("date_of_birth", "", TypeOfFormMessage::EMPTY);
        $this->validator->addFormMessage("national_id_number", "", TypeOfFormMessage::EMPTY);
        $this->validator->addFormMessage("identity_card_number", "", TypeOfFormMessage::EMPTY);
        $this->validator->addFormMessage("phone", "", TypeOfFormMessage::EMPTY);
        $this->validator->addFormMessage("area_code", "", TypeOfFormMessage::EMPTY);
        $this->validator->addFormMessage("state", "", TypeOfFormMessage::EMPTY);
        $this->validator->addFormMessage("mail", "", TypeOfFormMessage::EMPTY);
        $this->validator->addFormMessage("captcha", "", TypeOfFormMessage::EMPTY);
        $this->validator->addFormMessage("agreement", "", TypeOfFormMessage::EMPTY);
    }
    /** Funkce nastavý formulářová pole na prázdnou či defaultní hodnotu.
     * @return void  
     */
    public function clearFormField(): void
    {
        $_POST["person_id"] = "";
        $_POST["user_name"] = "";
        $_POST["password"] = "";
        $_POST['password_again'] = "";
        $_POST["street_and_number"] = "";
        $_POST["ZIP"] = "";
        $_POST["city"] = "";
        $_POST["first_name"] = "";
        $_POST["last_name"] = "";
        $_POST["date_of_birth"] = "";
        $_POST["national_id_number"] = "";
        $_POST["identity_card_number"] = "";
        $_POST["area_code"] = "";
        $_POST["phone"] = "";
        $_POST["state"] = "";
        $_POST["mail"] = "";
        $_POST["want_account"] = "";
        $_POST["captcha"] = "";
        $_POST["agreement"] = false;
    }

    public function userClear(): bool
    {
        $_POST["user_name"] = "";
        $_POST["password"] = "";
        $_POST["password_again"] = "";
        $this->validator->addFormMessage("user_name", "", TypeOfFormMessage::EMPTY);
        $this->validator->addFormMessage("password", "", TypeOfFormMessage::EMPTY);
        $this->validator->addFormMessage("password_again", "", TypeOfFormMessage::EMPTY);
        return false;
    }
}
