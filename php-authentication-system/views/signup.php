<?php

/**
 * サインアップ画面の実装内容
 * ①認証状態(セッションが残っている状態)でサインアップ画面にアクセスした場合はマイページへリダイレクトする
 * ②サインアップ画面で必要項目を入力し、postして処理をコントローラーに投げる
 * ③認証に失敗したらサインアップ画面に戻り、エラーを受け取って表示する
 * ④csrfトークンを生成しフォームにセット。同トークンをサーバーのセッションファイルに書き込む
 */

session_start(); 

require_once(dirname(__FILE__) . '/../classes/Auth.php'); 
require_once(dirname(__FILE__) . '/../functions.php');


// ①認証状態(セッションが残っている状態)でサインアップ画面にアクセスした場合はマイページへリダイレクトする
$isAuthenticated = Auth::checkIsAuthenticated();
if ($isAuthenticated) {
    header('Location: mypage.php');
    exit();
}

// ③認証に失敗したらサインアップ画面に戻り、エラーを受け取って表示する
$signup_error = [];

isset($_SESSION['err']) ? $signup_error = $_SESSION['err'] : null;
unset($_SESSION['err']);

// ④csrfトークンを生成しフォームにセット。同トークンをサーバーのセッションファイルに書き込
if (empty($_SESSION['csrf_token'])) {
    $token = generateCsrfToken();
    $_SESSION['csrf_token'] = $token;
} else {
    $token = $_SESSION['csrf_token'];
}
?>

<!-- ②サインアップ画面で必要項目を入力し、postして処理をコントローラーに投げる -->
<!DOCTYPE html>
<html lang="ja">

<?php $title = 'ユーザ登録画面'; include(dirname(__FILE__) . '/../layouts/layout.php'); ?>

<body>
    <section class="d-flex justify-content-center align-items-center vh-100">
        <div>
            <h2 class="mb-5">ユーザ登録フォーム</h2>
            <?php if (isset($signup_error)) : ?>
            <!-- foreach(繰り返し) -->
            <?php foreach ($signup_error as $error) : ?>
            <p class="text-danger"><?php echo h($error); ?></p>
            <?php endforeach; ?>
            <?php endif; ?>
            <form action="../controllers/signup_controller.php" method="POST">
                <div class="input-group mb-3">
                    <label for="username">ユーザ名：</label>
                    <input type="text" name="username" class="form-control">
                </div>
                <div class="input-group mb-3">
                    <label for="email">メールアドレス：</label>
                    <input type="email" name="email" class="form-control">
                </div>
                <div class="input-group mb-3">
                    <label for="password">パスワード：</label>
                    <input type="password" name="password" class="form-control">
                </div>
                <div class="input-group mb-3">
                    <label for="password_conf">パスワード確認：</label>
                    <input type="password" name="password_conf" class="form-control">
                </div>
                <input type="hidden" name="csrf_token" value="<?php echo h($token); ?>">
                <div class="input-group mb-3">
                    <input type="submit" value="新規登録" class="btn btn-success mb-3">
                </div>
            </form>
            <a href="./signin.php">アカウントをお持ちの方はこちら</a>
        </div>
    </section>
</body>

</html>