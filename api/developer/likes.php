<?php
  // database connection details:
  require_once "../../credentials.php";
  session_start();

  // connect directly to our database (notice 4th argument) we need the connection for sanitisation:
	$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

	// if the connection fails, we need to know, so allow this exit:
	if (!$connection)
	{
		header("Content-Type: application/json", NULL, 500);
    exit;
	}

	// Retrieve users and the number of likes on their posts.
	$query = "SELECT m.username AS 'username', COUNT(l.post_id) AS 'likes'
            FROM members m
            JOIN feed f USING(username)
            JOIN likes l USING(post_id)
            GROUP BY m.username
            ORDER BY likes DESC
            LIMIT 5";

	$result = mysqli_query($connection, $query);

	// Count the rows for reference
	$n = mysqli_num_rows($result);

  // Create an array for columns.
  $cols = [
    ['id' => "",'label' => "Username",'pattern' => "",'type'=> "string"],
    ['id' => "",'label' => "Total Likes",'pattern' => "",'type'=> "number"]
  ];

  // Create an array for the rows.
  $rows = [];

  // If a user exists, add them to the array in Google Chart's expected format.
	if($n > 0) {
    while($row = mysqli_fetch_assoc($result)) {
      $rows[] = [
          'c' => [
                  [
                    'v' => $row['username'],
                    'f' => null
                  ],
                  [
                    'v' => (int) $row['likes'],
                    'f' => null
                  ]
                ]
      ];
    }
	}

  // Close the connection, it's no longer required.
  mysqli_close($connection);

  // Print the JSON out for parsing by JavaScript.
  header("Content-Type: application/json", NULL, 200);
  echo json_encode([
    'cols' => $cols,
    'rows' => $rows
  ]);
?>
