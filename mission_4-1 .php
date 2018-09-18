<?php

//コード指定
header('Content-Type: text/html; charset=UTF-8');

//DBに接続
$dsn = 'データベース名;charset=utf8mb4';
$user = 'ユーザー名';
$password = 'パスワード';

//操作の準備
$pdo = new PDO($dsn, $user, $password);

//エラーポートと例外
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


//$pdo -> query("drop table bbs");   テーブル削除したい時のためにコメントアウトで削除コード書いておくと便利。

//テーブル作成
try {

//投稿番号：id（整数)
//名前：name（32文字の文字列）
//コメント：comment（可変長文字列）
//日付：date（datetime型）
//パスワード：pass(32文字の文字列)
/*
    $sql = "CREATE TABLE bbs("
        . "id INT AUTO_INCREMENT PRIMARY KEY," //primary key設定を追加（これがないとテーブル作成できない）
    . "name char(32),"
        . "comment TEXT,"
        . "date datetime,"
        . "pass char(32)"
        . ");";
    $res = $pdo->query($sql); //実行後「Table 'bbs' already exists」系エラーが出たらここまでコメントアウトする（多分出る）
*/

//変数の作成
    $name = $_POST["name"];
    $comment = $_POST["comment"];
    $date = date("Y/m/d H:i:s");
    $delete = $_POST["delete"];
    $edit = $_POST["edit"];
    $editnum = $_POST["editnum"];
    $pass = $_POST["pass"];
    $editpass = $_POST["editpass"];
    $deletepass = $_POST["deletepass"];


//編集機能（前置）対象データを入力フォームに再表示
//編集フォームがカラでない場合
    if (!empty($edit)) {
        $sql = 'SELECT*FROM bbs';
        $results = $pdo->query($sql); //以上2行を追加
   //ループ処理
        foreach ($results as $row) {
       	   //編集対象番号が該当したら
            if ($row['id'] == $edit) { 
		   //且つパスワードが一致した場合
                if ($editpass == $row['pass']) {
	   		   //変数を指定
                    $editname = $row['name'];
                    $editcom = $row['comment'];
                    $editnum = $row['id'];
                    $changepass = $row['pass'];
                }
		   //パスワードが違った場合
                else {
                    echo "パスワードが違います。";
                }
            }
        }
    }



   //投稿機能
   //コメントと名前がカラじゃない場合
    if (!empty($comment) && !empty($name)) {
	   //編集対象番号がカラの場合
        if (empty($editnum)) {
            //  name,comment,date,passをinsert（idは自動付与なので挿入しない）
            $sql = $pdo->prepare("INSERT INTO bbs (name,comment,date,pass) VALUES (:name,:comment,:date,:pass)");
            $sql->bindParam(':name', $name, PDO::PARAM_STR);
            $sql->bindParam(':comment', $comment, PDO::PARAM_STR);
            $sql->bindParam(':date', $date, PDO::PARAM_STR);
            $sql->bindParam(':pass', $pass, PDO::PARAM_STR);
            $sql->execute();
        }    
       //編集機能
        else {
                $sql = "update bbs set name = '$name' , comment = '$comment' , date = '$date' , pass = '$pass' where id = $editnum";
                $results = $pdo->query($sql);
        }
    }

   //データ削除
    if (!empty($delete))     //削除対象番号が空ではない場合
    {
     //テーブル内のデータを取得するSQL文　3-6参照。
        $sql = "select * from bbs";
        $results = $pdo->query($sql);
        foreach ($results as $row) {
            if ($row['id'] == $delete)     //row['id']と$deleteが一致した場合
            {
                if ($row['pass'] == $deletepass)     //かつ$row['pass']と$deletepassが一致した場合
                {
                    $sql = "update bbs set comment ='削除しました。' where id = '$delete'";
                    $res = $pdo->query($sql);
                }
            }
        }
    }

} catch (PDOException $er) {
    print "Error:" . $er->getmessage();
}

?>

<html>

   <head>
	<!--設定とタイトル-->
	<meta charset="UTF-8"/>
	<title>mission_4-1</title>
   </head>

   <body>
	<!--フォームの作成-->
	<form method="POST" action="mission_4-1.php">
   <input type="text" name="name" placeholder="名前" value="<?php echo $editname; ?>"/><br>
   <input type="text" name="comment" placeholder="コメント" value="<?php echo $editcom; ?>"/>
   <input type="password" name="pass" placeholder="パスワード"/>
   <input type="hidden" name="editnum" placeholder="編集番号" value="<?php echo $editnum; ?>"/>
	<input type="submit" value="送信"/><br><br>

   <input type="text" name="delete" placeholder="削除対象番号"/>
   <input type="password" name="deletepass" placeholder="パスワード"/>
	<input type="submit" value="削除"/><br>

   <input type="text" name="edit" placeholder="編集対象番号"/>
   <input type="password" name="editpass" placeholder="パスワード"/>
	<input type="submit" value="編集"/><br>

   </body>
</html>

<?php
try {
   //データ表示

    $sql = 'SELECT*FROM bbs';
    $results = $pdo->query($sql);
    foreach ($results as $row) {
//$rowの中にはテーブルのカラム名が入る
        echo $row['id'] . ',';
        echo $row['name'] . ',';
        echo $row['comment'] . ',';
        echo $row['date'] . "<br>";
    }
} catch (PDOException $er) {
    print "Error:" . $er->getmessage();
}

?>