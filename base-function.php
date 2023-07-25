<?php
function escape_string($var, $escape=false){
	global $db_con;

	$res = null;

	if(isset($var)){
		$res = htmlspecialchars($var);
		if($escape){
			$res = mysqli_real_escape_string($db_con, $res);
		}
	}

	return $res;
}

function escape_map($var){
	global $db_con;

	$res = htmlspecialchars($var);
	$res = mysqli_real_escape_string($db_con, $res);
	return $res;
}

function post($var, $escape=false){
	global $db_con;

	$res = null;

	if(isset($_POST[$var])){
		$res = htmlspecialchars($_POST[$var]);
		if($escape){
			$res = mysqli_real_escape_string($db_con, $res);
		}
	}

	return $res;
}

function get($var, $escape=false){
	global $db_con;
	$res = null;
	if(isset($_GET[$var])){
		$res = htmlspecialchars($_GET[$var]);
		if($escape){
			$res = mysqli_real_escape_string($db_con, $res);
		}
	}
	return $res;
}

function redirect($url){
	print '<script> window.location = "'.BASE_URL.$url.'" </script>';
}

function getIconFile($nama_file)
{
	$ekstensi = explode('.', $nama_file);
	$ekstensi = $ekstensi[1];

	switch ($ekstensi) {
		case 'pdf':
			return '<i class="fa fa-file-pdf-o" aria-hidden="true"></i>';
			break;

		case 'jpg':
			return '<i class="fa fa-file-image-o" aria-hidden="true"></i>';
			break;

		case 'jpeg':
			return '<i class="fa fa-file-image-o" aria-hidden="true"></i>';
			break;

		case 'png':
			return '<i class="fa fa-file-image-o" aria-hidden="true"></i>';
			break;

		default:
			return '<i class="fa fa-file-text-o" aria-hidden="true"></i>';
			break;
	}
}

//Date Format
function dateFormat($date, $format)
{
	$new_date = date_create($date);
	return date_format($new_date, $format);
}

//Fungsi convert angka arab ke romawi
//Sumber: https://stackoverflow.com/questions/14994941/numbers-to-roman-numbers-with-php
function numberToRoman($number) {
    $map = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
    $returnValue = '';
    while ($number > 0) {
        foreach ($map as $roman => $int) {
            if($number >= $int) {
                $number -= $int;
                $returnValue .= $roman;
                break;
            }
        }
    }
    return $returnValue;
}

//Get Max Value from table
function maxOf($id_field, $table){
	global $db_con;
	$sql = "SELECT max($id_field) FROM $table";
	return mysqli_fetch_row( mysqli_query($db_con, $sql) )[0];
}

//Custom DB Connect
function newConn( $nama_database ){ 

	$conn = $GLOBALS['db_custom']; 
	mysqli_select_db( $conn, $nama_database );
	
	return $conn;
}

//Database Model
class model {
	public $conn;
	public $nama_database;
	public $nama_tabel;
	public $list_column;
	public $empty_row;

	function __construct($nama_database = null, $nama_tabel = null, $conn = null) {
		if( is_null($conn) )
		{ 
			$conn = $GLOBALS['db_con']; 
		} 
		$this->conn = $conn;
		$this->nama_database = $nama_database;
		$this->nama_tabel = $nama_tabel;
		$result = mysqli_query( $this->conn,
			"SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='".$nama_database."' AND TABLE_NAME='".$nama_tabel."' ORDER BY ORDINAL_POSITION"
		);
		if($result){
			$kolom = array();
			$empty_row = array();
			while( $row = mysqli_fetch_assoc( $result ) ){
				$kolom[] = $row['COLUMN_NAME'];
				$empty_row[ $row['COLUMN_NAME'] ] = '';
			}
			$this->list_column = $kolom;
			$this->empty_row = $empty_row;
			return $this;
		} else {
			return false;
		}
	}

	function select($condition = null, $mode = null){
		$result = mysqli_query( $this->conn,
			"SELECT * FROM ".$this->nama_tabel." ".$condition
		);
		if($result && $mode != null){
			return mysqli_fetch_all( $result, MYSQLI_ASSOC );
		} else
		if($result){
			return $result;
		} else {
			return false;
		}
	}

	function insert( array $insert = null , array $insert_column = null ){
		if($insert_column == null && count($insert) == count($this->list_column)){
			return mysqli_query( $this->conn,
				"INSERT INTO ".$this->nama_tabel." ( ".implode(", ",$this->list_column)." ) VALUES ('".implode("', '", array_map('escape_map',$insert))."')"
			);
			// return "INSERT INTO ".$this->nama_tabel." ( ".implode(", ",$this->list_column)." ) VALUES ('".implode("', '",$insert)."')";
		}
		else 
		{
			return mysqli_query( $this->conn,
				"INSERT INTO ".$this->nama_tabel." ( ".implode(", ",$insert_column)." ) VALUES ('".implode("', '",$insert)."')"
			);
		}
	}

	function insert_row( $insert = null ){
		return mysqli_query( $this->conn,
			"INSERT INTO ".$this->nama_tabel." VALUES ".$insert
		);
	}
	
	function getInsertId( $qstr = null ){
		return mysqli_insert_id( $this->conn );
	}
	
	function query( $qstr = null ){
		if($qstr != null){
			return mysqli_query( $this->conn, $qstr );
		}
	}

	function update( $condition = null ){
		if($condition != null){
			$query = "UPDATE ".$this->nama_tabel." SET ".$condition;
			return mysqli_query( $this->conn, $query );
		}
	}

	function updateKolom( $value = null, $namakolom = null, $condition = null ){
		if($namakolom != null && $value != null){
			$query = "UPDATE ".$this->nama_tabel." SET ".$namakolom." = '".$value."' ";
			if($condition != null){
				$query .= " WHERE ".$condition;
			}
			return mysqli_query( $this->conn, $query );
		}
	}

	function updateRow( array $update = null, array $kolom = null ){
		if($kolom != null){
			if( count($update) == count($kolom) ){
				$query = "UPDATE ".$this->nama_tabel." SET ";
				foreach ($kolom as $index => $namakolom){
					if($index>0){
						$query .= $namakolom." = '".$update[$index]."', ";
					}
				}
				$query = substr($query, 0, -2);
				$query = $query." WHERE ".$kolom[0]." = '".$update[0]."'";
				return mysqli_query( $this->conn, $query);
			}
		} else
		if( count($update) == count($this->list_column) ){
			$query = "UPDATE ".$this->nama_tabel." SET ";
			foreach ($this->list_column as $index => $value){
				if($index>0){
					$query .= $value." = '".$update[$index]."', ";
				}
			}
			$query = substr($query, 0, -2);
			return mysqli_query( $this->conn, $query." WHERE ".$this->list_column[0]." = '".$update[0]."'");
		}
	}

	function remove( array $column_value = null, $column_name = null ){
		if($column_name != null){
			// [ 'value' , 'value' ], "nama_kolom"
			$value_list = "( '" . implode( "', '" , $column_value ) . "' )";
			return mysqli_query( $this->conn,
				"DELETE FROM ".$this->nama_tabel." WHERE ".$column_name." IN ".$value_list
			);
		} else
		if($column_value != null){
			// [ 'namakolom' , 'value' ]
			return mysqli_query( $this->conn,
				"DELETE FROM ".$this->nama_tabel." WHERE ".$column_value[0]." = '".$column_value[1]."'"
			);
		}
	}

	function removeId($id = null){
		if($id != null){
			return mysqli_query( $this->conn,
				"DELETE FROM ".$this->nama_tabel." WHERE ".$this->list_column[0]." = '".$id."'"
			);
		}
	}

	function finds( array $column_value = null, $column_name = null, $mode = null ){
		if(is_null($column_value)){
			$result = mysqli_query( $this->conn,
				"SELECT * FROM ".$this->nama_tabel
			);
			if($result && $mode != null){
				return mysqli_fetch_all( $result, MYSQLI_ASSOC );
			} else
			if($result){
				return $result;
			} else {
				return false;
			}
		} else
		if( is_array( $column_name ) ){
			$condition = "";
			foreach($column_name as $index => $kolom){
				$value_list = "( '" . implode( "', '" , $column_value[$index] ) . "' )";
				$condition .= $kolom." IN ".$value_list." AND ";
			}
			$condition = substr($condition, 0, -4);
			$result = mysqli_query( $this->conn,
				"SELECT * FROM ".$this->nama_tabel." WHERE ".$condition
			);
			if($result && $mode != null){
				return mysqli_fetch_all( $result, MYSQLI_ASSOC );
			} else
			if($result){
				return $result;
			} else {
				return false;
			}
		} else
		if($column_name != null){
			$value_list = "( '" . implode( "', '" , $column_value ) . "' )";
			$result = mysqli_query( $this->conn,
				"SELECT * FROM ".$this->nama_tabel." WHERE ".$column_name." IN ".$value_list
			);
			if($result && $mode != null){
				return mysqli_fetch_all( $result, MYSQLI_ASSOC );
			} else
			if($result){
				return $result;
			} else {
				return false;
			}
		}
	}

	function findsId( $id = null, $mode = null ){
		if($id != null){
			$result = mysqli_query( $this->conn,
				"SELECT * FROM ".$this->nama_tabel." WHERE ".$this->list_column[0]." IN (".$id.")"
			);
			if($result && $mode != null){
				return mysqli_fetch_all( $result, MYSQLI_ASSOC );
			} else
			if($result){
				return $result;
			} else {
				return false;
			}
		}
	}

	function findsIdByArray( array $id = null, $mode = null ){
		if($id != null){
			$value_list = "( '" . implode( "', '" , $id ) . "' )";
			// return "SELECT * FROM ".$this->nama_tabel." WHERE ".$this->list_column[0]." IN ".$value_list;
			$result = mysqli_query( $this->conn,
				"SELECT * FROM ".$this->nama_tabel." WHERE ".$this->list_column[0]." IN ".$value_list
			);
			if($result && $mode != null){
				return mysqli_fetch_all( $result, MYSQLI_ASSOC );
			} else
			if($result){
				return $result;
			} else {
				return false;
			}
		}
	}

}
?>