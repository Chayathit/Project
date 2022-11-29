<?php
    session_start();
    require_once 'config/db.php';
    if (!isset($_SESSION['uid']) || !isset($_SESSION['urole'])){
        header("Location: login.php");
    }
    
    try {
        if (isset($_POST['supplytoedit'])){
            $wanttoeditsupply = $conn->prepare("SELECT * FROM SCHOOL_SUPPLIES WHERE SUPPLY_ID = :supplyid AND SUPPLY_STATUS = 'Available';");
            $wanttoeditsupply->bindParam(":supplyid", $_POST['sidtoedit']);
            $wanttoeditsupply->execute();
            $wanttoeditsupply = $wanttoeditsupply->fetch(PDO::FETCH_NUM);
            if ((isset($wanttoeditsupply[0]))) {
                $_SESSION['sidtoedit'] = $wanttoeditsupply;
            } else {
                $_SESSION['error'] = "Supply does not exist or not avaliable";
            }
        }
        if (isset($_POST['editsupply'])){
            if (strlen($_POST['editmodel']) > 0){
                echo $_SESSION['sidtoedit'][0];
                $editmodel = $conn->prepare("UPDATE SCHOOL_SUPPLIES SET MODELS = :models WHERE SUPPLY_ID = :supplyid;");
                $editmodel->bindParam(":models", $_POST['editmodel']);
                $editmodel->bindParam(":supplyid", $_SESSION['sidtoedit'][0]);
                $editmodel->execute();
                $_SESSION['success'] = "Supply edited successfully.";
            }
            if (strlen($_POST['editeduty']) > 0){
                echo "-0ik09ja98ihjf";
                $editeduty = $conn->prepare("UPDATE SCHOOL_SUPPLIES SET END_OF_DUTY_DATE = :enddate WHERE SUPPLY_ID = :supplyid;");
                $editeduty->bindParam(":enddate", $_POST['editeduty']);
                $editeduty->bindParam(":supplyid", $_SESSION['sidtoedit'][0]);
                $editeduty->execute();
                $_SESSION['success'] = "Supply edited successfully.";
            }
            unset($_SESSION['sidtoedit']);
        }
        $searchsupply = $conn->prepare("SELECT SUPPLY_ID, MODELS FROM SCHOOL_SUPPLIES WHERE SUPPLY_STATUS = 'Available';");
        $searchsupply->execute();
        $searchsupply = $searchsupply->fetchAll(PDO::FETCH_NUM);
        $_SESSION['supply'] = $searchsupply;
        header("Location: editsupply.php");
    } catch (PDOException $e){
        echo $e->getMessage();
    }
?>