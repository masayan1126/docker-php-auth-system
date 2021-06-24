<?php

/**
 * XSS対策：エスケープ処理
 * 
 * @param string $str 対象の文字列
 * @return string 処理された文字列
 */

// xss対策(エスケープ処理)
//　ユーザーが入力した値などをechoで出力する際にはエスケープ処理が必ず必要(フォーム等で入力された悪意のあるスクリプトの実行を無効化できる)
function h($str) {
  return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
 * CSRF対策
 * @param void
 * @return string $csrf_token
 */
function setToken() {
  // トークンを生成
  // フォームからそのトークンを送信
  // 送信後の画面でそのトークンを照会
  // トークンを削除
  // post送信時は必ず必要
  $csrf_token = bin2hex(random_bytes(32));
  $_SESSION['csrf_token'] = $csrf_token;

  return $csrf_token;
}

// function inspectHasAuthenticationError($err) {
//   if (count($err) === 0) {
//     return false;
// }
//   return true;
// }