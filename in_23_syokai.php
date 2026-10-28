<?php
include("config.php");

$area_name = isset($_GET['name']) ? urldecode($_GET['name']) : null;

try {
  $dbh = new PDO($dsn, $user, $password);
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  if ($area_name !== null) {
    $sql = "SELECT"
      . " tiat.id"
      . ",tiat.name"
      . ",tiat.image_url"
      . ",tiat.description"
      . ",tiat.insert_at"
      . " FROM tt_in_areasyokai_tb tiat"
      . " WHERE tiat.name = :name";  // Added space and parameter placeholder
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':name', $area_name, PDO::PARAM_STR);
    $stmt->execute();
    $area = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
  <title>Document</title>
  <link rel="stylesheet" href="https://unpkg.com/ress/dist/ress.min.css" />
  <link rel="stylesheet" href="css/area_syokai.css">
</head>

<body>
  <header>
    <h1>
      <p><a href="top.php "><img src="img/logo.png" alt="東京 Travel" /></a></p>
    </h1>


    <ul class="navlist">
      <p class="navItem"><a href="user_login.php">ログイン</a></p>
      <p class="navItem"><a href="top.html">検索</a></p>
      <p class="navItem"><a href="list.php">おすすめ</a></p>
      <p class="navItem"><a href="Yoyaku_servise.html ">予約サービス</a></p>
    </ul>


  </header>
  <h1 id="area23">東京都23区</h1>
  <?php foreach ($area as $row): ?>
    <div id="regionInfo">
      <h2 id="regionName">
        <?php echo $row['name']; ?>
      </h2>
      <img id="regionImage" src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="区域图片">
      <p id="regionDescription">
        <?php echo $row['description']; ?>
      </p>
    </div>
  <?php endforeach; ?>
  <p id="tokyo23">
    <a href="in_23_map.php">東京都23区</a>
  </p>

  <p id="homepage">
    <a href="top.html">ホームページに戻る</a>
  </p>
 <footer>
    <p><a href="top.html"><img src="img/logo.png" alt="東京 Travel" /></a></p>
    <a href="jyoho_add.php" id="add">場所管理</a>
    <a href="area_add.php">区管理</a>
  </footer>

</body>
<div class="gtranslate_wrapper"></div>
<script>window.gtranslateSettings = {"default_language":"ja","wrapper_selector":".gtranslate_wrapper","float_switcher_open_direction":"bottom","flag_style":"3d"}</script>
<script src="https://cdn.gtranslate.net/widgets/latest/float.js" defer></script>
</html>