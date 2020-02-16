<?
/**
 * This Class is for db connection to whole site 
 *
 * @package		class.dbquery.php
 * @section		general
**/

class DBConnection 
{
	private $DBASE="";
	private $CONN="";


	/**
	* @access	public
	* @check database connection
	* @return	true/false
	*/
	public function DBConnection($server="",$dbase="", $user="", $pass="") 
	{
		$this->DBASE = $dbase;
		$conn = mysql_connect($server,$user,$pass);
		if(!$conn) {
			$this->MySQLDie("Connection attempt failed");
		}
		if(!mysql_select_db($dbase,$conn)) {
			$this->MySQLDie("Dbase Select failed");
		} 
		$this->CONN = $conn;
		mysql_query("SET NAMES 'utf8'",$conn);
    mysql_query("SET character SET 'utf8'",$conn);
    mysql_set_charset('UTF8',$conn);
    //mb_internal_encoding("UTF-8"); 
		return true;
	
	}

	/**
	* @access	public
	* @Close Database connection
	* @return	true/false
	*/
	public function MySQLClose()
	{
		$conn = $this->CONN ;
		$close = mysql_close($conn);
		if(!$close) {
			$this->MySQLDie("Connection close failed");
		}
		return true;
	}

	/**
	* @access	private
	* @Set Message for Die
	* @return	Message
	*/
	private function MySQLDie($text)
	{
		die($text);
	}

	/**
	* @access	public
	* @Retrive  Records
	* @param 	$sql query
	* @return	array
	*/
	public function MySQLSelect($sql="",$cached="")
	{	
		if(empty($sql)) { return false; }
		/*if(!eregi("^select",$sql))
		{
			echo "wrongquery<br>$sql<p>";
			echo "<H2>Wrong function silly!</H2>\n";
			return false;
		}*/
		if(empty($this->CONN)) { return false; }
		$conn = $this->CONN;
		$results = mysql_query($sql,$conn);
		if( (!$results) or (empty($results)) ) {
			return false;
		}
		$count = 0;
		$data = array();
		while ($row = mysql_fetch_assoc($results))
		{
			$data[$count] = $row;
			$count++;
		}
		mysql_free_result($results);
		return $data;
	}

	/**
	* @access	public
	* @get all fields from table 
	* @param 	$table name
	* @return	all fields
	*/
	public function MySQLGetFields($table)
	{
		$fields = mysql_list_fields($this->DBASE, $table, $this->CONN); 
		$columns = mysql_num_fields($fields); 
		for ($i = 0; $i < $columns; $i++) { 
		   $arr[]= mysql_field_name($fields, $i); 
		}
		return $arr;
	}
	/**
	* @access	public
	* @get all fields from table 
	* @param 	$table name
	* @return	all fields
	*/

	public function MySQLGetFieldsQuery($table,$primarykey='Yes')
	{
		$fields = mysql_list_fields($this->DBASE, $table, $this->CONN);

		$columns = mysql_num_fields($fields);

		for ($i = 0; $i < $columns; $i++)
		{
			if($primarykey=='Yes')
			{
				if($arrF !='')
						$arrF.= ",";
					
				$arrF.= mysql_field_name($fields, $i);
			}
			elseif($primarykey=='No')
			{
				if(!stristr(mysql_field_flags($fields, $i),'primary_key'))		
				{
					if($arrF !='')
						$arrF.= ",";
					
					$arrF.= mysql_field_name($fields, $i);
				}
			}
		}
		return $arrF;
	}
	
	
	/**
	* @access	public
	* @insert update/Query
	* @param 	$table name
	* @return	all fields
	*/
	public function MySQLQueryPerform($table, $data, $action = 'insert', $parameters = '')
	{
		$conn = $this->CONN;
		reset($data);
	    if ($action == 'insert'){$query = 'insert into ' . $table . ' (';while (list($columns, ) = each($data)) {
	        $query .= $columns . ', ';
	    }
	    $query = substr($query, 0, -2) . ') values (';reset($data);
		while (list(, $value) = each($data))
		{
			switch ((string)$value) {
			case 'null':
				$query .= 'null, ';
		    break;
		    default:
		    	$query .= '\'' . $this->cleanQuery($value) . '\', ';
		    	break;
		     }
	    }

		$query 		= substr($query, 0, -2) . ')'; //Insert Query ready
		
		$results 	= @mysql_query($query,$conn) or die("Query failed: " . mysql_error());
		$results 	= mysql_insert_id();
		if(!$results)
		   {
			 $this->MySQLDie("Query went bad!");
			 return false;
		   }
	    }
		elseif ($action == 'update')
		{
	      $query = 'update ' . $table . ' set ';
	      while (list($columns, $value) = each($data))
		  {
	        switch ((string)$value)
			{
	          case 'null':
	            $query .= $columns .= ' = null, ';
	             break;
	          default:
	            $query .= $columns . ' = \'' .$this->cleanQuery($value). '\', ';
	            break;
	        }
	     }
	    $query = substr($query, 0, -2) . ' where ' . $parameters; //Update Query ready
		//echo $query;exit;
		$results = @mysql_query($query,$conn) or die("Query failed: " . mysql_error());
		  if(!$results)
		  {
			 $this->MySQLDie("Query went bad!");
			 return false;
		  }
	    }
		return $results;
	}
	
	/**
	* @access	public
	* @Delete
	* @param 	$table,$where
	* @return	$query
	*/
	public function MySQLDelete( $table, $where)
	{
		$query = "DELETE FROM `$table` WHERE  $where";
		//echo $query;exit;
		$conn = $this->CONN;

		// or MySQLDie("DELETE ERROR ($query): " . mysql_error() )

		if( $conn )
			return mysql_query($query, $conn);
		return $query;
	}
	
	/**
	**/
	/*public function Getfieldtype($table,$field)
	{
		$data = array();
		if(empty($table)) { return false; }
		if(empty($this->CONN)) { return false; }
		$conn = $this->CONN;
		$sql = "select * from ".$table;
		$results = mysql_query($sql,$conn) or die(mysql_error()."query fail");
		
		if(!$results)
		{   $message = "Query went bad!";
			$this->error($message);
			return false;
		}
		$i = 0;
		while ($i < mysql_num_fields($results)) 
		{
		    $meta = mysql_fetch_field($results,$i);
			echo $meta->name;
			echo $meta->type;
			echo "</br>";
			
			/*if ($meta->name == $field)
			{
				$data[name]=$meta->name;
				$data[type]=$meta->type;
			}
			$i++;
		}
		if($data)
		{
			return $data;
		}
		else
		{
			return false;
		}
	}*/
	
	/**
	* @access	public
	* @Perform the query action
	* @param 	$sql;
	* @return	$data;
	*/
	
	public function sql_query($sql="")
	{	if(empty($sql)) { return false; }
		if(empty($this->CONN)) { return false; }
		$conn = $this->CONN;
		$results = mysql_query($sql,$conn) or die(mysql_error()."query fail");
		if(!$results)
		{   $message = "Query went bad!";
			$this->error($message);
			return false;
		}
		 $sql;
		if(strpos($sql, 'select') === false) {
			return true; }
		else {
			$count = 0;
			$data = array();
			while ( $row = mysql_fetch_array($results))	{
				$data[$count] = $row;
				$count++;
			}
			mysql_free_result($results);
			return $data;
	 	}
	}
	
	public function MySQLInsert ($sql="")
	{
		if(empty($sql)) { return false; }
		if(strpos(strtolower($sql), 'insert') === false)
		{
			return false;
		}
		if(empty($this->CONN))
		{
			return false;
		}
		$conn = $this->CONN;
		$results = mysql_query($sql,$conn);
		if(!$results) 
		{
			$this->error("<H2>No results!</H2>\n");
			return false;
		}
		$id = mysql_insert_id();
		return $id;
	}
	
	
	/**
	* @access	public
	* @insert  Query
	* @param 	$table name
	* @return	all fields
	*/
	public function MySQLInsertPerform($table, $data, $action = 'insert', $parameters = '')
	{
		$conn = $this->CONN;
		reset($data);
	    if ($action == 'insert'){$query = 'insert into ' . $table . ' (';while (list($columns, ) = each($data)) {
	        $query .= $columns . ', ';
	    }
	    $query = substr($query, 0, -2) . ') values (';reset($data);
		while (list(, $value) = each($data))
		{
			switch ((string)$value) {
			case 'null':
				$query .= 'null, ';
		    break;
		    default:
		    	$query .= '\'' . $value . '\', ';
		    	break;
		     }
	    }

		$query 		= substr($query, 0, -2) . ')'; //Insert Query ready
		$results 	= @mysql_query($query,$conn) or die("Query failed: " . mysql_error());
		if(!$results)
		   {
			 $this->MySQLDie("Query went bad!");
			 return false;
		   }
	    }
		return $results;
	}
	
	public function cache_array_new($query) {
	
    global $dbobj,$TIME_ELAPSE;
    
    $TIME_ELAPSE = !isset($TIME_ELAPSE) ? 10000:$TIME_ELAPSE;
    
    $filename = SPATH_ROOT."/cache_files/".md5($query).".txt";
      if (!file_exists($filename)) {
      	$content=	$this->MySQLSelect($query,"No");//Result array set of $array=$db->query($query, "query");
        
        if (!$handle = fopen($filename, 'w+')) {	//If File is not exists than attemp to create it
      		echo "not created";
      		exit();	
      	}
      	$content_file	=	serialize($content);
      	if (fwrite($handle, $content_file) === FALSE) {
      		echo "permision denied or file not exists";
      		exit();	
      	}
      	chmod($filename,0777);
      	fclose($handle);
      } else {
      	
        $time = filemtime($filename);
       	$time = $time + $TIME_ELAPSE;
      	$curTime = strtotime("now");
      	/*echo $curTime." < ".$time;
      	echo "<hr>";
      	echo $curTime < $time;*/
      	//echo "<pre>";
        if($curTime < $time) { 
          
        	if (!$handle = fopen($filename, 'r')) {	//If File exists than attemp to create it
        	
        		echo "not created";
        	
        		exit();	
        	}
        	$content	=	fread($handle, filesize($filename));
        	$content	=	unserialize($content);
        	//var_dump($content);
      	} else {
      	   $content=	$this->MySQLSelect($query,"No");	//Result array set of $array=$db->query($query, "query");
      
        	if (!$handle = fopen($filename, 'w+')) {	//If File is not exists than attemp to create it
        		echo "not created";
        		exit();	
        	}
        
        	$content_file	=	serialize($content);
        
        	if (fwrite($handle, $content_file) === FALSE) {
        		echo "permision denied or file not exists";
        		exit();	
        	}
        	chmod($filename,0777);
        	fclose($handle);
      	
        }
      }
    return $content;
    }
	
	function cleanQuery($string)
{
  if(get_magic_quotes_gpc())  // prevents duplicate backslashes
  {
    $string = stripslashes($string);
  }
  if (phpversion() >= '4.3.0')
  {
    $string = mysql_real_escape_string($string);
  }
  else
  {
    $string = mysql_escape_string($string);
  }
  return $string;
}

public function MyTokenId($tokenid)
	{
		echo '+++++++';
	} 
}
?>
