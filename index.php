<?php
$error = "";  

// Database'e bağlanma işlemi
$db = mysqli_connect('localhost', 'root', '', 'todo');  

// Eğer formdan AddButton butonuna tıklanmışsa
if (isset($_POST['AddButton'])) {
    $task = mysqli_real_escape_string($db, $_POST['task']);

    if (empty($task)) {  
        $error = "Lütfen içeriği boş bırakmayın";  
    } else {
        // Aynı görevin olup olmadığını kontrol ediyoruz
        $task_check_query = "SELECT * FROM tasks WHERE task='$task' LIMIT 1"; // bir SQL sorgusu belirledik
        // bu SQL sorgusu $task ile tablodaki herhangi bir task aynı mı diye kontrol ediyor
        $result = mysqli_query($db, $task_check_query); 
        // Bir önceki satırda tanımlanan SQL sorgusunu çalıştırır. 
        // $result bir mysqli_result nesnesi olur ve sonuç setini içerir.
        // eğer $task dbdeki bir task ile aynı ise $result'ın içi dolu olarak gelir
        // eğer $task dbdeki bir task ile aynı değil ise $result'ın içi boş gelir ama bu 
        // $result, null'a eşit demek değildir

        $existing_task = mysqli_fetch_assoc($result); 
        // Bu satır, $result nesnesinden bir satır alır ve bunu bir ilişkilendirilmiş dizi olarak döner.
        // Eğer sonuç setinde herhangi bir satır yoksa, $existing_task false döner.

        
        if ($existing_task) {
            $error = "Bu görev zaten mevcut!";
        } else {
            mysqli_query($db, "INSERT INTO tasks (task) VALUES ('$task')");
            // İşlemden sonra tekrar index.php sayfasına yönlendiriyoruz.
            header('location: index.php'); 
        }
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
        <?php if ($error) { ?> 
            <p><?php echo $error; ?></p>  
        <?php } ?>
       
        <input type="text" name="task" autocomplete="off" class="task_input">
        <button type="submit" class="add_btn" name="AddButton">Add Task</button> 
    </form>

    <table>
        <thead>
            <tr>
                <th>No</th>  
                <th>Task</th>  
                <th>Action</th>  
            </tr>
        </thead>

        <tbody>
            <?php $i = 1;  while ($row = mysqli_fetch_array($tasks)) { ?> 
                <tr>
                    <td><?php echo $i; ?></td>  
                    <td class="task"><?php echo $row['task']; ?></td>  
                    <td class="delete">
                        <a href="index.php?del_task=<?php echo $row['id']; ?>">x</a>
                    </td>
                </tr>
            <?php $i++; } ?> 
        </tbody>
    </table>
</body>
</html>
