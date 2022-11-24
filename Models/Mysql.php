<?php            
  class Mysql{
    public static PDO $connection; //Proměnná Která uchovává spojení.
    //Proměnná uchovává nastavení databáze.
    private static array $settings = array(
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, //Chyby se zpracují jako výjimky.
      PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",//Kódování bude utf8.
      PDO::ATTR_EMULATE_PREPARES => false,//Parametry do dotazů bude vkládat databáze. Je to rychlejší a bespečnější.                  
    );
    
    //Funkce která vytvoří spojení.
    public static function connect(string $host, string $user, string $password, string $databaze) : void
    {
      if (!isset(self::$connection)){
        self::$connection = @new PDO(
          "mysql:host=$host;dbname=$databaze",
          $user,
          $password,
          self::$settings
        );
      }
    }
    //Funkce pro dotaz vracející jeden řádek tabulky.
    public static function oneRow(string $query, array $parameters = array()) : array|bool{ 
      $return = self::$connection->prepare($query);
      $return->execute($parameters);
      return $return->fetch();
    }
    //Funkce pro dotaz vracející více řádků tabulky.
    public static function moreRows(string $query, array $parameters = array()) : array|bool{
      $return = self::$connection->prepare($query);
      $return->execute($parameters);
      return $return->fetchAll();
    }
    //Funkce pro vložení dotazu, který vrací jen jednu hodnotu. 
    public static function oneValue(string $query, array $parameters = array()) : string{
      $return = self::oneRow($query, $parameters);
      return $return[0];
    } 
    //Funkce pro vložení dotazu upravujícího tabulku. Vrací počet ovlivněných řádků.
    public static function edit(string $query, array $parameters = array()){      
      $return = self::$connection->prepare($query);
      $return->execute($parameters);
      return $return->rowCount();
    }

    public static function delete(string $table, $parameters) : int
    {
      return self::edit("DELETE FROM ".$table." WHERE ".implode(" = ?, ", array_keys($parameters))." = ?", array_values($parameters));
      
      
    }
    public static function insert(string $table, array $parameters = array()) : bool
    {
        return self::edit("INSERT INTO `$table` (`".
            implode('`, `', array_keys($parameters)).
            "`) VALUES (".str_repeat('?,', sizeOf($parameters)-1)."?)",
                array_values($parameters));
    }

    public static function update(string $table, array $parameters = array(), string $condition = "", array $parametersOfCondition = array()){      
      //Pokud je první parametr pole,
        
      $query="update `".$table."` set `";
      $query.=implode("` = ? , `", array_keys($parameters))."` = ? ";
      $query.="where ".$condition.";";
      $parameters=array_merge(array_values($parameters), $parametersOfCondition);
      self::edit($query, $parameters);
      
    } 

    /**začne transakci*/
    public static function startTransaction() : void
    {
      self::$connection->beginTransaction(); 
    }
    /**commitne transakci */
    public static function commit() : void 
    {
      self::$connection->commit(); 
    }
    public static function multiUpdate(string $table, array $param){
      //print_r($data);
      //pokud vkládáme více než jeden řádek.
      /*if(is_array($param["id"])){
        
        //Zjistí počet vkládaných záznamů
        $count_rows=count($param["id"]);
        //Zjistí jestli jsou všechna pole stejně dlouhá
        foreach($param as $col_name=>$data){          
          if(count($data)!=$count_rows){
            die "chyba";
          }                                    
        }*/
          $data="";
        foreach($param as $row){          
          $data.=implode("-$-",$row)."-$-";
          //$data=array_merge($data, $r);
          $rows[]="(".str_repeat("?, ",count($row)-1)."?)";
          
          
          
          //print_r($row);
          //echo"<br>";      
          //print_r($data);   
          //echo("<br> spojeno");
          
        } 
        $data=explode("-$-",$data);       
        //odebere z pole poslední položku.
        array_pop($data);
        //Do pole $col uloží názvy sloupců tabulky.
        $col=array_keys(reset($param));
        
        $string="";                                        
        foreach($col as $name){
          $string.=$name."=VALUES(".$name."), ";
        }
        
        $string=rtrim($string, ", ");
        $dotaz="INSERT INTO `".$table."` ";
        $dotaz.="(`".implode("`, `", $col)."`) ";
        $dotaz.="VALUES ".implode(", ", $rows)." ";
        $dotaz.="ON DUPLICATE KEY UPDATE ".$string.";";
        //echo "<br>".$dotaz."<br>";
        //echo "<br>".$data[0]."<br>";
        //echo "<br>".$data[7]."<br>";
        //print_r($data);
        self::edit($dotaz, $data);      
      /*}else{
      
      } */     
    }    
    /**Vrátí id posledního ovlivněného záznamu. */       
    public static function lastId() : int
    {
        return self::$connection->lastInsertId();
    }
  }      