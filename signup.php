<?php 
    session_start();
    $i = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration - Sign Up</title>

    <link rel="stylesheet" href="mystyle.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>
<body class="bodyMain">
    <br><br><br>
    <div class="container">
        <div class="row justify-content-md-center">
            <p class="fontTopic">Create Account</p>
        </div>
    </div>
    <div class="container">
        <div class="backgroundMain">
            <?php if (isset($_SESSION['error'])) { ?>
                <div class="alert alert-danger" role="alert">
                    <?php 
                        echo $_SESSION['error']; 
                        unset($_SESSION['error']);
                    ?>
                </div>
            <?php } else if (isset($_SESSION['success'])) { ?>
                <div class="alert alert-success" role="alert">
                    <?php 
                        echo $_SESSION['success'];
                        unset($_SESSION['success']);
                    ?>
                </div>
            <?php } ?>
            <div class="row">
                <form action="signup_db.php" method="post">
                    <br>
                    <div class="fontSignup">
                        <div class="row">
                            <div class="col-4"></div>
                            <div class="col-4">
                                <label for="auto" class="form-label">User ID:</label>
                                <input type="" class="form-control" name="uid" require>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4"></div>
                            <div class="col-4">
                                <label for="auto" class="form-label">Password:</label>
                                <input type="" class="form-control" name="pass" require>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4"></div>
                            <div class="col-4">
                                <label for="auto" class="form-label">National ID:</label>
                                <input type="" class="form-control" name="nid" placeholder="(Optional)">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4"></div>
                            <div class="col-4">
                                <label for="auto" class="form-label">First Name:</label>
                                <input type="" class="form-control" name="fn" require>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4"></div>
                            <div class="col-4">
                                <label for="auto" class="form-label">Last Name:</label>
                                <input type="" class="form-control" name="ln" require>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4"></div>
                            <div class="col-4">
                                <label for="auto" class="form-label" >Department:</label>
                                <select class="form-select" name="dep">
                                    <?php while (isset($_SESSION['selectdepartment'][$i])) {?>
                                    <option value='<?php echo $_SESSION['selectdepartment'][$i][0] ?>'><?php echo $_SESSION['selectdepartment'][$i][0] ?></option>
                                    <?php $i++; } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="fontCenter">
                        <button type="submit" class="btn btn-primary" name="signup">Sign Up</button>
                    </div>
                    <div class="row">
                        <div class="fontCenter">
                            <div class="col">
                                <label for="">If you have accout already, click </label>
                                <a href="login.php">here</a>
                                <label for=""> !!!</label>  
                            </div>
                        </div>
                    </div>
                    <br>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>