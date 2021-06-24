<?php

/**
 * サインイン画面の実装内容
 * ①認証状態(セッションが残っている)でサインイン画面に遷移した場合はマイページへリダイレクトする
 * ②サインイン画面で必要項目を入力し、postして処理をコントローラーに投げる
 * ③認証に失敗したらサインイン画面に戻り、エラーを受け取って表示する
 * ④csrfトークンを生成しフォームにセット。同トークンをサーバーのセッションファイルに書き込む
 */

/**
 * session_startにより、セッションIDが発行され、ブラウザのクッキーにはPHPSESSIDという名称でセッションが、サーバーの/tmpにセッションファイルが生成
 * (セッションを作成もしくはセッションIDに基づき現在のセッションを復帰)
 * セッション情報は、スーパーグローバル変数の $_SESSION に連想配列として格納可能になる
 */
session_start(); 

/**
 * // dirname(__FILE__)にはそのファイルの絶対パスとファイル名が入る
 * requireやincludeは絶対パスで指定することが推奨(相対パスだと、読み込み先のファイルでもrequire等でファイルを読み込んでいる場合に、最初に読み込んでいるファイルを起点として相対パスを書く必要があり、ややこしい)
 */
require_once(dirname(__FILE__) . '/../classes/Auth.php'); 
require_once(dirname(__FILE__) . '/../functions.php');

// ①認証状態(セッションが残っている)でサインイン画面に遷移した場合はマイページへリダイレクトする
$isAuthenticated = Auth::checkIsAuthenticated();
if ($isAuthenticated) {
    header('Location: mypage.php');
    exit(); // returnとは違い、ここで処理が即終了する(returnは、呼び出し元へ戻り値を返したい場合に使用)
}

// ③認証に失敗したらサインイン画面に戻り、エラーを受け取って表示する
$signin_error = [];

// セッションにサインインエラー内容が入っていればそれを$signin_errorへ入れる
isset($_SESSION['err']) ? $signin_error = $_SESSION['err'] : null;
unset($_SESSION['err']);

// ④csrfトークンを生成しフォームにセット。同トークンをサーバーのセッションファイルに書き込む
if (empty($_SESSION['csrf_token'])) {
    $token = h(setToken());
    $_SESSION['csrf_token'] = $token;
} else {
    $token = $_SESSION['csrf_token'];
}

// セッションを初期化
// $_SESSION = array();
// session_destroy();
?>

<!-- ②サインイン画面で必要項目を入力し、postして処理をコントローラーに投げる -->
<!DOCTYPE html>
<html lang="ja">

<!-- includeで共通部分をhtmlで読み込み、titleタグを動的に渡す -->
<?php $title = 'サインイン'; include(dirname(__FILE__) . '/../layouts/layout.php'); ?>

<body>
    <section class="d-flex justify-content-center align-items-center vh-100">
        <div>
            <h2 class="mb-5">サインイン</h2>
            <!-- isset関数は、変数に値がセットされていて、かつNULLでないときに、TRUEを返す -->
            <?php if (isset($signin_error['email_invalid'])) : ?>
            <p class="text-danger"><?php echo $signin_error['email_invalid']; ?></p>
            <?php endif; ?>
            <form action="../controllers/signin_controller.php" method="post">
                <div class="input-group mb-3">
                    <label for="email">メールアドレス：</label>
                    <input type="email" name="email" class="form-control">
                    <?php if (isset($signin_error['email_blank'])) : ?>
                    <p class="text-danger"><?php echo $signin_error['email_blank']; ?></p>
                    <?php endif; ?>
                </div>
                <div class="input-group mb-3">
                    <label for="password">パスワード：</label>
                    <input type="password" name="password" class="form-control">
                    <?php if (isset($signin_error['password_blank'])) : ?>
                    <p class="text-danger"><?php echo $signin_error['password_blank']; ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
                    <input type="submit" value="サインインする" class="btn btn-success mb-3">
                </div>
            </form>
            <a href="signup.php">新規登録はこちら</a>
        </div>
    </section>
</body>

</html>