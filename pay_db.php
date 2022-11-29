<?php 
    session_start(); 
    require_once 'config/db.php';

    if (!isset($_SESSION['uid']) || !isset($_SESSION['urole'])){
        header("Location: login.php");
    } else {
        try {
            if (isset($_POST['transitionnum'])){
                $updatebill = $conn->prepare("UPDATE BILL_PAYMENT SET TRANSITION_NUMBER = :tnum WHERE PAYMENT_ID = :paymentid");
                $updatebill->bindParam(":paymentid", $_SESSION['paymentid'][$_SESSION['billnum']]);
                $updatebill->bindParam(":tnum", $_POST['transitionnum']);
                $updatebill->execute();
                $_SESSION['paid'][$_SESSION['billnum']+1] = 1;
                $_SESSION['paid'][0] = $_SESSION['paid'][0] + 1;
                if ($_SESSION['paid'][0] == 3){
                    $updateenroll = $conn->prepare("UPDATE ENROLLMENT SET PAYMENT_STATUS = 1 WHERE ENROLLMENT_ID = :enrollmentid");
                    $updateenroll->bindParam(":enrollmentid", $_SESSION['enrollmentid']);
                    $updateenroll->execute();
                }
            }
            header("Location: enrollment.php");
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
?>