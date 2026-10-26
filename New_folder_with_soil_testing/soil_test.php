<?php
session_start();
require_once "db.php";
require_once "soil_helper.php";

/* Only logged-in users can use soil testing */
if (!isset($_SESSION["user_id"])) {
    header("Location: Login.php");
    exit;
}

$userId = (int) $_SESSION["user_id"];

if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

$params  = soil_parameters();
$errors  = [];
$old     = [];
$dbError = "";

/* ================= HANDLE FORM SUBMIT ================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $token = isset($_POST["csrf_token"]) ? $_POST["csrf_token"] : "";

    if (!hash_equals($_SESSION["csrf_token"], $token)) {

        $errors[] = "Your session expired. Please try again.";

    } elseif (isset($_POST["action"]) && $_POST["action"] === "delete") {

        /* ----- DELETE ----- */

        $id = (int) (isset($_POST["id"]) ? $_POST["id"] : 0);

        $stmt = $conn->prepare(
            "DELETE FROM soil_tests WHERE id = ? AND user_id = ?"
        );
        $stmt->bind_param("ii", $id, $userId);
        $stmt->execute();
        $stmt->close();

        header("Location: soil_test.php?deleted=1");
        exit;

    } else {

        /* ----- ADD ----- */

        $old = $_POST;

        $fieldName = trim(isset($_POST["field_name"]) ? $_POST["field_name"] : "");
        $location  = trim(isset($_POST["location"])   ? $_POST["location"]   : "");
        $testDate  = trim(isset($_POST["test_date"])  ? $_POST["test_date"]  : "");
        $notes     = trim(isset($_POST["notes"])      ? $_POST["notes"]      : "");

        if ($fieldName === "" || mb_strlen($fieldName) > 100) {
            $errors[] = "Enter a field name (up to 100 characters).";
        }

        if (mb_strlen($location) > 150) {
            $errors[] = "Location can be up to 150 characters.";
        }

        $d = DateTime::createFromFormat("Y-m-d", $testDate);

        if (!$d || $d->format("Y-m-d") !== $testDate) {
            $errors[] = "Enter a valid test date.";
        } elseif ($testDate > date("Y-m-d")) {
            $errors[] = "Test date cannot be in the future.";
        }

        $values = [];

        foreach ($params as $key => $p) {

            $raw = trim(isset($_POST[$key]) ? $_POST[$key] : "");

            if ($raw === "" || !is_numeric($raw)) {
                $errors[] = $p["label"] . " is required and must be a number.";
                continue;
            }

            $num = (float) $raw;

            if ($num < $p["min"] || $num > $p["max"]) {
                $errors[] = $p["label"] . " must be between "
                          . $p["min"] . " and " . $p["max"] . ".";
                continue;
            }

            $values[$key] = $num;
        }

        if (empty($errors)) {

            try {

                $stmt = $conn->prepare(
                    "INSERT INTO soil_tests
                     (user_id, field_name, location, test_date,
                      ph, ec, organic_carbon, nitrogen, phosphorus, potassium, notes)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
                );

                $types = "isss" . str_repeat("d", 6) . "s";

                $stmt->bind_param(
                    $types,
                    $userId,
                    $fieldName,
                    $location,
                    $testDate,
                    $values["ph"],
                    $values["ec"],
                    $values["organic_carbon"],
                    $values["nitrogen"],
                    $values["phosphorus"],
                    $values["potassium"],
                    $notes
                );

                $stmt->execute();
                $newId = $stmt->insert_id;
                $stmt->close();

                header("Location: soil_report.php?id=" . (int) $newId);
                exit;

            } catch (mysqli_sql_exception $ex) {

                $errors[] = "Could not save the soil test. "
                          . "Make sure soil_tests.sql has been run on the database.";
            }
        }
    }
}

/* ================= LOAD HISTORY ================= */

$tests = [];

try {

    $stmt = $conn->prepare(
        "SELECT id, field_name, location, test_date,
                ph, ec, organic_carbon, nitrogen, phosphorus, potassium
         FROM soil_tests
         WHERE user_id = ?
         ORDER BY test_date DESC, id DESC"
    );
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $tests = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

} catch (mysqli_sql_exception $ex) {

    $dbError = "The soil_tests table was not found. "
             . "Run soil_tests.sql on the smart_farming database first.";
}

/* Sample values for the "Fill sample values" button */
$samples = [];
foreach ($params as $key => $p) {
    $samples[$key] = $p["sample"];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Soil Testing - Smart Farming Management System</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f4f8f3;
        }

        .navbar {
            background: #02502b;
            padding: 12px 0;
        }

        .navbar-brand {
            font-weight: bold;
            font-size: 24px;
        }

        .nav-link {
            color: white !important;
            margin-left: 15px;
            font-weight: 500;
        }

        .nav-link:hover {
            color: #ffc107 !important;
        }

        .page-title {
            color: #198754;
            font-weight: bold;
        }

        .panel {
            background: white;
            border: none;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, .08);
        }

        .form-control {
            padding: 10px 12px;
            border-radius: 8px;
        }

        .ideal-hint {
            font-size: 12px;
            color: #6c757d;
        }

        .btn-success {
            background: #198754;
        }

        footer {
            background: #146c43;
            color: white;
            padding: 25px;
        }

    </style>

</head>

<body>

<!-- ================= NAVBAR ================= -->

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">

        <a class="navbar-brand" href="index.php">
            <i class="bi bi-flower1"></i>
            Smart Farming
        </a>

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMenu">
            <ul class="navbar-nav ms-auto">

                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="soil_test.php">
                        <i class="bi bi-clipboard2-pulse"></i>
                        Soil Testing
                    </a>
                </li>

                <li class="nav-item">
                    <span class="nav-link">
                        <i class="bi bi-person-circle"></i>
                        <?= soil_e(isset($_SESSION["username"]) ? $_SESSION["username"] : "") ?>
                    </span>
                </li>

            </ul>
        </div>

    </div>
</nav>


<!-- ================= CONTENT ================= -->

<div class="container py-5">

    <h2 class="page-title mb-1">
        <i class="bi bi-clipboard2-pulse"></i>
        Soil Testing
    </h2>

    <p class="text-muted mb-4">
        Enter your soil lab results to get a soil health score,
        fertiliser advice and suitable crops.
    </p>

    <?php if (isset($_GET["deleted"])) { ?>
        <div class="alert alert-success">Soil test deleted.</div>
    <?php } ?>

    <?php if ($dbError !== "") { ?>
        <div class="alert alert-warning"><?= soil_e($dbError) ?></div>
    <?php } ?>

    <div class="row g-4">


        <!-- ========== ADD TEST FORM ========== -->

        <div class="col-lg-5">
            <div class="panel">

                <h5 class="mb-3">New Soil Test</h5>

                <?php if (!empty($errors)) { ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            <?php foreach ($errors as $err) { ?>
                                <li><?= soil_e($err) ?></li>
                            <?php } ?>
                        </ul>
                    </div>
                <?php } ?>

                <form method="POST" id="soilForm" novalidate>

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= soil_e($_SESSION["csrf_token"]) ?>">

                    <div class="mb-3">
                        <label class="form-label">Field / Plot Name</label>
                        <input
                            type="text"
                            name="field_name"
                            class="form-control"
                            maxlength="100"
                            placeholder="e.g. North field"
                            value="<?= soil_e(isset($old["field_name"]) ? $old["field_name"] : "") ?>"
                            required>
                    </div>

                    <div class="row g-2 mb-3">

                        <div class="col-md-7">
                            <label class="form-label">Location</label>
                            <input
                                type="text"
                                name="location"
                                class="form-control"
                                maxlength="150"
                                placeholder="e.g. Parbhani"
                                value="<?= soil_e(isset($old["location"]) ? $old["location"] : "") ?>">
                        </div>

                        <div class="col-md-5">
                            <label class="form-label">Test Date</label>
                            <input
                                type="date"
                                name="test_date"
                                class="form-control"
                                max="<?= date("Y-m-d") ?>"
                                value="<?= soil_e(isset($old["test_date"]) ? $old["test_date"] : date("Y-m-d")) ?>"
                                required>
                        </div>

                    </div>

                    <hr>

                    <?php foreach ($params as $key => $p) { ?>

                        <div class="mb-3">

                            <label class="form-label mb-0">
                                <i class="bi <?= soil_e($p["icon"]) ?> text-success"></i>
                                <?= soil_e($p["label"]) ?>
                                <?php if ($p["unit"] !== "") { ?>
                                    <small class="text-muted">(<?= soil_e($p["unit"]) ?>)</small>
                                <?php } ?>
                            </label>

                            <input
                                type="number"
                                name="<?= soil_e($key) ?>"
                                class="form-control mt-1"
                                min="<?= soil_e($p["min"]) ?>"
                                max="<?= soil_e($p["max"]) ?>"
                                step="<?= soil_e($p["step"]) ?>"
                                value="<?= soil_e(isset($old[$key]) ? $old[$key] : "") ?>"
                                required>

                            <div class="ideal-hint">
                                Ideal: <?= soil_e($p["ideal"]) ?>
                            </div>

                        </div>

                    <?php } ?>

                    <div class="mb-3">
                        <label class="form-label">Notes (optional)</label>
                        <textarea
                            name="notes"
                            class="form-control"
                            rows="2"><?= soil_e(isset($old["notes"]) ? $old["notes"] : "") ?></textarea>
                    </div>

                    <div class="d-flex gap-2">

                        <button type="submit" class="btn btn-success flex-grow-1">
                            <i class="bi bi-clipboard2-check"></i>
                            Analyse Soil
                        </button>

                        <button
                            type="button"
                            class="btn btn-outline-secondary"
                            onclick="fillSample()">
                            Sample values
                        </button>

                    </div>

                </form>

            </div>
        </div>


        <!-- ========== HISTORY ========== -->

        <div class="col-lg-7">
            <div class="panel">

                <h5 class="mb-3">Your Soil Tests</h5>

                <?php if (empty($tests)) { ?>

                    <p class="text-muted mb-0">
                        No soil tests yet. Add your first test using the form.
                    </p>

                <?php } else { ?>

                    <div class="table-responsive">
                        <table class="table align-middle">

                            <thead>
                                <tr>
                                    <th>Field</th>
                                    <th>Date</th>
                                    <th>pH</th>
                                    <th>N / P / K</th>
                                    <th>Health</th>
                                    <th></th>
                                </tr>
                            </thead>

                            <tbody>

                            <?php foreach ($tests as $t) {

                                $res = soil_analyse($t);
                                $grade = $res["grade"];
                            ?>

                                <tr>

                                    <td>
                                        <strong><?= soil_e($t["field_name"]) ?></strong>
                                        <?php if ($t["location"] !== "" && $t["location"] !== null) { ?>
                                            <br>
                                            <small class="text-muted"><?= soil_e($t["location"]) ?></small>
                                        <?php } ?>
                                    </td>

                                    <td><?= soil_e(date("d M Y", strtotime($t["test_date"]))) ?></td>

                                    <td><?= soil_e(rtrim(rtrim(number_format((float) $t["ph"], 2), "0"), ".")) ?></td>

                                    <td>
                                        <?= soil_e(round($t["nitrogen"])) ?> /
                                        <?= soil_e(round($t["phosphorus"], 1)) ?> /
                                        <?= soil_e(round($t["potassium"])) ?>
                                    </td>

                                    <td>
                                        <span class="badge text-bg-<?= soil_e($grade[1]) ?>">
                                            <?= soil_e($grade[0]) ?> (<?= (int) $res["score"] ?>)
                                        </span>
                                    </td>

                                    <td class="text-end text-nowrap">

                                        <a
                                            href="soil_report.php?id=<?= (int) $t["id"] ?>"
                                            class="btn btn-sm btn-outline-success">
                                            View
                                        </a>

                                        <form
                                            method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Delete this soil test?');">

                                            <input type="hidden" name="csrf_token"
                                                   value="<?= soil_e($_SESSION["csrf_token"]) ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= (int) $t["id"] ?>">

                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>

                                        </form>

                                    </td>

                                </tr>

                            <?php } ?>

                            </tbody>

                        </table>
                    </div>

                <?php } ?>

            </div>
        </div>

    </div>

</div>


<footer class="text-center">
    <p class="mb-1">© 2026 Smart Farming Management System</p>
</footer>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

<script>

    const samples = <?= json_encode($samples) ?>;

    function fillSample() {

        const form = document.getElementById("soilForm");

        if (!form.field_name.value) {
            form.field_name.value = "Sample field";
        }

        Object.keys(samples).forEach(function (key) {
            form[key].value = samples[key];
        });

    }

</script>

</body>

</html>
