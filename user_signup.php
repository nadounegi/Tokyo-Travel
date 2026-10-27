<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>
<?php

//データベース接続情報を格納
include("config.php");

$txt_user = "";
$txt_email = "";
$txt_pass = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    //POSTデータの取得
    $txt_user = $_POST['txt_user'];
    $txt_email = $_POST['txt_email'];
    $txt_pass = $_POST['txt_pass'];

    $err = "";

    if ($txt_user == "") {
        $err .= "【ユーザー名】";
    }
    if ($txt_email == "") {
        $err .= "【メールアドレス】";
    }
    if ($txt_pass == "") {
        $err .= "【パスワード】";
    }

    if ($err == "") {
        $sql = "insert into tt_user_mst("
            . "user_name"
            . ",user_email"
            . ",user_password"
            . ",delete_ku"
            . ",insert_at"
            . ",update_at"
            . ") values ("
            . ":user_name"
            . ",:user_email"
            . ",:user_password"
            . ",'0'"
            . ",now()"
            . ",now()"
            . ");";
        $dbh = new PDO($dsn, $user, $password);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':user_name', $txt_user, PDO::PARAM_STR);
        $stmt->bindValue(':user_email', $txt_email, PDO::PARAM_STR);
        $stmt->bindValue(':user_password', password_hash($txt_pass, PASSWORD_DEFAULT), PDO::PARAM_STR);
        if (!$stmt->execute()) {
            return "データの書き込みに失敗しました。";
        } else {
            header("Location: user_login.php");
        }
    }
}
?>

<html>

<head>
    <title>会員登録</title>
    <link rel="stylesheet" href="css/sign_up.css">
</head>

<body>
    <div class="gtranslate_wrapper"></div>
    <script>window.gtranslateSettings = { "default_language": "ja", "native_language_names": true, "wrapper_selector": ".gtranslate_wrapper", "flag_style": "3d" }</script>
    <script src="https://cdn.gtranslate.net/widgets/latest/float.js" defer></script>

    <header>
        <div class="Trave"><a href="index.html"><img src="img/logo.png" alt=""></a></div>
    </header>
    <main>
        <form action="" method="POST">
            <div class="anniu">
                <p>会員登録/SignUp</p>
            </div>
            <div class="register">
                <p>
                    <label>ユーザー名：</label>
                    <input type="text" name="txt_user">
                </p>
                <p>
                    <label>メールアドレス：</label>
                    <input type="text" name="txt_email">
                </p>
                <p>
                    <label>パスワード：</label>
                    <input type="password" name="txt_pass">
                </p>
                <input type="submit" name="submit" value="会員登録する" id="toroku">
            </div>
        </form>
        <div class="motoru">
            <p><a href="top.php"><img src="デザイン/トップページ　ボタン.jpg" alt=""></a></p>
            <p><a href="index.html"><img src="img/ホームページ　ボタン2.png" alt=""></a></p>
        </div>
    </main>
    <footer>
        <div class="left">東京<img src="/デザイン/グループ 6.jpg" alt="">Travel</div>
        <div class="right" style="font-size: 30px;">お問い合わせ</div>
    </footer>
</body>
<div class="gtranslate_wrapper"></div>
<script>window.gtranslateSettings = {"default_language":"ja","wrapper_selector":".gtranslate_wrapper","float_switcher_open_direction":"bottom","flag_style":"3d"}</script>
<script src="https://cdn.gtranslate.net/widgets/latest/float.js" defer></script>
</html>