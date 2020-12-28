<!DOCTYPE html>
<html>
<head>
<script src="jquery-3.5.1.min.js"></script> 
</head>
<body>
<table>
<tr><th>ID</th><th>Item</th><th>Stored</th><th>Expiry</th></tr>
<?php

$db = new SQLite3("food.db");

if (isset($_POST["food_id"])) {

    $prepared_statement = $db -> prepare("DELETE FROM food WHERE id=:id");
    $prepared_statement -> bindValue(":id", $_POST["food_id"]);
    $prepared_statement -> execute();

}

$result = $db -> query("SELECT id, food_item, stored, expiry FROM food");

$row = $result -> fetchArray();

while ($row != false) {
    echo sprintf("<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>\n", $row["id"], $row["food_item"], date("F j Y", $row["stored"]), date("F j Y", $row["expiry"]));
    $row = $result -> fetchArray();
}

?>
</table>

<form id="consume_form" method="post">
<label for="food_id">Item:</label><br>
<input type="text" id="food_id" name="food_id" autofocus autocomplete="off"><br>
<br>
<input type="submit" value="Submit"><br>
</form>

<?php

if (isset($_POST["food_id"])) {

    echo sprintf("Consumed item ID %s", $_POST["food_id"]);

}

?>
<script>
$("#consume_form").on('keydown', '#food_id', function(e) {
  var keyCode = e.keyCode || e.which;

  if (keyCode == 9) {
    e.preventDefault();
    $("#consume_form").submit();
  }
});
</script>
</body>
</html>