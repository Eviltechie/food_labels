<!DOCTYPE html>
<html>
<head></head>
<body>

<form method="post">
<label for="food_item">Item:</label><br>
<input type="text" id="food_item" name="food_item" autocomplete="off" autofocus><br>
<br>
<label for="expiry">Expiry (days):</label><br>
<input type="text" id="expiry" name="expiry" autocomplete="off"><br>
<br>
<input type="submit" value="Submit"><br>
</form>

<?php

require __DIR__ . '/vendor/autoload.php';
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\ImagickEscposImage;
use Mike42\Escpos\Printer;

if (isset($_POST["food_item"]) && isset($_POST["expiry"])) {

    $db = new SQLite3("food.db");

    $prepared_statement = $db -> prepare("INSERT INTO food (food_item, stored, expiry) VALUES (:item, :stored, :expiry)");
    $prepared_statement -> bindValue(":item", $_POST["food_item"]);
    $prepared_statement -> bindValue(":stored", time());
    $prepared_statement -> bindValue(":expiry", time() + ($_POST["expiry"] * 86400));
    $prepared_statement -> execute();

    $last_row = $db -> lastInsertRowID();

    echo sprintf("Printing label for item ID %s", $last_row);

    $connector = new NetworkPrintConnector("192.168.1.213", 9100);
    $printer = new Printer($connector);

    $printer -> initialize();
    $printer -> setTextSize(2, 2);
    $printer -> text(sprintf("%s\n", $_POST["food_item"]));
    $printer -> setTextSize(1, 1);
    $printer -> text(sprintf("Packaged: %s\n", date("F j Y", time())));
    $printer -> text(sprintf("Use by: %s\n", date("F j Y",time() + ($_POST["expiry"] * 86400))));
    $printer -> barcode($last_row);
    $printer -> text($last_row);
    $printer -> feed(3);
    $printer -> cut();
    $printer -> close();
}

?>
</body>
</html>