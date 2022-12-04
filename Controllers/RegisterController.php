<?php
class RegisterController extends Controller
{
    private Validator $validator;
    private UserManager $userManager;
    private PersonManager $personManager;
    private AddressManager $addressManager;

    public function work(array $parameters): void
    {
        //Vytvoříme instance manažerů.
        $this->userManager = new UserManager();
        $this->personManager = new PersonManager();
        $this->addressManager = new AddressManager();
        $this->validator = new Validator();
        if ($_POST) //Pokud byl formulář již odeslán
        {
            //Připravíme si zvalidovaná data.
            //Pokud registrujeme nového uživatele.
            if (empty($_POST["person_id"])) {
                $captcha = $this->validator->captchaValidation();
                $agreement = $this->validator->agreementValidation();

                $user = $this->userManager->userValidation($this->validator);
                //Pokud updatujeme uživatele.
            } else {
                //Captch a asouhlas nastavýme na true aby prošli kontrolou. 
                //Ve formuláři je totiž mít ebudeme protože update provádí jedině přihlášený admin.
                $captcha = true;
                $agreement = true;
                //Díky druhému parametru bude funkce ignorovat, když je prázdné heslo, protože ho měnit nechceme.
                $user = $this->userManager->userValidation($this->validator, true);
            }
            $address = $this->addressManager->addressValidation($this->validator);
            $person = $this->personManager->personValidation($this->validator);
            //Pokud jsou všechna data v pořádku.
            if ($captcha && $agreement && $user && $address && $person) {
                //Pokud updatujeme.
                if (!empty($_POST["person_id"])) {
                    $this->data["formType"] = "update";
                    //Do pole persons, které připravila funkce personValidation přidáme id.
                    $person["persons_id"] = $_POST["person_id"];
                    $person = $this->update($user, $address, $person);

                    //Pokud registrujeme.
                } else {
                    $this->data["formType"] = "registration";
                    $this->register($user, $address, $person);
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
            }else{
                $id = null;
            }
            $this->prepareForm($operation, $id);
        }
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
    public function register(array $user, array $address, array $person): void
    {

        $userManager = $this->userManager;
        $addressManager = $this->addressManager;
        $personManager = $this->personManager;
        $validator = $this->validator;
        try {
            //Zahájení transakce aby byly ovlivněny všechny tabulky nebo žádná.
            Mysql::startTransaction();
            //Vložíme uživatele do databáze.
            $userId = $userManager->addUser($user, $validator);
            //Vložíme adresu.
            $addressId = $addressManager->addAddress($address);
            //Vložíme osobu.
            $personManager->addPerson($person, $userId, $addressId);
            //Comitneme.
            Mysql::commit();
        }
        //Pokud je vyhozena vyjímka...
        catch (Exception $error) {
            //Pokud uživatelské jméno již existuje.
            if ($error->getCode() == 20103) {
                $this->addMessage($error->getmessage(), TypeOfMessage::ERROR);
            } else {
                $this->addMessage("Registrace se nezdařila!", TypeOfMessage::ERROR);
            }
        }
        $this->addMessage('Byl jste úspěšně zaregistrován.', TypeOfMessage::SUCCESS);
        $this->userManager->logIn($_POST['user'], $_POST['password']);
        $this->redirect('admin');
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
        try {
            //Zahájíme transakci
            Mysql::startTransaction();
            //Zjistíme přezdívku, jméno příjmení a id uživatelského účtu.
            $person = Mysql::oneRow("SELECT users.user AS user, first_name, last_name, persons.user AS userId 
                    FROM persons JOIN users ON users_id=persons.user  WHERE persons_id = ?", array($personId));
            $personManager->deletePerson($personId);
            $userManager->deleteUser($person["userId"]);
            Mysql::commit();
        } catch (Exception $error) {
            $this->addMessage("Nepodařilo se odstranit záznam! Chyba je pravděpodobně na naší straně.", TypeOfMessage::ERROR);
            $this->redirect("admin");
        }
        $this->addmessage("Uživatel " . $person["first_name"] . " " . $person["last_name"] . " s přezdívkou " . $person["user"] . " byl úspěšně smazán", TypeOfMessage::SUCCESS);
        $this->redirect("admin");
    }
    

    public function update(array $user, array $address, array $person): array
    {
        $userManager = $this->userManager;
        $personManager = $this->personManager;
        $addressManager = $this->addressManager;
        $validator = $this->validator;
        try {
            //Zahájíme transakci
            Mysql::startTransaction();
            //Zjistíme přezdívku, jméno příjmení a id uživatelského účtu.
            $id = Mysql::oneRow("SELECT persons.user AS user, persons.address AS `address` 
                    FROM persons WHERE persons_id = ?", array($person["persons_id"]));
            $personManager->updatePerson($person);
            $user["users_id"] = $id["user"];

            $userManager->updateUser($user);
            $personManager->updateAddress($address, $person["persons_id"]);

            Mysql::commit();
        } catch (Exception $error) {
            $this->addMessage("Nepodařilo se upravit záznam! Chyba je pravděpodobně na naší straně.", TypeOfMessage::ERROR);
            $this->redirect("admin");
        }
        $this->addmessage("Záznam byl upraven!", TypeOfMessage::SUCCESS);
        $this->redirect("admin");
    }
    public function prepareForm(string $operation, ?int $id = null) : void
    {
        switch ($operation) {
            case "delete":
                $person = $this->delete($id);
                break;

                //Pokud $parametr 0 = update.
            case 'update':

                $this->userVerify(true);
                //Předáme šabloně veškerá data o klientovi.
                try {
                    $_POST = Mysql::oneRow(
                        "SELECT * 
                                    FROM persons 
                                    JOIN users ON persons.user = users_id
                                    JOIN addresses ON `address` = addresses_id
                                    WHERE persons_id = ?",
                        array($id)
                    );
                } catch (Exception $error) {
                    $this->addMessage("Nepodařilo se načíst data o klientovi!", TypeOfMessage::ERROR);
                    $this->redirect("admin");
                }
                //Heslo chceme ponechat prázdné protože hasch je nám zde na nic.
                $_POST["password"] = "";
                $_POST["password_again"] = "";
                //Telefonní číslo chceme bez předvolby. Ta je již v addonu.
                $_POST["phone"] = mb_substr($_POST["phone"], 4);

                //změna hlavičky
                $this->data["formType"] = "update";
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
                $this->head['title'] = 'Registrace';
                $this->data["formType"] = "registration";
                $this->data['form_messages'] = $this->validator->getFormMessages();
            break;
        }
    }
    /** Funkce vyčistí formulářové zprávy.
     * @return void
     */
    public function clearFormMessages()
    {
        $this->validator->addFormMessage("user", "", TypeOfFormMessage::EMPTY);
        $this->validator->addFormMessage("password", "", TypeOfFormMessage::EMPTY);
        $this->validator->addFormMessage('password_again', '', TypeOfFormMessage::EMPTY);
        $this->validator->addFormMessage("street", "", TypeOfFormMessage::EMPTY);
        $this->validator->addFormMessage("building_identification_number", "", TypeOfFormMessage::EMPTY);
        $this->validator->addFormMessage("house_number", "", TypeOfFormMessage::EMPTY);
        $this->validator->addFormMessage("ZIP", "", TypeOfFormMessage::EMPTY);
        $this->validator->addFormMessage("city", "", TypeOfFormMessage::EMPTY);
        $this->validator->addFormMessage("first_name", "", TypeOfFormMessage::EMPTY);
        $this->validator->addFormMessage("last_name", "", TypeOfFormMessage::EMPTY);
        $this->validator->addFormMessage("date_of_birth", "", TypeOfFormMessage::EMPTY);
        $this->validator->addFormMessage("national_id_number", "", TypeOfFormMessage::EMPTY);
        $this->validator->addFormMessage("identity_card_number", "", TypeOfFormMessage::EMPTY);
        $this->validator->addFormMessage("phone", "", TypeOfFormMessage::EMPTY);
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
}
