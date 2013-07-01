<?

if ($_SESSION["extension"])
    $extension = "'" . $_SESSION["extension"] . "'";
if (!$extension) {
    echo (out(array("success" => false, "message" => "Extension is undefined")));
    exit;
}

echo out("hehey!");
?>