<?php
    session_start();
    require_once 'config/db.php';

    $i = 0;
    $j = 0;
    $error = 0;
    $found = 0;

    if (!isset($_SESSION['uid']) || !isset($_SESSION['urole'])){
        header("Location: login.php");

    } else {
        try {
            if (isset($_POST['addcourse'])) {
                $availablecourse = $conn->prepare("SELECT c.COURSE_CODE
                                                    FROM COURSE c JOIN CORE_COURSE cc ON c.COURSE_CODE = cc.COURSE_CODE
                                                    WHERE cc.DEPARTMENT = (SELECT DEPARTMENT
                                                                            FROM USER_T
                                                                            WHERE USER_ID = :userid)
                                                    AND cc.YEAR <= (SELECT YEAR
                                                                    FROM USER_T
                                                                    WHERE USER_ID = :userid)
                                                    AND (cc.YEAR*10) + cc.SEMESTER <= ((SELECT YEAR
                                                                                        FROM USER_T
                                                                                        WHERE USER_ID = :userid)*10) + (SELECT SEMESTER FROM 1CURRENT_TIME);");
                $likeuid = $_SESSION['uid'] . '%';
                $availablecourse->bindParam(":userid", $_SESSION['uid']);
                $availablecourse->bindParam(":likeuserid", $likeuid);
                $availablecourse->execute();
                $availablecourse = $availablecourse->fetchALL(PDO::FETCH_NUM);
                while (isset($availablecourse[$i])) {
                    if ($availablecourse[$i][0] == $_POST['addcourse']) {
                        $found = 1;
                        break;
                    } 
                    $i++;
                }
                $i = 0;
                while (isset($_SESSION['enrolledcourse'][$i])) {
                    if ($_SESSION['enrolledcourse'][$i][0] == $_POST['addcourse']) {
                        $error = 1;
                        break;
                    }
                    $i++;
                }
                $i = 0;
                while (isset($_SESSION['finishedcourse'][$i])) {
                    if ($_SESSION['finishedcourse'][$i][0] == $_POST['addcourse']) {
                        $error = 2;
                        break;
                    }
                    $i++;
                }

                if ($found == 0) {
                    $_SESSION['error'] = "This Course is not available to add.";
                } else if ($error == 1) {
                    $_SESSION['error'] = "You are already enrolled in this course.";
                } else if ($error == 2) {
                    $_SESSION['error'] = "You have already finished this course before.";
                } 
                if (($error == 0) && ($found == 1)){
                    $addcoursedata = $conn->prepare("SELECT c.COURSE_CODE, c.COURSE_TITLE, pr.CREDIT, pr.PRICE
                                                        FROM (COURSE c JOIN CORE_COURSE cc ON c.COURSE_CODE = cc.COURSE_CODE) JOIN PRICE_COURSE pr ON c.PRICE_RATE_ID = pr.PRICE_RATE_ID
                                                        WHERE c.COURSE_CODE = :coursecode");
                    $addcoursedata->bindParam(":coursecode", $_POST['addcourse']);
                    $addcoursedata->execute();
                    $addcoursedata = $addcoursedata->fetch(PDO::FETCH_NUM);
                    $i = 0;
                    while (isset($_SESSION['enrolledcourse'][$j])) {
                        $j++;
                    }
                    $_SESSION['enrolledcourse'][$j] = $addcoursedata;
                    unset($_POST['addcourse']);
                    header("Location: selectcourse.php");
                }
                header("Location: selectcourse.php");
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
?>

