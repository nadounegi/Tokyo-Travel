<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include('config.php');

// 画像アップロードを処理する共通関数（画像パスを返す）
function uploadImage($fileInputName) {
    if (isset($_FILES[$fileInputName]) && $_FILES[$fileInputName]['error'] === UPLOAD_ERR_OK) {
        $folder = date('ymd') . rand(1000, 9999); 
        if (!file_exists('uploads/' . $folder)) mkdir('uploads/' . $folder, 0777, true);
        $save_path = 'uploads/' . $folder . '/' . basename($_FILES[$fileInputName]['name']);
        if (move_uploaded_file($_FILES[$fileInputName]['tmp_name'], $save_path)) {
            return $save_path;
        }
    }
    return null;
}

// 区番号を取得、または作成する
function getAreaNo($area_name, $dbh){
    $stmt = $dbh->prepare("SELECT area_no FROM tt_in_areasyokai_tb WHERE name = :name AND delete_ku = '0'");
    $stmt->execute([':name' => $area_name]);
    if($row = $stmt->fetch(PDO::FETCH_ASSOC)){ return $row['area_no']; }

    $stmt2 = $dbh->prepare("SELECT area_no FROM tt_other_areasyokai_tb WHERE name = :name AND delete_ku = '0'");
    $stmt2->execute([':name' => $area_name]);
    if($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)){ return $row2['area_no']; }
    
    echo "<script>alert('該当する区（" . htmlspecialchars($area_name) . "）が登録されていません。先に区情報を追加してください。'); window.location.href = 'area_add.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $dbh = new PDO($dsn, $user, $password);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $txt_name = $_POST['txt_name'] ?? '';
        $txt_area_name = $_POST['txt_area_name'] ?? '';
        $txt_syoka = $_POST['txt_syoka'] ?? '';
        $txt_description = $_POST['txt_description'] ?? '';
        
        $category_select = $_POST['category_select'] ?? '';
        $new_category_name = $_POST['new_category_name'] ?? '';

        // 使用するカテゴリー名を決定
        $final_category_name = ($category_select === 'new') ? $new_category_name : $category_select;

        $err = "";
        if ($txt_name == "") $err .= "場所名 ";
        if ($final_category_name == "") $err .= "カテゴリー ";
        if ($txt_area_name == "") $err .= "区名 ";
        if ($txt_syoka == "") $err .= "紹介 ";

        if ($err == "") {
            $area_no = getAreaNo($txt_area_name, $dbh);
            
            $dbh->beginTransaction();

            // 1. 画像の処理（スポット画像とカテゴリー画像）
            $spot_image_url = uploadImage('up_file');
            $cat_image_url = uploadImage('cat_image'); // カテゴリー画像もアップロードされた場合

            // 2. カテゴリーの処理（新規作成の場合は画像も保存）
            $stmt = $dbh->prepare("SELECT category_no FROM tt_category_mst WHERE category_name = :c_name AND delete_ku = '0'");
            $stmt->execute([':c_name' => $final_category_name]);
            
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $category_no = $row['category_no'];
                // 新しいカテゴリー画像がアップロードされた場合は更新
                if ($cat_image_url) {
                    $up_cat = $dbh->prepare("UPDATE tt_category_mst SET image_url = :img WHERE category_no = :no");
                    $up_cat->execute([':img' => $cat_image_url, ':no' => $category_no]);
                }
            } else {
                // 新規カテゴリー作成
                $ins_cat = $dbh->prepare("INSERT INTO tt_category_mst (category_name, image_url, delete_ku, insert_at, update_at) VALUES (:c_name, :img, '0', now(), now())");
                $ins_cat->execute([':c_name' => $final_category_name, ':img' => $cat_image_url]);
                $category_no = $dbh->lastInsertId();
            }

            // 3. スポット情報の挿入
            $sql = "INSERT INTO tt_spot_tb 
                    (category_no, area_no, name, syoka, description, image_url, delete_ku, insert_at, update_at) 
                    VALUES 
                    (:category_no, :area_no, :name, :syoka, :description, :image_url, '0', now(), now())";
            
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':category_no', $category_no, PDO::PARAM_INT);
            $stmt->bindValue(':area_no', $area_no, PDO::PARAM_INT);
            $stmt->bindValue(':name', $txt_name, PDO::PARAM_STR);
            $stmt->bindValue(':syoka', mb_substr($txt_syoka, 0, 100), PDO::PARAM_STR); 
            $stmt->bindValue(':description', $txt_description, PDO::PARAM_STR);
            $stmt->bindValue(':image_url', $spot_image_url, $spot_image_url === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->execute();
            $spotid = $dbh->lastInsertId();

            // 4. ファイル情報を付属テーブルに記録
            if ($spotid && $spot_image_url) {
                $path_parts = pathinfo($spot_image_url);
                $folder_name = basename($path_parts['dirname']);
                $file_name = $path_parts['basename'];

                $sql2 = "INSERT INTO tt_spots_fileupload_tb (spots_no, folder_name, file_name, delete_ku, insert_at, update_at) 
                         VALUES (:spots_no, :folder_name, :file_name, '0', now(), now())";
                $stmt2 = $dbh->prepare($sql2);
                $stmt2->bindValue(':spots_no', $spotid, PDO::PARAM_INT);
                $stmt2->bindValue(':folder_name', $folder_name, PDO::PARAM_STR);
                $stmt2->bindValue(':file_name', $file_name, PDO::PARAM_STR);
                $stmt2->execute();
            }

            $dbh->commit();
            header('Location: test_spot_load.php?category_no=' . urlencode($category_no));
            exit;
        } else {
            echo "<div style='color:red; text-align:center;'>" . $err . "を入力してください。</div>";
        }
    } catch (Exception $e) {
        if (isset($dbh) && $dbh->inTransaction()) { $dbh->rollBack(); }
        echo "データの書き込みに失敗しました: " . $e->getMessage();
    }
}

// データベースから既存のカテゴリーを読み込む
try {
    $dbh = new PDO($dsn, $user, $password);
    $existing_categories = $dbh->query("SELECT category_name FROM tt_category_mst WHERE delete_ku = '0'")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $existing_categories = [];
}
?>      


<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>スポット情報登録</title>
  <link rel="stylesheet" href="https://unpkg.com/ress/dist/ress.min.css" />
  <link rel="stylesheet" href="css/add.css" />
</head>
<body>
  <!-- 头部代码 (保持不变) -->

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
      <h1 id="add">スポット情報登録</h1>
  </div>
  
  <form action="jyoho_add.php" method="POST" enctype="multipart/form-data">
      <div class="nyuryoku">
          <h3>【スポット・アニメ】の情報</h3>
          
          <label>区名 (Area Name)・地域名</label>
          <input type="text" name="txt_area_name" required />

          <label>場所名・アニメ名 (Spot Name)</label>
          <input type="text" name="txt_name" required maxlength="20" />

          <!-- 👇 分类合并逻辑：下拉菜单 + 新分类输入框 -->
          <label>カテゴリー (Category)</label>
          <select name="category_select" id="category_select" required style="display:block; width:100%; margin-bottom:15px; padding:10px; box-sizing:border-box;">
              <option value="">-- 選択してください --</option>
              <?php foreach ($existing_categories as $cat): ?>
                  <option value="<?php echo htmlspecialchars($cat['category_name'], ENT_QUOTES, 'UTF-8'); ?>">
                      <?php echo htmlspecialchars($cat['category_name'], ENT_QUOTES, 'UTF-8'); ?>
                  </option>
              <?php endforeach; ?>
              <option value="new" style="color: blue; font-weight: bold;">+ 新しいカテゴリーを作成する</option>
          </select>

          <!-- 默认隐藏，选中 new 时才显示 -->
          <div id="new_category_box" style="display: none; background: #f9f9f9; padding: 15px; border-radius: 5px; margin-bottom: 15px; border: 1px dashed #ccc;">
              <label style="margin-top: 0;">新しいカテゴリー名</label>
              <input type="text" name="new_category_name" id="new_category_name" style="margin-bottom: 10px;" />
              
              <label>カテゴリー画像 (Category Image)</label>
              <input type="file" name="cat_image" id="cat_image" accept="image/*" />
          </div>

          <label>紹介 (Introduction - 100字以内)</label>
          <textarea name="txt_syoka" class="short-text"></textarea>

          <label>説明 (Description)</label>
          <textarea name="txt_description" class="long-text"></textarea>

          <label>画像 (Image)</label>
          <input type="file" name="up_file" accept="image/*" />

          <input type="submit" value="添加" />
      </div>
  </form>

  <!-- 简单的 JS：当选中“新建分类”时，显示对应的输入框 -->
  <script>
    document.getElementById('category_select').addEventListener('change', function() {
      var newBox = document.getElementById('new_category_box');
      var newInput = document.getElementById('new_category_name');
      if (this.value === 'new') {
        newBox.style.display = 'block';
        newInput.required = true;
      } else {
        newBox.style.display = 'none';
        newInput.required = false;
      }
    });
  </script>
</body>
</html>