<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>トップページ</title>
    <link rel="stylesheet" href="https://unpkg.com/ress/dist/ress.min.css" />
    <link rel="stylesheet" href="css/top.css" />
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
    <nav class="Nav_list">
        <ul>
            <form action="search.php" method="POST" id="search">
                <input type="text" size=50 placeholder="場所名、エリアなどキーワードを入力してください" name="keywords">
                <button type="submit">検索</button>
            </form>
        </ul>
    </nav>

    <main>
        <p class="M_visual">
        <h1>東京２３区で検索する</h1>
        <div class="Tokyo_23">
            <a href="Tokyo_23.html">
                <img src="img/top_shibuya.jpg" alt="２３区地図" width="500px" height="308px"></a>
	</p>
        </div>
        

        <p class="M_visual">
        <h1>東京２３区以外で検索する</h1>
        <div class="Tokyo_23">
            <a href="kensaku_other_23.html">
                <img src="img/Toukyou23igai_kensaku_image.png" alt="２３区以外" width="500px" height="308px">
            </a>
        </div>
        </p>
    </main>
	<br>
	<br>
    <footer>
        <p><a href="index.html"><img src="img/logo.png" alt="東京 Travel" /></a></p>
        <p><a href="Otoiawase.html">お問い合わせ</a></p>
    </footer>
</body>
<div class="gtranslate_wrapper"></div>
<script>window.gtranslateSettings = {"default_language":"ja","wrapper_selector":".gtranslate_wrapper","float_switcher_open_direction":"bottom","flag_style":"3d"}</script>
<script src="https://cdn.gtranslate.net/widgets/latest/float.js" defer></script></html>