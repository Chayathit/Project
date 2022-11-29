<?php 
    session_start(); 
    require_once 'config/db.php';

    $i = 0;
    if (!isset($_SESSION['uid']) || !isset($_SESSION['urole'])){
        header("Location: login.php");
    } else {
        try {
            if (isset($_POST['borrownb']) || isset($_POST['borrowlt'])) {
                $borrowed = $conn->prepare("SELECT SUPPLY_TYPE 
                                            FROM BORROW_LIST bl JOIN SCHOOL_SUPPLIES ss ON bl.SUPPLY_ID = ss.SUPPLY_ID
                                            WHERE USER_ID = :userid");
                $borrowed->bindParam(":userid", $_SESSION['uid']);
                $borrowed->execute();
                $borrowed = $borrowed->fetchALL(PDO::FETCH_NUM);
                $nb = 0;
                $tl = 0;
                while (isset($borrowed[$i])){
                    if($borrowed[$i][0] == "Notebook"){
                        $nb++;
                    } else if ($borrowed[$i][0] == "Tablet"){
                        $tl++;
                    }
                    $i++;
                }
                if (isset($_POST['borrownb'])){
                    $idtoborrow = $_POST['nbidtoborrow'];
                } else {
                    $idtoborrow = $_POST['tlidtoborrow'];
                }
                $checkexist = $conn->prepare("SELECT SUPPLY_ID FROM SCHOOL_SUPPLIES WHERE SUPPLY_ID = :supplyid");
                $checkexist->bindParam(":supplyid", $idtoborrow);
                $checkexist->execute();
                $checkexist = $checkexist->fetch(PDO::FETCH_NUM);
                if (strlen($checkexist[0]) == 0) {
                    $_SESSION['error'] = "Supply ID does not exist.";
                } else {
                    $wanttoborrow = $conn->prepare("SELECT SUPPLY_ID, SUPPLY_TYPE, MODELS FROM SCHOOL_SUPPLIES WHERE SUPPLY_ID = :supplyid;"); 
                    $wanttoborrow->bindParam(":supplyid", $idtoborrow);
                    $wanttoborrow->execute();
                    $wanttoborrow = $wanttoborrow->fetch(PDO::FETCH_NUM);
                    if (($wanttoborrow[1] == "Notebook") && ($nb > 0)) {
                        $_SESSION['error'] = "You already borrowed a notebook in this semester.";
                    } else if (($wanttoborrow[1] == "Tablet") && ($tl > 0)){
                        $_SESSION['error'] = "You already borrowed a tablet in this semester.";
                    } else {
                        $borrow = $conn->prepare("INSERT INTO BORROW_LIST (USER_ID, SUPPLY_ID, ACADEMIC_YEAR, SEMESTER, BORROW_STATUS) 
                                                    VALUES (:userid, :supplyid, :academicyear, :semester, 'BORROWED');");
                        $borrow->bindParam(":userid", $_SESSION['uid']);
                        $borrow->bindParam(":supplyid", $wanttoborrow[0]);
                        $borrow->bindParam(":academicyear", $_SESSION['currenttime']['ACADEMIC_YEAR']);
                        $borrow->bindParam(":semester", $_SESSION['currenttime']['SEMESTER']);
                        $borrow->execute();
                        $updatesupply = $conn->prepare("UPDATE SCHOOL_SUPPLIES SET SUPPLY_STATUS = 'Borrowed' WHERE SUPPLY_ID = :supplyid;");
                        $updatesupply->bindParam(":supplyid", $wanttoborrow[0]);
                        $updatesupply->execute();
                        $_SESSION['success'] = "You have successfully borrowed a ".$wanttoborrow[0]." ".$wanttoborrow[2].".";
                    }
                }
            } 
            if (isset($_POST['returnsp'])){
                $checkexist = $conn->prepare("SELECT SUPPLY_ID FROM SCHOOL_SUPPLIES WHERE SUPPLY_ID = :supplyid");
                $checkexist->bindParam(":supplyid", $_POST['returnsupply']);
                $checkexist->execute();
                $checkexist = $checkexist->fetch(PDO::FETCH_NUM);
                if (strlen($checkexist[0]) == 0) {
                    $_SESSION['error'] = "Supply ID does not exist.";
                } else {
                    $borrowed = $conn->prepare("SELECT ss.SUPPLY_ID, ss.MODELS 
                                                FROM BORROW_LIST bl JOIN SCHOOL_SUPPLIES ss ON bl.SUPPLY_ID = ss.SUPPLY_ID
                                                WHERE USER_ID = :userid AND ss.SUPPLY_ID = :supplyid AND BORROW_STATUS = 'BORROWED';");
                    $borrowed->bindParam(":userid", $_SESSION['uid']);
                    $borrowed->bindParam(":supplyid", $_POST['returnsupply']);
                    $borrowed->execute();
                    $borrowed = $borrowed->fetch(PDO::FETCH_NUM);
                    if (isset($borrowed[0])){
                        $updatesupply = $conn->prepare("UPDATE SCHOOL_SUPPLIES SET SUPPLY_STATUS = 'Available' WHERE SUPPLY_ID = :supplyid;");
                        $updatesupply->bindParam(":supplyid", $borrowed[0]);
                        $updatesupply->execute();
                        $updateborrowlist = $conn->prepare("UPDATE BORROW_LIST SET BORROW_STATUS = 'RETURNED' WHERE SUPPLY_ID = :supplyid AND USER_ID = :userid;");
                        $updateborrowlist->bindParam(":supplyid", $borrowed[0]);
                        $updateborrowlist->bindParam(":userid", $_SESSION['uid']);
                        $updateborrowlist->execute();
                        $_SESSION['success'] = "You have successfully returned a ".$borrowed[0]." ".$borrowed[1].".";
                    } else {
                        $_SESSION['error'] = "You did not borrowed ".$_POST['returnsupply'].".";
                    }
                }
            }
            $searchnotebook = $conn->prepare("SELECT SUPPLY_ID, MODELS FROM SCHOOL_SUPPLIES WHERE SUPPLY_STATUS = 'Available' AND SUPPLY_TYPE = 'Notebook';");
            $searchnotebook->execute();
            $searchnotebook = $searchnotebook->fetchAll(PDO::FETCH_NUM);
            $searchtablet = $conn->prepare("SELECT SUPPLY_ID, MODELS FROM SCHOOL_SUPPLIES WHERE SUPPLY_STATUS = 'Available' AND SUPPLY_TYPE = 'Tablet';");
            $searchtablet->execute();
            $searchtablet = $searchtablet->fetchAll(PDO::FETCH_NUM);
            $_SESSION['searchnotebook'] = $searchnotebook;
            $_SESSION['searchtablet'] = $searchtablet;
            $notreturn = $conn->prepare("SELECT bl.SUPPLY_ID, ss.MODELS, bl.BORROW_STATUS 
                                            FROM BORROW_LIST bl JOIN SCHOOL_SUPPLIES ss ON bl.SUPPLY_ID = ss.SUPPLY_ID
                                            WHERE bl.USER_ID = :userid AND bl.BORROW_STATUS = 'BORROWED';");
            $notreturn->bindParam(":userid", $_SESSION['uid']);
            $notreturn->execute();
            $notreturn = $notreturn->fetchAll(PDO::FETCH_NUM);
            $_SESSION['notreturn'] = $notreturn;
            header("Location: borrow.php");
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
?>