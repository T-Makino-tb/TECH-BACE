<html>
	<head>
		<title>News.ch ～ニュース共有掲示板と言えばNews.ch～</title>
		<link rel="stylesheet" type="text/css" href="mission_5-1style.css" />
	</head>
	<?php
		//データベースに接続
		//データベース名
		$dsn = '(ここに入力)';

		//ユーザ名
		$user = '(ここに入力)';

		//パスワード
		$password = '(ここに入力)';

		//データベース上のエラーを表示
		$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

		//テーブルの作成
		$sql = "CREATE TABLE IF NOT EXISTS 5_tb"
		." ("
		."id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,"
		."name char(32),"
		."comment TEXT,"
		."date char(32),"
		."pass char(32)"
		.");";
		$stmt = $pdo->query($sql);

		//エラー「Notice」を表示しない。
		error_reporting(E_ALL & ~E_NOTICE);

		//データの受け取り
		//名前
		$name = ($_POST['name']);

		//名無し
		$no_name = "名無し";

		//コメント
		$comment = ($_POST['comment']);

		//日付データ
		$date = date("Y年m月d日 H:i:s");

		//編集申請番号
		$edit = ($_POST['edit']);

		//編集番号
		$edit2 = ($_POST['editnum']);

		//編集マーク
		$editmark = "<編集済>";

		//削除番号
		$delete = ($_POST['delete']);

		//パスワード
		$pass = ($_POST['pass']);

		//編集時パスワード
		$e_pass = ($_POST['e_pass']);

		//削除時パスワード
		$d_pass = ($_POST['d_pass']);

		//新規投稿送信ボタン
		$button1 = ($_POST['button1']);

		//編集送信ボタン
		$button2 = ($_POST['button2']);

		//削除送信ボタン
		$button3 = ($_POST['button3']);

		//パスワード記入ミスの表示
		$miss = "パスワードが誤っています。";

		//新規送信ボタンをクリックされたとき
		if(isset($button1)){

			//名前、パスワードが入力されているとき
			if(!empty($name) && !empty($pass) && empty($edit2)){

				//投稿データを入力
				$sql = $pdo -> prepare("INSERT INTO 5_tb (name, comment, date, pass) VALUES (:name, :comment, :date, :pass)");
				$sql -> bindParam(':name', $name, PDO::PARAM_STR);
				$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
				$sql -> bindParam(':date', $date, PDO::PARAM_STR);
				$sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
				$sql -> execute();

			//名前の記入忘れ
			}else if(empty($name) && empty($edit2) && !empty($pass)){

				//名無しの投稿データを入力
				$sql = $pdo -> prepare("INSERT INTO 5_tb (name, comment, date, pass) VALUES (:name, :comment, :date, :pass)");
				$sql -> bindParam(':name', $no_name, PDO::PARAM_STR);
				$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
				$sql -> bindParam(':date', $date, PDO::PARAM_STR);
				$sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
				$sql -> execute();

			//新規投稿時の記入漏れの警告
			//パスワードの記入忘れ
			}else if(!empty($name) && empty($edit2) && empty($pass)){
				echo "パスワードを入力してください。";

			//更に編集番号が入力されているとき
			}else if(!empty($name) && !empty($pass) && !empty($edit2)){
				$sql = 'SELECT * FROM 5_tb';
				$stmt = $pdo->query($sql);
				$results = $stmt->fetchAll();
				foreach($results as $row){						

					//投稿番号が一致した投稿を編集
					if($row['id']==$edit2){
						$sql = 'update 5_tb set name=:name,comment=:comment,date=:date,pass=:pass where id=:id';
						$stmt = $pdo->prepare($sql);
						$stmt -> bindParam(':id', $edit2, PDO::PARAM_INT);
						$stmt -> bindParam(':name', $name, PDO::PARAM_STR);
						$stmt -> bindParam(':comment', $comment, PDO::PARAM_STR);
						$stmt -> bindParam(':date', $date, PDO::PARAM_STR);
						$stmt -> bindParam(':pass', $pass, PDO::PARAM_STR);
						$stmt -> execute();
					}
				}

			//編集時の記入忘れの警告
			//名前の記入忘れ
			}else if(empty($name) && !empty($edit2) && !empty($pass)){
				echo "名前を入力してください。";

			//パスワードの記入忘れ
			}else if(!empty($name) && !empty($edit2) && empty($pass)){
				echo "新しく設定するパスワードを入力してください。";

			//名前、パスワード両方の記入忘れ
			}else if(empty($name) && !empty($edit2) && empty($pass)){
				echo "名前と新しく設定するパスワードを入力してください。";
			}
		}

		//編集ボタンをクリックされたとき
		if(isset($button2)){

			//編集申請番号とパスワードが入力されているとき
			if(!empty($edit) && !empty($e_pass)){
				$sql='SELECT * FROM 5_tb';
				$stmt = $pdo->query($sql);
				$results = $stmt->fetchAll();
				foreach($results as $row){

					//編集元の投稿番号とパスワードが一致した投稿をフォームに表示
					if($row['id']==$edit && $row['pass']==$e_pass){
						$editname = $row['name'];
						$editcomment = $row['comment'];
						$editnum = $row['id'];

					//指定した投稿のパスワードが一致しないときの警告
					}else if($row['id']==$edit && $row['pass']!=$e_pass){
						echo $miss;
					}
				}

			//編集申請時の記入忘れの警告
			//投稿番号の指定し忘れ
			}else if(empty($edit) && !empty($e_pass)){
				echo "投稿番号を指定してください。";

			//パスワードの記入忘れ
			}else if(!empty($edit) && empty($e_pass)){
				echo "パスワードを入力してください。";
			}
		}

		//削除ボタンをクリックされたとき
		if(isset($button3)){
			
			//削除番号とパスワードが入力されているとき
			if(!empty($delete) && !empty($d_pass)){
				$sql = 'SELECT * FROM 5_tb';
				$stmt = $pdo->query($sql);
				$results = $stmt->fetchAll();
				foreach($results as $row){

					//削除元の投稿番号とパスワードが一致したときに削除
					if($row['id']==$delete && $row['pass']==$d_pass){
						$sql = 'delete from 5_tb where id=:delete';
						$stmt = $pdo->prepare($sql);
						$stmt -> bindParam(':delete', $delete, PDO::PARAM_INT);
						$stmt -> execute();

					//指定した投稿のパスワードが一致しないときの警告
					}else if($row['id']==$delete && $row['pass']!=$d_pass){
						echo $miss;
					}
				}

			//削除時の記入忘れの警告
			//投稿番号の指定し忘れ
			}else if(empty($delete) && !empty($d_pass)){
				echo "投稿番号を指定してください。";

			//パスワードの記入忘れ
			}else if(!empty($delete) && empty($d_pass)){
				echo "パスワードを入力してください。";
			}
		}
	?>
	<body>
		<h1>News.ch</h1>
		<h2>あなたの最近気になるニュースは？</h2>
		<form action="mission_5-1.php" method="POST" enctype="">

			<!-名前入力フォーム-->
			<input type="text" name="name" size="16" placeholder="名前" value="<?php if(isset($button2)){ echo $editname; } ?>" /><br />

			<!-コメント入力フォーム-->
			<input type="text" name="comment" size="32"placeholder="コメント" value="<?php echo $editcomment; ?>" /><br />

			<!-パスワード入力フォーム-->
			<input type="password" name="pass" size="16" placeholder="パスワード" value="" /><br />

			<!-編集申請番号フォーム-->
			<input type="hidden" name="editnum" size="" value="<?php echo $editnum; ?>" />

			<!-送信ボタン-->
			<input type="submit" name="button1" value="送信" /><br />

			<br />

			<!-編集番号入力フォーム-->
			<input type="number" name="edit" sizie="3" placeholder="半角英数" value="" /><br />

			<!-パスワード入力フォーム-->
			<input type="password" name="e_pass" size="16" placeholder="パスワード" value="" /><br />

			<!-編集ボタン-->
			<input type="submit" name="button2" value="編集" /><br />

			<br />

			<!-削除番号入力フォーム-->
			<input type="number" name="delete" size="3" placeholder="半角英数" value="" /><br />

			<!-パスワード入力フォーム-->
			<input type="password" name="d_pass" size="16" placeholder="パスワード" value="" /><br />

			<!-削除ボタン-->
			<input type="submit" name="button3" value="削除" /><br />
		</form>
		<div id="tweet">
			<?php
				//投稿内容を表示
				$sql = 'SELECT * FROM 5_tb';
				$stmt = $pdo->query($sql);
				$results = $stmt->fetchAll();
				foreach($results as $row){
					echo $row['id'].' / ';
					echo $row['name'].' / ';
					echo $row['comment'].' / ';
					echo $row['date'].'<br>';
					echo "<hr>";
				}
			?>
		</div>
	</body>
</html>