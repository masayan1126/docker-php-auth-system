<?php

// phpは<?phpのタグで開始する

/**
 * require_onceはファイルを１度だけ読み込む。(同じプログラムの中に
 * ２回同じ外部ファイルへのrequire_onceを書いても、２回目は読み込まれない)
 */
require_once ( dirname(__FILE__) . '/../env.php'); // dirname(__FILE__)にはそのファイルが存在するディレクトリの絶対パスが入る

function connectToDatabase () {
    $host = DB_HOST;
    $db = DB_NAME;
    $user = DB_USER;
    $pass = DB_PASS;

    /**
     * dsn(データソースネーム)はデータベースの接続情報に付ける識別用の名前
     * デフォルトのポート番号 (3306)
     */
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

    /**
     * その処理をすることにより例外エラーが生じる処理については、try ~ catchで例外エラーを受けることができるようにする
     * new PDO('DSN','ユーザー名','パスワード',オプション);
     * pdoからインスタンスを生成する
     * pdoクラスの詳細 -> https://php.net/manual/en/class.pdo.php
     */ 
    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // PDOのエラー時に例外(PDOException)がthrowされる
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // 取得したデータを連想配列で返す
        ]);

        return $pdo;

    } catch (PDOException $e) {
        echo '接続失敗しました。' . h($e->getMessage()); // echoでブラウザに出力
        exit(); // returnは呼び出し元に値を返す。exitはその時点で処理を中止し値は返さない
    }

}