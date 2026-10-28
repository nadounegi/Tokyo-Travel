<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>

<?php
// データベース接続情報を格納
include("config.php");

$txt_email = "";
$txt_pass = "";

// メッセージを保存する変数を初期化
$alert_message = "";

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    // POSTデータの取得
    $txt_email = $_POST['txt_email'];
    $txt_pass = $_POST['txt_pass'];

    $err = "";

    if($txt_email == ""){
        $err .= "【メールアドレス】";
    }
    if($txt_pass == ""){
        $err .= "【パスワード】";
    }

    if($err == ""){
        try{
            $dbh = new PDO($dsn, $user, $password);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "select"
                    ." tum.user_id"
                    .",tum.user_name"
                    .",tum.user_password"
                    ." from tt_user_mst tum"
                    ." where tum.user_email = :user_email"
                    ." and tum.delete_ku = '0'";

            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':user_email', $txt_email , PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch();
            
            $alert_message= "";
            if(password_verify($txt_pass, $result['user_password'])){
                session_start();
                $_SESSION['user_id'] = $result['user_id'];
                $_SESSION['alert_message'] = $result['user_name']."さん、ログイン認証に成功しました";
                header("Location:top.php");
                exit;
            }else{
                $alert_message = "ログイン認証に失敗しました";            
            } 
        }catch (PDOException $e){
            $alert_message = $e->getMessage();
            die();
        }
    }else{
        $alert_message =$err."を修正してください。"; 
    }
}
?>


<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="https://unpkg.com/ress/dist/ress.min.css">
    <link rel="stylesheet" href="css/login.css">
    <title>Document</title>
</head>

<body>
<div class="gtranslate_wrapper"></div>
  <script>window.gtranslateSettings = { "default_language": "ja", "native_language_names": true, "wrapper_selector": ".gtranslate_wrapper", "flag_style": "3d" }</script>
  <script src="https://cdn.gtranslate.net/widgets/latest/float.js" defer></script>
  
  <?php if (!empty($alert_message)): ?>
    <div class="error"><?php echo $alert_message; ?></div>
<?php endif; ?>
    <header>
        <div class="Trave"><a href="index.html"><img src="img/logo.png" alt=""></a></div>
    </header>
    <main>
        <form action="user_login.php" method="POST" autocomplete="off">

            <div class="anniu">
                <p>ログイン/Login</p>
            </div>

            <div class="delu">
                <p>メールアドレス/Mail address <br>
                    <input type="email" id="Mail" name="txt_email" autocomplete="off">
                </p>

                <p>パスワード/Password <br>
                   <input type="password" id="Password" name="txt_pass" autocomplete="off">
                </p>

            <div class="lginbotan">
                <input class="botan" type="submit" value="送信/Send" id="Send">
                <div class="botan"><a href="user_signup.php">新規登録はこちら</a></div>
            </div>
            

            </div>
        </form>
        
        <div class="motoru">
            <p><a href="top.php"><img src="デザイン/トップページ　ボタン.jpg" alt=""></a></p>
            <p><a href="index.html"><img src="img/ホームページ　ボタン2.png" alt=""></a></p>
        </div>
    </main>
    <footer>
        <p><a href="index.html"><img src="img/logo.png" alt="東京 Travel" /></a></p>
    </footer>
</body>
<div class="gtranslate_wrapper"></div>
<script>window.gtranslateSettings = {"default_language":"ja","wrapper_selector":".gtranslate_wrapper","float_switcher_open_direction":"bottom","flag_style":"3d"}</script>
<script src="https://cdn.gtranslate.net/widgets/latest/float.js" defer></script>
</html>