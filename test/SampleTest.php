
<?php

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use PHPUnit\Framework\TestCase;
use Facebook\WebDriver\WebDriverBy;

class SampleTest extends TestCase
{
    protected $pdo; // PDOオブジェクト用のプロパティ(メンバ変数)の宣言
    protected $driver;

    public function setUp(): void
    {
        // PDOオブジェクトを生成し、データベースに接続
        $dsn = "mysql:host=db;dbname=minishop;charset=utf8";
        $user = "mini";
        $password = "shop";
        try {
            $this->pdo = new PDO($dsn, $user, $password);
        } catch (Exception $e) {
            echo 'Error:' . $e->getMessage();
            die();
        }

        #XAMPP環境で実施している場合、$dsn設定を変更する必要がある
        //ファイルパス
        $rdfile = __DIR__ . '/../src/classes/dbdata.php';
        $val = "host=db;";

        //ファイルの内容を全て文字列に読み込む
        $str = file_get_contents($rdfile);
        //検索文字列に一致したすべての文字列を置換する
        $str = str_replace("host=localhost;", $val, $str);
        //文字列をファイルに書き込む
        file_put_contents($rdfile, $str);

        // chrome ドライバーの起動
        $host = 'http://172.17.0.1:4444/wd/hub'; #Github Actions上で実行可能なHost
        // chrome ドライバーの起動
        $this->driver = RemoteWebDriver::create($host, DesiredCapabilities::chrome());
    }

    public function testCartAdd()
    {
        // 指定URLへ遷移 (Google)
        $this->driver->get('http://php/src/index.php');

        // ラジオボタンをクリック
        $this->driver->findElement(WebDriverBy::xpath("//input[@type='radio' and @name='genre' and @value='music']"))->click();

        // inputタグの要素を取得
        $element_input = $this->driver->findElements(WebDriverBy::tagName('input'));

        // 画面遷移実行
        $element_input[3]->submit();

        // ジャンル別商品一覧画面の詳細リンクをクリック
        $element_a = $this->driver->findElements(WebDriverBy::tagName('a'));
        $element_a[0]->click();

        // 注文数を「2」にし、「カートに入れる」をクリック
        $selector = $this->driver->findElement(WebDriverBy::tagName('select'))
            ->findElement(WebDriverBy::cssSelector("option[value='2']"))
            ->click();

        // 画面遷移実行
        $selector->submit();

        //データベースの値を取得
        $sql = 'select items.ident, items.name, items.maker, items.price, cart.quantity, 								
        items.image, items.genre from cart join items on cart.ident = items.ident where items.ident = ?';       // SQL文の定義
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([11]);
        $cart = $stmt->fetch();
        $this->assertEquals(2, $cart['quantity'], 'カート追加処理に誤りがあります。');
    }



    public function testCartChange()
    {
        // 指定URLへ遷移 (Google)
        $this->driver->get('http://php/src/index.php');

        // ラジオボタンをクリック
        $this->driver->findElement(WebDriverBy::xpath("//input[@type='radio' and @name='genre' and @value='music']"))->click();

        // inputタグの要素を取得
        $element_input = $this->driver->findElements(WebDriverBy::tagName('input'));

        // 画面遷移実行
        $element_input[3]->submit();

        // リンクをクリック
        $element_a = $this->driver->findElements(WebDriverBy::tagName('a'));
        $element_a[0]->click();

        // 注文数を「2」にする
        $selector = $this->driver->findElement(WebDriverBy::tagName('select'))
            ->findElement(WebDriverBy::cssSelector("option[value='2']"))
            ->click();

        // 画面遷移実行
        $selector->submit();

        // カート画面で注文数を「4」にする
        $selector = $this->driver->findElement(WebDriverBy::tagName('select'))
            ->findElement(WebDriverBy::cssSelector("option[value='4']"))
            ->click();

        // カート画面のinputタグの要素を取得
        $element_form = $this->driver->findElements(WebDriverBy::tagName('form'));
        $element_form[0]->submit();


        //データベースの値を取得
        $sql = 'select items.ident, items.name, items.maker, items.price, cart.quantity, 								
       items.image, items.genre from cart join items on cart.ident = items.ident where items.ident = ?';       // SQL文の定義
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([11]);
        $cart = $stmt->fetch();
        $this->assertEquals(4, $cart['quantity'], '注文数変更処理に誤りがあります。');
    }

    public function testCartdelete()
    {
        // 指定URLへ遷移 (Google)
        $this->driver->get('http://php/src/index.php');

        // ラジオボタンをクリック
        $this->driver->findElement(WebDriverBy::xpath("//input[@type='radio' and @name='genre' and @value='music']"))->click();

        // inputタグの要素を取得
        $element_input = $this->driver->findElements(WebDriverBy::tagName('input'));

        // 画面遷移実行
        $element_input[3]->submit();

        // リンクをクリック
        $element_a = $this->driver->findElements(WebDriverBy::tagName('a'));
        $element_a[0]->click();

        // 注文数を「2」にし、「カートに入れる」をクリック
        $selector = $this->driver->findElement(WebDriverBy::tagName('select'))
            ->findElement(WebDriverBy::cssSelector("option[value='2']"))
            ->click();

        // 画面遷移実行
        $selector->submit();

        // カート画面のinputタグの要素を取得
        $element_form = $this->driver->findElements(WebDriverBy::tagName('form'));
        // var_dump($element_form);
        $element_form[1]->submit();

        //データベースの値を取得
        $sql = 'select items.ident, items.name, items.maker, items.price, cart.quantity, 								
        items.image, items.genre from cart join items on cart.ident = items.ident';     // SQL文の定義
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([]);
        $count = $stmt->rowCount();    // レコード数の取得
        $this->assertEquals(0, $count, 'カート削除処理に誤りがあります。');

        $this->driver->close();
    }
}
