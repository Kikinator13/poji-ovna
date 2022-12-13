<?php
    class AreaCodeManager{
        public function getAreaCode($areaCodeId, ...$columns){
            $columns = implode(", ", $columns);
            return Mysql::oneRow("SELECT ".$columns." FROM area_code 
            WHERE area_code_id = ?", 
            array($areaCodeId), PDO::FETCH_ASSOC);
        }
        public function getAreaCodes(){
            return Mysql::moreRows("SELECT * FROM area_code");
        }
    }