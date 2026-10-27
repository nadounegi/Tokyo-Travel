<?php
include("config.php");

$area_name = isset($_GET['name']) ? urldecode($_GET['name']) : null;

try {
  $dbh = new PDO($dsn, $user, $password);
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  if ($area_name !== null) {
    $sql = "SELECT"
      . " toat.id"
      . ",toat.name"
      . ",toat.image_url"
      . ",toat.description"
      . ",toat.insert_at"
      . " FROM tt_other_areasyokai_tb toat"
      . " WHERE toat.name = :name";  // Added space and parameter placeholder
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':name', $area_name, PDO::PARAM_STR);
    $stmt->execute();
    $out_area = $stmt->fetchAll(PDO::FETCH_ASSOC);
  } else {
    echo "選択されたカテゴリータイプがありません";
    exit;
  }
} catch (PDOException $e) {
  echo $e->getMessage();
  die();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://unpkg.com/ress/dist/ress.min.css" />
  <link rel="stylesheet" href="css/area_syokai.css">
  <title>Document</title>
</head>

<body>
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
  <h1 id="title">東京都23区以外</h1>
  <?php foreach ($out_area as $row): ?>
    <div id="regionInfo">
      <h2 id="regionName">
        <?php echo $row['name']; ?>
      </h2>
      <img id="regionImage" src="<?php echo $row['image_url']; ?>" alt="区域图片">
      <p id="regionDescription">
        <?php echo $row['description']; ?>
      </p>
    </div>
  <?php endforeach; ?>
  <p id="tokyo23">
    <a href="out_map.php">東京都23区以外</a>
  </p>

  <p id="homepage">
    <a href="index.html">ホームページに戻る</a>
  </p>
  <footer>
        <p><a href="index.html"><img src="img/logo.png" alt="東京 Travel" /></a></p>
        <a href="jyoho_add.php" id="jyoho_add">場所管理</a>
        <p><a href="Otoiawase.html">お問い合わせ</a></p>
    </footer>


</body>
<div class="gtranslate_wrapper"></div>
<script>window.gtranslateSettings = {"default_language":"ja","wrapper_selector":".gtranslate_wrapper","float_switcher_open_direction":"bottom","flag_style":"3d"}</script>
<script src="https://cdn.gtranslate.net/widgets/latest/float.js" defer></script>
</html>