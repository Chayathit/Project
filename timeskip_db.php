<?php 
    session_start(); 
    require_once 'config/db.php';

    if (!isset($_SESSION['uid']) || !isset($_SESSION['urole'])){
        header("Location: login.php");
    } else {
        try {
        if ($_SESSION['currenttime']['PHASE']+1 == 5) {
            if ($_SESSION['currenttime']['SEMESTER']+1 == 3) {
                $_SESSION['currenttime']['PHASE'] = 1;
                $_SESSION['currenttime']['SEMESTER'] = 1;
                $_SESSION['currenttime']['ACADEMIC_YEAR']++;
                $updateyearuser = $conn->prepare("UPDATE USER_T SET YEAR = YEAR+1 WHERE ROLE = 'STUDENT'");
                $updateyearuser->execute(); 
            } else {
                $_SESSION['currenttime']['PHASE'] = 1;
                $_SESSION['currenttime']['SEMESTER']++;
            }
        } else {
            $_SESSION['currenttime']['PHASE']++;
            $dropunpaiden = $conn->prepare("UPDATE ENROLLMENT 
                                            SET GPA = NULL,
                                            TOTAL_CREDIT = NULL,
                                            PAYMENT_STATUS = -1
                                            WHERE ENROLLMENT_ID IN (SELECT ENROLLMENT_ID 
                                                                    FROM BILL_PAYMENT
                                                                    WHERE TRANSITION_NUMBER IS NULL
                                                                    AND DUE_PHASE < :phase);");
            $dropunpaiden->bindParam(":phase", $_SESSION['currenttime']['PHASE']);
            $dropunpaiden->execute();
            $dropunpaidensec = $conn->prepare("UPDATE ENROLLED_SECTION
                                                SET GRADE = -1
                                                WHERE ENROLLMENT_ID IN (SELECT ENROLLMENT_ID
                                                                        FROM ENROLLMENT
                                                                        WHERE PAYMENT_STATUS = -1);");
            $dropunpaidensec->execute();
        }
        $updatetime = $conn->prepare("UPDATE 1CURRENT_TIME SET PHASE = :phase, SEMESTER = :semester, ACADEMIC_YEAR = :academicyear");
        $updatetime->bindParam(":phase", $_SESSION['currenttime']['PHASE']);
        $updatetime->bindParam(":semester", $_SESSION['currenttime']['SEMESTER']);
        $updatetime->bindParam(":academicyear", $_SESSION['currenttime']['ACADEMIC_YEAR']);
        $updatetime->execute();
        $uid = $_SESSION['uid'];
        $urole = $_SESSION['urole'];
        session_unset();
        session_destroy();
        session_start();
        $_SESSION['uid'] = $uid;
        $_SESSION['urole'] = $urole;
        header("Location: main_db.php");
        } catch (PDOException $e){
            echo $e->getMessage();
        }
    }
?>