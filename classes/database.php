<?php

mysql_connect(DATABASE_HOSTNAME, DATABASE_USERNAME) or mysql_error(); // loob ühenduse mysql serveriga
mysql_select_db(DATABASE_DATABASE) or mysql_error(); // ühendus andmebaasiga
mysql_query("SET NAMES 'utf8'"); // päringud mis saadab on utf8 kodeeringus, et server saaks aru
mysql_query("SET CHARACTER 'utf8'");

function q($sql, & $query_pointer = NULL, $debug = false){
	if ($debug){
		print "<pre>$sql</pre>";
	}
	$query_pointer = mysql_query($sql) or mysql_error();
	switch (substr($sql, 0, 4)){
		case 'SELE':
			return mysql_num_rows($query_pointer);
		case 'INSE':
			return mysql_insert_id();
		default:
			return mysql_affected_rows();
	}
}
function get_one($sql, & $query_pointer = NULL, $debug = false){
	if ($debug){ // kui debug on TRUE
		print "<pre>$sql</pre>";
	}
	$query_pointer = mysql_query($sql) or mysql_error();
	switch (substr($sql, 0, 4)){
		case 'SELE':
			return mysql_num_rows($query_pointer);
		case 'INSE':
			return mysql_insert_id();
		default:
			return mysql_affected_rows();
	}
	$q = mysql_query($sql) or exit(mysql_error());
	if (mysql_num_rows($q) === false){
		exit($sql);
	}
	$result = mysql_fetch_row($q); // teeb massiivi $q-st
	// kas $result on array ja on rohkem kui 0 elementi, siis tagastab esimese elemendi
	return is_array($result) && count($result) > 0 ? $result[0] : null;
}
function get_all($sql){
	$q = mysql_query($sql) or exit(mysql_error());
	while (($result[] = mysql_fetch_assoc($q)) || array_pop($result)){
		;
	}
	return $result;
}