<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Факториал числа</title>
</head>
<body>   
    <h1>Вычисление факториала</h1>

    <form method="post">
        <label for="number">Введите число:</label>
        <input type="number" name="number">
        <input type="submit" value="Вычислить факториал">
    </form>
       
    <?php
    // Подключение к базе данных   
    $conn = new mysqli("localhost", "root", "1", "testdb", 3307);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Вычисление факториала
    function factorial($number) {
        return ($number == 0 || $number == 1) ? 1 : $number * factorial($number - 1);        
    }

    $filename = 'result.txt';
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $number = $_POST['number'];
        // Проверка корректности введённых данных
        if (is_numeric($number)) {
            // Вычисление и отображение результата
            $result = factorial($number);
            echo "<br>Факториал числа $number равен $result<br>";
            
            // Проверка отсутствия данного значения в базе и расположения исходного числа в диапазоне от 0 до 20
            if ($conn->query("SELECT COUNT(*) as count FROM calculations WHERE input_number = $number")->fetch_assoc()['count'] == 0 && $number <= 20) {
                // Запись результата в базу данных                
                if ($conn->query("INSERT INTO calculations (input_number, result) VALUES ($number, $result)") === TRUE) {
                    echo "Результат успешно добавлен в базу данных.";
                } else {
                    echo "Ошибка: " . $sql . "<br>" . $conn->error;
                }
            }
            
            /*
            // Запись данных в текстовый файл
            $file = fopen($filename, 'a'); 
            fwrite($file, "\nФакториал числа $number равен $result");
            
            // Проверка наличия одинаковых строк в файле
            $lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $uniqueLines = array_unique($lines);
            $newContent = implode("\n", $uniqueLines);
            file_put_contents($filename, $newContent);

            fclose($file);
            */
        } else {
            echo "<br>Введенное значение не является числом.<br>";
        }                         
    }
    /*
    // Получение данных из текстового файла
    if (file_exists($filename)){
        echo "<br>История вычислений:<br>" . nl2br(file_get_contents($filename));
    } 
    */

    // Получение данных из базы
    echo "<br>История вычислений:<br>";    
    $calculations = $conn->query("SELECT * FROM calculations");

    if ($calculations->num_rows > 0) {
        while($row = $calculations->fetch_assoc()) {
            echo "Число: " . $row["input_number"]. ", Факториал: " . $row["result"]. "<br>";
        }
    } else {
        echo "Нет сохраненных результатов вычислений.";
    }

    $conn->close();  
    ?>
</body>
</html>