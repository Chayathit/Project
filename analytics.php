<?php 
    session_start(); 
    if (!isset($_SESSION['uid']) || !isset($_SESSION['urole'])){
        header("Location: login.php");
    }
    $i = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration - Analytics</title>

    <link rel="stylesheet" href="mystyle.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>
<body class="bodyMain">
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="main_db.php">Home</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="selectcourse_db.php">Student Registration</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="borrow_db.php">Borrow School Supply</a>
                        </li>
                        <?php if ($_SESSION['urole'] == 'Moderator') { ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Moderator
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="addsupply_db.php">Add New Supplies</a></li>
                                <li><a class="dropdown-item" href="editsupply_db.php">Modify Supplies</a></li>
                                <li><a class="dropdown-item" href="updategrade_db.php">Update Grades</a></li>
                            </ul>
                        </li>
                        <?php } ?>
                        <?php if ($_SESSION['urole'] == 'Admin') { ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Admin
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="newcourse.php">Add New Courses</a></li>
                            </ul>
                        </li>
                        <?php } ?>
                        <li class="nav-item">
                            <a class="nav-link" href="analytics_db.php">Analytics</a>
                        </li>
                    </ul>
                    <ul class="navbar-nav mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link">Academic Year: <?php echo $_SESSION['currenttime']['ACADEMIC_YEAR'] ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link">Semester: <?php echo $_SESSION['currenttime']['SEMESTER'] ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link">Phase: <?php echo $_SESSION['currenttime']['PHASE'] ?></a>
                    </li>
                    <form action="timeskip_db.php" method="post">
                        <?php $_SESSION['previourpage'] = "main_db.php" ?>
                        <button type="submit" class="btn btn-outline-secondary" name="timeskip">Time Skip</button>
                    </form>
                    <li class="nav-item">
                        <a class="nav-link">User ID: <?php echo $_SESSION['uid'] ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link">Role: <?php echo $_SESSION['urole'] ?></a>
                    </li>
                </ul>
                <a href="signout.php"><button class="btn btn-outline-primary" type="submit" href=>Sign out</button></a>
                </div>
            </div>
        </nav>
    </header>
    <br>
    <div class="container">
        <div class="row justify-content-md-center">
            <p class="fontTopic">Advance Analysis Report</p>
        </div>
    </div>
    <br><br>
    <div class="container">
        <div class="fontAnalysis">
            <p>Analysis 1: The most borrowed school supplies type</p>
        </div>
        <div class="row justify-content-md-center">
            <table class="tableRegist">
                <tr>
                    <!-- <th>Supply ID</th> -->
                    <th>Supply Type</th>
                    <th>Amount Borrowed Supply</th>
                </tr>
                <?php while ((isset($_SESSION['analytic1'][$i])) && $i < 10){ ?>
                <tr>
                    <?php for ($j = 0; $j < 2; $j++){ ?>
                        <td>
                            <?php 
                                echo $_SESSION['analytic1'][$i][$j];
                            ?>
                        </td>
                    <?php } ?>
                </tr>
                <?php $i++; } $i = 0;?>
            </table>
        </div>
    </div>
    <br>
    <div class="container">
        <div class="fontAnalysis">
            <p>Analysis 2: Percentage of Students in Course</p>
        </div>
        <div class="row justify-content-md-center">
            <table class="tableRegist">
                <tr>
                    <th>Course Code</th>
                    <th>Total Student in Course</th>
                    <th>Max Student</th>
                    <th>Percentage</th>
                </tr>
                <?php while ((isset($_SESSION['analytic2'][$i])) && $i < 10){ ?>
                <tr>
                    <?php for ($j = 0; $j < 4; $j++){ ?>
                        <td>
                            <?php 
                                echo $_SESSION['analytic2'][$i][$j];
                            ?>
                        </td>
                    <?php } ?>
                </tr>
                <?php $i++; } $i = 0;?>
            </table>
        </div>
    </div>
    <br>
    <div class="container">
        <div class="fontAnalysis">
            <p>Analysis 3: Number of time that Students Taking The Same Course Twice or More</p>
        </div>
        <div class="row justify-content-md-center">
            <table class="tableRegist">
                <tr>
                    <th>Course Code</th>
                    <th>Student ID</th>
                    <th>Number of time taking this Course</th>
                </tr>
                <?php while ((isset($_SESSION['analytic3'][$i])) && $i < 10){ ?>
                <tr>
                    <?php for ($j = 0; $j < 3; $j++){ ?>
                        <td>
                            <?php 
                                echo $_SESSION['analytic3'][$i][$j];
                            ?>
                        </td>
                    <?php } ?>
                </tr>
                <?php $i++; } $i = 0;?> 
            </table>
        </div>
    </div>
    <br>
    <div class="container">
        <div class="fontAnalysis">
            <p>Analysis 4: Calculate GPA</p>
        </div>
        <div class="row justify-content-md-center">
            <table class="tableRegist">
                <tr>
                    <th>Student ID</th>
                    <th>Academic Year</th>
                    <th>Semester</th>
                    <th>GPA</th>
                </tr>
                <?php while ((isset($_SESSION['analytic4'][$i])) && $i < 10){ ?>
                <tr>
                    <?php for ($j = 0; $j < 4; $j++){ ?>
                        <td>
                            <?php 
                                echo $_SESSION['analytic4'][$i][$j];
                            ?>
                        </td>
                    <?php } ?>
                </tr>
                <?php $i++; } $i = 0;?>
            </table>
        </div>
    </div>
    <br>
    <div class="container">
        <div class="fontAnalysis">
            <p>Analysis 5: Average Grade of Course</p>
        </div>
        <div class="row justify-content-md-center">
            <table class="tableRegist">
                <tr>
                    <th>Course Code</th>
                    <th>Academic Year</th>
                    <th>Semester</th>
                    <th>Grade</th>
                </tr>
                <?php while ((isset($_SESSION['analytic5'][$i])) && $i < 10){ ?>
                <tr>
                    <?php for ($j = 0; $j < 4; $j++){ ?>
                        <td>
                            <?php 
                                echo $_SESSION['analytic5'][$i][$j];
                            ?>
                        </td>
                    <?php } ?>
                </tr>
                <?php $i++; }?>
            </table>
        </div>
    </div>
    <br>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>