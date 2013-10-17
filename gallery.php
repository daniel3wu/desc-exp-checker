<?php

include "common.php.inc";
$dbh = getDBHandle();

$stm = $dbh->prepare("SELECT files.files.expname, files.files.ccd, qa.comment, qa.timestamp, users.username FROM qa JOIN files ON (files.files.rowid=qa.fileid) JOIN users ON (qa.userid = users.rowid) WHERE qa.rowid IN (SELECT MIN(qa.rowid) FROM qa WHERE problem=-1 GROUP BY qa.fileid)");
$stm->execute();
$response = $stm->fetchAll(PDO::FETCH_ASSOC);
if (isset($_GET['output'])) {
    if ($_GET['output'] == "html") {
        echo "<table class='table table-condensed table-striped'><thead><tr><th>Image</th><th>Description</th><th>Username</th><th>Timestamp</th></tr></thead><tbody>\n";
        foreach ($response as $row) {
            $row['comment'] = json_decode($row['comment']);
            echo "<tr><td class='imagenamecol'><a href='viewer.html?expname=".$row['expname']."&ccd=".$row['ccd']."'>".$row['expname'].", CCD ".$row['ccd']."</a></td>\n";
            echo "<td>".$row['comment']->detail."</td>\n";
            echo "<td>".$row['username']."</td>\n";
            echo "<td class='timecol'>".$row['timestamp']."</td>\n";
        }
        echo "</tbody></table>";
    }
}
else 
    echo json_encode($response);
?>