<?php
/*
NA MJESEČNOJ BAZI
Broj kreiranih konverzacija
Broj razmjenjenih poruka po konverzaciji
*/
include "mysqli.php";

$start_date=$_POST["start_date"];
$end_date=$_POST["end_date"];

echo "<h2>Between $start_date AND $end_date</h2>";

$query =
"
SELECT
    COUNT(*) AS number_of_messages
FROM ".$table_names["Messages"]."
WHERE last_updated BETWEEN '".$start_date."' AND '".$end_date."'
";
$result = $mysqli->query($query);
$row = $result->fetch_assoc();
echo '<table class="table table-bordered table-striped table-hover">' ;
echo "
<tr>
<td>Total number of conversations</td>
<td>".$row['number_of_messages']."</td>
</tr>";

$query =
"
SELECT
    COUNT(*) AS number_of_messages
FROM ".$table_names["Messages Reply"]."
WHERE send_date BETWEEN '".$start_date."' AND '".$end_date."'
";
$result = $mysqli->query($query);
$row = $result->fetch_assoc();
echo "
<tr>
<td>Total number of messages</td>
<td>".$row['number_of_messages']."</td>
</tr>";
echo '</table>';
?>