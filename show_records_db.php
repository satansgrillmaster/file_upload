<?php

// setup the db connection
require_once ('include_db/connect.php');
$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

// register record
if(isset($_POST['upload'])){


    $sql = "insert into personen (vorname, name, email, registrierdatum, geburtsdatum, bildname, alternativtext, bild,
                       type, size)
            values (?,?,?,CURRENT_TIMESTAMP,?,?,?,?,?,?)";

    $prepare_state = $db->prepare($sql);
    $img_name = $_FILES['upload_img']['name'];
    $firstname = htmlspecialchars($_POST['firstname']);
    $lastname = htmlspecialchars($_POST['lastname']);
    $email = htmlspecialchars($_POST['email']);
    $alt_Text = htmlspecialchars($_POST['email']);
    $file = fopen($_FILES['upload_img']['tmp_name'],'rb');
    $size = $_FILES['upload_img']['size'];
    $img_string = fread($file, $_FILES['upload_img']['size']);
    $img_type = $_FILES['upload_img']['type'];


    $prepare_state->bindParam(1, $firstname);
    $prepare_state->bindParam(2,$lastname);
    $prepare_state->bindParam(3,$email);
    $prepare_state->bindParam(4,$_POST['birthday']);
    $prepare_state->bindParam(5,$img_name);
    $prepare_state->bindParam(6,$alt_Text);
    $prepare_state->bindParam(7,$img_string);
    $prepare_state->bindParam(8,$img_type);
    $prepare_state->bindParam(9,$size);

    if($prepare_state->execute()=== true){
        echo "true";
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
echo '<form method="post" action="show_records_db.php" enctype="multipart/form-data">';
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
    echo '<td><img src="bild.php?id='  . $row['id'] . '" style="width: 200px"></td>';
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
