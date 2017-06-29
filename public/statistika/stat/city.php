<?php

include "mysqli.php";

$start_date=$_GET["start_date"];
$end_date=$_GET["end_date"];
$country=$_GET["country"];

$query =
"
SELECT COUNT(city) AS county_city, city
FROM ".$table_names["LRG"]."
LEFT JOIN ".$table_names["Location"]." ON (".$table_names["Location"].".ID=".$table_names["LRG"].".IDlocation)
WHERE post_time BETWEEN '$start_date' AND '$end_date' AND country='$country'
GROUP BY city
ORDER BY county_city DESC
";

echo "<h3>Between $start_date and $end_date</h3>";
echo '<table class="table table-bordered table-striped table-hover">' ;
echo "
<tr>
<th>City</th>
<th>Count</th>
</tr>";
$result = $mysqli->query($query);
while($row = $result->fetch_assoc())
{
    echo "
    <tr>
    <td><b>".$row['city']."</b></td>
    <td>".$row['county_city']."</td>
    </tr>";
}
echo '</table>'

?>