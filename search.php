<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>

<?php
//データベース接続情報を格納
include('config.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $searchTerm = $_POST['keywords'];
  $keywords = explode(' ', $searchTerm);

  $sql = "SELECT"
    . " ttst.id"
    . ",ttst.name"
    . ",ttst.syoka"
    . ",ttst.insert_at"
    . ",ttsft.folder_name"
    . ",ttsft.file_name"
    . ",ttam.area_name"
    . ",ttcm.category_name"
    . " FROM tt_test_spot_tb ttst"
    . " LEFT JOIN tt_test_spots_fileupload_tb ttsft ON ttst.id = ttsft.spots_no" // Assuming spots_no is a reference to tt_test_spot_tb.id
    . " LEFT JOIN tt_test_area_mst ttam ON ttst.area_no = ttam.area_no"
    . " LEFT JOIN tt_test_cate_mst ttcm ON ttst.category_no = ttcm.category_no"
    . " WHERE ttsft.delete_ku = '0'"
    . " AND ttam.delete_ku = '0'"
    . " AND ttcm.delete_ku = '0'"
    . " AND ttst.delete_ku = '0'";


  $params = [];
  foreach ($keywords as $word) {
    $sql .= " AND (ttst.name LIKE ? OR ttst.syoka LIKE ? OR ttam.area_name LIKE ? OR ttcm.category_name LIKE ?)";
    $params[] = '%' . $word . '%';
    $params[] = '%' . $word . '%';
    $params[] = '%' . $word . '%';
    $params[] = '%' . $word . '%';
  }

  try {
    $dbh = new PDO($dsn, $user, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $dbh->prepare($sql);
    $stmt->execute($params);

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    echo $e->getMessage();
    die();
  }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>検索結果</title>
  <link rel="stylesheet" href="https://unpkg.com/ress/dist/ress.min.css" />
  <link rel="stylesheet" href="css/search.css" />
</head>

<body>
<div class="gtranslate_wrapper"></div>
  <script>window.gtranslateSettings = { "default_language": "ja", "native_language_names": true, "wrapper_selector": ".gtranslate_wrapper", "flag_style": "3d" }</script>
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
    <!-- ビデオゲーム -->
    <h1>検索結果</h1>
    <div class="list">
      <ul class="Osusume_list">
          <?php if (!empty($results)): ?>
            <?php foreach ($results as $row): ?>
          <li><a href="info.php?id=<?php echo $row['id']; ?>">
              <?php
              $imgFilePath = "uploads/" . $row['folder_name'] . "/" . $row['file_name'];
              if (isset($row['file_name']) && file_exists($imgFilePath)) {
                echo "<img src='" . $imgFilePath . "' width='100%' height='200' object-fit: cover>";
              }
              ?>
            </a>
            <p class="spot_name">
              <?php echo $row['name']; ?>
            </p>
            <p>
              <?php echo $row['syoka']; ?>
            </p>
          </li>
          <?php endforeach; ?>
    
        <?php endif; ?>
        </ul>
        </div>

    <div class="Yoyaku_haiti">
      <a href="list.php"><input type="image" src="img/Osusume_retuen_button.png" height="85" width="250" /></a>
      <a href="index.html"><input type="image" src="img/ホームページ　ボタン2.png" height="85" width="250" /></a>
    </div>
    </div>

  </main>

  <footer>
    <p><a href="top.php"><img src="img/logo.png" alt="東京 Travel" /></a></p>
    <p><a href="Otoiawase.html"></a>お問い合わせ</p>
    <p><a href="jyoho_add.php">場所管理</a></p>

  </footer>

</body>
<div class="gtranslate_wrapper"></div>
<script>window.gtranslateSettings = {"default_language":"ja","wrapper_selector":".gtranslate_wrapper","float_switcher_open_direction":"bottom","flag_style":"3d"}</script>
<script src="https://cdn.gtranslate.net/widgets/latest/float.js" defer></script>
</html>