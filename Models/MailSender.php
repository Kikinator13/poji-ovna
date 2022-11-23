<?php
    class MailSender
    {

        /**
         * Odešle email jako HTML, lze tedy používat základní HTML tagy a nové
         * řádky je třeba psát jako <br /> nebo používat odstavce. Kódování je
         * odladěno pro UTF-8.
         * @param string $to E-mailová adresa příjemce
         * @param string $subject Předmět e-mailu
         * @param string $message Obsah e-mailu jako HTML řetězec
         * @param string $from E-mailová adresa odesílatele
         * @return bool TRUE, pokud se odeslání e-mailu podařilo, FALSE, pokud ne
         */
        public function sendMail(string $to, string $subject, string $message, string $from) : bool
        {
            $head = "From: " . $from;
            $head .= "\nMIME-Version: 1.0\n";
            $head .= "Content-Type: text/html; charset=\"utf-8\"\n";
            if (!mb_send_mail($to, $subject, $message, $head))
                throw new ChybaUzivatele('Email se nepodařilo odeslat.');
        }

        public function checkAntispam(string $year, string $to, string $subject, string $message, string $from) : void
        {
            if ($year != date("Y"))
                throw new UserErrorsException('Chybně vyplněný antispam.');
            $this->send($to, $subject, $message, $from);
        }
    }