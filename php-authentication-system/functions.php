<?php

/**
 * @param string $str 対象の文字列
 * @return string 処理された文字列
 */

/**
 * xss対策(エスケープ処理)
 * ユーザーが入力した値などをechoで出力する際にはエスケープ処理が必ず必要(フォーム等で入力された悪意のあるスクリプトの実行を無効化できる)
 * htmlspecialchars( 変換対象, 変換パターン, 文字コード ) 
 * ENT_QUOTESは特殊文字のうちシングルクォーテーションとダブルクォーテーションも変換対象に含めるようになる
 * < → &lt;
 * > → &gt;
 * " → &quot;
 * laravelのbladeでは{{ name }}
 */
function h($str) {
  return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
 * @return string $csrf_token
 */

 /**
  * csrfトークンはCSRF対策用のワンタイムトークン
  * csrfトークンはざっくりいうと、なりすましを防止する役割。基本的にPOSTリクエストには必須
  */
function generateCsrfToken() {
  $csrf_token = bin2hex(random_bytes(32));
  return $csrf_token;
}