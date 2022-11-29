<?php
    session_start();
    require_once 'config/db.php';
    if (!isset($_SESSION['uid']) || !isset($_SESSION['urole'])){
        header("Location: login.php");
    }
    $i = 0;

    try {
        $searchdepartment = $conn->prepare("SELECT DEPARTMENT FROM USER_T GROUP BY DEPARTMENT;");
        $searchdepartment->execute();
        $searchdepartment = $searchdepartment->fetchAll(PDO::FETCH_NUM);
        $_SESSION['searchdepartment'] = $searchdepartment;
        if(isset($_POST['corecourse'])){
            if (strlen($_SESSION['pcid'] == 0)){
                $insertprice = $conn->prepare("INSERT INTO PRICE_COURSE (PRICE_RATE_ID, PRICE, CREDIT) VALUES (:pcid, :price, :credit);");
                $pcid = "PR" . $_SESSION['newcd'] . $_SESSION['newcp'];
                $insertprice->bindParam(":pcid", $pcid);
                $insertprice->bindParam(":price", $_SESSION['newcp']);
                $insertprice->bindParam(":credit", $_SESSION['newcd']);
                $insertprice->execute();
                $_SESSION['pcid'] = $pcid;
            }
            $insertnewcourse = $conn->prepare("INSERT INTO COURSE (COURSE_CODE, COURSE_TITLE, PRICE_RATE_ID) VALUES (:coursecode, :coursetitle, :pcid);");
            $insertnewcourse->bindParam(":coursecode", $_SESSION['newcc']);
            $insertnewcourse->bindParam(":coursetitle", $_SESSION['newct']);
            $insertnewcourse->bindParam(":pcid", $_SESSION['pcid']);
            $insertnewcourse->execute();
            while (isset($_SESSION['newsc'][$i])){
                $insertnewsec = $conn->prepare("INSERT INTO SECTION (SECTION_CODE, COURSE_CODE, MAX_STUDENT) VALUES (:sectioncode, :coursecode, :maxstudent);");
                $insertnewsec->bindParam(":sectioncode", $_SESSION['newsc'][$i]);
                $insertnewsec->bindParam(":coursecode", $_SESSION['newcc']);
                $insertnewsec->bindParam(":maxstudent", $_SESSION['newsecms'][$i]);
                $insertnewsec->execute();
                $insertsecteacher = $conn->prepare("INSERT INTO SECTION_TEACHER (SECTION_CODE, TEACHER_ID) VALUES (:sectioncode, :teacherid);");
                $insertsecteacher->bindParam(":sectioncode", $_SESSION['newsc'][$i]);
                $insertsecteacher->bindParam(":teacherid", $_SESSION['newsect'][$i]);
                $insertsecteacher->execute();
                $i++;
            } 
            $i = 0;
            while (isset($_SESSION['searchdepartment'][$i])){
                if(isset($_POST['dep'.$i])){
                    $insertcorecourse = $conn->prepare("INSERT INTO CORE_COURSE (COURSE_CODE, DEPARTMENT, CORE_COURSE, SEMESTER, YEAR) VALUES (:coursecode, :department, :corecourse, :semester, :year);");
                    $insertcorecourse->bindParam(":coursecode", $_SESSION['newcc']);
                    $insertcorecourse->bindParam(":department", $_SESSION['searchdepartment'][$i][0]);
                    $insertcorecourse->bindParam(":corecourse", $_POST['core'.$i]);
                    $insertcorecourse->bindParam(":semester", $_POST['semester'.$i]);
                    $insertcorecourse->bindParam(":year", $_POST['year'.$i]);
                    $insertcorecourse->execute();
                }
                $i++;
            }            
            $uid = $_SESSION['uid'];
            $urole = $_SESSION['urole'];
            $udata = $_SESSION['udata'];
            $enrollmentid = $_SESSION['enrollmentid'];
            $currenttime = $_SESSION['currenttime'];
            $payment = $_SESSION['paymentid'];
            $paid = $_SESSION['paid'];
            $havegrade = $_SESSION['havegrade'];
            session_unset();
            session_destroy();
            session_start();
            $_SESSION['uid'] = $uid;
            $_SESSION['urole'] = $urole;
            $_SESSION['udata'] = $udata;
            $_SESSION['enrollmentid'] = $enrollmentid;
            $_SESSION['currenttime'] = $currenttime;
            $_SESSION['paymentid'] = $payment;
            $_SESSION['paid'] = $paid;
            $_SESSION['havegrade'] = $havegrade;
            $_SESSION['success'] = "New course has been created successfully.";
            header("Location: newcourse.php");
        } else {
            header("Location: selectdepartment.php");
        }
        
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
?>