<?php
class GeneratorController extends Controller
{
    public function work(array $parameters) : void
    {
        $userManager = new UserManager();
        for ($i = 0; $i < 100; $i++) {
            $user = array(
                "user" => "dddd" . $i,
                "password" => "ccc" . $i,
            );
            $address = array(
                "street" => "aad" . $i,
                "building_identification_number" => $i,
                "house_number" => $i,
                "ZIP" => $i,
                "city" => "dddsfa" . $i
            );
            $person = array(
                "first_name" => "aaddsss" . $i,
                "last_name" => "pdsfadf46" . $i,
                "date_of_birth" => "2525-55-15",
                "identity_card_number" => $i,
                "national_id_number" => $i,
                "phone" => "486 486" . $i,
                "mail" => $i . "@seznam.cz"
            );
            $userManager->register($user, $address, $person);
        }
    }
}
