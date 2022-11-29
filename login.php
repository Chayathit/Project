<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration - Login</title>

    <link rel="stylesheet" href="mystyle.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>
<body class="bodyLogin"> 
    <div class="container">
        <div class="fontTopic">Student Registration</div>
        <h3 class="mt-5">Login</h3>
        <hr>
        <form action="login_db.php" method="post">
            <?php if(isset($_SESSION['error'])) {?>
                <div class="alert alert-danger" role="alert">
                    <?php 
                        echo $_SESSION['error']; 
                        unset($_SESSION['error']);
                    ?>
                </div>
            <?php }?>
            <div class="mb-3">
                <label for="userid" class="form-label">User ID</label>
                <input type="text" class="form-control" name="userid">
                <div id="useridHelp" class="form-text">Student ID or Personnel ID</div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" name="password">
            </div>
            <button type="submit" class="btn btn-primary" name="login">Login</button>
        </form>
        <hr>
        <div class="row">
            <div class="col">
                <label for="">If you don't have accout, create </label>
                <a href="signup_db.php">here</a>
                <label for=""> !!!</label>  
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>