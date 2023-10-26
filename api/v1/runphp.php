<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    global $variables;
    $phpCode = $_POST["phpCode"];
    file_put_contents("temp.txt", $phpCode);
}
?>
