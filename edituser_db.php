<?php
    session_start();
    require_once 'config/db.php';
    if (!isset($_SESSION['uid']) || !isset($_SESSION['urole'])){
        header("Location: login.php");
    }
    
    try {
        if (isset($_POST['edituser'])){
            if (strlen($_POST['editpw'] > 0)){
                if ($_POST['editpw'] == $_POST['repw']){
                    $edituser = $conn->prepare("UPDATE USER_T SET PASSWORD = :password WHERE USER_ID = :userid");
                    $edituser->bindParam(":password", $_POST['editpw']);
                    $edituser->bindParam(":userid", $_SESSION['uid']);
                    $edituser->execute();
                } else {
                    $_SESSION['error'] = "Passwords do not match";
                }
            }
            if ((strlen($_POST['editfname'] > 0)) && (!isset($_SESSION['error']))){
                $edituser = $conn->prepare("UPDATE USER_T SET FIRST_NAME = :firstname WHERE USER_ID = :userid");
                $edituser->bindParam(":firstname", $_POST['editfname']);
                $edituser->bindParam(":userid", $_SESSION['uid']);
                $edituser->execute();
            }
            if ((strlen($_POST['editlname'] > 0)) && (!isset($_SESSION['error']))){
                $edituser = $conn->prepare("UPDATE USER_T SET LAST_NAME = :lastname WHERE USER_ID = :userid");
                $edituser->bindParam(":lastname", $_POST['editlname']);
                $edituser->bindParam(":userid", $_SESSION['uid']);
                $edituser->execute();
            }
        }
        if (isset($_SESSION['error'])){
            header("Location: edituser.php");
        } else {
            header("Location: main_db.php");
        }
    } catch (PDOException $e){
        echo $e->getMessage();
    }
?>