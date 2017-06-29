<?php

include "mysqli.php";

/*
UKUPNO
broj korisnika
broj fotki po korisniku
broj korisnika koji su prošli post registraciju
*/
$query =
"
SELECT
    (SELECT COUNT(*) FROM ".$table_names["User"].") AS number_of_users,
    (SELECT COUNT(*) FROM ".$table_names["UP"].") AS total_pictures,
    (SELECT COUNT(*) FROM ".$table_names["UI"].") AS post_reg_users
";

$result = $mysqli->query($query);
$row = $result->fetch_assoc();
echo '<table class="table table-bordered table-striped table-hover">' ;
echo "
<tr>
<td>Total number of users</td>
<td>".$row['number_of_users']."</td>
</tr>";

echo "
<tr>
<td>Total pictures</td>
<td>".$row['total_pictures']."</td>
</tr>";

echo "
<tr>
<td>Total post registration users</td>
<td>".$row['post_reg_users']."</td>
</tr>";
echo '</table>';



/*
NA MJESEČNOJ BAZI
broj aktivnih korisnika u trenutnom mjesecu
 */
$start_date=$_POST["start_date"];
$start_date_timestamp=strtotime($start_date);

$end_date=$_POST["end_date"];
$end_date_timestamp=strtotime($end_date);

$query=
"
SELECT COUNT(*) as active_users
FROM ".$table_names["UI"]."
WHERE date_visited BETWEEN '".$start_date."' AND '".$end_date."'
";
$result = $mysqli->query($query);
$row = $result->fetch_assoc();

/*
NA MJESEČNOJ BAZI
broj korisnika
broj fotki po korisniku
broj korisnika koji su prošli post registraciju
*/
$query=
"
SELECT COUNT(".$table_names["User"].".id) AS number_of_users,
        COUNT(".$table_names["UI"].".ID) AS post_reg_users,
        COUNT(".$table_names["UP"].".ID) AS total_pictures
FROM ".$table_names["User"]."
LEFT JOIN ".$table_names["UI"]." ON (".$table_names["UI"].".IDuser=".$table_names["User"].".id)
LEFT JOIN ".$table_names["UP"]." ON (".$table_names["UP"].".IDuser=".$table_names["User"].".id)
WHERE confirmed_at BETWEEN $start_date_timestamp AND $end_date_timestamp
";
//echo $query; return;
$result = $mysqli->query($query);
$row2 = $result->fetch_assoc();

echo "<h2>Between $start_date and $end_date</h2>";
echo '<table class="table table-bordered table-striped table-hover">';

echo "
<tr>
<td>Total number of users</td>
<td>".$row2['number_of_users']."</td>
</tr>";

echo "
<tr>
<td>Total pictures</td>
<td>".$row2['total_pictures']."</td>
</tr>";

echo "
<tr>
<td>Total post registration users</td>
<td>".$row2['post_reg_users']."</td>
</tr>";

echo "
<tr>
<td>Active users</td>
<td>".$row['active_users']."</td>
</tr>";

echo '</table>';



?>