<?php

/**
 * サインイン画面からマイページへのルーティングなどの処理
 * ①サインアウト処理
 * ②セッションを削除
 */

session_start();
require_once(dirname(__FILE__) . '/../classes/Auth.php'); 

// サインアウトボタンを押してサインアウトを実行しているか
if (!$signout = filter_input(INPUT_POST, 'signout')) {
  exit('不正なリクエストです。');
}

// ①サインアウト処理、②セッションを削除
Auth::signOut();