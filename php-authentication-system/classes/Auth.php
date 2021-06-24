<?php
// dirname(__FILE__)にはそのファイルの絶対パスが入る
require_once ( dirname(__FILE__) . '/../database/dbconnect.php');

class Auth
{
    private $email;
    private $username;
    private $password;
    private $confirm_password;
    private $err = [];

    public function __construct($email, $password, $username, $confirm_password){
      //引数をメンバー変数に格納
      $this->email = $email;
      $this->password = $password;
      $this->username = $username;
      $this->confirm_password = $confirm_password;
    }

    /**
     * ユーザーを登録する
     * @param array $userData
     */

    // ユーザー登録が成功したら、trueを失敗したらfalseを返す
    public static function createUser($userData) {
        
        $result = false;
        
        // ;DELETE FROM user--"などのsqlインジェクション対策
        // SQL中に、後に可変値を埋め込みたい場所を「$1」「$2」あるいは「?」などの特別な文字列、すなわちプレースホルダで確保しておき、ここに埋め込む値はSQL本体とは分離して渡す
        $sql = 'INSERT INTO users (name, email, password) VALUES (?, ?, ?)'; // プレースホルダ
        
        $arr = [];
        $arr[] = $userData['username'];
        $arr[] = $userData['email'];
        // ハッシュ化して保存
        $arr[] = password_hash($userData['password'],PASSWORD_DEFAULT);
        
        try {
            // PDOStatementオブジェクト
            $stmt = connectToDatabase()->prepare($sql);
            // pdoのexecuteを実行
            $stmt->execute($arr); // executeはプリペアドステートメントを実行する。成功した場合に true を、失敗した場合に false を返す。
            $user = self::searchUserBasedEmail($userData['email']); // ユーザーをemailから検索して取得
            return $user;
                
        } catch(\Exception $e) {
          echo $e;
          return null;   
        }
    }

    /**
     * ユーザーを登録する
     * @return array $err
     * @param string $email
     * @param string $password
     */

    public function validateSignIn() 
    {
      if(empty($this->email)){
        $this->err['email_blank'] = 'メールアドレスを入力してください。';
      }
      if(empty($this->password)) {
        $this->err['password_blank'] = 'パスワードを入力してください。';
      }
      return $this->err;
    }
    
    /**
     * ユーザーを登録する
     * @return array $err
     * @param string $email
     * @param string $password
     */

    public function validateSignUp() 
    {
      if(empty($this->username)) {
        $this->err['username_blank'] = 'ユーザ名を入力してください。';
      }
      
      if(empty($this->email)) {
        $this->err['email_blank'] = 'メールアドレスを入力してください。';
      }
      
      // 正規表現(preg_match)
      if (!preg_match("/\A[a-z\d]{8,100}+\z/i", $this->password)) {
        $this->err['password_invalid'] = 'パスワードは英数字8文字以上100文字以下にしてください。';
      }
      
      if ($this->password !== $this->confirm_password) {
        $this->err['confirm_password_invalid'] = '確認用パスワードと異なっています。';
      }
      return $this->err;
    }

    public static function signUp($newUser)
    {
      if(!empty($newUser)) {
        $_SESSION['err']['user_registration_failure'] = 'ユーザー登録に失敗しました';
        header('Location: ../views/signup.php');
        exit();
      }
      
      session_regenerate_id(true); // セッションハイジャック対策
      $_SESSION['signin_user'] = $newUser;
      unset($_SESSION['err']); // サインインできたら、セッションのエラーを消去する
      header('Location: ../views/mypage.php');
    }

      /**
   * サインイン処理
   * @param string $email
   * @param string $password
   * 
   */
  public static function signIn($email, $password)
  {
    /**
     * $thisは、自分自身のオブジェクトを指し、インスタンス化した際、クラス内のメンバ変数やメソッドにアクセスする際に使用
     * self:: は、自クラスを示します。クラス定数、static変数については、インスタンス化せずに使用します。そのため、$thisは使用せず、代わりにselfを使用します。(staticメソッドにアクセスできます)
     */
    $target_user = self::searchUserBasedEmail($email); // ユーザーをemailから検索して取得

    // emailが一致するユーザーがいなかったら早期リターン
    if (!$target_user) {
      $_SESSION['err']['email_invalid'] = 'メールアドレスが不正です。';
      header('Location: ../views/signin.php');
      exit();
    }

    // パスワードが不一致なら早期リターン(password_verifyはハッシュされたパスワードとベタがきのパスワードが正しいかチェックできる)
    if (!password_verify($password, $target_user['password'])) {
      header('Location: ../views/signin.php');
      exit();
    }
    
    // emailとパスワードが一致した場合はサインイン
    session_regenerate_id(true); // セッションハイジャック対策
    $_SESSION['signin_user'] = $target_user;
    unset($_SESSION['err']); // サインインできたら、セッションのエラーを消去する
    header('Location: ../views/mypage.php');
  }
  
  /**
   * emailからユーザを取得
   * @param string $email
   * @return array|bool $user|false
   */
  public static function searchUserBasedEmail($email)
  {
    $sql = 'SELECT * FROM users WHERE email = ?';

    // emailを配列に入れる
    $arr = [];
    $arr[] = $email;

    try {
      $stmt = connectToDatabase()->prepare($sql);
      $stmt->execute($arr);
      $user = $stmt->fetch(); // fetchメソッドでSQLの結果を返すことが可能
      return $user;
      
    } catch(\Exception $e) {
      return false;
    }
  }

  /**
   * 認証状態のチェック
   * @param void
   * @return bool $result
   */
  public static function checkIsAuthenticated()
  {
    $isAuthenticated = false;
    
    // セッションにサインインユーザが入っていればtrue
    if (isset($_SESSION['signin_user'])) {
      return $isAuthenticated = true;
    }
    return $isAuthenticated;
  }

  /**
   * サインアウト処理
   */
  public static function signOut()
  {
    $_SESSION = array();
    session_destroy();
    header('Location: ../views/signin.php');
  }

    
}