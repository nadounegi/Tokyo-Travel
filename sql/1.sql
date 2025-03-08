CREATE TABLE tt_other_areasyokai_tb(
	id INT NOT NULL AUTO_INCREMENT,
	NAME VARCHAR(200) NOT NULL,
	image_url VARCHAR(200) NOT NULL,
	description VARCHAR(3000) NOT NULL,
	delete_ku CHAR(1) NOT NULL,
	insert_at DATETIME NOT NULL,
	update_at DATETIME NOT NULL,
	
	PRIMARY KEY(
	id)
);

INSERT INTO tt_spot_tb (
   category_no,
   NAME,
   syoka,
   description
) VALUES
(
1,
"渋谷PARCO",
"渋谷PARCO（しぶやパルコ）は、 東京都渋谷区宇田川町にある複合商業ビル。株式会社パルコが運営するファッションビルブランド「PARCO（パルコ）」の旗艦店であり、パルコ全店の中で最大の取扱高を有する[1]。

1973年開業。20世紀渋谷カルチャーの代名詞的存在であり、「公園通り」「スペイン坂」「オルガン坂」[2]などのストリート名（通り名）付けなどの街づくり戦略も話題になった。渋谷パルコの開業は、渋谷が現在のような「若者の街」へと発展する契機となった[3]。

建物の老朽化のため、2016年8月7日21時をもって閉店。その後建て替えに入り、2019年11月22日に「渋谷 パルコ・ヒューリックビル」としてリニューアルオープンした[4]。",
),
("アニメ"),
("飲食");

	
