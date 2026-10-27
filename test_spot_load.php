 <?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

?>

<?php
include("config.php");

$category_no = isset($_GET['category_no']) ? (int)$_GET['category_no'] : 0;

try{
    $dbh = new PDO($dsn, $user, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
    $dbh ->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    
      $sql = "SELECT"
        . " ttst.id"    
        . ", ttst.name"
        . ", ttst.syoka"
        . ", ttst.description"
        . ", ttst.insert_at"
        . ", ttsft.folder_name"
        . ", ttsft.file_name"
        . ", ttcm.category_name"
        . " FROM tt_spot_tb ttst"
        . " LEFT JOIN tt_spots_fileupload_tb ttsft ON ttst.id = ttsft.spots_no"
        . " LEFT JOIN tt_category_mst ttcm ON ttst.category_no = ttcm.category_no"
        . " WHERE ttcm.delete_ku = '0' AND ttcm.category_no = :category_no";

    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':category_no', $category_no, PDO::PARAM_INT);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);


    if (!$results) {
      exit("選択されたカテゴリーの場所がありません。");
  }
} catch (PDOException $e) {
  exit("データベースのエラー: " . $e->getMessage());
}

// foreach ($result as $row) {
//   $load_category_no = htmlspecialchars($row['category_no'], ENT_QUOTES, 'UTF-8');
//   $load_category_name = htmlspecialchars($row['category_name'], ENT_QUOTES, 'UTF-8');
//   $load_name = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
//   $load_syoka = htmlspecialchars($row['syoka'], ENT_QUOTES, 'UTF-8');
//   $load_description = htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8');
//   $load_folder_name = htmlspecialchars($row['folder_name'], ENT_QUOTES, 'UTF-8');
//   $load_file_name = htmlspecialchars($row['file_name'], ENT_QUOTES, 'UTF-8');
// }

?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>場所のおすすめ</title>
  <link rel="stylesheet" href="https://unpkg.com/ress/dist/ress.min.css" />
  <link rel="stylesheet" href="css/spot_load.css" />
</head>

<body>
<div class="gtranslate_wrapper"></div>
  <script>window.gtranslateSettings = { "default_language": "ja", "native_language_names": true, "wrapper_selector": ".gtranslate_wrapper", "flag_style": "3d" }</script>
  <script src="https://cdn.gtranslate.net/widgets/latest/float.js" defer></script>

  <header>
    <h1>
      <p><a href="top.php"><img src="img/logo.png" alt="東京 Travel" /></a></p>
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
    <?php if ($category_no !== null && !empty($results)): ?>
      <?php
    $load_id = "";
    $load_category_no = ""; 
    $load_category_name = "";
    $load_name = "";
    $load_syoka = "";
    $load_description = "";
    $load_folder_name = "";
    $load_file_name = "";
      ?>
      <?php if ($category_no !== null && !empty($results)):?>
      <?php
      $load_category_name = htmlspecialchars($results[0]['category_name'], ENT_QUOTES, 'UTF-8');
      ?>
      <h1>
        <?php
        // 默认的类别名称，以防结果中没有类别名称
        echo $load_category_name ? $load_category_name : ''; ?>
        のおすすめ
      </h1>
      <div class="list">
        <ul class="Osusume_list">
        <?php foreach ($results as $row): ?>
          <?php
               $load_id = htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'); 
               $load_name = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
               $load_syoka = htmlspecialchars($row['syoka'], ENT_QUOTES, 'UTF-8');
               $load_description = htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8');
               $load_folder_name = htmlspecialchars($row['folder_name'], ENT_QUOTES, 'UTF-8'); 
               $load_file_name = htmlspecialchars($row['file_name'], ENT_QUOTES, 'UTF-8');
          ?>

          <li>
          <a href="info.php?id=<?php echo $row['id']; ?>">
              <?php
              $imgFilePath = "uploads/" . $load_folder_name . "/" . $load_file_name;
              if (isset($load_file_name) && file_exists($imgFilePath)) {
                echo "<img src='" . $imgFilePath . "' width='100%' height='200' object-fit: cover>";
              }
              ?>
            <p class="spot_name">
              <?php echo $load_name; ?>
            </p>
          </a>
            <p>
              <?php echo nl2br($load_syoka); ?>
            </p>
          </li>
        <?php endforeach; ?>
  </ul>
</div>
<?php endif; ?>

        <?php if (!empty($results)): ?>
    <div class="Setumei_kakoi">
        <p>
        <?php echo htmlspecialchars($results[0]['description'], ENT_QUOTES, 'UTF-8'); ?>
        </p>
    </div>
<?php endif; ?>
<?php endif; ?>


    <div class="Yoyaku_haiti">
      <a href="test_list.php"><input type="image" src="img/Osusume_retuen_button.png" height="85" width="250" /></a>
      <input type="image" src="img/ホームページ　ボタン2.png" height="85" width="250" />
    </div>

  </main>

  <footer>
    <p><a href="top.php"><img src="img/logo.png" alt="東京 Travel" /></a></p>
    <p>お問い合わせ</p>
    <p><a href="jyoho_add.php">場所管理</a></p>

  </footer>
</body>
<div class="gtranslate_wrapper"></div>
<script>window.gtranslateSettings = {"default_language":"ja","wrapper_selector":".gtranslate_wrapper","float_switcher_open_direction":"bottom","flag_style":"3d"}</script>
<script src="https://cdn.gtranslate.net/widgets/latest/float.js" defer></script>