<?php
function queryDatabase($query)
{	
    $conn = mysqli_connect( '35.199.45.70' , "apiuser" , "bignorman56!" , "PTmiddleware" );
    $result =  mysqli_query($conn , $query);
    mysqli_close($conn);
    return($result);
}
function insertInDatabaseAndGetInsertId($query)
{
    $conn = mysqli_connect( '35.199.45.70' , "apiuser" , "bignorman56!" , "PTmiddleware" );
    $result =  mysqli_query($conn , $query);
    if($result){
		$insertId = mysqli_insert_id($conn); 
	}
    mysqli_close($conn);
    return($insertId);
}
function multiQueryDatabase($query)
{
	$insertId = 0;
    $conn = mysqli_connect( '35.199.45.70' , "apiuser" , "bignorman56!" , "PTmiddleware" );
    $result =  mysqli_multi_query($conn , $query);
    mysqli_close($conn);
    return($result);
}
//Generates Secure String to avoid SQL Injection
function getSecureString($sourceString)
{
    $conn = mysqli_connect( '35.199.45.70' , "apiuser" , "bignorman56!" , "PTmiddleware" );
	$result =  mysqli_real_escape_string($conn,$sourceString);
    mysqli_close($conn);
	return($result);
}
?>