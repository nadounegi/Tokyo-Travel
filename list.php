<?php
include("config.php");
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.php");
    exit();
}

try {
  $dbh = new PDO($dsn, $user, $password);
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // 1. 削除されていないすべてのカテゴリーを一度に取得
  $sql_categories = "SELECT category_no, category_name,image_url FROM tt_category_mst WHERE delete_ku = '0' ORDER BY category_no ASC";
  $stmt_categories = $dbh->query($sql_categories);
  $categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
  echo $e->getMessage();
  die();
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>トップページ</title>
  <link rel="stylesheet" href="https://unpkg.com/ress/dist/ress.min.css" />
  <link rel="stylesheet" href="css/list.css" />
</head>

<body>
  <div class="gtranslate_wrapper"></div>
  <script>
    window.gtranslateSettings = {
      "default_language": "ja",
      "native_language_names": true,
      "wrapper_selector": ".gtranslate_wrapper",
      "flag_style": "3d"
    }
  </script>
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
  <main>
    <p class="yoyaku">おすすめ</p>

    <div class="koumoku">
      <?php 
      // 👇 核心逻辑：循环遍历每一个分类
      foreach ($categories as $cat): 
          $cat_no = $cat['category_no'];
          $cat_name = htmlspecialchars($cat['category_name'], ENT_QUOTES, 'UTF-8');

          // 为了不在循环里查数据库（影响性能），我们直接在这个页面一次性查出来
          $stmt = $dbh->prepare("SELECT name FROM tt_spot_tb WHERE category_no = :cat_no AND delete_ku = '0' ORDER BY id ASC LIMIT 5");
          $stmt->execute([':cat_no' => $cat_no]);
          $spots = $stmt->fetchAll(PDO::FETCH_ASSOC);
      ?>
      
      <!-- 动态生成每一个卡片的 HTML -->
      <div class="card">
        <p class="komi"><a href="test_spot_load.php?category_no=<?php echo $cat_no; ?>"><?php echo $cat_name; ?></a></p>
        <ul>
          <li>
            <?php
            $counter = 1;
            if (!empty($spots)) {
                foreach ($spots as $row) {
                    echo $counter . '.' . htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') . '<br>';
                    $counter++;
                }
            } else {
                echo "まだ登録されていません。";
            }
            ?>
          </li>
        </ul>
        <?php
     // 检查这个分类有没有上传图片
      $cat_img = $cat['image_url'] ?? '';
   if (!empty($cat_img) && file_exists($cat_img)) {
      // 如果数据库有图片，就显示数据库里的图片
      echo '<img src="' . htmlspecialchars($cat_img, ENT_QUOTES, 'UTF-8') . '" alt="" class="card-img">';
    } else {
      // 如果没上传图片，就显示一张默认的占位图
      echo '<img src="img/default.png" alt="デフォルト画像" class="card-img">';
  }
?>
        <p><a href="test_spot_load.php?category_no=<?php echo $cat_no; ?>">おすすめの<?php echo $cat_name; ?>を<br>もっと見る</a></p>
      </div>

      <?php endforeach; ?>
    </div>
    
    <p class="step_area">
      <a href="index.html"><img src="img/ホームページ　ボタン2.png" height="85" width="250" id="home_img" /></a>
    </p>
  </main>

 <footer>
    <p><a href="top.html"><img src="img/logo.png" alt="東京 Travel" /></a></p>
    <a href="jyoho_add.php" id="add">場所管理</a>
    <a href="area_add.php">区管理</a>
  </footer>
</body>


</body>
<div class="gtranslate_wrapper"></div>
<script>
  window.gtranslateSettings = {
    "default_language": "ja",
    "wrapper_selector": ".gtranslate_wrapper",
    "float_switcher_open_direction": "bottom",
    "flag_style": "3d"
  }
</script>
<script src="https://cdn.gtranslate.net/widgets/latest/float.js" defer></script>

</html>