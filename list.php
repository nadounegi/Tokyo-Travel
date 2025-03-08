<?php
// 包含数据库配置
include("config.php");

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.php");
    exit();
}

try {
  $dbh = new PDO($dsn, $user, $password);
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // 用于获取数据的函数
  function getCategoryName($dbh, $categoryNo) {
    $sql = "SELECT ttcm.category_name 
            FROM tt_category_mst ttcm
            WHERE ttcm.category_no = :categoryNo";

    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':categoryNo', $categoryNo, PDO::PARAM_INT);
    $stmt->execute();
    $row= $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? $row['category_name'] : null;
}

function getDataByCategoryNo($dbh,$categoryNo){
  $categoryName = getCategoryName($dbh,$categoryNo);
  if($categoryName){
    $sql = "SELECT ttst.id,ttst.name,ttst.insert_at
            FROM tt_spot_tb ttst
            WHERE ttst.category_no = :categoryNo";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':categoryNo',$categoryNo,PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);        
  }else{
    return [];
  }
}

  //類型名を取得 
  $basyoName = getCategoryName($dbh, 1);
  $animeName = getCategoryName($dbh, 2);
  $gurumeName = getCategoryName($dbh, 3);
  //類型ごとのデータを取得
  $basyodata = getDataByCategoryNo($dbh,1);
  $animedata = getDataByCategoryNo($dbh,2);
  $gurumedata = getDataByCategoryNo($dbh,3);

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
      <p><a href="index.html"><img src="img/logo.png" alt="東京 Travel" /></a></p>
    </h1>

    <p>
    <p>
    <p><a href="loginout.php">ログアウト</a></p>
    <p><a href="top.php">検索</a></p>
    <p><a href="list.php">おすすめ</a></p>
    <p><a href="Yoyaku_servise.html">予約サービス</a></p>
    </p>
    </p>

  </header>

  <main>
    <p class="yoyaku">
      おすすめ
    </p>

    <div class="koumoku">

      <div class="basyo">
        <p class="komi"><a href="test_spot_load.php?category_no=1">場所</a></p>
        <ul>
          <li>
            <?php
            $counter = 1;
            foreach ($basyodata as $row) {
              echo $counter . '.' . $row['name'] . '<br>';
              $counter++;
            }
            ?>
          </li>

        </ul>
        <img src="img/p1.png" alt="">

        <p><a href="test_spot_load.php?category_no=1">おすすめの<?php echo $basyoName ?>を<br>もっと見る</a></p>
      </div>

      <div class="anime">
        <p class="komi"><a href="test_spot_load.php?category_no=2">アニメ</a></p>
        <ul>
          <li>
            <?php
            $counter = 1;
            foreach ($animedata as $row) {
              echo $counter . '.' . $row['name'] . '<br>';
              $counter++;
            }
            ?>
          </li>

        </ul>
        <img src="img/p2.png" alt="">

        <p><a href="test_spot_load.php?category_no=2">おすすめの<?php echo $animeName ?>を<br>もっと見る</a></p>
      </div>

      <div class="insyoku">
        <p class="komi"><a href="test_spot_load.php?category_no=3">飲食</a></p>
        <ul>
          <li>
            <?php
            $counter = 1;
            foreach ($gurumedata as $row) {
              echo $counter . '.' . $row['name'] . '<br>';
              $counter++;
            }
            ?>
          </li>
        </ul>
        <img src="img/p3.png" alt="">
        <p><a href="test_spot_load.php?category_no=3">おすすめの<?php echo $gurumeName ?>を<br>もっと見る</a></p>
      </div>

    </div>
    <p class="step_area">
      <a href="index.html">
        <img src="img/ホームページ　ボタン2.png" height="85" width="250" id="home_img" />
      </a>
    </p>

  </main>

  <footer>
    <p><a href="top.html"><img src="img/logo.png" alt="東京 Travel" /></a></p>
    <a href="jyoho_add.php" id="jyoho_add">場所管理</a>
    <p><a href="Otoiawase.html">お問い合わせ</a></p>
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