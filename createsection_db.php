<?php
    session_start();
    require_once 'config/db.php';
    if (!isset($_SESSION['uid']) || !isset($_SESSION['urole'])){
        header("Location: login.php");
    }
    $i = 0;

    try {
        $searchteacher = $conn->prepare("SELECT * FROM TEACHER");
        $searchteacher->execute();
        $searchteacher = $searchteacher->fetchAll(PDO::FETCH_NUM);
        $_SESSION['searchteacher'] = $searchteacher;
        if (isset($_POST['newsect0'])){
            while (isset($_POST['newsect'.$i])){
                $newsect[$i] = $_POST['newsect'.$i];
                $newsecms[$i] = $_POST['newsecms'.$i];
                unset($_POST['newsect'.$i]);
                unset($_POST['newsecms'.$i]);
                $i++;
            }
            $_SESSION['newsect'] = $newsect;
            $_SESSION['newsecms'] = $newsecms;
            header("Location: selectdepartment_db.php");
        } else {
            header("Location: createsection.php");
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
?>