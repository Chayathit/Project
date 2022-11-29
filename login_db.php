<?php
    session_start();
    require_once 'config/db.php';
    if (!isset($_SESSION['uid']) || !isset($_SESSION['urole'])){
        header("Location: login.php");
    }
    
    if (isset($_POST['login'])){
        $userid = $_POST['userid'];
        $password = $_POST['password'];
        $check_data = $conn->prepare("SELECT * FROM USER_T WHERE USER_ID = :userid");
        $check_data->bindParam(":userid", $userid);
        $check_data->execute();
        $row = $check_data->fetch(PDO::FETCH_ASSOC);
        try{
            if ($check_data->rowCount() == 1 && $password == $row['PASSWORD']){
                $_SESSION['uid'] = $row['USER_ID'];
                $_SESSION['urole'] = $row['ROLE'];
                header("Location: main_db.php");
            } else {
                $_SESSION['error'] = "Username or password is incorrect";
                header("Location: login.php");
            }
        } catch (PDOException $e){
            echo $e->getMessage();
        }
    }
?>