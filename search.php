<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>

<?php
// データベース接続情報を格納
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $searchTerm = $_POST['keywords'];
  $keywords = explode(' ', $searchTerm);

  $sql = "SELECT"
    . " ttst.id"
    . ",ttst.name as spot_name"
    . ",ttst.syoka"
    . ",ttst.insert_at"
    . ",ttsft.folder_name"
    . ",ttsft.file_name"
    . ",ttcm.category_name"
    . ",COALESCE(ttam1.name, ttam2.name) as area_name" // 👈 2つの区テーブルから名前を取得
    . " FROM tt_spot_tb ttst"
    . " LEFT JOIN tt_spots_fileupload_tb ttsft ON ttst.id = ttsft.spots_no" 
    . " LEFT JOIN tt_in_areasyokai_tb ttam1 ON ttst.area_no = ttam1.area_no" // 23区を結合
    . " LEFT JOIN tt_other_areasyokai_tb ttam2 ON ttst.area_no = ttam2.area_no" // 23区以外を結合
    . " LEFT JOIN tt_category_mst ttcm ON ttst.category_no = ttcm.category_no"
    . " WHERE ttsft.delete_ku = '0'"
    . " AND ttcm.delete_ku = '0'"
    . " AND ttst.delete_ku = '0'";

  $params = [];
  foreach ($keywords as $word) {
    $sql .= " AND (ttst.name LIKE ? OR ttst.syoka LIKE ? OR COALESCE(ttam1.name, ttam2.name) LIKE ? OR ttcm.category_name LIKE ?)"; // 👈 検索条件にも2つの区テーブルを追加
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
    <!-- ビデオゲーム -->
    <h1>検索結果</h1>
    <div class="list">
      <ul class="Osusume_list">
          <?php if (!empty($results)): ?>
            <?php foreach ($results as $row): ?>
          <li>
              <?php
              $imgFilePath = "uploads/" . $row['folder_name'] . "/" . $row['file_name'];
              if (isset($row['file_name']) && file_exists($imgFilePath)) {
                echo "<img src='" . $imgFilePath . "' width='100%' height='200' object-fit: cover>";
              }
              ?>
            <p class="area_name">
              <?php echo $row['area_name']; ?>
            </p>
            <p class="spot_name">
              <?php echo $row['spot_name']; ?>
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
        <a href="list.php" class="btn">おすすめに戻る</a>
        <a href="index.html" class="btn">ホームページに戻る</a>
    </div>
    </div>

  </main>

  
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