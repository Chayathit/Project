<?php
    session_start();
    require_once 'config/db.php';

    try {
        $getdepartment = $conn->prepare("SELECT DEPARTMENT FROM USER_T GROUP BY DEPARTMENT;");
        $getdepartment->execute();
        $getdepartment = $getdepartment->fetchAll(PDO::FETCH_NUM);
        $_SESSION['selectdepartment'] = $getdepartment;
        if(isset($_POST['signup'])) {
            $uid = $_POST['uid'];
            $pass = $_POST['pass'];
            $nid = $_POST['nid'];
            $fn = $_POST['fn'];
            $ln = $_POST['ln'];
            $dep = $_POST['dep'];      
            if (isset($_POST['signup'])){
                $checkuser = $conn->prepare("SELECT * FROM USER_T WHERE USER_ID = :uid;");
                $checkuser->bindParam(':uid', $uid);
                $checkuser->execute();
                $checkuser = $checkuser->fetch(PDO::FETCH_ASSOC);
                if(isset($checkuser['USER_ID'])){
                    $_SESSION['error'] = "User ID already exists.";
                } else {
                    $insertuser = $conn->prepare("INSERT INTO USER_T (USER_ID, PASSWORD, NATIONAL_ID, FIRST_NAME, LAST_NAME, DEPARTMENT, ROLE, YEAR, TOTAl_CREDIT)
                                                VALUES (:uid, :pass, :nid, :fn, :ln, :dep, 'Student', 1, 0);");
                    $insertuser->bindParam(':uid', $uid);
                    $insertuser->bindParam(':pass', $pass);
                    $insertuser->bindParam(':nid', $nid);
                    $insertuser->bindParam(':fn', $fn);
                    $insertuser->bindParam(':ln', $ln);
                    $insertuser->bindParam(':dep', $dep);
                    $insertuser->execute();
                    $_SESSION['success'] = "User added successfully.";
                }
            }
        }
        header("Location: signup.php");
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    
?>