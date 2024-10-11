<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluate Model</title>
</head>
<body>
    <h1>Evaluate Recommendation Model</h1>
    <form method="POST" action="evaluate.php">
        <input type="submit" value="Evaluate Model">
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Flask API endpoint
        $url = "http://localhost:5000/evaluate";

        // Use curl to make the GET request to the Flask API
        $result = file_get_contents($url);

        // Handle the API response
        if ($result === FALSE) {
            echo "<p>Error evaluating model.</p>";
        } else {
            $metrics = json_decode($result, true);
            echo "<h2>Model Evaluation Metrics</h2>";
            echo "<p>Precision: " . $metrics['precision'] . "</p>";
            echo "<p>Recall: " . $metrics['recall'] . "</p>";
            echo "<p>F1 Score: " . $metrics['f1_score'] . "</p>";
            echo "<p>Accuracy: " . $metrics['accuracy'] . "</p>";
        }
    }
    ?>
</body>
</html>
