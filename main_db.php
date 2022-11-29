<?php 
    session_start(); 
    require_once 'config/db.php';

    if (!isset($_SESSION['uid']) || !isset($_SESSION['urole'])){
        header("Location: login.php");
    } else {
        try {
            $udata = $conn->prepare("SELECT FIRST_NAME, LAST_NAME, YEAR, DEPARTMENT, GPAX, TOTAL_CREDIT FROM USER_T WHERE USER_ID = :userid;");
            $udata->bindParam(":userid", $_SESSION['uid']);
            $udata->execute();
            $udata = $udata->fetch(PDO::FETCH_ASSOC);
            $currenttime = $conn->prepare("SELECT * FROM 1CURRENT_TIME;");
            $currenttime->execute();
            $currenttime = $currenttime->fetch(PDO::FETCH_ASSOC);
            $_SESSION['currenttime'] = $currenttime;
            $_SESSION['udata'] = $udata;
            $_SESSION['enrollmentid'] = $_SESSION['uid'] . $_SESSION['currenttime']['SEMESTER'] . $_SESSION['currenttime']['ACADEMIC_YEAR'];
            $enrollment = $conn->prepare("SELECT * FROM ENROLLMENT WHERE ENROLLMENT_ID = :enrollmentid");
            $enrollment->bindParam(":enrollmentid",  $_SESSION['enrollmentid']);
            $enrollment->execute();
            $enrollment = $enrollment->fetch(PDO::FETCH_ASSOC);
            $_SESSION['enrollment'] = $enrollment;
            $_SESSION['paymentid'][0] = $_SESSION['enrollmentid'] . "1";
            $_SESSION['paymentid'][1] = $_SESSION['enrollmentid'] . "2";
            $_SESSION['paymentid'][2] = $_SESSION['enrollmentid'] . "3";
            $_SESSION['paid'][0] = 0;
            for ($i = 0; $i < 3; $i++){
                $transitionnum_check = $conn->prepare("SELECT TRANSITION_NUMBER FROM BILL_PAYMENT WHERE PAYMENT_ID = :paymentid");
                $transitionnum_check->bindParam(":paymentid", $_SESSION['paymentid'][$i]);
                $transitionnum_check->execute();
                $transitionnum_check = $transitionnum_check->fetch(PDO::FETCH_ASSOC);
                if ($transitionnum_check['TRANSITION_NUMBER'] != NULL){
                    $_SESSION['paid'][0] = $_SESSION['paid'][0] + 1;
                    $_SESSION['paid'][$i+1] = 1;
                } else {
                    $_SESSION['paid'][$i+1] = 0;
                }
            }
            $grade_check = $conn->prepare("SELECT GRADE FROM ENROLLED_SECTION WHERE ENROLLMENT_ID = :enrollmentid");
            $grade_check->bindParam(":enrollmentid", $_SESSION['enrollmentid']);
            $grade_check->execute();
            $grade_check = $grade_check->fetchALL(PDO::FETCH_NUM);
            $_SESSION['havegrade'] = 0;
            $i = 0;
            while (isset($grade_check[$i])){
                if (($grade_check[$i][0] != NULL) && ($grade_check[$i][0] == -1)){
                    $_SESSION['havegrade'] = -1;
                    break;
                }
                else if (($grade_check[$i][0] != NULL) && ($grade_check[$i][0] != 0)){
                    $_SESSION['havegrade'] = 1;
                } 
                $i++;
            }
            $getpassenrollment = $conn->prepare("SELECT ACADEMIC_YEAR FROM ENROLLMENT WHERE ENROLLMENT_ID LIKE :likeenrollmentid GROUP BY ACADEMIC_YEAR;");
            $likeenrollmentid = $_SESSION['uid'] . "%";
            $getpassenrollment->bindParam(":likeenrollmentid", $likeenrollmentid);
            $getpassenrollment->execute();
            $getpassenrollment = $getpassenrollment->fetchALL(PDO::FETCH_NUM);
            $_SESSION['passenrollment'] = $getpassenrollment;
            if (isset($_SESSION['gradehist'])){
                unset($_SESSION['gradehist']);
            }
            if (isset($_SESSION['gpahist'])){
                unset($_SESSION['gpahist']);
            }
            if (isset($_POST['gradehist'])){
                echo $_POST['aytosearch'] . " " . $_POST['smtosearch'];
                $getgpahist = $conn->prepare("SELECT GPA, PAYMENT_STATUS
                                                FROM ENROLLMENT
                                                WHERE USER_ID = :userid AND ACADEMIC_YEAR = :academicyear AND SEMESTER = :semester;");
                $getgpahist->bindParam(":userid", $_SESSION['uid']);
                $getgpahist->bindParam(":academicyear", $_POST['aytosearch']);
                $getgpahist->bindParam(":semester", $_POST['smtosearch']);
                $getgpahist->execute();
                $getgpahist = $getgpahist->fetch(PDO::FETCH_NUM);
                $_SESSION['gpahist'] = $getgpahist;
                if ($_SESSION['gpahist'][0] == NULL){
                    $_SESSION['error'] = "No enrollment found for this semester.";
                } else if ($_SESSION['gpahist'][1] == -1){
                    $_SESSION['error'] = "You have not paid for this semester.";
                } else {
                    $getgradehist = $conn->prepare("SELECT SECTION_CODE, GRADE 
                                                    FROM ENROLLED_SECTION es JOIN ENROLLMENT e ON es.ENROLLMENT_ID = e.ENROLLMENT_ID 
                                                    WHERE e.USER_ID = :userid AND e.ACADEMIC_YEAR = :academicyear AND e.SEMESTER = :semester;");
                    $getgradehist->bindParam(":userid", $_SESSION['uid']);
                    $getgradehist->bindParam(":academicyear", $_POST['aytosearch']);
                    $getgradehist->bindParam(":semester", $_POST['smtosearch']);
                    $getgradehist->execute();
                    $getgradehist = $getgradehist->fetchALL(PDO::FETCH_NUM);
                    $_SESSION['gradehist'] = $getgradehist;
                }
            }
            header("Location: main.php");
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
?>