<?php 
    session_start(); 
    require_once 'config/db.php';
    $i = 0;
    $j = 0;
    $k = -1;

    if (!isset($_SESSION['uid']) || !isset($_SESSION['urole'])){
        header("Location: login.php");
    } else {
        try {
            if (isset($_POST['dropedcourse'])) {
                if (!isset($_SESSION['enrolledcourse'][1])) {
                    unset($_SESSION['enrolledcourse'][0]);
                }
                else {
                    while (isset($_SESSION['enrolledcourse'][$j])){
                        if ($j == $_POST['dropedcourse']) {
                            unset($_SESSION['enrolledcourse'][$j]);
                            break;
                        }
                        $j++;
                    }
                    while (isset($_SESSION['enrolledcourse'][$j+1])) {
                        $_SESSION['enrolledcourse'][$j] = $_SESSION['enrolledcourse'][$j+1];
                        $j++;
                    }
                }
                unset($_SESSION['enrolledcourse'][$j]);
                unset($_POST['dropedcourse']);
            } else {
                if (isset($_SESSION['enrollment']['ENROLLMENT_ID'])) {
                    $enrolledcourse = $conn->prepare("SELECT c.COURSE_CODE, c.COURSE_TITLE, pr.CREDIT, pr.PRICE
                                                        FROM (((COURSE c JOIN CORE_COURSE cc ON c.COURSE_CODE = cc.COURSE_CODE) JOIN PRICE_COURSE pr ON c.PRICE_RATE_ID = pr.PRICE_RATE_ID) JOIN SECTION s ON c.COURSE_CODE = s.COURSE_CODE) JOIN ENROLLED_SECTION es ON s.SECTION_CODE = es.SECTION_CODE
                                                        WHERE es.ENROLLMENT_ID = :enrolledmentid
                                                        GROUP BY c.COURSE_CODE
                                                        ORDER BY c.COURSE_CODE;");
                    $enrolledcourse->bindParam(":enrolledmentid", $_SESSION['enrollment']['ENROLLMENT_ID']);
                    $enrolledcourse->execute();
                    $enrolledcourse = $enrolledcourse->fetchALL(PDO::FETCH_NUM);
                    $_SESSION['enrolledcourse'] = $enrolledcourse;
                }
                if (!isset($_SESSION['enrolledcourse'][0])){
                    $corecourse = $conn->prepare("SELECT c.COURSE_CODE, c.COURSE_TITLE, pr.CREDIT, pr.PRICE
                                                    FROM (COURSE c JOIN CORE_COURSE cc ON c.COURSE_CODE = cc.COURSE_CODE) JOIN PRICE_COURSE pr ON c.PRICE_RATE_ID = pr.PRICE_RATE_ID
                                                    WHERE cc.DEPARTMENT = (SELECT DEPARTMENT
                                                                            FROM USER_T
                                                                            WHERE USER_ID = :userid)
                                                    AND cc.YEAR = (SELECT YEAR
                                                                    FROM USER_T
                                                                    WHERE USER_ID = :userid)
                                                    AND cc.SEMESTER = :currentsemester
                                                    AND cc.CORE_COURSE = 1
                                                    ORDER BY c.COURSE_CODE;");
                    $corecourse->bindParam(":userid", $_SESSION['uid']);
                    $corecourse->bindParam(":currentsemester", $_SESSION['currenttime']['SEMESTER']);
                    $corecourse->execute();
                    $_SESSION['amountcorecourse'] = $corecourse->rowCount();
                    $corecourse = $corecourse->fetchALL(PDO::FETCH_NUM);
                    $_SESSION['corecourse'] = $corecourse;
                    $_SESSION['enrolledcourse'] = $corecourse;
                } 
            }
            $finishedcourse = $conn->prepare("SELECT c.COURSE_CODE
                                                FROM (COURSE c JOIN SECTION s ON c.COURSE_CODE = s.COURSE_CODE) JOIN ENROLLED_SECTION es ON s.SECTION_CODE = es.SECTION_CODE
                                                WHERE es.ENROLLMENT_ID LIKE :likeuserid
                                                AND es.ENROLLMENT_ID != :enrollmentid
                                                AND es.GRADE > 0;");
            $likeuid = $_SESSION['uid'] . '%';
            $finishedcourse->bindParam(":likeuserid", $likeuid);
            $finishedcourse->bindParam(":enrollmentid", $_SESSION['enrollmentid']);
            $finishedcourse->execute();
            $finishedcourse = $finishedcourse->fetchALL(PDO::FETCH_NUM);
            $_SESSION['finishedcourse'] = $finishedcourse;
            $searchcourse = $conn->prepare("SELECT c.COURSE_CODE, c.COURSE_TITLE
                                            FROM COURSE c JOIN CORE_COURSE cc ON c.COURSE_CODE = cc.COURSE_CODE
                                            WHERE cc.DEPARTMENT = (SELECT DEPARTMENT
                                                                    FROM USER_T
                                                                    WHERE USER_ID = :userid)
                                            AND cc.YEAR <= (SELECT YEAR
                                                            FROM USER_T
                                                            WHERE USER_ID = :userid)
                                            AND (cc.YEAR*10) + cc.SEMESTER <= ((SELECT YEAR
                                                                                FROM USER_T
                                                                                WHERE USER_ID = :userid)*10) + :currentsemester");
            $searchcourse->bindParam(":userid", $_SESSION['uid']);
            $searchcourse->bindParam(":likeuserid", $likeuid);
            $searchcourse->bindParam(":currentsemester", $_SESSION['currenttime']['SEMESTER']);
            $searchcourse->execute();
            $searchcourse = $searchcourse->fetchALL(PDO::FETCH_NUM);
            $_SESSION['searchcourse'] = $searchcourse;
            if (($_SESSION['currenttime']['PHASE'] != 1) && (!isset($_SESSION['enrollment']['ENROLLMENT_ID']))) {
                $_SESSION['error'] = "You can't enroll course now.";
                header("Location: main_db.php");
            } else if (($_SESSION['havegrade'] != 0) || (($_SESSION['currenttime']['PHASE'] != 1) && (isset($_SESSION['enrollment']['ENROLLMENT_ID'])))){
                header("Location: enrollment_db.php");
            } else if ($_SESSION['paid'][0] > 0) {
                header("Location: selectsection_db.php");
            } else {
                header("Location: selectcourse.php");
            }
        } catch (PDOException $e){
            echo $e->getMessage();
        }
    }
?>