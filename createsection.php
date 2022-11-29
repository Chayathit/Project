<?php 
    session_start(); 
    if (!isset($_SESSION['uid']) || !isset($_SESSION['urole'])){
        header("Location: login.php");
    }
    $i = 0;
    $j = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration - Add New Course - Create Section</title>

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
            <p class="fontTopic">Add New Course</p>
        </div>
    </div>
    <br>
    <div class="container">
        <div class="row">
            <div class="col-1"></div>
            <div class="col">
                <p class="fontInfo">Name: <?php echo $_SESSION['udata']['FIRST_NAME'] ?></p> 
            </div>
            <div class="col">
                <p class="fontInfo">Surname: <?php echo $_SESSION['udata']['LAST_NAME'] ?></p>
            </div>
            <div class="col">
                <p class="fontInfo">Department: <?php echo $_SESSION['udata']['DEPARTMENT'] ?></p>
            </div>
            <hr width="150%" color="black">
        </div>
    </div>
    <br>
    <div class="container">
        <div class="backgroundBorrow">
        <br>
            <div class="row">
                <?php if(isset($_SESSION['error'])) {?>
                    <div class="alert alert-danger" role="alert">
                        <?php 
                            echo $_SESSION['error']; 
                            unset($_SESSION['error']);
                        ?>
                    </div>
                <?php }?>
                <div class="container">
                    <div class="fontHead">
                        <div class="row">
                            <div class="col-3"></div>
                            <div class="col">Select Section that Courses</div>
                            <div class="col-3"></div>
                        </div>
                    </div>
                    <br>
                    <div class="container">
                        <div class="row">
                            <div class="col-2"></div>
                            <div class="col">
                                <p class="fontInfo">Course Code ID: <?php echo $_SESSION['newcc'] ?></p>
                            </div>
                            <div class="col">
                                <p class="fontInfo">Course Title: <?php echo $_SESSION['newct'] ?></p>
                            </div>
                            <div class="col">
                                <p class="fontInfo">Credit: <?php echo $_SESSION['newcd'] ?></p>
                            </div>
                        </div>
                        <br>
                        <form action="createsection_db.php" method="post">
                            <div class="row">
                                <div class="col-3"></div>
                                <?php while (isset($_SESSION['newsc'][$i])) { ?>
                                    <div class="row">
                                        <div class="col-4"></div>
                                        <div class="col">
                                            <label for="coursecode" class="fontNew">Section Code: <?php echo $_SESSION['newsc'][$i] ?></label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-4"></div>
                                        <div class="col">
                                            <label for="coursecode" class="fontNew">Teacher ID:</label>
                                            <div class="row">
                                                <div class="col-8">
                                                    <input class="form-control" list="datalistOptions" placeholder="Type to search..." name=<?php echo "newsect".$i ?> required>
                                                    <datalist id="datalistOptions">
                                                        <?php while (isset($_SESSION['searchteacher'][$j])) { ?>
                                                        <option value=<?php echo $_SESSION['searchteacher'][$j][0]?>><?php echo $_SESSION['searchteacher'][$j][0] . " - " . $_SESSION['searchteacher'][$j][1] . " " . $_SESSION['searchteacher'][$j][2]?></option>
                                                        <?php $j++; } $j = 0;?>
                                                    </datalist>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-4"></div>
                                        <div class="col">
                                            <label for="coursecode" class="fontNew">Max Student:</label>
                                            <div class="row">
                                                <div class="col-3">
                                                    <input class="form-control" type="number" step="5" min="30" max="120" placeholder="Type to search..." name=<?php echo "newsecms".$i ?> required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php $i++; } ?>
                            </div>
                            <br>
                            <div class="containter">
                                <div class="fontCenterSec">
                                    <button type="submit" class="btn btn-primary" name="selected_section">Confirm</button>
                                </div>
                            </div>  
                        </form>    
                    </div>    
                </div>
            </div>
        </div>
        <br>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>