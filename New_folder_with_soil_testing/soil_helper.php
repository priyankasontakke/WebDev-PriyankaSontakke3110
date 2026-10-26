<?php
/*
 * Soil testing helpers for Smart Farming Management System.
 *
 * Rating limits follow the commonly used Indian soil-test interpretation
 * (Soil Health Card style). They are general guidance only.
 */

/* Safe output */
function soil_e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

/* The six values a soil test records. Keys match the database columns. */
function soil_parameters()
{
    return [
        "ph" => [
            "label"  => "pH",
            "unit"   => "",
            "min"    => 3,
            "max"    => 11,
            "step"   => "0.1",
            "ideal"  => "6.5 - 7.5",
            "sample" => 6.8,
            "icon"   => "bi-droplet-half",
        ],
        "ec" => [
            "label"  => "Electrical Conductivity (EC)",
            "unit"   => "dS/m",
            "min"    => 0,
            "max"    => 20,
            "step"   => "0.01",
            "ideal"  => "below 1.0",
            "sample" => 0.5,
            "icon"   => "bi-lightning-charge",
        ],
        "organic_carbon" => [
            "label"  => "Organic Carbon",
            "unit"   => "%",
            "min"    => 0,
            "max"    => 5,
            "step"   => "0.01",
            "ideal"  => "above 0.75",
            "sample" => 0.6,
            "icon"   => "bi-recycle",
        ],
        "nitrogen" => [
            "label"  => "Available Nitrogen (N)",
            "unit"   => "kg/ha",
            "min"    => 0,
            "max"    => 2000,
            "step"   => "1",
            "ideal"  => "280 - 560",
            "sample" => 300,
            "icon"   => "bi-wind",
        ],
        "phosphorus" => [
            "label"  => "Available Phosphorus (P)",
            "unit"   => "kg/ha",
            "min"    => 0,
            "max"    => 500,
            "step"   => "0.1",
            "ideal"  => "10 - 25",
            "sample" => 14,
            "icon"   => "bi-flower2",
        ],
        "potassium" => [
            "label"  => "Available Potassium (K)",
            "unit"   => "kg/ha",
            "min"    => 0,
            "max"    => 2000,
            "step"   => "1",
            "ideal"  => "108 - 280",
            "sample" => 180,
            "icon"   => "bi-gem",
        ],
    ];
}

/*
 * Rate one value. Returns [label, bootstrap colour].
 * success = fine, warning = needs attention, danger = needs correction.
 */
function soil_rate($key, $value)
{
    $v = (float) $value;

    switch ($key) {

        case "ph":
            if ($v < 5.5)   return ["Strongly acidic", "danger"];
            if ($v < 6.5)   return ["Slightly acidic", "warning"];
            if ($v <= 7.5)  return ["Neutral", "success"];
            if ($v <= 8.5)  return ["Slightly alkaline", "warning"];
            return ["Strongly alkaline", "danger"];

        case "ec":
            if ($v < 1.0)   return ["Normal", "success"];
            if ($v < 2.0)   return ["Slightly high", "warning"];
            return ["High (saline)", "danger"];

        case "organic_carbon":
            if ($v < 0.5)   return ["Low", "danger"];
            if ($v <= 0.75) return ["Medium", "warning"];
            return ["High", "success"];

        case "nitrogen":
            if ($v < 280)   return ["Low", "danger"];
            if ($v <= 560)  return ["Medium", "warning"];
            return ["High", "success"];

        case "phosphorus":
            if ($v < 10)    return ["Low", "danger"];
            if ($v <= 25)   return ["Medium", "warning"];
            return ["High", "success"];

        case "potassium":
            if ($v < 108)   return ["Low", "danger"];
            if ($v <= 280)  return ["Medium", "warning"];
            return ["High", "success"];
    }

    return ["Unknown", "secondary"];
}

/* Practical advice for a given parameter and rating label. */
function soil_advice_text($key, $label)
{
    $map = [
        "ph" => [
            "Strongly acidic"   => "Apply agricultural lime (ideally based on a lime requirement test) and add well-decomposed farmyard manure to raise the pH.",
            "Slightly acidic"   => "Add farmyard manure or compost. Light liming can help if your crop is sensitive to acidity.",
            "Slightly alkaline" => "Add organic matter (FYM, compost, green manure). Acid-forming fertilisers such as ammonium sulphate can help; use gypsum if the soil is sodic.",
            "Strongly alkaline" => "Use gypsum (sodic soils) or elemental sulphur, add plenty of organic matter and improve drainage. A gypsum requirement test is recommended.",
        ],
        "ec" => [
            "Slightly high"  => "Avoid over-fertilising, use good-quality irrigation water and be careful with salt-sensitive crops at germination.",
            "High (saline)"  => "The soil is saline. Improve drainage, leach salts with good-quality water, add organic matter and choose salt-tolerant crops.",
        ],
        "organic_carbon" => [
            "Low"    => "Add farmyard manure or compost, grow green-manure crops (such as sunhemp or dhaincha) and retain crop residue.",
            "Medium" => "Keep adding FYM or compost every season to move organic carbon into the high range.",
        ],
        "nitrogen" => [
            "Low"    => "Apply nitrogen (urea or another N source) in split doses and include legumes in the crop rotation.",
            "Medium" => "Apply the crop's recommended nitrogen dose, split across growth stages.",
            "High"   => "Nitrogen is high. You can reduce the basal nitrogen dose and avoid over-application.",
        ],
        "phosphorus" => [
            "Low"    => "Apply phosphorus (SSP or DAP) at sowing, placed near the root zone.",
            "Medium" => "Apply the recommended phosphorus dose for your crop to maintain the level.",
            "High"   => "Phosphorus is high. You can skip or reduce phosphorus fertiliser this season.",
        ],
        "potassium" => [
            "Low"    => "Apply potassium (MOP) as per crop requirement and add organic manure.",
            "Medium" => "Apply a maintenance dose of potassium for your crop.",
            "High"   => "Potassium is high. You can skip or reduce potassium fertiliser this season.",
        ],
    ];

    return isset($map[$key][$label]) ? $map[$key][$label] : "";
}

/*
 * Approximate crops for Maharashtra-type conditions.
 * [name, min pH, max pH, max EC (dS/m) tolerated]
 * These ranges are indicative, not agronomic prescriptions.
 */
function soil_suggest_crops($ph, $ec)
{
    $crops = [
        ["Cotton",              6.0, 8.0, 6.0],
        ["Soybean",             6.0, 7.5, 4.0],
        ["Pigeon pea (Tur)",    6.0, 7.5, 2.0],
        ["Chickpea (Gram)",     6.0, 7.5, 2.0],
        ["Green gram (Moong)",  6.0, 7.5, 2.0],
        ["Groundnut",           6.0, 7.0, 3.0],
        ["Wheat",               6.0, 7.5, 4.0],
        ["Sorghum (Jowar)",     6.0, 8.0, 4.0],
        ["Maize",               5.8, 7.5, 2.0],
        ["Rice",                5.5, 7.0, 3.0],
        ["Sugarcane",           6.0, 8.0, 3.0],
        ["Onion",               6.0, 7.0, 1.5],
        ["Tomato",              6.0, 7.0, 2.5],
    ];

    $result = [];

    foreach ($crops as $c) {
        if ($ph >= $c[1] && $ph <= $c[2] && $ec <= $c[3]) {
            $result[] = $c[0];
        }
    }

    return $result;
}

/* Overall grade from a 0-100 score. Returns [label, bootstrap colour, hex colour]. */
function soil_grade($score)
{
    if ($score >= 75) return ["Good",  "success", "#198754"];
    if ($score >= 50) return ["Fair",  "warning", "#e0a800"];
    return ["Poor", "danger", "#dc3545"];
}

/*
 * Analyse one soil test.
 * $row needs the keys: ph, ec, organic_carbon, nitrogen, phosphorus, potassium
 */
function soil_analyse($row)
{
    $points  = ["success" => 100, "warning" => 60, "danger" => 25];
    $order   = ["danger" => 0, "warning" => 1, "success" => 2];
    $params  = soil_parameters();

    $ratings = [];
    $advice  = [];
    $total   = 0;

    foreach ($params as $key => $p) {

        $value = (float) $row[$key];
        list($label, $color) = soil_rate($key, $value);

        $ratings[$key] = [
            "value" => $value,
            "label" => $label,
            "color" => $color,
        ];

        $total += $points[$color];

        $text = soil_advice_text($key, $label);

        if ($text !== "") {
            $advice[] = [
                "param" => $p["label"],
                "color" => $color,
                "text"  => $text,
            ];
        }
    }

    /* Most urgent advice first */
    usort($advice, function ($a, $b) use ($order) {
        return $order[$a["color"]] - $order[$b["color"]];
    });

    $score = (int) round($total / count($params));

    return [
        "ratings" => $ratings,
        "score"   => $score,
        "grade"   => soil_grade($score),
        "advice"  => $advice,
        "crops"   => soil_suggest_crops((float) $row["ph"], (float) $row["ec"]),
    ];
}
