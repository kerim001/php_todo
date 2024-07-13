<?php

    $error ="";  

    // Database'e bağlanma işlemi
    $db = mysqli_connect('localhost', 'root', '', 'todo');  

    // Eğer formdan AddButton butonuna tıklanmışsa
    if (isset($_POST['AddButton'])) {
        $task = $_POST['task']; 
        if (empty($task)) {  
            $error = "lütfen içeriği boş bırakmayın";  
        } else {
            mysqli_query($db, "INSERT INTO tasks (task) VALUES ('$task')");
            // İşlemden sonra tekrar index.php sayfasına yönlendiriyoruz. Bu sayede form tekrar gönderilmiyor.
            // AMA BUNU YAPMAZSAK NASIL VE NEDEN FORM HER SAYFAYI YENİLEDİĞİMİZDE TEKRAR GÖNDERİLİYOR???
            header('location:index.php'); 
        }
    }

    
    if (isset($_GET['del_task'])) {  // Eğer URL'de del_task parametresi; varsa silinecek görev ID'sini alıyoruz ve siliyoz
        $id = $_GET['del_task'];  
        mysqli_query($db, "DELETE FROM tasks WHERE id=$id");  
        header('location: index.php');  
    }

    
    $tasks = mysqli_query($db, "SELECT * FROM tasks");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <link rel="stylesheet" href="style.css">  
    <title>Todo list</title>  
</head>
<body>
    <div class="heading">
        <h2>To do List</h2>  
    </div>


    <form action="index.php" method="POST">  
        <!-- BURADA İÇ İÇE PHPLER YAZMA OLAYINI DA ANLAYAMIYORUM Bİ TÜRLÜ ÇOK GARİP BİR SYNTAX -->
        <?php if (isset($error)) {  ?> 
            <p><?php echo $error; ?></p>  
        <?php } ?>
       
        <input type="text" name="task" class="task_input">  
        <button type="submit" class="add_btn" name="AddButton">Add Task</button> 
    </form>


    <!-- Görevleri listelemek için bir tablo oluşturuyoruz -->
    <table>
        <thead>
            <tr>
                <th>No</th>  
                <th>Task</th>  
                <th>Action</th>  
            </tr>
        </thead>

        <tbody>
            <?php $i = 1;  while ($row = mysqli_fetch_array($tasks)) { ?>  <!-- Görevleri veritabanından çekip tablo satırlarına yerleştiriyoruz-->
                <tr>
                    <td><?php echo $i; ?></td>  
                    <td class="task"><?php echo $row['task']; ?></td>  
                    <td class="delete">
                        <a href="index.php?del_task=<?php echo $row['id']; ?>">x</a>  <!-- Görevi silmek için bir bağlantı ekliyoruz. -->
                    </td>
                </tr>
            <?php $i++; } ?>  <!-- Her görev için görev numarasını bir artırıyoruz. -->
        </tbody>
    </table>
</body>
</html>
