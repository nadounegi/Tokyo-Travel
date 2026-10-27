<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

?>

<?php
include('config.php');
// カテゴリー名からカテゴリー番号を取得する
function getCateNo($category_name, $dbh){
    // 查询是否已有该类别
    $stmt = $dbh->prepare("SELECT category_no FROM tt_category_mst WHERE category_name = :category_name");
    $stmt->execute([':category_name' => $category_name]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        // 如果找到了，返回类别编号
        return $row['category_no'];
    } else {
        // 如果没有找到，插入新类别
        $insertStmt = $dbh->prepare("INSERT INTO tt_category_mst (category_name, delete_ku, insert_at, update_at) VALUES (:category_name, '0', now(), now())");
        $insertStmt->execute([':category_name' => $category_name]);
        return $dbh->lastInsertId();
    }
}

function getAreaNo($area_name,$dbh){
    $stmt = $dbh->prepare("SELECT area_no FROM tt_area_tb where area_name = :area_name");
    $stmt->execute([':area_name'=>$area_name]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if($row){
        return $row['area_no'];
    }else{
        // カテゴリー名が存在しない場合は、新規登録する
        $stmt = $dbh->prepare("INSERT INTO  tt_area_tb (area_name,delete_ku,insert_at,update_at) VALUES (:area_name,'0',now(),now())");
        $stmt->execute([':area_name'=>$area_name]);
        return $dbh->lastInsertId();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $dbh = new PDO($dsn, $user, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //POSTデータの取得
    $txt_name = $_POST['txt_name'];
    $txt_category_name = $_POST['txt_category_name'];
    $txt_area_name = $_POST['txt_area_name'];
    $txt_syoka = $_POST['txt_syoka'];
    $txt_description = $_POST['txt_description'];

    $err = "";
// エラーチェック
    if ($txt_name == "") {
        $err .= "場所名たはアニメ名";
    }

    if ($txt_category_name == "") {
        $err .= "カテゴリー";
    }
    if ($txt_area_name == "") {
        $err .= "区名";
    }
    if ($txt_syoka == "") {
        $err .= "紹介";
    }

    // エラーがなければ、データを登録する
    if ($err == "") {
         // フォームから送信されたデータを各変数に格納
        $category_no = getCateNo($_POST['txt_category_name'],  $dbh);
        $area_no = getAreaNo($_POST['txt_area_name'],$dbh);

    $sql = "insert into tt_spot_tb("
        . "category_no"
        . ",area_no"
        . ",name"
        . ",syoka"
        . ",delete_ku"
        . ",insert_at"
        . ",update_at"
        . ") values ("
        . ":category_no"
        . ",:area_no"
        . ",:name"
        . ",:syoka"
        . ",'0'"
        . ",now()"
        . ",now()"
        . ");";
    $sql = "insert into "    
    $dbh = new PDO($dsn, $user, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// トランザクション処理を開始
    try {
        $stmt = $dbh->prepare($sql);
        // 对于 tt_spot_tb 表的插入操作
        $stmt->bindValue(':category_no', $category_no, PDO::PARAM_INT);
        $stmt->bindValue(':area_no', $area_no, PDO::PARAM_INT);
        $stmt->bindValue(':name', $txt_name, PDO::PARAM_STR);
        $stmt->bindValue(':syoka', $txt_syoka, PDO::PARAM_STR);
        $stmt->execute();
        $spotid = $dbh->lastInsertId();
    } catch (Exception $e) {
        echo "データの書き込みに失敗しました。" . $e->getMessage();
    }

    if (isset($spotid)) {
        //フォルダ名を日付＋ランダム文字列（４桁）で生成する//
        if (isset($_FILES['up_file'])) {
            //ASCIIコードによる文字変換は65がA、90がZとなります。
            $folder = date(ymd) . chr(mt_rand(65, 90)) . chr(mt_rand(65, 90)) . chr(mt_rand(65, 90)) . chr(mt_rand(65, 90));
            mkdir('uploads/' . $folder);
        }

        $save = 'uploads/' . $folder . '/' . basename($_FILES['up_file']['name']);

        //move_uploaded_fileで、一時ファイルを保存先ディレクトリに移動させる
        if (move_uploaded_file($_FILES['up_file']['tmp_name'], $save)) {
            $sql = "insert into tt_spots_fileupload_tb("
                . "spots_no"
                . ",folder_name"
                . ",file_name"
                . ",delete_ku"
                . ",insert_at"
                . ",update_at"
                . ") values ("
                . ":spots_no"
                . ",:folder_name"
                . ",:file_name"
                . ",'0'"
                . ",now()"
                . ",now()"
                . ");";
            $dbh = new PDO($dsn, $user, $password);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            try {
                $stmt = $dbh->prepare($sql);
                $stmt->bindValue(':spots_no', $spotid, PDO::PARAM_INT);
                $stmt->bindValue(':folder_name', $folder, PDO::PARAM_STR);
                $stmt->bindValue(':file_name', basename($_FILES['up_file']['name'], PDO::PARAM_STR));
                $stmt->execute();
            } catch (Exception $e) {
                echo "データの書き込みに失敗しました。" . $e->getMessage();
            }

            header('Location: test_spot_load.php?category_no=' . urlencode($category_no));

        } else {
            echo 'アップロード失敗！';
        }
    }
} else {
    echo $err . "を修正してください。";
}
}

?>

<html>

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>トップページ</title>
  <link rel="stylesheet" href="https://unpkg.com/ress/dist/ress.min.css" />
  <link rel="stylesheet" href="css/add.css" />
</head>

<body>
<div class="gtranslate_wrapper"></div>
  <script>window.gtranslateSettings = { "default_language": "ja", "native_language_names": true, "wrapper_selector": ".gtranslate_wrapper", "flag_style": "3d" }</script>
  <script src="https://cdn.gtranslate.net/widgets/latest/float.js" defer></script>
  <header>
        <h1>
            <p><a href="t_index.html"><img src="img/logo.png" alt="東京 Travel" /></a></p>
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
    <div class="anniu">
    <h1 id="add">情報登録</h1>
    </div>
    <form action="jyoho_add.php" method="POST" enctype="multipart/form-data">
        <div class="nyuryoku">
        場所名・アニメ名<br>
        <input type="text" name="txt_name" /><br>
        カテゴリー<br>
        <input type="text" name="txt_category_name" /><br>
        区名<br>
        <input type="text" name="txt_area_name" /><br>
        地域<br>
        <input type="text" name="txt_area_name" /><br>
        紹介<br>
        <textarea name="txt_syoka" style="width:300px;height:150px;"></textarea><br>
        説明<br>
        <textarea name="txt_description" style="width:300px;height:150px;"></textarea><br>
        画像<br>
        <input type="file" name="up_file"><br><br>
        <input type="submit" value="添加">
        </div>
    </form>
</body>

</html>