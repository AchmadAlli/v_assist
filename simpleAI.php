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

        $kemiripanTerbesar = 0; // persentase kemiripan tebesar
        $kategoriDimaksud = ""; // kategori yang di maksud
        $pecahKataUser = multiexplode([" ", ",", ".", "?", "!", "-", "_"], strtolower($kalimat)); // memecah kata user
        foreach ($kategori as $namaKategori => $kategori) { // perulangan untuk kategori
            $kemiripanTerbesarKategori = 0; // persentase kemiripan terbesar dari setiap kategori
            echo "<ul>";

            for ($i = 0; $i < count($kategori); $i++) {  // perulangan untuk kata-kata dalam kategori
                $pecahKataDb = multiexplode([" ", ",", ".", "?", "!", "-", "_"], strtolower($kategori[$i])); // memecah kata di db
                $kataSama = 0;

                foreach ($pecahKataUser as $kataUser) { // kata User
                    foreach ($pecahKataDb as $kataDb) { // kata Db
                        if ($kataUser == $kataDb) {
                            $kataSama++;
                        }
                    }
                }

                $kemiripan = ($kataSama / count($pecahKataUser)) * 100; // persentase kemiripan 
                if ($kemiripan >= $kemiripanTerbesarKategori) { // proses untuk mencari kemiripan terbesar dari setiap kategori
                    $kemiripanTerbesarKategori = $kemiripan;
                }

                echo "<li>" . $kemiripan . "</li>";
            }
            echo "<li>Kemiripan terbesar kategori $namaKategori : $kemiripanTerbesarKategori %</li>";

            if ($kemiripanTerbesar < $kemiripanTerbesarKategori) { // proses untuk mencari kemiripan terbesar dari semua kategori
                $kemiripanTerbesar = $kemiripanTerbesarKategori;
                $kategoriDimaksud = $namaKategori;
            } else if ($kemiripanTerbesar == $kemiripanTerbesarKategori) { // jika ada 2 atau lebih kategori yang memiliki kemiripan terbesar
                $kategoriDimaksud = "Aku bingung";
            }
            echo "</ul>";
        }
        if ($kemiripanTerbesar < 60) { // jika kemiripan terbesar tidak memeuhi standar
            $kategoriDimaksud = "aku gak ngerti";
        }
        echo "kategori yang dimaksud : " . $kategoriDimaksud;

    }

    function multiexplode($delimiters, $string)
    {
        $ready = str_replace($delimiters, $delimiters[0], $string);
        $launch = explode($delimiters[0], $ready);
        return $launch;
    }
    
    ?>
</body>
</html>