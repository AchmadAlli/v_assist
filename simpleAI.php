<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>iki form</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    
    <form action="" method="get">
        <input type="text" name="kalimat"><br>
        <input type="submit" value="submit" name="submit">
    </form>

    <?php

    $kategori = [
        "get nilai" => [
            "tampilkan nilai",
            "tampilkan nilai saya",
            "nilai"
        ]
    ];

    if (isset($_GET['submit'])) {
        $kalimat = strtolower($_GET["kalimat"]);
        foreach ($kategori as $key => $value) {
            for ($i=0; $i < count($value); $i++) { 
                if ($kalimat == $value[$i]) {
                    echo $key;
                }
            }
        }
    }

    ?>
</body>
</html>