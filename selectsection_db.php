<?php 
    session_start(); 
    require_once 'config/db.php';
    $i = 0;
    $j = 0;
    $temp;

    if (!isset($_SESSION['uid']) || !isset($_SESSION['urole'])){
        header("Location: login.php");
    } else {
        try {
            if (!isset($_SESSION['enrolledcourse'][$i])){
                $_SESSION['error'] = "Please select at least one course.";
                header("Location: selectcourse.php");
            } else {
                while (isset($_SESSION['enrolledcourse'][$i])){
                    $ssection = $conn->prepare("SELECT c.COURSE_CODE, s.SECTION_CODE, t.FIRST_NAME, t.LAST_NAME, MAX_STUDENT
                                                FROM ((COURSE c JOIN SECTION s ON c.COURSE_CODE = s.COURSE_CODE) JOIN SECTION_TEACHER st ON s.SECTION_CODE = st.SECTION_CODE) JOIN TEACHER t ON st.TEACHER_ID = t.TEACHER_ID
                                                WHERE c.COURSE_CODE = :coursecode
                                                AND MAX_STUDENT > (SELECT COUNT(*)
                                                                    FROM ENROLLED_SECTION es JOIN SECTION s ON es.SECTION_CODE = s.SECTION_CODE
                                                                    WHERE s.COURSE_CODE = :coursecode)
                                                GROUP BY s.SECTION_CODE
                                                ORDER BY s.SECTION_CODE;");
                    $ssection->bindParam(":coursecode", $_SESSION['enrolledcourse'][$i][0]);
                    $ssection->execute();
                    $ssection = $ssection->fetchAll(PDO::FETCH_NUM);
                    $section[$i] = $ssection;
                    $i++;
                }
                $_SESSION['section'] = $section;
                $_SESSION['numstudentinsec'] = $numstudentinsec;
                header("Location: selectsection.php");
            }
        } catch (PDOException $e){
            echo $e->getMessage();
        }
    }
?>