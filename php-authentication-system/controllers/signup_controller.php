<?php

/**
 * サインアップ画面からマイページへのルーティングなどの処理
 * ①csrfトークンの照合
 * ②postされたusername、email、password、confirm_passwordのバリデーション
 * ③ユーザー登録処理と認証
 */

session_start();

require_once(dirname(__FILE__) . '/../classes/Auth.php'); 

/**
 * ①csrfトークンの照合
 */
if (!isset($_SESSION['csrf_token']) || filter_input(INPUT_POST, 'csrf_token') !== $_SESSION['csrf_token']) {
  exit('不正なリクエスト');
}

unset($_SESSION['csrf_token']);

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