<?php
    session_start();
    require_once 'config/db.php';
    if (!isset($_SESSION['uid']) || !isset($_SESSION['urole'])){
        header("Location: login.php");
    }
    
    try {
        $coursecode = $_POST['coursecode'];
        $coursetitle = $_POST['coursetitle'];
        $credit = $_POST['credit'];
        $price = $_POST['price'];
        $amountsec = $_POST['amountsec'];
        $chechduplicate = $conn->prepare("SELECT COURSE_CODE FROM COURSE WHERE COURSE_CODE = :coursecode");
        $chechduplicate->bindParam(":coursecode", $coursecode);
        $chechduplicate->execute();
        $chechduplicate = $chechduplicate->fetch(PDO::FETCH_ASSOC);
        if (isset($chechduplicate['COURSE_CODE'])){
            $_SESSION['error'] = "Course code already exists.";
            unset($_POST['coursecode']);
            unset($_POST['coursetitle']);
            unset($_POST['credit']);
            unset($_POST['price']);
            unset($_POST['amountsec']);
            header("Location: newcourse.php");
        } else {
            $checkduplicatepc = $conn->prepare("SELECT PRICE_RATE_ID FROM PRICE_COURSE WHERE PRICE = :price AND CREDIT = :credit");
            $checkduplicatepc->bindParam(":price", $price);
            $checkduplicatepc->bindParam(":credit", $credit);
            $checkduplicatepc->execute();
            $checkduplicatepc = $checkduplicatepc->fetch(PDO::FETCH_ASSOC);
            $_SESSION['pcid'] = $checkduplicatepc['PRICE_RATE_ID'];
            $_SESSION['newcc'] = $coursecode;
            $_SESSION['newct'] = $coursetitle;
            $_SESSION['newcp'] = $price;
            $_SESSION['newcd'] = $credit;
            for ($i = 0; $i < $amountsec; $i++){
                $_SESSION['newsc'][$i] = $coursecode . "_" . $i+1;
            }
            header("Location: createsection_db.php");
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
?>