<?php
require_once ( dirname(__FILE__) . '/../env.php');

// // 本番環境ではログに記録する
// ini_set('log_errors','On');
// // ログの保存先
// ini_set('error_log','/log/php_error.log');

function connectToDatabase () {

    $host = DB_HOST;
    $db = DB_NAME;
    $user = DB_USER;
    $pass = DB_PASS;

    // 接続するデータベースの情報
    // dsn(データソースネーム)はデータベースの接続情報に付ける識別用の名前
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

    try {
        $pdo = new PDO($dsn, $user, $pass, [ // new PDO('DSN','ユーザー名','パスワード',オプション);
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // //PDOのエラー時に例外(PDOException)が発生するようになる
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // フィールド名で添字を付けた配列を返す
        ]);

        return $pdo;

    } catch (PDOException $e) {
        echo '接続失敗しました。' . $e->getMessage();
        exit();
    }

}
