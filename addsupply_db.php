<?php
    session_start();
    require_once 'config/db.php';

    if (!isset($_SESSION['uid']) || !isset($_SESSION['urole'])){
        header("Location: login.php");
    } else {
        try {
            if (isset($_POST['้hohohaha'])) {
                $amountsupply = $conn->prepare("SELECT COUNT(SUPPLY_ID) FROM SCHOOL_SUPPLIES;");
                $amountsupply->execute();
                $amountsupply = $amountsupply->fetch(PDO::FETCH_NUM);
                if($amountsupply[0] >= 100){
                    $newsupplyid = "S" . $amountsupply[0]+1;
                } else if($amountsupply[0] >= 10){
                    $newsupplyid = "S0" . $amountsupply[0]+1;
                } else {
                    $newsupplyid = "S00" . $amountsupply[0]+1;
                }
                $insertsupply = $conn->prepare("INSERT INTO SCHOOL_SUPPLIES (SUPPLY_ID, SUPPLY_TYPE, MODELS, SUPPLY_STATUS, END_OF_DUTY_DATE)
                                                VALUES (:supplyid, :supplytype, :models, 'Available', :endofdutydate);");
                $insertsupply->bindParam(':supplyid', $newsupplyid);
                $insertsupply->bindParam(':supplytype', $_POST['supplytype']);
                $insertsupply->bindParam(':models', $_POST['model']);
                $insertsupply->bindParam(':endofdutydate', $_POST['endofdutydate']);
                $insertsupply->execute();
                $_SESSION['success'] = "Supply added successfully.";
                unset($_POST['endofdutydate']);
            } else if (isset($_POST['endofdutydate'])){
                $_SESSION['error'] = "Please fill in all the fields.";
                unset($_POST['endofdutydate']);
            }
            header("Location: addsupply.php");
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
?>