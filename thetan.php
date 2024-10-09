<?php
// Fungsi untuk menyimpan data ke file JSON
function saveMatchStats($email, $total_match, $total_win) {
    // Menghitung persentase kemenangan
    if ($total_match > 0) {
        $win_percentage = ($total_win / $total_match) * 100;
    } else {
        $win_percentage = 0;
    }

    // Membuat array untuk data baru
    $data = [
        'email' => $email,
        'total_match' => $total_match,
        'total_win' => $total_win,
        'win_percentage' => number_format($win_percentage, 2)
    ];

    // Membaca data yang ada dari file JSON
    $filename = 'match_stats.json';
    if (file_exists($filename)) {
        $current_data = json_decode(file_get_contents($filename), true);
        
        // Menghapus data lama jika email sudah ada
        foreach ($current_data as $key => $item) {
            if ($item['email'] === $email) {
                unset($current_data[$key]);
                break;
            }
        }
    } else {
        $current_data = [];
    }

    // Menambahkan data baru ke array
    $current_data[] = $data;

    // Menyimpan kembali ke file JSON
    file_put_contents($filename, json_encode($current_data, JSON_PRETTY_PRINT));
}

// Fungsi untuk menghapus data berdasarkan email
function deleteMatchStats($email) {
    $filename = 'match_stats.json';
    if (file_exists($filename)) {
        $current_data = json_decode(file_get_contents($filename), true);
        $found = false;

        // Mencari dan menghapus data berdasarkan email
        foreach ($current_data as $key => $item) {
            if ($item['email'] === $email) {
                unset($current_data[$key]);
                $found = true;
                break;
            }
        }

        // Jika ditemukan, simpan kembali data yang tersisa
        if ($found) {
            file_put_contents($filename, json_encode(array_values($current_data), JSON_PRETTY_PRINT));
            return true;
        }
    }
    return false;
}

// Memeriksa apakah form untuk menyimpan atau menghapus telah disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $email_to_delete = $_POST['email_to_delete'] ?? '';
        deleteMatchStats($email_to_delete);
    } else {
        $email = $_POST['email'] ?? '';
        $total_match = $_POST['total_match'] ?? 0;
        $total_win = $_POST['total_win'] ?? 0;

        // Menyimpan data ke file JSON
        saveMatchStats($email, $total_match, $total_win);
    }
}

// Membaca data dari file JSON untuk ditampilkan
$filename = 'match_stats.json';
$current_data = [];
if (file_exists($filename)) {
    $current_data = json_decode(file_get_contents($filename), true);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thetan Arena Winrate Calculator</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        h1, h2 {
            color: #333;
        }
        form {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="email"],
        input[type="number"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="submit"] {
            background: #5cb85c;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background: #4cae4c;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #fff;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tbody tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <h1>Input Match Stats</h1>
    <form method="post" action="">
        <label for="email">Email:</label>
        <input type="email" name="email" required>
        <label for="total_match">Total Match:</label>
        <input type="number" name="total_match" required>
        <label for="total_win">Total Win:</label>
        <input type="number" name="total_win" required>
        <input type="submit" value="Simpan Data">
    </form>

    <h2>Hapus Data Match Stats</h2>
    <form method="post" action="">
        <label for="email_to_delete">Email yang ingin dihapus:</label>
        <input type="email" name="email_to_delete" required>
        <input type="hidden" name="action" value="delete">
        <input type="submit" value="Hapus Data">
    </form>

    <h2>Data Match Stats</h2>
    <table>
        <thead>
            <tr>
                <th>Email</th>
                <th>Total Match</th>
                <th>Total Win</th>
                <th>Persentase Kemenangan (%)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($current_data)): ?>
                <?php foreach ($current_data as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['email']); ?></td>
                        <td><?php echo htmlspecialchars($item['total_match']); ?></td>
                        <td><?php echo htmlspecialchars($item['total_win']); ?></td>
                        <td><?php echo htmlspecialchars($item['win_percentage']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">Tidak ada data.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
