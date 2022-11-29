<?php
    session_start(); 
    require_once 'config/db.php';

    if (!isset($_SESSION['uid']) || !isset($_SESSION['urole'])){
        header("Location: login.php");
    } else{
        try{
            $analytic1 = $conn->prepare("SELECT SUPPLY_TYPE, SUM(CASE WHEN SUPPLY_STATUS = 'Borrowed' THEN 1 END) 
                                            FROM SCHOOL_SUPPLIES 
                                            GROUP BY SUPPLY_TYPE 
                                            ORDER BY SUM(CASE WHEN SUPPLY_STATUS = 'Borrowed' THEN 1 END) DESC;");
            $analytic1->execute();
            $analytic1 = $analytic1->fetchAll(PDO::FETCH_NUM);
            $_SESSION['analytic1'] = $analytic1;
            $analytic2 = $conn->prepare("SELECT COURSE_CODE,COUNT(es.SECTION_CODE),s.MAX_STUDENT + s.MAX_STUDENT, (COUNT(es.SECTION_CODE)/(s.MAX_STUDENT + s.MAX_STUDENT))*100
                                            FROM (ENROLLED_SECTION es JOIN SECTION s ON es.SECTION_CODE = s.SECTION_CODE) JOIN ENROLLMENT e ON e.ENROLLMENT_ID = es.ENROLLMENT_ID
                                            WHERE e.ACADEMIC_YEAR = :academicyear AND e.SEMESTER = :semester
                                            GROUP BY COURSE_CODE 
                                            ORDER BY COUNT(es.SECTION_CODE) DESC;");
            $analytic2->bindParam(":academicyear", $_SESSION['currenttime']['ACADEMIC_YEAR']);
            $analytic2->bindParam(":semester", $_SESSION['currenttime']['SEMESTER']);
            $analytic2->execute();
            $analytic2 = $analytic2->fetchAll(PDO::FETCH_NUM);
            $_SESSION['analytic2'] = $analytic2;
            $analytic3 = $conn->prepare("SELECT COURSE_CODE, USER_ID, COUNT(*), s.SECTION_CODE
                                            FROM ENROLLMENT e JOIN ENROLLED_SECTION es ON e.ENROLLMENT_ID = es.ENROLLMENT_ID JOIN SECTION s ON es.SECTION_CODE = s.SECTION_CODE
                                            GROUP BY e.USER_ID, COURSE_CODE
                                            HAVING COUNT(e.USER_ID) > 1;");
            $analytic3->execute();
            $analytic3 = $analytic3->fetchAll(PDO::FETCH_NUM);
            $_SESSION['analytic3'] = $analytic3;
            $analytic4 = $conn->prepare("SELECT USER_ID, ACADEMIC_YEAR, SEMESTER, GPA FROM ENROLLMENT ORDER BY GPA DESC;");
            $analytic4->execute();
            $analytic4 = $analytic4->fetchAll(PDO::FETCH_NUM);
            $_SESSION['analytic4'] = $analytic4;
            $analytic5 = $conn->prepare("SELECT COURSE_CODE, ACADEMIC_YEAR, SEMESTER, SUM(grade)/COUNT(grade) 
                                            FROM ((ENROLLED_SECTION es JOIN SECTION s ON es.SECTION_CODE = s.SECTION_CODE) JOIN ENROLLMENT e ON e.ENROLLMENT_ID = es.ENROLLMENT_ID) 
                                            WHERE GRADE != -1
                                            GROUP BY COURSE_CODE
                                            ORDER BY SUM(grade)/COUNT(grade) DESC;");
            $analytic5->execute();
            $analytic5 = $analytic5->fetchAll(PDO::FETCH_NUM);
            $_SESSION['analytic5'] = $analytic5;
            header("Location: analytics.php");
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
?>
