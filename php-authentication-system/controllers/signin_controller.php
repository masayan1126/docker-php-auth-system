<?php

/**
 * サインイン画面からマイページへのルーティングなどの処理
 * ①csrfトークンの照合
 * ②postされたemailとpasswordのバリデーション
 * ③サインイン処理
 */

session_start();

require_once(dirname(__FILE__) . '/../classes/Auth.php'); 

/**
 * ①csrfトークンの照合
 * セッションにトークンがない、もしくはフォームで送信されたトークンと一致しない場合、処理を中止
 */
if (!isset($_SESSION['csrf_token']) || filter_input(INPUT_POST, 'csrf_token') !== $_SESSION['csrf_token']) {
  exit('不正なリクエスト');
}

unset($_SESSION['csrf_token']);  // セッションの中のcsrf_tokenを削除(ワンタイム利用のため)　フォーム画面以外からのセッションや、二重送信(フォーム再送信のconfirmからの送信)を対策

// ②postされたemailとpasswordのバリデーション(メールアドレス、パスワードが未入力でないかチェックする)
$auth = new Auth(filter_input(INPUT_POST, 'email'), filter_input(INPUT_POST, 'password'), $username = null, $confirm_password = null);
$signin_error = $auth->validate("signin");

if (count($signin_error) > 0) {
    $_SESSION['err'] = $signin_error;
    header('Location: ../views/signin.php');
    exit();
}

// ③サインイン処理
$auth = new Auth(filter_input(INPUT_POST, 'email'), filter_input(INPUT_POST, 'password'), $username = null, $confirm_password = null);
$auth->signIn();