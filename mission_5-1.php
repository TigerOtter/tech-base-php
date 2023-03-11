<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-1</title>
    <style>
        button {
            margin-bottom: 10px;
            background-color: #eb6100;
            border: 1px solid #333;
            border-radius: 10%;
            color: #fff;
        }
    </style>
</head>
<body>
    <h2>好きな「ご飯のお供」について語ろう!!</h2>
    <?php
        //データベース接続用
        $dsn = 'データベース名';
        $user = 'ユーザー名';
        $password = 'パスワード';
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
        
         $sql = "CREATE TABLE IF NOT EXISTS bulletinBoard"
        ." ("
        . "id INT AUTO_INCREMENT PRIMARY KEY,"
        . "name char(32),"
        . "comment TEXT,"
        . "date DATETIME,"
        . "password char(32)"
        .");";
        $stmt = $pdo->query($sql);
        
        //投稿用フォームが送信されたときの処理
        if(isset($_POST["submit"])) {
            $name = $_POST["name"];
            $comment = $_POST["comment"];
            $password = $_POST["password"];
            //名前とコメントとパスワードがすべて空でないとき
            if(!empty($name) && !empty($comment) && !empty($password)) {
                $date = date("Y/m/d H:i:s");
                //新規投稿の場合の処理
                if(empty($_POST["check"])) {
                    $sql = $pdo->prepare("INSERT INTO bulletinBoard (name, comment, date, password) VALUES (:name, :comment, :date, :password)");
                    $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                    $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                    $sql -> bindParam(':date', $date, PDO::PARAM_STR);
                    $sql -> bindParam(':password', $password, PDO::PARAM_STR);
                    $sql -> execute();
                } else {     //編集モードの場合の処理
                    $currentEdit = $_POST["check"];
                    $sql = 'UPDATE bulletinBoard SET name=:name, comment=:comment, date=:date, password=:password WHERE id=:id';
                    $stmt = $pdo->prepare($sql);
                    $stmt -> bindParam(':name', $name, PDO::PARAM_STR);
                    $stmt -> bindParam(':comment', $comment, PDO::PARAM_STR);
                    $stmt -> bindParam(':date', $date, PDO::PARAM_STR);
                    $stmt -> bindParam(':password', $password, PDO::PARAM_STR);
                    $stmt -> bindParam(':id', $currentEdit, PDO::PARAM_STR);
                    $stmt -> execute();
                }
            }
        } 
        
        //削除用フォームが送信されたときの処理
        if(isset($_POST["delete"])) {
            $delNum = $_POST["delNum"];
            $delPass = $_POST["delPass"];
            if(!empty($delNum) && !empty($delPass)) {
                $sql = 'delete from bulletinBoard WHERE id=:id AND password=:password';
                $stmt = $pdo->prepare($sql);
                $stmt -> bindParam(':id', $delNum, PDO::PARAM_INT);
                $stmt -> bindParam(':password', $delPass, PDO::PARAM_STR);
                $stmt -> execute();
            }
        }
        
        //編集用フォームが送信されたときの処理
        if(isset($_POST["edit"])) {
            $editNum = $_POST["editNum"];
            $editPass = $_POST["editPass"];
            if(!empty($editNum) && !empty($editPass)) {
                $sql = 'SELECT * FROM bulletinBoard WHERE id=:id AND password=:password';
                $stmt = $pdo->prepare($sql);
                $stmt -> bindParam(':id', $editNum, PDO::PARAM_INT);
                $stmt -> bindParam(':password', $editPass, PDO::PARAM_STR);
                $stmt -> execute();
                $results = $stmt->fetchAll();
                foreach($results as $row) {
                    $editCheck = $row['id'];
                    $nameValue = $row['name'];
                    $comValue = $row['comment'];
                    $passValue = $row['password'];
                }
            }
        }
    ?>
    
    <form action="" method="post">
        <input type="text" name="name" placeholder="名前を入力" value="<?php if(isset($nameValue)) {echo $nameValue;}?>">
        <input type="text" name="comment" placeholder="コメントを入力" value="<?php if(isset($comValue)) {echo $comValue;}?>">
        <input type="password" name="password" placeholder="パスワードを入力" value="<?php if(isset($passValue)) {echo $passValue;}?>">
        <input type="hidden" name="check" value="<?php if(isset($editCheck)) {echo $editCheck;} ?>">
        <button name="submit">送信</button><br>
        <input type="number" name="delNum" placeholder="削除する投稿番号を入力">
        <input type="password" name="delPass" placeholder="パスワードを入力">
        <button name="delete">削除</button><br>
        <input type="number" name="editNum" placeholder="編集する投稿番号を入力">
        <input type="password" name="editPass" placeholder="パスワードを入力">
        <button name="edit">編集</button>
    </form>

    <?php
        $sql = 'SELECT * FROM bulletinBoard';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach($results as $row) {
            echo $row['id'].' ';
            echo $row['name'].' ';
            echo $row['comment'].' ';
            echo $row['date'].'<br>';
        }
    ?>
</body>
</html>