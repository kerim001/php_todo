<?php
$error = "";  // Hata mesajını tutacak değişken

// Veritabanı bağlantısı için gerekli bilgiler
$host = "localhost";
$username = "root";
$password = "";
$db = "todo";

try {
    // PDO ile veritabanına bağlanma
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $username, $password);
    
} catch (PDOException $e) {
    echo "Bağlantı hatası: " . $e->getMessage(); // Bağlantı hatası mesajını göster
    exit; // Hata durumunda scripti durdur
}

// Eğer AddButton butonuna tıklanmışsa
if (isset($_POST['AddButton'])) {
    $task = $_POST['task']; // Formdan gelen görevi al

    if (empty($task)) {
        $error = "Lütfen içeriği boş bırakmayın"; // Görev boşsa hata mesajı
    } else {
        // Aynı görevin olup olmadığını kontrol et
        $stmt = $pdo->prepare("SELECT * FROM tasks WHERE task = :task LIMIT 1"); 
        // yeni eklenen :task tablodaki tasklerde aranması için olan sql sorgusu
        $stmt->execute(['task' => $task]);
        // execute fonksiyonu, :task parametresini $task değişkeninin değeriyle değiştirir ve sorguyu yürütür.
        $existing_task = $stmt->fetch(PDO::FETCH_ASSOC); // Sorgu sonucunu al
        // sorgu sonucunu bir ilişkilendirilmiş dizi (associative array) olarak alır. 
        // Eğer sonuç varsa, $existing_task değişkeni bu sonucu içerir; aksi takdirde false döner.
        if ($existing_task) {
            $error = "Bu görev zaten mevcut!"; // Görev zaten varsa hata mesajı
        } else {
            // Yeni görevi veritabanına ekle
            $stmt = $pdo->prepare("INSERT INTO tasks (task) VALUES (:task)");
            $stmt->execute(['task' => $task]);
            header('Location: index.php'); // İşlemden sonra sayfayı yeniden yükle
            exit;
        }
    }
}

// Eğer URL'de del_task parametresi varsa
if (isset($_GET['del_task'])) {
    $id = $_GET['del_task']; // Silinecek görevin ID'sini al
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = :id");
    $stmt->execute(['id' => $id]); // Görevi veritabanından sil
    header('Location: index.php'); // İşlemden sonra sayfayı yeniden yükle
    exit;
}

// Tüm görevleri veritabanından getir
$stmt = $pdo->query("SELECT * FROM tasks");
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC); // Tüm görevleri al
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8"> <!-- Türkçe karakter desteği için UTF-8 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsive tasarım için viewport ayarı -->
    <link rel="stylesheet" href="style.css"> <!-- Harici CSS dosyasını bağla -->
    <title>Yapılacaklar Listesi</title> <!-- Sayfa başlığı -->
</head>
<body>
    <div class="heading">
        <h2>Yapılacaklar Listesi</h2> <!-- Başlık -->
    </div>

    <form action="index.php" method="POST"> <!-- Form başlangıcı -->
        <input type="text" name="task" autocomplete="off" class="task_input"> <!-- Görev girişi için input -->
        <button type="submit" class="add_btn" name="AddButton">Görev Ekle</button> <!-- Görev ekleme butonu -->
    </form>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Görev</th>
                <th>İşlem</th>
            </tr>
        </thead>

        <tbody>
            <?php $i = 1; foreach ($tasks as $row) { ?> <!-- Tüm görevleri listele -->
                <tr>
                    <td><?php echo $i; ?></td> <!-- Görev numarası -->
                    <td class="task"><?php echo htmlspecialchars($row['task']); ?></td> <!-- Görev içeriği -->
                    <td class="delete">
                        <a href="index.php?del_task=<?php echo $row['id']; ?>">x</a> <!-- Görev silme bağlantısı -->
                    </td>
                </tr>
            <?php $i++; } ?>
        </tbody>
    </table>
</body>
</html>
