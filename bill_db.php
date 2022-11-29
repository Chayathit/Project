<?php 
    session_start(); 
    require_once 'config/db.php';

    if (!isset($_SESSION['uid']) || !isset($_SESSION['urole'])){
        header("Location: login.php");
    } else {
        try {
            $_SESSION['billnum'] = $_POST['bill']-1;
            if (isset($_SESSION['paymentid'][$_SESSION['billnum']])) {
                if ($_POST['bill'] == 1){
                    $multiplier = 0.5;
                } else {
                    $multiplier = 0.25;
                }
                $billprice = $_SESSION['totalprice'] * $multiplier;
                $_SESSION['billprice'] = $billprice;
                header("Location: bill.php");
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
?>