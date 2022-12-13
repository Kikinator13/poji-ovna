<?php
class Formater{
    //Naformátuje PSČ
    public static function formatZIP(string $ZIP) : string
    {
        return mb_substr($ZIP, 0, 3) . " " .mb_substr($ZIP, 3);
    }
    //Naformátuje telefonní číslo.
    public static function formatPhoneNumber(string $phone) : String
    {
        if (mb_strlen($phone)==9)
            return mb_substr($phone,0,3)." ".
            mb_substr($phone,3,3)." ".
            mb_substr($phone,6,3)." ";
            
        else{
            return mb_substr($phone,0,4)." ".
                mb_substr($phone,4,2)." ".
                mb_substr($phone,6,2);
        }
    }
    //Naformátuje rodné číslo
    public static function formatNationalIdNumber(string $nationalIdNumber) : string
    {
        $beforeSlash=mb_substr($nationalIdNumber, 0,6);
        $afterSlash=mb_substr($nationalIdNumber, 6,4);
        return $beforeSlash."/".$afterSlash;
    }
}