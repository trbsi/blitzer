<?php
/*
$hostname = "127.0.0.1";
$username = "root";
$password = "";
$database = "pleasurre_new";
*/
$hostname = "127.0.0.1";
$username = "thettaco_pleasur";
$password = "kdgfn834&34Jfn4f9(&$";
$database = "thettaco_pleasurre";


$mysqli = new mysqli($hostname, $username, $password, $database);
$mysqli->set_charset("utf8");
$table_names=[];
$table_names["UI"]="rre_user_information";
$table_names["User"]="rre_user";
$table_names["UP"]="rre_user_pictures";
$table_names["Location"]="rre_locations";
$table_names["Request Hangout"]="rre_request_hangout";
$table_names["Messages"]="rre_messages";
$table_names["Messages Reply"]="rre_messages_reply";
$table_names["LRG"]="rre_location_reverse_geo";

/* check connection */
if (mysqli_connect_errno())
{
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}