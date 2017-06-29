<?php

include "mysqli.php";

$start_date=$_POST["start_date"];
$end_date=$_POST["end_date"];

$query =
"
SELECT COUNT(country) AS county_country, country
FROM ".$table_names["LRG"]."
LEFT JOIN ".$table_names["Location"]." ON (".$table_names["Location"].".ID=".$table_names["LRG"].".IDlocation)
WHERE post_time BETWEEN '$start_date' AND '$end_date'
GROUP BY country
ORDER BY country
";

echo "<h3>Between $start_date and $end_date</h3>";
echo '<table class="table table-bordered table-striped table-hover">' ;
echo "
<tr>
<th>Country</th>
<th>Count country</th>
</tr>";
$result = $mysqli->query($query);
while($row = $result->fetch_assoc())
{
    echo "
    <tr>
    <td><a href='index.php?query=city&country=".$row['country']."&start_date=$start_date&end_date=$end_date' target='_blank'><b>".$row['country']."</b></a></td>
    <td>".$row['county_country']."</td>
    </tr>";
}
echo '</table>'

?>