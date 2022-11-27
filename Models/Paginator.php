<?php
    class Paginator{
        //private int $modifiedLimit;
        private int $totalPages;
        private int $totalItems;
        private int $from;
        private int $to;
        private int $limit;
        public function __construct(int $totalItems, int $from=0, int $to=0){
            //pokud je do menší než do tak je prohodíme
            if($to < $from){
                $auxiliary = $from;
                $from = $to;
                $to = $auxiliary;
            }                        
            $this->to = $to;
            $this->limit = $to - $from + 1;
            
            if(!is_numeric($from)){
                $from=0;
            }
            
            $this->from = $from;            
            $this->totalItems = $totalItems;
            if($this->from % $this->limit==0){
                $this->totalPages = ceil($totalItems/$this->limit);
            }else{
                $this->totalPages = $totalItems/$this->limit+1;
            }
            
        }
       
        public function GeneratePagination(int $radius) : array
        {
            
            $current = $this->getCurrent();
            if($this->to-$this->from>=$this->totalItems){
                return array();
            }
            $min = $current-$radius;
            $max = $current+$radius;
            
            $pagination=array();
            $pagination["&laquo;"] = ($current==1)?"disabled":$this->generateUrlParameters($current-1);
            $pagination["1"] = (1==$current)?"active":$this->generateUrlParameters(1);            
            if($min>2) $pagination["left_dots"]="...";
            for($i=$min;$i<=$max;$i++){
                if($i>1 && $i<=$this->totalPages-1){
                    $pagination[(string)$i] = ($i==$current)?"active":$this->generateUrlParameters($i);
                    
                }
            }              
            if($max<$this->totalPages-1) $pagination["right_dots."] = "...";
            $pagination[(string)$this->totalPages] = ($this->totalPages==$current)?"active":$this->generateUrlParameters($this->totalPages);
            $pagination["&raquo;"] = ($this->totalPages==$current)?"disabled":$this->generateUrlParameters($current+1);
            return $pagination;
        }
 
        /** Funkce dostane stránku a vygeneruje nám parametri do url tedy od kolikátéhho záznamu a kolik jich zobrazit. 
         *  @param int $page číslo stránky
         *  @return string $urlParameters
         */
        private function generateUrlParameters(int $page) : string
        {
            $limit = $this->limit;
            
            $modulo = $this->from % $limit;
            if($modulo==0){
                $from = $page * $limit-$limit;
            }else{
                if($this->from<0){
                    $from = $page * $limit-$limit + $modulo;
                    echo $from." ";
                }else{
                    $from = $page * $limit-2*$limit + $modulo;
                }
            }
            $to = $from + $limit-1;
                          
            $urlParameters = $from."/".$to;
            return $urlParameters; 
        }
        public function getCurrent() : int 
        {  
           return ceil($this->from/$this->limit)+1;
        }
        public function getLimit() : int
        {      
            $from = ($this->from<0)?0:$this->from;
            $to = ($this->to>$this->totalItems)?$this->totalItems:$this->to;
            $limit=$to-$from+1;
            return $limit;
        }
        public function getFrom() : int
        {
            return ($this->from<0)?0:$this->from;
        }
    }
