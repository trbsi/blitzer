<?php
/*
NA MJESEČNOJ BAZI
Broj pinova
Broj obrisanih pinova
Broj "hangout found" pinova
*/
include "mysqli.php";

$start_date=$_POST["start_date"];
$end_date=$_POST["end_date"];
$HANGOUT_REJECTED=1;
$HANGOUT_ACCEPTED=2;
$HANGOUT_REQUESTED=0; //a.k.a. HANGOUT_NOT_DECIDED

echo "<h2>Between $start_date AND $end_date</h2>";

$query =
"
SELECT
    COUNT(*) AS number_of_pins,
    COUNT(IF(deleted=1,1,NULL)) AS deleted_pins,
    COUNT(IF(hangout_found=1,1,NULL)) AS hangout_found
FROM ".$table_names["Location"]."
WHERE post_time BETWEEN '".$start_date."' AND '".$end_date."'
";
$result = $mysqli->query($query);
$row = $result->fetch_assoc();
echo '<table class="table table-bordered table-striped table-hover">' ;
echo "
<tr>
<td>Total number of pins</td>
<td>".$row['number_of_pins']."</td>
</tr>";

echo "
<tr>
<td>Total number of deleted pins</td>
<td>".$row['deleted_pins']."</td>
</tr>";

echo "
<tr>
<td>Total number of 'hangout found'</td>
<td>".$row['hangout_found']."</td>
</tr>";


/*
NA MJESEČNOJ BAZI
Broj rekvestova ukupno
Tip rekvesta
 */
 $query =
"
SELECT
    COUNT(*) AS number_of_requets,
    COUNT(IF(request_status=$HANGOUT_REJECTED,1,NULL)) AS hangout_rejected,
    COUNT(IF(request_status=$HANGOUT_ACCEPTED,1,NULL)) AS hangout_accepted,
    COUNT(IF(request_status=$HANGOUT_REQUESTED,1,NULL)) AS hangout_requested
FROM ".$table_names["Request Hangout"]."
WHERE notification_time BETWEEN '".$start_date."' AND '".$end_date."'
";
$result = $mysqli->query($query);
$row = $result->fetch_assoc();
echo "
<tr>
<td>Total number of requests</td>
<td>".$row['number_of_requets']."</td>
</tr>";

echo "
<tr>
<td>Total number of requested hangouts</td>
<td>".$row['hangout_requested']."</td>
</tr>";

echo "
<tr>
<td>Total number of accepted hangouts</td>
<td>".$row['hangout_accepted']."</td>
</tr>";

echo "
<tr>
<td>Total number of rejected hangouts</td>
<td>".$row['hangout_rejected']."</td>
</tr>";

echo '</table>';
?>