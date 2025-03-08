<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>

<?php
include("config.php");


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = $_GET['id'] ?? 0; // 使用空合并操作符来避免未定义索引的问题
}


try{
    $dbh = new PDO($dsn, $user, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "select"
        ." ttst.id"
        .",ttst.name"
        .",ttst.syoka"
        .",ttst.map_url"
        . ", ttsft.folder_name"
        . ", ttsft.file_name"
        ." from tt_test_spot_tb ttst"
        . " LEFT JOIN tt_test_spots_fileupload_tb ttsft ON ttst.id = ttsft.spots_no"
        ." where ttst.delete_ku = '0' and ttst.id = :id";
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':id',$id,PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch();
}catch (PDOException $e){
    echo($e->getMessage());
    die();
}

/* データベースからの値を受け取る変数準備 */
$load_name = "";
$load_syoka = "";
$load_map_url = "";
$load_folder_name = "";
$load_file_name = "";

/* データベースからの値の取得 */
$load_name = htmlspecialchars($result['name'],ENT_QUOTES,"UTF-8");
$load_syoka = htmlspecialchars($result['syoka'],ENT_QUOTES,"UTF-8");
$load_map_url = htmlspecialchars($result['map_url'],ENT_QUOTES,"UTF-8");
$load_folder_name = htmlspecialchars($result['folder_name'], ENT_QUOTES, "UTF-8");
$load_file_name = htmlspecialchars($result['file_name'], ENT_QUOTES, "UTF-8");
$iframeSrc = filter_var($load_map_url, FILTER_VALIDATE_URL) ? $load_map_url : 'about:blank'; // 确保地图URL有效

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>スポット情報</title>
    <link rel="stylesheet" href="https://unpkg.com/ress/dist/ress.min.css" />
    <link rel="stylesheet" href="css/info.css">
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
  <main>
    <div class="content-container">
        <div class="name">
            <h2><?php echo $load_name; ?></h2>
        </div>
        <div class="image">
            <?php
            $imgFilePath = "uploads/" . $load_folder_name . "/" . $load_file_name;
            if (isset($load_file_name) && file_exists($imgFilePath)) {
                echo "<img src='" . $imgFilePath . "' alt='Image' width='500'>";
            }
            ?>
        </div>
        <?php if (!empty($load_map_url)) : ?>
            <div class="map">
                <iframe src="<?php echo $load_map_url; ?>" width="750" height="400" frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
            </div>
        <?php endif; ?>
        <div class="syokai">
            <p><?php echo $load_syoka; ?></p>
        </div>
    </div>
</main>

    
    <div class="Yoyaku_haiti">
      <a href="list.php"><img src="img/Osusume_retuen_button.png" alt="" height="85" width="250" ></a>
     <a href="index.html"><img src="img/ホームページ　ボタン2.png" alt="" height="85" width="250" id="home"></a>
    </div>
    </div>

  </main>

  <footer>
    <p><a href="index.html"><img src="img/logo.png" alt="東京 Travel" /></a></p>
    <p>お問い合わせ</p>
    <p><a href="jyoho_add.php">場所管理</a></p>

  </footer>
</body>
<div class="gtranslate_wrapper"></div>
<script>window.gtranslateSettings = {"default_language":"ja","wrapper_selector":".gtranslate_wrapper","float_switcher_open_direction":"bottom","flag_style":"3d"}</script>
<script src="https://cdn.gtranslate.net/widgets/latest/float.js" defer></script>
</body>
</html>
