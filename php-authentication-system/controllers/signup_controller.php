<?php

/**
 * サインイン画面からマイページへのルーティングなどの処理
 * ①csrfトークンの照合
 * ②postされたusername、email、password、confirm_passwordのバリデーション
 * ③ユーザー登録処理と認証
 */

session_start(); // ★この位置で書くと、require_onceの読み込み先ファイルでもsessionが使用できる

require_once(dirname(__FILE__) . '/../classes/Auth.php'); 
require_once(dirname(__FILE__) . '/../functions.php');

/**
 * ①csrfトークンの照合
 * セッションにトークンがない、もしくはフォームで送信されたトークンと一致しない場合、処理を中止
 */
if (!isset($_SESSION['csrf_token']) || filter_input(INPUT_POST, 'csrf_token') !== $_SESSION['csrf_token']) {
  exit('不正なリクエスト');
}

unset($_SESSION['csrf_token']); // セッションの中のcsrf_tokenを削除(ワンタイム利用のため)　フォーム画面以外からのセッションや、二重送信(フォーム再送信のconfirmからの送信)を対策

// ②postされたusername、email、password、confirm_passwordのバリデーション
$auth = new Auth(filter_input(INPUT_POST, 'email'), filter_input(INPUT_POST, 'password'), filter_input(INPUT_POST, 'username'), filter_input(INPUT_POST, 'password_conf'));
$signup_error = $auth->validate("signup");

if (count($signup_error) > 0) {
  $_SESSION['err'] = $signup_error;
  header('Location: ../views/signup.php');
  exit();
}

// ③ユーザー登録処理と認証
$auth = new Auth(filter_input(INPUT_POST, 'email'), filter_input(INPUT_POST, 'password'), filter_input(INPUT_POST, 'username'), $confirm_password = null);
$hasCreated = $auth->createUser();
Auth::signUp($hasCreated, filter_input(INPUT_POST, 'email'));