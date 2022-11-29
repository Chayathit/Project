<?php 
    session_start(); 
    require_once 'config/db.php';
    $i = 0;
    $j = 0;
    $k = 1;
    
    $_SESSION['totalprice'] = 0;
    $_SESSION['totalcredit'] = 0;

    if (!isset($_SESSION['uid']) || !isset($_SESSION['urole'])){
        header("Location: login.php");
    } else {
        try {
            if (isset(($_SESSION['enrolledcoursedata']))){
                unset($_SESSION['enrolledcoursedata']);
            }
            if (isset($_SESSION['enrollment']['ENROLLMENT_ID'])) {
                $billstatus = $conn->prepare("SELECT TRANSITION_NUMBER FROM BILL_PAYMENT WHERE ENROLLMENT_ID = :enrollmentid");
                $billstatus->bindParam(":enrollmentid", $_SESSION['enrollmentid']);
                $billstatus->execute();
                $billstatus = $billstatus->fetchAll(PDO::FETCH_NUM);
                $grade = $conn->prepare("SELECT GRADE FROM ENROLLED_SECTION WHERE ENROLLMENT_ID = :enrollmentid");
                $grade->bindParam(":enrollmentid", $_SESSION['enrollmentid']);
                $grade->execute();
                $grade = $grade->fetchAll(PDO::FETCH_NUM);
                $gpa = $conn->prepare("SELECT GPA FROM ENROLLMENT WHERE ENROLLMENT_ID = :enrollmentid");
                $gpa->bindParam(":enrollmentid", $_SESSION['enrollmentid']);
                $gpa->execute();
                $gpa = $gpa->fetch(PDO::FETCH_ASSOC);
                $sec = $conn->prepare("SELECT SECTION_CODE FROM ENROLLED_SECTION WHERE ENROLLMENT_ID = :enrollmentid");
                $sec->bindParam(":enrollmentid", $_SESSION['enrollmentid']);
                $sec->execute();
                $sec = $sec->fetchAll(PDO::FETCH_NUM);
                $_SESSSION['billstatus'] = $billstatus;
                $dropbill = $conn->prepare("DELETE FROM BILL_PAYMENT WHERE ENROLLMENT_ID = :enrollmentid");
                $dropbill->bindParam(":enrollmentid", $_SESSION['enrollmentid']);
                $dropbill->execute();
                $dropenrollsec = $conn->prepare("DELETE FROM ENROLLED_SECTION WHERE ENROLLMENT_ID LIKE :enrollmentid");
                $dropenrollsec->bindParam(":enrollmentid", $_SESSION['enrollmentid']);
                $dropenrollsec->execute();
                $dropenroll = $conn->prepare("DELETE FROM ENROLLMENT WHERE ENROLLMENT_ID = :enrollmentid");
                $dropenroll->bindParam(":enrollmentid", $_SESSION['enrollmentid']);
                $dropenroll->execute();
            }
            $i = 0;
            if (isset($_POST['selectedsection0'])){
                while (isset($_POST['selectedsection'.$i])){
                    $sectiontoenroll[$i] = $_POST['selectedsection'.$i];
                    unset($_POST['selectedsection'.$i]);
                    $i++;
                }
            } else {
                while (isset($sec[$i])){
                    $sectiontoenroll[$i] = $sec[$i][0];
                    unset($sec[$i]);
                    $i++;
                }
            }
            $i = 0;
            while (isset($sectiontoenroll[$i])){
                $coursedata = $conn->prepare("SELECT c.COURSE_CODE, c.COURSE_TITLE, pc.CREDIT, s.SECTION_CODE, t.FIRST_NAME, pc.PRICE, pc.CREDIT
                                                FROM (((COURSE c JOIN SECTION s ON c.COURSE_CODE = s.COURSE_CODE) JOIN PRICE_COURSE pc ON c.Price_Rate_ID = pc.Price_Rate_ID) JOIN SECTION_TEACHER st ON s.SECTION_CODE = st.SECTION_CODE) JOIN TEACHER t ON t.TEACHER_ID = st.TEACHER_ID
                                                WHERE s.SECTION_CODE = :sectioncode;");
                $coursedata->bindParam(":sectioncode", $sectiontoenroll[$i]);
                $coursedata->execute();
                $coursedata = $coursedata->fetch(PDO::FETCH_NUM);
                $_SESSION['enrolledcoursedata'][$i] = $coursedata;
                $_SESSION['totalprice'] += $coursedata[5];
                $_SESSION['totalcredit'] += $coursedata[6];
                $i++;
            }
            $i = 0;
            $create_enrollment = $conn->prepare("INSERT INTO ENROLLMENT (ENROLLMENT_ID, USER_ID, TOTAL_PRICE, PAYMENT_STATUS, SEMESTER, ACADEMIC_YEAR, GPA, TOTAL_CREDIT)
                                                    VALUES (:enrollmentid, :userid, :totalprice, :paymentstatus, :semester, :academicyear, :gpa, :totalcredit);");
            $create_enrollment->bindParam(":enrollmentid", $_SESSION['enrollmentid']);
            $create_enrollment->bindParam(":userid", $_SESSION['uid']);
            $create_enrollment->bindParam(":totalprice", $_SESSION['totalprice']);
            if ($_SESSION['havegrade'] == -1){
                $create_enrollment->bindParam(":paymentstatus", $_SESSION['havegrade']);
            } else if ($_SESSION['paid'][0] == 3){
                $create_enrollment->bindParam(":paymentstatus", $k);
            } else {
                $create_enrollment->bindParam(":paymentstatus", $i);
            }
            $create_enrollment->bindParam(":semester", $_SESSION['currenttime']['SEMESTER']);
            $create_enrollment->bindParam(":academicyear", $_SESSION['currenttime']['ACADEMIC_YEAR']);
            $create_enrollment->bindParam(":gpa", $gpa['GPA']);
            $create_enrollment->bindParam(":totalcredit", $_SESSION['totalcredit']);
            $create_enrollment->execute();
            while (isset($sectiontoenroll[$i])){
                $insert_enrolledsection = $conn->prepare("INSERT INTO ENROLLED_SECTION (ENROLLMENT_ID, SECTION_CODE, GRADE)
                                                            VALUES (:enrollmentid, :sectioncode, :grade);");
                $insert_enrolledsection->bindParam(":enrollmentid", $_SESSION['enrollmentid']);
                $insert_enrolledsection->bindParam(":sectioncode", $sectiontoenroll[$i]);
                if ($_SESSION['havegrade'] == -1){
                    $insert_enrolledsection->bindParam(":grade", $_SESSION['havegrade']);
                } else {
                    $insert_enrolledsection->bindParam(":grade", $grade[$i][0]);
                }
                $insert_enrolledsection->bindParam(":grade", $grade[$i][0]);
                $insert_enrolledsection->execute();
                $i++;
            }

            $price[0] = $_SESSION['totalprice'] * 0.5;
            $price[1] = $_SESSION['totalprice'] * 0.25;
            $price[2] = $_SESSION['totalprice'] * 0.25;
            for ($j = 1; $j < 4; $j++){
                $insert_bill = $conn->prepare("INSERT INTO BILL_PAYMENT (PAYMENT_ID, ENROLLMENT_ID, TRANSITION_NUMBER, DUE_PHASE, PRICE)
                                                VALUES (:paymentid, :enrollmentid, :transitionnum, $j, :price);");
                $paymentid = $_SESSION['enrollmentid'].$j;
                $insert_bill->bindParam(":paymentid", $paymentid);
                $insert_bill->bindParam(":enrollmentid", $_SESSION['enrollmentid']);
                $insert_bill->bindParam(":transitionnum", $_SESSSION['billstatus'][$j-1][0]);
                $insert_bill->bindParam(":price", $price[$j-1]);
                $insert_bill->execute();
            }
            header("Location: enrollment.php");
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    } 
?>