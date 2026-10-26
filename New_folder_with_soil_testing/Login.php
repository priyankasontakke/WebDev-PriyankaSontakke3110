
<?php
session_start();

require_once __DIR__ . "/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || $password === "") {

        $error = "Please enter username and password.";

    } else {

        $sql = "SELECT id, username, password
                FROM smart_farming.users
                WHERE LOWER(TRIM(username)) = LOWER(TRIM(?))
                LIMIT 1";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("SQL Error: " . htmlspecialchars($conn->error));
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {

            $storedPassword = (string) $user["password"];

            // Supports plain-text passwords and password_hash() passwords
            $passwordMatch =
                hash_equals($storedPassword, $password) ||
                password_verify($password, $storedPassword);

            if ($passwordMatch) {

                session_regenerate_id(true);

                $_SESSION["user_id"] = $user["id"];
                $_SESSION["username"] = $user["username"];

                $stmt->close();

                header("Location: dashboard.php");
                exit;

            } else {

                $error = "Invalid username or password.";

            }

        } else {

            $error = "Invalid username or password.";

        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - Smart Farming Management System</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: Arial, sans-serif;
            background:
                linear-gradient(rgba(0, 65, 25, 0.60),
                                rgba(0, 65, 25, 0.60)),
                url("images/farming-bg.jpg");
            background-size: cover;
            background-position: center;
            background-color: #e8f5e9;
        }

        .login-card {
            width: 100%;
            max-width: 560px;
            padding: 45px;
            border-radius: 22px;
            background: #ffffff;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.2);
        }

        .user-icon {
            width: 76px;
            height: 76px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 5px solid #198754;
            border-radius: 50%;
            color: #198754;
            font-size: 43px;
        }

        h1 {
            color: #198754;
            font-weight: bold;
            text-align: center;
        }

        .subtitle {
            text-align: center;
            color: #555;
            margin-bottom: 25px;
        }

        .form-label {
            font-weight: 600;
        }

        .form-control {
            min-height: 52px;
            font-size: 16px;
        }

        .login-btn {
            width: 100%;
            min-height: 54px;
            margin-top: 15px;
            background: #198754;
            border: none;
            font-size: 18px;
            font-weight: bold;
        }

        .login-btn:hover {
            background: #146c43;
        }

        @media (max-width: 576px) {
            .login-card {
                padding: 28px 22px;
            }
        }
    </style>
</head>

<body>

<div class="login-card">

    <div class="user-icon">&#128100;</div>

    <h1>Login</h1>

    <p class="subtitle">
        Smart Farming Management System
    </p>

    <?php if ($error !== ""): ?>
        <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="" autocomplete="on">

        <div class="mb-4">
            <label for="username" class="form-label">
                Username
            </label>

            <input
                type="text"
                class="form-control"
                id="username"
                name="username"
                placeholder="Enter username"
                autocomplete="username"
                required
            >
        </div>

        <div class="mb-4">
            <label for="password" class="form-label">
                Password
            </label>

            <input
                type="password"
                class="form-control"
                id="password"
                name="password"
                placeholder="Enter password"
                autocomplete="current-password"
                required
            >
        </div>

        <button type="submit" class="btn btn-success login-btn">
            &#10148; Login
        </button>

    </form>

</div>

</body>
</html>
