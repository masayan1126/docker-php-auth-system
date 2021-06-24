<?php

/**
 * マイページ画面の実装内容
 * ①認証しているか判定し、していなかったら新規登録画面へ返す
 */

session_start();

require_once(dirname(__FILE__) . '/../classes/Auth.php'); 
require_once(dirname(__FILE__) . '/../functions.php');

// ①認証しているか判定し、していなかったら新規登録画面へ返す
$isAuthenticated = Auth::checkIsAuthenticated();
$session_expired = null;

if (!$isAuthenticated) {
    $session_expired = 'セッションが切れたため、再度サインインしてください。';
    // セッションを初期化
    $_SESSION = array();
    session_destroy();
    header('Location: signin.php');
    exit();
}

$signin_user = $_SESSION['signin_user'];

?>
<!DOCTYPE html>
<html lang="ja">
<?php $title = 'マイページ'; include(dirname(__FILE__) . '/../layouts/layout.php'); ?>

<body>
    <section class="d-flex justify-content-center align-items-center vh-100">
        <div>
            <h2 class="mb-5">マイページ</h2>
            <p>サインインユーザ：<?php echo h($signin_user['name']) ?></p>
            <p>メールアドレス：<?php echo h($signin_user['email']) ?></p>
            <form action="../controllers/signout_controller.php" method="POST">
                <input type="submit" name="signout" value="サインアウトする" class="btn btn-success mb-3">
            </form>
        </div>
    </section>
</body>

</html>