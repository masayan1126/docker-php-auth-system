<?php
require_once ( dirname(__FILE__) . '/../database/dbconnect.php');
require_once ( dirname(__FILE__) . '/../functions.php');

/**
 * Authクラス(認証関連のフィールドとメソッドの集まり)
 * ■フィールド
 * $email、$username、$password、$confirm_password、$varidate_err
 * 
 * ■メソッド
 * ①ユーザー作成(createUser)
 * ②バリデーション(validate)
 * ③サインアップ(signUp)
 * ④サインイン(signIn)
 * ⑤Eメールでユーザー検索(searchUserBasedEmail)
 * ⑥セッションから認証状態のチェック(checkIsAuthenticated)
 * ⑦サインアウト(signOut)
 */

class Auth
{
    // オブジェクト指向について
    // アクセス修飾子(private、protected、public)でカプセル化
    private $email;
    private $username;
    private $password;
    private $confirm_password;
    private $varidate_err = [];

    /**
     * コンストラクタ(インスタンス生成時に動的に値を渡して、フィールドに値をセットできる)
     * マジックメソッド(特殊な状況下で実行されるメソッド)
     *  */ 
    public function __construct($email, $password, $username, $confirm_password)
    {
      //初期化
      $this->email = $email;
      $this->password = $password;
      $this->username = $username;
      $this->confirm_password = $confirm_password;
    }

    /**
     * phpDoc
     */

    /**
     * @return bool $hasCreated
     */

    // ①ユーザー作成(createUser)
    public function createUser() 
    {
      /**
       * ;DELETE FROM user--"などのsqlインジェクション対策
       * プレースホルダでSQL文の可変値を「$1」「$2」あるいは「?」などの特別な文字列で設定しておき、後から埋め込む
       */
      $sql = 'INSERT INTO users (name, email, password) VALUES (?, ?, ?)';
      $hasCreated = false;
      
      try {
          // PDOStatementオブジェクト->prepare
          $stmt = connectToDatabase()->prepare($sql);
          // executeはプリペアドステートメントを実行する。成功した場合に true を、失敗した場合に false を返す。
          $hasCreated = $stmt->execute([$this->username, $this->email, password_hash($this->password,PASSWORD_DEFAULT)]);  // パスワードはハッシュ化
              
      } catch(\Exception $e) {
        echo h($e);
      }
      return $hasCreated;
    }

     /**
     * @param string $authenticationOperation
     * @return array $varidate_err
     */

    /**
     * ②バリデーション(validate)
     */
    public function validate($authenticationOperation) 
    {
      if(empty($this->email)) {
        $this->varidate_err['email_blank'] = 'メールアドレスを入力してください。';
      }
      
      if ($authenticationOperation === "signin") {
        if(empty($this->password)) {
          $this->varidate_err['password_blank'] = 'パスワードを入力してください。';
        }

      } else {
        if(empty($this->username)) {
          $this->varidate_err['username_blank'] = 'ユーザ名を入力してください。';
        }
  
        // 正規表現(preg_match)
        if (!preg_match("/\A[a-z\d]{8,100}+\z/i", $this->password)) {
          $this->varidate_err['password_invalid'] = 'パスワードは英数字8文字以上100文字以下にしてください。';
        }
        
        if ($this->password !== $this->confirm_password) {
          $this->varidate_err['confirm_password_invalid'] = '確認用パスワードと異なっています。';
        }
      }
      
      return $this->varidate_err;
    }

     /**
     * @param bool $hasCreated
     * @param string $email
     * @return void
     */

    // ③サインアップ(signUp)
    public static function signUp($hasCreated, $email) // static
    {
      if(!$hasCreated) {
        $_SESSION['err']['user_registration_failure'] = 'ユーザー登録に失敗しました';
        header('Location: ../views/signup.php');
        exit();
      }

      /**
       * $thisは、自分自身のオブジェクトを指し、インスタンス化した際、クラス内のメンバ変数やメソッドにアクセスする際に使用
       * self:: は、自クラスを示します。クラス定数、static変数については、インスタンス化せずに使用します。そのため、$thisは使用せず、代わりにselfを使用します。
       */
      $newUser = self::searchUserBasedEmail($email);
      session_regenerate_id(true); // セッションハイジャック対策(「セッション」を窃取し、本人に成り代わって通信を行うというサイバー攻撃 = なりすまし)のため、必ずセッションIDを再生成する
      $_SESSION['signin_user'] = $newUser;
      unset($_SESSION['err']); // サインインできたら、セッションのエラーを消去する
      header('Location: ../views/mypage.php');
    }

    /**
     * ④サインイン(signIn)
     * @return void
     */
    public function signIn()
    {
      $target_user = $this->searchUserBasedEmail($this->email); // ユーザーをemailから検索して取得
      // emailが一致するユーザーがいなかったら早期リターン
      if (count($target_user) === 0) {
        $_SESSION['err']['email_invalid'] = 'メールアドレスが不正です。';
        header('Location: ../views/signin.php');
        exit();
      }
      
      // パスワードが不一致なら早期リターン(password_verifyはハッシュされたパスワードとベタがきのパスワードが正しいかチェックできる)
      if (!password_verify($this->password, $target_user['password'])) {
        header('Location: ../views/signin.php');
        exit();
      }
      
      // emailとパスワードが一致した場合はサインイン
      session_regenerate_id(true); // セッションハイジャック対策(「セッション」を窃取し、本人に成り代わって通信を行うというサイバー攻撃 = なりすまし)のため、必ずセッションIDを再生成する
      $_SESSION['signin_user'] = $target_user;
      unset($_SESSION['err']); // サインインできたら、セッションのエラーを消去する(unset = 変数や配列の特定の要素を削除)
      header('Location: ../views/mypage.php');
    }
    
    /**
     * @param string $email
     * @return array|bool $user|false
     */

    // ⑤Eメールでユーザー検索(searchUserBasedEmail)
    public static function searchUserBasedEmail($email)
    {
      $sql = 'SELECT * FROM users WHERE email = ?';

      try {
        $stmt = connectToDatabase()->prepare($sql);
        // execute(array|null)
        $stmt->execute([$email]);
        $user = $stmt->fetch(); // fetchメソッドでSQLの結果を返すことが可能
        return $user;
        
      } catch(\Exception $e) {
        echo h($e);
        return false;
      }
    }

    /**
     * @return bool $isAuthenticated
     */

    // ⑥セッションから認証状態のチェック(checkIsAuthenticated)
    public static function checkIsAuthenticated()
    {
      $isAuthenticated = false;
      
      // セッションにサインインユーザが入っていればtrue
      if (isset($_SESSION['signin_user'])) {
        $isAuthenticated = true;
      }
      return $isAuthenticated;
    }

  /**
   * @return void
   */

    // ⑦サインアウト(signOut)
    public static function signOut()
    {
      //セッションを破棄($_SESSIONはphpからセッションデータにアクセスするためのスーパーグローバル変数)
      $_SESSION = array();
      session_destroy();
      header('Location: ../views/signin.php');
    }
}