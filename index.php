<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie Recommendation System</title>
</head>
<body>
    <h1>Get Movie Recommendations</h1>
    <form method="POST" action="index.php">
        <label for="user_id">User ID:</label>
        <input type="text" id="user_id" name="user_id" required><br><br>
        <label for="n_recommendations">Number of Recommendations:</label>
        <input type="text" id="n_recommendations" name="n_recommendations" required><br><br>
        <input type="submit" value="Get Recommendations">
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $user_id = $_POST['user_id'];
        $n_recommendations = $_POST['n_recommendations'];

        // Flask API endpoint
        $url = "http://localhost:5000/recommend";

        // Data to send in the POST request
        $data = array(
            'user_id' => $user_id,
            'n_recommendations' => $n_recommendations
        );

        // Use curl to make the POST request to the Flask API
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            )
        );

        // Create a context and send the request
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        // Handle the API response
        if ($result === FALSE) {
            echo "<p>Error fetching recommendations.</p>";
        } else {
            $recommendations = json_decode($result, true);
            echo "<h2>Recommended Items</h2>";
            echo "<ul>";
            foreach ($recommendations['recommendations'] as $item) {
                echo "<li>Item ID: " . $item . "</li>";
            }
            echo "</ul>";
        }
    }
    ?>
</body>
</html>
