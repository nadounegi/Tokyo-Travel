<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $dbh = new PDO($dsn, $user, $password);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $area_name = $_POST['area_name'] ?? '';
        $description = $_POST['description'] ?? '';

        if ($area_name == "") {
            throw new Exception("区名は必須です。");
        }
        $last_char = mb_substr($area_name, -1, 1, 'UTF-8'); 
        
        if ($last_char == '区') {
            $table_name = 'tt_in_areasyokai_tb'; // 東京23区テーブル
            $type_name = '区';
        } else {
            $table_name = 'tt_other_areasyokai_tb'; // その他の地域テーブル
            $type_name = '地域';
        }

        $dbh->beginTransaction();

        // 1. 重複チェック（判定したテーブル名でクエリを実行）
        $check_stmt = $dbh->prepare("SELECT id FROM {$table_name} WHERE name = :name AND delete_ku = '0'");
        $check_stmt->execute([':name' => $area_name]);
        if ($check_stmt->fetch()) {
            throw new Exception("該当する{$type_name}（" . htmlspecialchars($area_name) . "）は既に登録されています！");
        }
        // 画像アップロード処理
        $image_url = null;
        if (isset($_FILES['up_file']) && $_FILES['up_file']['error'] === UPLOAD_ERR_OK) {
            $folder = date('ymd') . rand(1000, 9999); 
            if (!file_exists('uploads/' . $folder)) mkdir('uploads/' . $folder, 0777, true);
            $save_path = 'uploads/' . $folder . '/' . basename($_FILES['up_file']['name']);
            if (move_uploaded_file($_FILES['up_file']['tmp_name'], $save_path)) {
                $image_url = $save_path;
            }
        }

        // テーブルに直接挿入
        // 注意：area_no は NOT NULL のため、一旦 0 で仮挿入する
        $sql = "INSERT INTO {$table_name}  (area_no, name, description, image_url, delete_ku, insert_at, update_at) 
                VALUES (0, :name, :desc, :img, '0', now(), now())";
        
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':name', $area_name, PDO::PARAM_STR); 
        $stmt->bindValue(':desc', $description, PDO::PARAM_STR);
        $stmt->bindValue(':img', $image_url, $image_url === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        
        $stmt->execute();
        // 採番された番号を取得し、area_no を更新する
        $new_id = $dbh->lastInsertId();
        if ($table_name == 'tt_other_areasyokai_tb') {
            $area_no = $new_id + 100000; 
        } else {
            $area_no = $new_id;
        }

        $update_sql = "UPDATE {$table_name} SET area_no = :area_no WHERE id = :id";
        $update_stmt = $dbh->prepare($update_sql);
        $update_stmt->execute([':area_no' => $area_no, ':id' => $new_id]);
        $dbh->commit();

        $message = "<div style='color:green; text-align:center; margin-top:20px;'>{$type_name}の情報を登録しました！</div>";

    } catch (Exception $e) {
        $message = "<div style='color:red; text-align:center; margin-top:20px;'>エラー: " . $e->getMessage() . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>区の情報登録</title>
  <link rel="stylesheet" href="https://unpkg.com/ress/dist/ress.min.css" />
  <link rel="stylesheet" href="css/add.css" />
</head>
<body>
<div class="gtranslate_wrapper"></div>
  <script>window.gtranslateSettings = { "default_language": "ja", "native_language_names": true, "wrapper_selector": ".gtranslate_wrapper", "flag_style": "3d" }</script>
  <script src="https://cdn.gtranslate.net/widgets/latest/float.js" defer></script>
  
 
<header>
        <h1>
            <a href="index.html"><img src="img/logo.png" alt="東京 Travel" /></a>
        </h1>
        <div class="nav-links">
            <a href="loginout.php">ログアウト</a>
            <a href="top.php">検索</a>
            <a href="list.php">おすすめ</a>
            <a href="Yoyaku_servise.html">予約サービス</a>
        </div>
    </header>
  <div class="anniu">
      <h1>区/地域の情報登録</h1>
  </div>
 <?php if(isset($message)) echo $message; ?>

  <form method="POST" enctype="multipart/form-data">
      <div class="nyuryoku">
          <h3>【区・地域】の情報</h3>
          
          <label>区名 / 地域名</label>
          <input type="text" name="area_name" required placeholder="例：新宿区、横浜市" />

          <label>区介绍 / 地域介绍</label>
          <textarea name="description" class="long-text" placeholder="区の説明を入力してください..."></textarea>
          
          <label>区画像 / 地域画像</label>
          <input type="file" name="up_file" accept="image/*" />
          
          <input type="submit" value="区を登録する" />
      </div>
  </form>

</body>
</html>