<?php
    class Paginator{
        //private int $modifiedLimit;
        private int $totalPages;
        private int $totalItems;
        private int $from;
        private int $to;
        private int $limit;
        private string $url;
        public function __construct(string $url,int $totalItems, int $from=0, int $to=0){
            $this->url = $url;
            
            
            //pokud je do menší než do tak je prohodíme
            if($to < $from){
                $auxiliary = $from;
                $from = $to;
                $to = $auxiliary;
            }                        
            $this->to = $to;
            $this->limit = $to - $from+1;
            
            if(!is_numeric($from)){
                $from=0;
            }
            //$from=($from<0)?($from+$this->limit):$from;
            $this->from = $from;            
            ($from<0-$this->limit)?0:$from;
            ($to<0)?0:$to;
            if($from<0) $from += $this->limit;
            $this->totalItems = $totalItems;
            echo "from: ".$from."<br>";
            $page1=$from % $this->limit;
            echo "1. strana : ".$page1."<br>";
            echo "limit: ".$this->limit."<br>";
            echo "položek: ".$this->totalItems."<br>";
            $totalPages=ceil($totalItems / $this->limit);
            $modulo = $totalItems % $this->limit;
            echo "Položek navíc:".$modulo."<br>";
            if($modulo != $page1)
                    $totalPages++;
            
    

            $this->totalPages=$totalPages;
            echo "<br>počet stran: ". $totalPages;
            /*
            echo "Počet stránek bez první a poslední:". $totalPages=floor($totalItems / $this->limit);
            echo "<br>položek na první straně: ".$startOverflow = ($this->from) % $this->limit;
            echo "<br>položek na poslední straně: ".$endOverflow = $this->totalItems-($this->limit*$totalPages)-$startOverflow;
            //($startOverflow > 0 )? $startOverflow:0;
            //($endOverflow > 0 )? $startOverflow:0;
            echo "<br>Počáteční přetečení: ".$startOverflow."<bt> Přetečení na konci:". $endOverflow.
            "<br> limit:". $this->limit."<br> počet položek:". $totalItems."<br> počet položek / limit: ".($this->totalItems / $this->limit);
            
            $this->totalPages = ceil($totalItems/$this->limit);
            echo "<br>podmínka: ". $endOverflow - $startOverflow;
            if ($endOverflow + $startOverflow==0){
                echo"<br> stránek celkem: ".$this->totalPages;
                $this->totalPages++;
            }
            echo"<br> stránek celkem: ".$this->totalPages;
                
           */
            
        }
       
        public function GeneratePagination(int $radius) : array
        {
            $pagination=array();
            $current = $this->getCurrent();
            //if($this->to-$this->from+1>=$this->totalItems){
            if($this->totalPages == 1){
                return $pagination;
            }
            //Minimum a maximum poloměru $radius
            $radiusMin = $current-$radius;
            $radiusMax = $current+$radius;
            
            //šipka zpět
            $pagination[0]["label"] = "<";
            //Pokud je aktuální stránka první.
            if($current==1){
                $pagination[0]["class"] = "disabled";
                $pagination[0]["url"] = "javascript: false";
                $pagination[0]["tabindex"] = -1;
            }else{
                $pagination[0]["class"] = "";
                $pagination[0]["url"] = $this->generateUrlParameters($current-1);                
                $pagination[0]["tabindex"] = 0;
            }
            //strana 1
            $pagination[1]["label"] = "1";    
            //Pokud strana 1 je aktuální strana.
            if (1==$current){
                $pagination[1]["class"] = "active";  
                $pagination[1]["url"] = "javascript: false";
                $pagination[1]["tabindex"] = -1;
            }else{
                $pagination[1]["class"] = "";  
                $pagination[1]["url"] = $this->generateUrlParameters(1);
                $pagination[1]["tabindex"] = 0;
            }        
            //tečky pokud je minimum poloměru větší než 2(tedy je za šipkou a první stránkou). 
            if($radiusMin>2){
                $pagination[2]["label"]="...";
                $pagination[2]["class"]="disabled";
                $pagination[2]["url"]="javascript: false";
                $pagination[2]["tabindex"] = -1;
            }       
            //klíč pole pagintion bude pokračovat trojkou pokud jsou tečky zobrazeny jinak dvojkou;
            $key = (isset($pagination["2"]))?3:2;
            //stránky které jsou v rozsahu poloměru $radius      
            for($i=$radiusMin;$i<=$radiusMax;$i++){
                if($i>1 && $i<=$this->totalPages-1){
                    //strany v rozsahu poloměru     
                    $pagination[$key]["label"]=$i;
                    //pokud je strana aktuální
                    if($i==$current){
                        $pagination[$key]["class"]="active";
                        $pagination[$key]["url"]="javascript: false";
                        $pagination[$key]["tabindex"] = -1;
                    }else{
                        $pagination[$key]["class"]="";
                        $pagination[$key]["url"]=$this->generateUrlParameters($i);
                        $pagination[$key]["tabindex"] = 0;
                    }
                    //klíč se o jedna zvýší
                    $key++;
                }
            }              
            //Tčeky pokud je maximum poloměru menší než předposlení strana.
            if($radiusMax<$this->totalPages-1){ 
                $pagination[$key]["label"] = "...";
                $pagination[$key]["class"]="disabled";
                $pagination[$key]["url"]="javascript: false";
                $pagination[$key]["tabindex"] = -1;
                //Klíč zvýšíme o jedničku protože tečky vytvořili další položku.
                $key++;
            }
            
            //poslední strana
            $pagination[$key]["label"] = $this->totalPages;
            //Pokud je poslední strana aktuální.
            if ($this->totalPages==$current){
                $pagination[$key]["class"]="active";
                $pagination[$key]["url"]="javascript: false";
                $pagination[$key]["tabindex"] = -1;
            }else{
                $pagination[$key]["class"]="";
                $pagination[$key]["url"]=$this->generateUrlParameters($this->totalPages);
                $pagination[$key]["tabindex"] = 0;
            }
            //Klíč zvedneme o 1;
            $key++;
            //šipka vpřed
            $pagination[$key]["label"] = ">";
            //Pokud je poslední položka aktuální
            if($current == $this->totalPages){
                $pagination[$key]["class"] = "disabled";
                $pagination[$key]["url"] = "javascript: false";
                $pagination[$key]["tabindex"] = -1;
            }else{
                $pagination[$key]["class"] = "";
                $pagination[$key]["url"] = $this->generateUrlParameters($current+1);
                $pagination[$key]["tabindex"] = 0;
            }
            return $pagination;
        }
 
        /** Funkce dostane stránku a vygeneruje nám parametri do url tedy od kolikátéhho záznamu a kolik jich zobrazit. 
         *  @param int $page číslo stránky
         *  @return string $urlParameters
         */
        private function generateUrlParameters(int $page) : string
        {
            $limit = $this->limit;
            $url = $this->url;
            $modulo = $this->from % $limit;
            if($modulo==0){
                $from = $page * $limit-$limit;
            }else{
                if($this->from<0){
                    $from = $page * $limit-$limit + $modulo;
                }else{
                    $from = $page * $limit-2*$limit + $modulo;
                }
            }
            $to = $from + $limit-1;
                        
            $url = $url."/".$from."/".$to;
            return $url; 
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
