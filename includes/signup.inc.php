<?php
// checks if signup-submit was acted upon
if (isset($_POST['signup-submit'])) {
    require 'dbhandler.inc.php';
    $username = $_POST['uid'];
    $email = $_POST['email'];
    $password = $_POST['pwd'];
    $passwordRepeat = $_POST['pwd-repeat'];

    if(empty($username) || empty($email) || empty($password) || empty($passwordRepeat)) {
        header("Location: ../signup.php?error=emptyfields&uid=".$username."&email=".$email);
        exit();
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL) && !preg_match("/^[a-zA-Z0-9]*$/", $username)) {
        header("Location: ../signup.php?error=invalidemailuid");
        exit();
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../signup.php?error=invalidemail&uid=".$username);
        exit();
    } else if (!preg_match("/^[a-zA-Z0-9]*$/", $username)) {
        header("Location: ../signup.php?error=invalidusername*&email=".$email);
        exit();
    } else if ($password !== $passwordRepeat) {
        header("Location: ../signup.php?error=passwordcheck&uid=".$username."&email=".$email);
        exit();
    } else {
        $sql = "SELECT uidusers FROM users WHERE uidusers=?";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: ../signup.php?error=invaliduid&email=".$email);
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            $resultCheck = mysqli_stmt_num_rows($stmt);
            if ($resultCheck > 0) {
                header("Location: ../signup.php?error=usertaken&email=".$email);
                exit();
            } else {
                $sql = "INSERT INTO users (uidusers, emailusers, pwdusers) VALUES (?, ?, ?)";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    header("Location: ../signup.php?error=sqlerror");
                    exit();
                } else {
                    $hashedPwd = password_hash($password, PASSWORD_DEFAULT);
                    mysqli_stmt_bind_param($stmt, "sss", $username, $email, $hashedPwd);
                    mysqli_stmt_execute($stmt);
                    header("Location: ../signup.php?signup=success");
                    exit();
                }
            }
        }
    }
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
    else {
        // sending back to signup page
        header("Location: ../signup.php?signup.php");
        exit();
    }