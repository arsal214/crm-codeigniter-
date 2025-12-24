<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DeskTime Data</title>
</head>
<body>
    <h1>DeskTime Company Data</h1>
    
    <?php if (isset($desktime_data) && !empty($desktime_data)): ?>
        <pre><?php print_r($desktime_data); ?></pre>
    <?php else: ?>
        <p><?php echo $error; ?></p>
    <?php endif; ?>

</body>
</html>
