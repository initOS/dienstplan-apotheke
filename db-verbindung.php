<?php
global $verbindungi;
$verbindungi = new mysqli("localhost", $config['database_user'], $config['database_password'] , $config['database_name'] );
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
// change character set to utf8
if (!$verbindungi->set_charset("utf8")) {
    printf("Error loading character set utf8: %s\n", $verbindungi->error);
} else {
//    printf("Current character set: %s\n", $verbindungi->character_set_name());
}

/*
 * This function sends a query to the MySQL server and returns the result.
 * 
 * If an error occurs, the error is logged into a file and written directly to the output.
 * The execution of the program is stopped!
 * 
 * @param string $sql_query
 * @return object|boolean $result
 */
function mysqli_query_verbose($sql_query) {
    $result = mysqli_query($GLOBALS['verbindungi'], $sql_query)
            or $message = "Error: $sql_query <br>".  \mysqli_error($GLOBALS['verbindungi'])
            and error_log($message) 
            and die($message);
    return $result;
}
/*
 * This function sends a query to the MySQL server and returns the result.
 * 
 * Only the first row is returned as an object.
 * Every other row is lost.
 * If an error occurs, the error is logged into a file and written directly to the output.
 * The execution of the program is stopped!
 * 
 * @param string $sql_query
 * @return object $row
 */
function mysqli_query_for_single_object($sql_query) {
    $result = mysqli_query($GLOBALS['verbindungi'], $sql_query)
            or $message = "Error: $sql_query <br>".  \mysqli_error($GLOBALS['verbindungi'])
            and error_log($message) 
            and die($message);
    $row = mysqli_fetch_object($result);
    return $row;
}
