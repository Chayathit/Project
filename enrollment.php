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
    <title>Student Registration - Enrollment</title>

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
            <p class="fontTopic">Enrollment</p>
        </div>     
    </div>
    <br><br>
    <div class="container">
        <div class="row">
            <div class="col">
                <p class="fontInfo">Enrollment ID: <?php echo $_SESSION['enrollmentid'] ?></p>
            </div>
            <div class="col">
                <p class="fontInfo">Semester: <?php echo $_SESSION['currenttime']['SEMESTER'] ?></p>
            </div>
            <div class="col">
                <p class="fontInfo">Academic Year: <?php echo $_SESSION['currenttime']['ACADEMIC_YEAR'] ?></p>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <p class="fontInfo">Name: <?php echo $_SESSION['udata']['FIRST_NAME']?></p> 
            </div>
            <div class="col">
                <p class="fontInfo">Surname: <?php echo $_SESSION['udata']['LAST_NAME']?></p>
            </div>
            <div class="col"></div>
        </div>
        <div class="row">
            <div class="col">
                <p class="fontInfo">Department: <?php echo $_SESSION['udata']['DEPARTMENT']?></p>
            </div>
            <div class="col">
                <p class="fontInfo">Year: <?php echo $_SESSION['udata']['YEAR']?></p>
            </div>
            <div class="col"></div>
        </div>
        <div class="row">
            <hr width="150%" color="black">
        </div>
    </div>
    <br>
    <div class="container">
        <div class="row">
            <div class="col">
                <p class="fontInfo">Total Credit: <?php echo $_SESSION['totalcredit'] ?></p>
            </div>
            <div class="col">
                <p class="fontInfo">Total Price: <?php echo $_SESSION['totalprice'] ?></p>
            </div>
            <div class="col">
            </div>
        </div>

        <div class="row">
            <div class="fontInfo">Payment Status: 
                <?php if ($_SESSION['havegrade'] == -1) { ?>
                    <button type="button" class="btn btn-danger" disabled>Registration has been cancelled</button>
                <?php } else if ($_SESSION['paid'][0] == 3) {?>
                    <button type="button" class="btn btn-success" disabled>Paid</button>
                <?php } else {?>
                    <button type="button" class="btn btn-danger" disabled>Not Paid</button>
                <?php }?>
            </div>
        </div>   
    </div>
    <br>
    <div class="container">
        <div class="row justify-content-md-center">
            <table class="tableRegist">
                <tr>
                    <th>Course Code</th>
                    <th>Course Title</th>
                    <th>Credit</th>
                    <th>Section</th>
                    <th>Teacher</th>
                    <th>Price</th>
                </tr>
                <?php while (isset($_SESSION['enrolledcoursedata'][$i])){ ?>
                <tr>
                    <?php for ($j = 0; $j < 6; $j++){ ?>
                        <td>
                            <?php 
                                echo $_SESSION['enrolledcoursedata'][$i][$j];
                            ?>
                        </td>
                    <?php } ?>
                </tr>
                <br>
                <?php $i++; }?>
            </table>
        </div>
    </div>
    <br>
    
    <div class="container">
        <div class="font">
            <form action="bill_db.php" method="post">
                <div class="row justify-content-md-center">
                    <div class="col col-auto">
                        <?php if ($_SESSION['havegrade'] == -1) { ?>
                            <button type="submit" class="btn btn-lg btn-danger disabled" name="bill">Bill 1 - Payment Timeout</button>
                        <?php } else if ($_SESSION['paid'][1] == 1){ ?>
                            <button type="submit" class="btn btn-lg btn-success disabled" value="1" name="bill">Bill 1 - Paid</button>
                        <?php } else { ?>
                            <button type="submit" class="btn btn-lg btn-danger" value="1" name="bill">Bill 1 - Not Paid</button> 
                        <?php } ?>
                    </div>
                    <div class="col-md-auto">
                        <?php if ($_SESSION['havegrade'] == -1) { ?>
                            <button type="submit" class="btn btn-lg btn-danger disabled" name="bill">Bill 2 - Payment Timeout</button>
                        <?php } else if ($_SESSION['paid'][2] == 1){ ?>
                                <button type="submit" class="btn btn-lg btn-success disabled" value="2" name="bill">Bill 2 - Paid</button> 
                        <?php } else { ?>
                                <button type="submit" class="btn btn-lg btn-danger" value="2" name="bill">Bill 2 - Not Paid</button> 
                        <?php } ?>
                    </div>
                    <div class="col col-auto">
                        <?php if ($_SESSION['havegrade'] == -1) { ?>
                            <button type="submit" class="btn btn-lg btn-danger disabled" name="bill">Bill 3 - Payment Timeout</button>
                        <?php } else if ($_SESSION['paid'][3] == 1){ ?>
                            <button type="submit" class="btn btn-lg btn-success disabled" value="3" name="bill">Bill 3 - Paid</button> 
                        <?php } else { ?>
                            <button type="submit" class="btn btn-lg btn-danger" value="3" name="bill">Bill 3 - Not Paid</button> 
                        <?php } ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="container">
        <a href="selectsection_db.php">
            <div class="backButton">
                <button type="submit" class="btn btn-primary">Back</button>
            </div>
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>