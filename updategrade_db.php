<?php
    session_start();
    require_once 'config/db.php';
    $i = 0;
    $j = 0;
    $k = 0;
    $isnull = 0;

    if (!isset($_SESSION['uid']) || !isset($_SESSION['urole'])){
        header("Location: login.php");
    } else {
        try {
            if (isset($_POST['sectiontoupdate'])) {
                $tampstudentinsection = $conn->prepare("SELECT u.USER_ID, u.FIRST_NAME, u.LAST_NAME, es.GRADE, es.ENROLLMENT_ID, es.SECTION_CODE, es.ENROLLMENT_ID, e.ACADEMIC_YEAR, e.SEMESTER
                                                        FROM (ENROLLED_SECTION es JOIN ENROLLMENT e ON es.ENROLLMENT_ID = e.ENROLLMENT_ID) JOIN USER_T u ON e.USER_ID = u.USER_ID 
                                                        WHERE es.SECTION_CODE = :sectioncode
                                                        AND e.ACADEMIC_YEAR = :academicyear
                                                        AND e.SEMESTER = :semester
                                                        AND e.PAYMENT_STATUS = 1
                                                        AND (GRADE != -1 OR GRADE IS NULL);");
                $tampstudentinsection->bindParam(':sectioncode', $_POST['sectiontoupdate']);
                $tampstudentinsection->bindParam(':academicyear', $_SESSION['currenttime']['ACADEMIC_YEAR']);
                $tampstudentinsection->bindParam(':semester', $_SESSION['currenttime']['SEMESTER']);
                $tampstudentinsection->execute();
                $tampstudentinsection = $tampstudentinsection->fetchAll(PDO::FETCH_BOTH);
                $_SESSION['studentinsection'] = $tampstudentinsection;
                if (!isset($_SESSION['studentinsection'][0])) {
                    $_SESSION['error'] = "There is no student in this section.";
                } else {
                    $_SESSION['sectiontoupdate'] = $_POST['sectiontoupdate'];
                    unset($_POST['sectiontoupdate']);
                }
            } 
            if (isset($_POST['grade0'])) {
                while (isset($_SESSION['studentinsection'][$i])) {
                    $updategrade = $conn->prepare("UPDATE ENROLLED_SECTION SET GRADE = :grade WHERE ENROLLMENT_ID = :enrollmentid AND SECTION_CODE = :sectioncode;");
                    $updategrade->bindParam(':grade', $_POST['grade'.$i]);
                    $updategrade->bindParam(':enrollmentid', $_SESSION['studentinsection'][$i]['ENROLLMENT_ID']);
                    $updategrade->bindParam(':sectioncode', $_SESSION['sectiontoupdate']);
                    $updategrade->execute();
                    $tempgradeuser = $conn->prepare("SELECT GRADE FROM ENROLLED_SECTION WHERE ENROLLMENT_ID = :enrollmentid;");
                    $tempgradeuser->bindParam(':enrollmentid', $_SESSION['studentinsection'][$i]['ENROLLMENT_ID']);
                    $tempgradeuser->execute();
                    $tempgradeuser = $tempgradeuser->fetchAll(PDO::FETCH_ASSOC);
                    $j = 0;
                    $isnull = 0;
                    while (isset($tempgradeuser[$j])) {
                        if(strlen($tempgradeuser[$j]['GRADE']) == 0){
                            $isnull = 1;
                            break;
                        }
                        $j++;
                    }
                    if ($isnull == 0) {
                        $updategpa = $conn->prepare("UPDATE ENROLLMENT
                                                        SET GPA = (SELECT SUM(GRADE * CREDIT) / SUM(CREDIT)
                                                                    FROM (((PRICE_COURSE pc JOIN COURSE c ON pc.PRICE_RATE_ID = c.PRICE_RATE_ID) 
                                                                        JOIN SECTION s ON c.COURSE_CODE = s.COURSE_CODE) 
                                                                        JOIN ENROLLED_SECTION es ON s.SECTION_CODE = es.SECTION_CODE) 
                                                                        JOIN ENROLLMENT e ON es.ENROLLMENT_ID = e.ENROLLMENT_ID
                                                                    WHERE es.ENROLLMENT_ID = :enrollmentid
                                                                    AND (GRADE != -1 OR GRADE IS NULL)),
                                                            TOTAL_CREDIT = (SELECT SUM(CREDIT)
                                                                            FROM (((PRICE_COURSE pc JOIN COURSE c ON pc.PRICE_RATE_ID = c.PRICE_RATE_ID) 
                                                                                JOIN SECTION s ON c.COURSE_CODE = s.COURSE_CODE) 
                                                                                JOIN ENROLLED_SECTION es ON s.SECTION_CODE = es.SECTION_CODE) 
                                                                                JOIN ENROLLMENT e ON es.ENROLLMENT_ID = e.ENROLLMENT_ID
                                                                            WHERE es.ENROLLMENT_ID = :enrollmentid
                                                                            AND (GRADE != -1 OR GRADE IS NULL))
                                                            WHERE ENROLLMENT_ID = :enrollmentid;");
                        $updategpa->bindParam(':enrollmentid', $_SESSION['studentinsection'][$i]['ENROLLMENT_ID']);
                        $updategpa->execute();
                        $updategpax = $conn->prepare("UPDATE USER_T
                                                        SET GPAX = (SELECT SUM(GPA * TOTAL_CREDIT) / SUM(TOTAL_CREDIT)
                                                                    FROM ENROLLMENT
                                                                    WHERE USER_ID = :userid
                                                                    AND GPA IS NOT NULL),
                                                            TOTAL_CREDIT = (SELECT SUM(TOTAL_CREDIT)
                                                                            FROM ENROLLMENT
                                                                            WHERE USER_ID = :userid
                                                                            AND GPA IS NOT NULL)
                                                        WHERE USER_ID = :userid;");
                        $updategpax->bindParam(':userid', $_SESSION['studentinsection'][$i]["USER_ID"]);
                        $updategpax->execute();
                    }
                    $i++;
                }
                unset($_SESSION['sectiontoupdate']);
                unset($_SESSION['studentinsection']);
                $_SESSION['success'] = "Grade updated successfully.";
            }
            $allsection = $conn->prepare("SELECT SECTION_CODE FROM SECTION;");
            $allsection->execute();
            $allsection = $allsection->fetchAll(PDO::FETCH_NUM);
            $_SESSION['allsection'] = $allsection;
            header("Location: updategrade.php");
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
?>