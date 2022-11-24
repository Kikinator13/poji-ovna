<?php
    class RegistrationController extends Controller
    {
        public function work(array $parameters) : void
        {
            // Hlavička stránky
            $this->head['title'] = 'Registrace';
            $userManager = new UserManager();            
            
            if(!empty($parameters[0]) && $parameters[0] == 'delete'){
                $this->userVerify(true);
                if(!empty($parameters[1])){
                    try{
                        $userManager->deletePerson($parameters[1]);
                    }catch(UserException $error)
                    {
                        $this->addMessage($error->getmessage(), TypeOfMessage::ERROR);
                        $this->redirect("admin");
                    }
                    
                    $this->addmessage("Uživatel byl úspěšně smazán", TypeOfMessage::SUCCESS);
                    $this->redirect("admin");
                    
                }
                $this->addmessage("Je třeba zadat id osoby, která má být smazána!", TypeOfMessage::ERROR);
                $this->redirect("admin");
                
            }
            
            if ($_POST) //Pokud byl formulář již odeslán
            {               
                //Zjistíme zda-li je vše zadáno správně
                $captcha = $userManager->captchaValidation();
                $agreement = $userManager->agreementValidation();
                $user = $userManager->userValidation();
                $address = $userManager->addressValidation();
                $person = $userManager->personValidation();

                if($captcha&&$agreement&&$user&&$address&&$person){
                    try
                    {
                        $userManager->register($user, $address, $person);
                        $userManager->logIn($_POST['user'], $_POST['password']);
                    }
                    catch (UserException $error)
                    {
                        $this->addMessage($error->getMessage(), TypeOfMessage::ERROR);
                        
                    }
                    $this->addMessage('Byl jste úspěšně zaregistrován.', TypeOfMessage::SUCCESS);
                    $this->redirect('admin');
                }else{
                    $this->addMessage("Jedno nebo více polí nejsou vyplněny správně!", TypeOfMessage::ERROR);
                    $this->data['form_messages'] = $userManager->getFormMessages();
                    
                }
            }else{ //Pokud formulář nebyl ještě odeslán
                
                //Nastavýme prázené řetězce na proměnné, které vypisuje formulář. (Validační hlášky a zadaná data)
                $userManager->clearForm();
                $this->data['form_messages'] = $userManager->getFormMessages();
                
                    
            }
            // Nastavení šablony
            $this->view = 'registration';
        }

         
    }