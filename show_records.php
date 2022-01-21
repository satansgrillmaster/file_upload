<?php

// setup the db connection
require_once ('include/connect.php');
$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

// register record
if(isset($_POST['upload'])){


    $sql = "insert into personen (vorname, name, email, registrierdatum, geburtsdatum, bildname, alternativtext)
            values (?,?,?,CURRENT_TIMESTAMP,?,?,?)";

    $prepare_state = $db->prepare($sql);
    $img_name = $_FILES['upload_img']['name'];
    $prepare_state->bindParam(1, $_POST['firstname']);
    $prepare_state->bindParam(2,$_POST['lastname']);
    $prepare_state->bindParam(3,$_POST['email']);
    $prepare_state->bindParam(4,$_POST['birthday']);
    $prepare_state->bindParam(5,$img_name);
    $prepare_state->bindParam(6,$_POST['alt_text']);

    if($prepare_state->execute()=== true){
        $img = imagecreatefromjpeg($_FILES['upload_img']['tmp_name']);
        $width = imagesx($img);
        $height = imagesy($img);

        $new_width = 100;
        $new_height = 70;

        $new_img = imagecreatetruecolor($new_width,$new_height);

        imagecopyresampled($new_img,$img,0, 0, 0, 0, $new_width, $new_height,$width,$height);
        imagejpeg($new_img,'images/' . $_FILES['upload_img']['name']);
    }
}

// delete records
if(isset($_POST['delete'])){
    $checkboxes = $_POST['record_check'];
    $delete_string = '';
    foreach ($checkboxes as $id){
        if($id != ''){
            $delete_string = $delete_string . $id . ",";
        }
    }

    $sql = "delete from personen where id in ($delete_string 0)";
    $db->exec($sql);
}

$sql = 'select id, vorname, name, email, DATE_FORMAT(registrierdatum, "%D %M %Y") as regidatum,
       DATE_FORMAT(geburtsdatum, "%d.%m.%Y") as geburtsdatum, alternativtext, bildname from personen';

// send header
header('Content-Type: text/html; charset=UTF-8');

// print page
echo <<<'head'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>show records</title>
    <link href="style.css" rel="stylesheet">
</head>
<body>
<div class="container">
head;

// print form
echo '<h1>Personen</h1>';
echo '<form method="post" action="show_records.php" enctype="multipart/form-data">';
echo '<div>';
echo '<label style="align-self: flex-start"><input type="checkbox" onclick="toggle()" id="select_all">Alle auswählen</label>';

// print table
echo '<table>';
echo '<th>Auswählen</th><th>Vorname</th><th>Nachname</th><th>Email</th><th>Registrationsdatum</th>
<th>Geburtsdatum</th><th>Alternativtext</th><th>Bild</th>';

foreach ($result = $db->query($sql) as $row){
    echo '<tr><td><input type="checkbox" name="record_check[]" value="'. $row['id'].'" class="check"></td>';
    echo '<td>' . $row['vorname'] . '</td>';
    echo '<td>' . $row['name'] . '</td>';
    echo '<td>' . $row['email'] . '</td>';
    if ($row['geburtsdatum'] == date("d.m.Y")){
        echo '<td style="color: red">' . $row['geburtsdatum'] . '</td>';
    }
    else{
        echo '<td>' . $row['geburtsdatum'] . '</td>';
    }
    echo '<td>' . $row['regidatum'] . '</td>';
    echo '<td>' . $row['alternativtext'] . '<br>Bild vom:<br>' . $row['regidatum'] . '</td>';
    echo '<td><img src="images/' . $row['bildname'] . '"></td>';
    echo '<tr>';
}
echo '</table>';
echo '</div>';

// form to register records
echo'<h2>Datensätze Registrieren</h2>';
echo '<label>Vorname: <input type="text" name="firstname"></label>';
echo '<label>Nachname: <input type="text" name="lastname"></label>';
echo '<label>Email: <input type="text" name="email"></label>';
echo '<label>Geburtsdatum: <input type="date" name="birthday"></label>';
echo '<label>Alternativ Text: <input type="text" name="alt_text"></label>';
echo '<input type="file" name="upload_img" size="50">';
echo '<input type="submit" value="Hochladen" name="upload">';
echo '<input type="submit" name="delete" value="Datensätze löschen">';
echo '</form></div></body></html>';
?>

<script>
    // function to select or deselect all records
    function toggle() {
        checkboxes = document.getElementsByClassName('check');
        for(var checkbox in checkboxes){
            checkboxes[checkbox].checked = checkboxes[checkbox].checked !== true;}
    }
</script>
