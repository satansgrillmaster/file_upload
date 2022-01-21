<?php
    require_once ('include_db/connect.php');
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    $id = $_GET['id'];
    $img = null;
    $sql = "select bild from personen where id = $id";
    foreach ($result = $db->query($sql) as $row){
        $img = $row['bild'];
    }

    header('Content-Type: image/jpeg;');
    echo $img;