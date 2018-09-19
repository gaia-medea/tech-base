<?php

//PHPとMySQLを接続
$dsn = 'データベース名';
$user = 'ユーザー名';
$password = 'パスワード';
$pdo = new PDO($dsn,$user,$password);

//エラーをはかせる処理
//$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
//ini_set('display_errors', 1);
//error_reporting(E_ALL);


//$name2,$comment2が空かどうかの条件分岐（編集フォームへ行く前の編集ボタンが正しく押されたか）
if(empty($_POST['name2']) && empty($_POST['comment2'])){
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//投稿フォーム作成
echo <<< EOT

	<!DOCTYPE html>
	<html lang="ja">
	<head>
	<meta charset="utf-8">
	<title>mission_4-1</title>
	</head>
	<body> <!--ブラウザに表示させたいものを入力-->

	<form action = "mission_4-1.php" method = "post"><!--タグ-->
	&emsp;&emsp;&emsp;&emsp;名前:
	<input type = "text" name = "name" value = "名前" size = "30"><br>  <!--「名前」と「コメント」の入力フォーム作成-->
	&emsp;&emsp;コメント:
	<input type = "text" name = "comment" value = "コメント" size = "30"><br>
	&emsp;パスワード:
	<input type = "text" name = "pass" value = "" size = "30"><br>  <!--「パスワード」入力フォーム作成-->
	&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;<input type = "submit" name = "" value = "送信" size = "">
	<input type = "hidden" name = "kakushi" value = "$edit" size = "30">  <!--隠し要素-->
	</form>
	<br>
	<form action = "mission_4-1.php" method = "post"><!--タグ-->

	削除対象番号:
	<input type = "number" name = "delete" value = "削除対象番号" size = "30"><br>  <!--行の削除のためのフォーム作成-->
	&emsp;パスワード:
	<input type = "text" name = "pass-delete" value = "" size = "30"><br>  <!--「パスワード」入力フォーム作成-->
	&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;<input type = "submit" name = "sakujo" value = "削除" size = "">
	</form>
	<br>
	<form action = "mission_4-1.php" method = "post"><!--タグ-->

	編集対象番号:
	<input type = "number" name = "edit" value = "編集対象番号" size = "30"><br>  <!--編集のためのフォーム作成-->
	&emsp;パスワード:
	<input type = "text" name = "pass-edit" value = "" size = "30"><br>  <!--「パスワード」入力フォーム作成-->
	&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;<input type = "submit" name = "hensyuu" value = "編集" size = "">
	</form>

EOT;
		//nameとcommentが送信されたかの条件分岐
	if(!empty($_POST['name']) && !empty($_POST['comment'])){	//送信された場合
			//passがあるかの条件分岐
		if(!empty($_POST['pass'])){	//passがある場合
			$name = $_POST['name'];
			$comment = $_POST['comment'];
			$date = date('Y/m/d H:i:s');
			$password = $_POST['pass'];
				//パスワード保存処理
				//テーブルがあるかの条件分岐
			$stmt = $pdo->query("SHOW TABLES LIKE 'keijiban'");
			if($stmt->fetch(PDO::FETCH_ASSOC)){
					//id取得、テーブルへ入力処理
				$inp = $pdo->prepare("INSERT INTO keijiban(name,comment,date,password) VALUES(:name,:comment,:date,:password)");
				$inp->bindParam(':name',$name,PDO::PARAM_STR);
				$inp->bindParam(':comment',$comment,PDO::PARAM_STR);
				$inp->bindParam(':date',$date,PDO::PARAM_STR);
				$inp->bindParam(':password',$password,PDO::PARAM_STR);
				$name = $_POST['name'];
				$comment = $_POST['comment'];
				$date = date('Y/m/d H:i:s');
				$password = $_POST['pass'];
				$inp->execute();
			}else{	//テーブルなしの場合
					//テーブル作成
				$table = "CREATE TABLE keijiban"
				."("
				."id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,"
				."name char(32),"
				."comment TEXT,"
				."date DATETIME,"
				."password TEXT"
				.");";
				$stm = $pdo->query($table);

					//初期の入力処理、idを1としてデータ入力
				$inp = $pdo->prepare("INSERT INTO keijiban(name,comment,date,password) VALUES(:name,:comment,:date,:password)");
				$inp->bindParam(':name',$name,PDO::PARAM_STR);
				$inp->bindParam(':comment',$comment,PDO::PARAM_STR);
				$inp->bindParam(':date',$date,PDO::PARAM_STR);
				$inp->bindParam(':password',$password,PDO::PARAM_STR);
				$name = $_POST['name'];
				$comment = $_POST['comment'];
				$date = date('Y/m/d H:i:s');
				$password = $_POST['pass'];
				$inp->execute();
			}
			echo "ご入力ありがとうございます。<br/>".$date."に".$comment."を受け付けました。<br/>";
		}else{	//passがない場合
			echo "パスワードを入力してください<br/>";
		}
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//削除機能
		//deleteが送信されたかの条件分岐
	if(!empty($_POST['delete'])){	//送信された場合
		$delete = intval($_POST['delete']);
			//pass-deleteがあるかの条件分岐
		if(!empty($_POST['pass-delete'])){	//pass-deleteがある場合
				//一致するidのpassを取得する
			$sel = "SELECT * FROM keijiban";
			$stmt = $pdo->query($sel);
			$res = $stmt->fetchAll();
			foreach($res as $row){
				if($delete == $row['id']){	//idと削除番号が一致の場合
						//passとpass-deleteが一致するかの条件分岐
					if($row['password'] == $_POST['pass-delete']){	//passと一致する場合
							//MySQLで削除処理
						$id = $delete;
						$sql = "delete from keijiban where id=$id";
						$result = $pdo->query($sql);

						echo "削除しました<br/>";

					}elseif($row['password'] !== $_POST['pass-delete']){	//passと不一致の場合
						echo "パスワードが違います<br/>";
					}
				}
			}
		}else{	//pass-deleteがない場合
			echo "パスワードを入力してください<br/>";
		}
	}

	///////////////////////////////////////////////////////////////////////////////////////////////////////////
		//編集機能
		//editが送信されたかの条件分岐
	if(!empty($_POST['edit'])){	//送信された場合
		$edit = intval($_POST['edit']);
			//pass-editがあるかの条件分岐
		if(!empty($_POST['pass-edit'])){	//pass-editがある場合
			$sel = "SELECT * FROM keijiban where id=$edit";
			$stmt = $pdo->query($sel);
			$res = $stmt->fetchAll();
			foreach($res as $row){
						//passとpass-editが一致するかの条件分岐
					if($row['password'] == $_POST['pass-edit']){	//passと一致の場合
							//$name2と$comment2に代入処理
						$id = $edit;
						$namae = $row['name'];
						$comme = $row['comment'];

echo <<< EOT
						<form action = "mission_4-1.php" method = "post"><!--タグ-->
						編集フォーム<br>
						<br>
						&emsp;&emsp;&emsp;&emsp;名前:
						<input type = "text" name = "name2" value = "$namae" size = "30"><br>  <!--「名前」と「コメント」の入力フォーム作成-->
						&emsp;&emsp;コメント:
						<input type = "text" name = "comment2" value = "$comme" size = "30"><br>
						&emsp;パスワード:
						<input type = "text" name = "pass2" value = "" size = "30"><br>  <!--「パスワード」入力フォーム作成-->
						&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;<input type = "submit" name = "sub" value = "送信" size = ""><br>
						<input type = "hidden" name = "kakushi" value = "$edit" size = "30">  <!--隠し要素-->
						</form>
EOT;

					}elseif($row['password'] !== $_POST['pass-edit']){	//passと不一致の場合
						echo "パスワードが違います<br/>";
					}
			}
		}else{	//pass-editがない場合
			echo "パスワードを入力してください<br/>";
		}
	}

	//$name2と$comment2が空かどうかの条件分岐
}else{
echo <<< EOT

	<!DOCTYPE html>
	<html lang="ja">
	<head>
	<meta charset="utf-8">
	<title>mission_4-1</title>
	</head>
	<body> <!--ブラウザに表示させたいものを入力-->

	<form action = "mission_4-1.php" method = "post"><!--タグ-->
	&emsp;&emsp;&emsp;&emsp;名前:
	<input type = "text" name = "name" value = "名前" size = "30"><br>  <!--「名前」と「コメント」の入力フォーム作成-->
	&emsp;&emsp;コメント:
	<input type = "text" name = "comment" value = "コメント" size = "30"><br>
	&emsp;パスワード:
	<input type = "text" name = "pass" value = "" size = "30"><br>  <!--「パスワード」入力フォーム作成-->
	&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;<input type = "submit" name = "" value = "送信" size = "">
	<input type = "hidden" name = "kakushi" value = "$edit" size = "30">  <!--隠し要素-->
	</form>
	<br>
	<form action = "mission_4-1.php" method = "post"><!--タグ-->

	削除対象番号:
	<input type = "number" name = "delete" value = "削除対象番号" size = "30"><br>  <!--行の削除のためのフォーム作成-->
	&emsp;パスワード:
	<input type = "text" name = "pass-delete" value = "" size = "30"><br>  <!--「パスワード」入力フォーム作成-->
	&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;<input type = "submit" name = "sakujo" value = "削除" size = "">
	</form>
	<br>
	<form action = "mission_4-1.php" method = "post"><!--タグ-->

	編集対象番号:
	<input type = "number" name = "edit" value = "編集対象番号" size = "30"><br>  <!--編集のためのフォーム作成-->
	&emsp;パスワード:
	<input type = "text" name = "pass-edit" value = "" size = "30"><br>  <!--「パスワード」入力フォーム作成-->
	&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;<input type = "submit" name = "hensyuu" value = "編集" size = "">
	</form>

EOT;
						//pass2があるかの条件分岐
						$kakushi = $_POST['kakushi'];
						if(!empty($_POST['pass2'])){	//pass2がある場合
							$sel = "SELECT * FROM keijiban where id=$kakushi";
							$stmt = $pdo->query($sel);
							$res = $stmt->fetchAll();
							foreach($res as $row){
								//passとpass2が一致するかの条件分岐
									if($row['password'] == $_POST['pass2']){	//一致の場合
											//MySQLで編集処理
										$id = $row['id'];
										$name2 = $_POST['name2'];
										$comment2 = $_POST['comment2'];
										$date2 = date('Y/m/d H:i:s');
										$sql = "update keijiban set name='$name2',comment='$comment2',date='$date2' where id=$kakushi";
										$result = $pdo->query($sql);
										echo "編集しました<br/>";
									}elseif($row['password'] !== $_POST['pass2']){	//不一致の場合
										echo "パスワードが違います<br/>";
									}
							}
						}else{	//pass2がない場合
							echo "パスワードを入力してください<br/>";
						}

}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//テーブルの情報をMySQLでブラウザへ表示

	//テーブルがあるかの条件分岐
$stmt = $pdo->query("SHOW TABLES LIKE 'keijiban'");
if($stmt->fetch(PDO::FETCH_ASSOC)){
	$sql = 'SELECT * FROM keijiban ORDER BY id';
	$results = $pdo->query($sql);
	$result = $results->fetchAll();
	foreach($result as $row){
			echo $row['id'].' '.$row['name'].' '.$row['comment'].' '.$row['date'].'<br>';
	}
}


echo <<<EOT
</body>
</html>
EOT;

?>


