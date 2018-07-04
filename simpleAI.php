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
        "get_nilai" => [
            "tampilkan nilai",
            "tampilkan nilai saya",
            "nilai"
        ],
        "jadwal_kegiatan" => [
            "minta jadwal kegiatan raja brawijaya",
            "jadwal raja brawijaya apa aja ?",
            "jadwalnya raja brawijaya"
        ],
        "profile_raja_brawijaya" => [
            "apa itu raja brawijaya",
            "raja brawijaya itu apaan ?",
            "raja brawijaya itu ngapain"
        ],
        "lokasi_nongkrong" => [
            "lokasi yang asik di brawijaya",
            "tempat nongkrong di UB",
            "tempat belajar yang nyaman di UB"
        ]
    ];

    if (isset($_GET['submit'])) {
        $kalimat = strtolower($_GET["kalimat"]);
        
        $kategoriDimaksud = [0, ""];
        foreach ($kategori as $namaKategori => $kategori) {
            $kemiripanTerbesarKategori = 0;
            echo "<ul>";
            for ($i = 0; $i < count($kategori); $i++) {
                similar_text($kalimat, $kategori[$i], $kemiripan);
                if ($kemiripan >= $kemiripanTerbesarKategori) {
                    $kemiripanTerbesarKategori = $kemiripan;
                }
            }
            echo "<li>Kemiripan terbesar kategori $namaKategori : $kemiripanTerbesarKategori %</li>";

            if ($kategoriDimaksud[0] < $kemiripanTerbesarKategori) {
                $kategoriDimaksud[0] = $kemiripanTerbesarKategori;
                $kategoriDimaksud[1] = $namaKategori;
            } else if ($kategoriDimaksud[0] == $kemiripanTerbesarKategori) {
                $kategoriDimaksud[1] = "Aku bingung";
            }

            echo "</ul>";
        }
        if ($kategoriDimaksud[0] < 60) {
            $kategoriDimaksud[1] = "aku gak ngerti";
        }
        echo "kategori yang dimaksud : " . $kategoriDimaksud[1];

    }

    ?>
</body>
</html>