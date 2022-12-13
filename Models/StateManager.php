<?php
    class StateManager{
        public function getState($stateId){
            return Mysql::oneRow("SELECT * FROM state WHERE state_id = ?", array($stateId));
        }
        public function getStates(){
            return Mysql::moreRows("SELECT * FROM state");
        }
    }