<?php
session_start();
require_once "db.php";
require_once "soil_helper.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: Login.php");
    exit;
}

$userId = (int) $_SESSION["user_id"];
$id     = (int) (isset($_GET["id"]) ? $_GET["id"] : 0);

$row = null;

try {

    $stmt = $conn->prepare(
        "SELECT * FROM soil_tests WHERE id = ? AND user_id = ?"
    );
    $stmt->bind_param("ii", $id, $userId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

} catch (mysqli_sql_exception $ex) {

    $row = null;
}

if (!$row) {
    http_response_code(404);
}

$params = soil_parameters();

if ($row) {
    $report = soil_analyse($row);
    $grade  = $report["grade"];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Soil Report - Smart Farming Management System</title>

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

        .score-ring {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            background: conic-gradient(var(--clr) var(--pct), #e9ecef 0);
        }

        .score-ring div {
            width: 112px;
            height: 112px;
            border-radius: 50%;
            background: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .score-number {
            font-size: 34px;
            font-weight: bold;
            line-height: 1;
        }

        .param-card {
            background: white;
            border: none;
            border-radius: 15px;
            padding: 20px;
            height: 100%;
            text-align: center;
            box-shadow: 0 3px 12px rgba(0, 0, 0, .08);
        }

        .param-card .icon {
            font-size: 32px;
            color: #198754;
        }

        .param-value {
            font-size: 28px;
            font-weight: bold;
            color: #333;
        }

        .ideal-hint {
            font-size: 12px;
            color: #6c757d;
        }

        .advice-item {
            border-left: 5px solid;
            padding: 10px 14px;
            margin-bottom: 12px;
            background: #fafafa;
            border-radius: 0 8px 8px 0;
        }

        .advice-item.danger  { border-color: #dc3545; }
        .advice-item.warning { border-color: #ffc107; }
        .advice-item.success { border-color: #198754; }

        .crop-badge {
            font-size: 15px;
            font-weight: 500;
            padding: 8px 14px;
            margin: 0 6px 8px 0;
        }

        footer {
            background: #146c43;
            color: white;
            padding: 25px;
        }

        @media print {
            .no-print { display: none !important; }
            body { background: white; }
        }

    </style>

</head>

<body>

<!-- ================= NAVBAR ================= -->

<nav class="navbar navbar-expand-lg navbar-dark no-print">
    <div class="container">

        <a class="navbar-brand" href="index.php">
            <i class="bi bi-flower1"></i>
            Smart Farming
        </a>

        <div class="ms-auto">
            <a class="nav-link d-inline" href="soil_test.php">
                <i class="bi bi-clipboard2-pulse"></i>
                Soil Testing
            </a>
        </div>

    </div>
</nav>


<div class="container py-5">

<?php if (!$row) { ?>

    <div class="panel text-center">
        <h4>Soil test not found</h4>
        <p class="text-muted">
            This report does not exist or belongs to another account.
        </p>
        <a href="soil_test.php" class="btn btn-success">Back to Soil Testing</a>
    </div>

<?php } else { ?>

    <!-- ================= HEADER ================= -->

    <div class="d-flex flex-wrap justify-content-between align-items-start mb-4 gap-2">

        <div>
            <h2 class="page-title mb-1">
                <i class="bi bi-clipboard2-pulse"></i>
                Soil Report: <?= soil_e($row["field_name"]) ?>
            </h2>

            <div class="text-muted">
                <?= soil_e(date("d M Y", strtotime($row["test_date"]))) ?>
                <?php if ($row["location"] !== "" && $row["location"] !== null) { ?>
                    &middot; <?= soil_e($row["location"]) ?>
                <?php } ?>
            </div>
        </div>

        <div class="no-print">
            <a href="soil_test.php" class="btn btn-outline-success">
                <i class="bi bi-arrow-left"></i> Back
            </a>
            <button class="btn btn-success" onclick="window.print()">
                <i class="bi bi-printer"></i> Print
            </button>
        </div>

    </div>


    <!-- ================= SCORE + CROPS ================= -->

    <div class="row g-4 mb-4">

        <div class="col-md-4">
            <div class="panel text-center h-100">

                <div
                    class="score-ring"
                    style="--pct: <?= (int) $report["score"] ?>%; --clr: <?= soil_e($grade[2]) ?>;">
                    <div>
                        <span class="score-number"><?= (int) $report["score"] ?></span>
                        <small class="text-muted">out of 100</small>
                    </div>
                </div>

                <h5 class="mt-3 mb-0">
                    Soil health:
                    <span class="text-<?= soil_e($grade[1]) ?>"><?= soil_e($grade[0]) ?></span>
                </h5>

            </div>
        </div>

        <div class="col-md-8">
            <div class="panel h-100">

                <h5 class="mb-3">
                    <i class="bi bi-flower3 text-success"></i>
                    Crops that suit this soil
                </h5>

                <?php if (empty($report["crops"])) { ?>

                    <p class="text-muted mb-0">
                        None of the common crops match these pH and salinity
                        values well. Correct the soil first using the advice
                        below, then test again.
                    </p>

                <?php } else { ?>

                    <?php foreach ($report["crops"] as $crop) { ?>
                        <span class="badge text-bg-success crop-badge">
                            <?= soil_e($crop) ?>
                        </span>
                    <?php } ?>

                    <p class="ideal-hint mt-2 mb-0">
                        Based on pH and salinity only. Rainfall, irrigation,
                        season and market should also guide your crop choice.
                    </p>

                <?php } ?>

            </div>
        </div>

    </div>


    <!-- ================= PARAMETERS ================= -->

    <div class="row g-4 mb-4">

        <?php foreach ($params as $key => $p) {

            $r = $report["ratings"][$key];
        ?>

            <div class="col-6 col-lg-4">
                <div class="param-card">

                    <i class="bi <?= soil_e($p["icon"]) ?> icon"></i>

                    <h6 class="mt-2"><?= soil_e($p["label"]) ?></h6>

                    <div class="param-value">
                        <?= soil_e(rtrim(rtrim(number_format($r["value"], 2, ".", ""), "0"), ".")) ?>
                        <small class="text-muted fs-6"><?= soil_e($p["unit"]) ?></small>
                    </div>

                    <span class="badge text-bg-<?= soil_e($r["color"]) ?> mb-1">
                        <?= soil_e($r["label"]) ?>
                    </span>

                    <div class="ideal-hint">Ideal: <?= soil_e($p["ideal"]) ?></div>

                </div>
            </div>

        <?php } ?>

    </div>


    <!-- ================= ADVICE ================= -->

    <div class="panel mb-4">

        <h5 class="mb-3">
            <i class="bi bi-lightbulb text-warning"></i>
            Recommendations
        </h5>

        <?php if (empty($report["advice"])) { ?>

            <div class="advice-item success">
                All values are in a healthy range. Keep adding organic
                matter every season and repeat the soil test every 2 to 3 years.
            </div>

        <?php } else { ?>

            <?php foreach ($report["advice"] as $a) { ?>

                <div class="advice-item <?= soil_e($a["color"]) ?>">
                    <strong><?= soil_e($a["param"]) ?></strong><br>
                    <?= soil_e($a["text"]) ?>
                </div>

            <?php } ?>

        <?php } ?>

        <p class="ideal-hint mb-0">
            This is general guidance. Exact fertiliser doses depend on the
            crop, expected yield and local conditions, so confirm them with
            your local agriculture office or Krishi Vigyan Kendra.
        </p>

    </div>


    <?php if ($row["notes"] !== null && $row["notes"] !== "") { ?>

        <div class="panel mb-4">
            <h5>Notes</h5>
            <p class="mb-0"><?= nl2br(soil_e($row["notes"])) ?></p>
        </div>

    <?php } ?>

<?php } ?>

</div>


<footer class="text-center no-print">
    <p class="mb-1">© 2026 Smart Farming Management System</p>
</footer>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

</body>

</html>
