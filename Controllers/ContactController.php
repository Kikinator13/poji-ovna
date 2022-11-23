<?php

class ContactController extends Controller
{
    public function work(array $parameters): void
    {
        $this->head = array(
            'title' => 'Kontaktní formulář',
            'keyWords' => 'kontakt, email, formulář',
            'description' => 'Kontaktní formulář našeho webu.'
        );
        if ($_POST) {
            try {
                $mailSender = new MailSender();
                $mailSender->checkAntispam($_POST['year'], "martin@kikta.cz", "Email z webu", $_POST['message'], $_POST['email']);
                $this->addMessage('Email byl úspěšně odeslán.');
                $this->redirect('contact');
            } catch (UserException $error) {
                $this->addMessage($error->getMessage());
            }
        }
        $this->view = 'contact';
    }
}
