<?php 
// 右記のように書くと指定したパスにリダイレクトできる -> header('Location: リダイレクト先のファイルパス')
header('Location: ../views/signin.php');

// require_once('./classes/Human.php');
// require_once('./classes/Ninja.php');
// require_once('./classes/UchihaClan.php');
// require_once('./classes/HyugaClan.php');


// // 村人A
// echo "<h3>人A</h3>";

// $name = "人A";
// $clan = "不明";
// $amount_chakra = 0;

// Human::sleeping();
// $human = new Human($name, $clan, $amount_chakra);
// $human->greeting();
// $human->generateChakra();
// $human->executeShadowCloningTechnique();

// // 忍者A
// echo "<h3>忍者A</h3>";

// $name = "忍者A";
// $clan = "不明";
// $amount_chakra = 100;

// Ninja::sleeping();
// $ninja = new Ninja($name, $clan, $amount_chakra);
// $ninja->greeting();
// $ninja->generateChakra();
// $ninja->executeShadowCloningTechnique();

// // うちはサスケ
// echo "<h3>うちはサスケ</h3>";

// $name = "うちはサスケ";
// $clan = "うちは一族";
// $amount_chakra = 100;

// UchihaClan::sleeping();
// $uchiha_clan = new UchihaClan($name, $clan, $amount_chakra);
// $uchiha_clan->greeting();
// $uchiha_clan->generateChakra();
// $uchiha_clan->executeShadowCloningTechnique();
// $uchiha_clan->executeSharingan();


// // 日向ネジ
// echo "<h3>日向ネジ</h3>";

// $name = "日向ネジ";
// $clan = "日向一族";
// $amount_chakra = 100;

// HyugaClan::sleeping();
// $hyuga_clan = new HyugaClan($name, $clan, $amount_chakra);
// $hyuga_clan->greeting();
// $hyuga_clan->generateChakra();
// $hyuga_clan->executeShadowCloningTechnique();
// $hyuga_clan->executeByakugan();