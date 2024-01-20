<?php header("Content-Type:text/html;charset=utf-8"); ?>
<?php //error_reporting(E_ALL | E_STRICT);
##-----------------------------------------------------------------------------------------------------------------##
#
#  PHPメールプログラム　フリー版 ver2.0.3 最終更新日2022/02/01
#　改造や改変は自己責任で行ってください。
#	
#  HP: http://www.php-factory.net/
#
#  重要！！サイトでチェックボックスを使用する場合のみですが。。。
#  チェックボックスを使用する場合はinputタグに記述するname属性の値を必ず配列の形にしてください。
#  例　name="当サイトをしったきっかけ[]"  として下さい。
#  nameの値の最後に[と]を付ける。じゃないと複数の値を取得できません！
#
##-----------------------------------------------------------------------------------------------------------------##
if (version_compare(PHP_VERSION, '5.1.0', '>=')) { //PHP5.1.0以上の場合のみタイムゾーンを定義
  date_default_timezone_set('Asia/Tokyo'); //タイムゾーンの設定（日本以外の場合には適宜設定ください）
}
/*-------------------------------------------------------------------------------------------------------------------
* ★以下設定時の注意点　
* ・値（=の後）は数字以外の文字列（一部を除く）はダブルクオーテーション「"」、または「'」で囲んでいます。
* ・これをを外したり削除したりしないでください。後ろのセミコロン「;」も削除しないください。
* ・また先頭に「$」が付いた文字列は変更しないでください。数字の1または0で設定しているものは必ず半角数字で設定下さい。
* ・メールアドレスのname属性の値が「Email」ではない場合、以下必須設定箇所の「$Email」の値も変更下さい。
* ・name属性の値に半角スペースは使用できません。
*以上のことを間違えてしまうとプログラムが動作しなくなりますので注意下さい。
-------------------------------------------------------------------------------------------------------------------*/


//---------------------------　必須設定　必ず設定してください　-----------------------

//サイトのトップページのURL　※デフォルトでは送信完了後に「トップページへ戻る」ボタンが表示されますので
$site_top = "index.html";

//管理者のメールアドレス ※メールを受け取るメールアドレス(複数指定する場合は「,」で区切ってください 例 $to = "aa@aa.aa,bb@bb.bb";)
$to = "info@reprohouse.jp";

//送信元メールアドレス（管理者宛て、及びユーザー宛メールの送信元メールアドレスです）
//必ず実在するメールアドレスでかつ出来る限り設置先サイトのドメインと同じドメインのメールアドレスとすることを強く推奨します
//管理者宛てメールの返信先（reply）はユーザーのメールアドレスになります。
$from = "info@reprohouse.jp";

//フォームのメールアドレス入力箇所のname属性の値（name="○○"　の○○部分）
$Email = "メールアドレス";
//---------------------------　必須設定　ここまで　------------------------------------


//---------------------------　セキュリティ、スパム防止のための設定　------------------------------------

//スパム防止のためのリファラチェック（フォーム側とこのファイルが同一ドメインであるかどうかのチェック）(する=1, しない=0)
//※有効にするにはこのファイルとフォームのページが同一ドメイン内にある必要があります
$Referer_check = 0;

//リファラチェックを「する」場合のドメイン ※設置するサイトのドメインを指定して下さい。
//もしこの設定が間違っている場合は送信テストですぐに気付けます。
$Referer_check_domain = "php-factory.net";

/*セッションによるワンタイムトークン（CSRF対策、及びスパム防止）(する=1, しない=0)
※ただし、この機能を使う場合は↓の送信確認画面の表示が必須です。（デフォルトではON（1）になっています）
※【重要】ガラケーは機種によってはクッキーが使えないためガラケーの利用も想定してる場合は「0」（OFF）にして下さい（PC、スマホは問題ないです）*/
$useToken = 1;
//---------------------------　セキュリティ、スパム防止のための設定　ここまで　------------------------------------


//---------------------- 任意設定　以下は必要に応じて設定してください ------------------------

// Bccで送るメールアドレス(複数指定する場合は「,」で区切ってください 例 $BccMail = "aa@aa.aa,bb@bb.bb";)
$BccMail = "";

// 管理者宛に送信されるメールのタイトル（件名）
$subject = "【土地探しからのオーダーハウス】のお問い合わせがありました";

// 送信確認画面の表示(する=1, しない=0)
$confirmDsp = 1;

// 送信完了後に自動的に指定のページ(サンクスページなど)に移動する(する=1, しない=0)
// CV率を解析したい場合などはサンクスページを別途用意し、URLをこの下の項目で指定してください。
// 0にすると、デフォルトの送信完了画面が表示されます。
$jumpPage = 1;

// 送信完了後に表示するページURL（上記で1を設定した場合のみ）※httpから始まるURLで指定ください。（相対パスでも基本的には問題ないです）
$thanksPage = "contact-thanks.html";

// 必須入力項目を設定する(する=1, しない=0)
$requireCheck = 1;

/* 必須入力項目(入力フォームで指定したname属性の値を指定してください。（上記で1を設定した場合のみ）
値はシングルクォーテーションで囲み、複数の場合はカンマで区切ってください。フォーム側と順番を合わせると良いです。 
配列の形「name="○○[]"」の場合には必ず後ろの[]を取ったものを指定して下さい。*/
$require = array('希望の場所', '希望の都道府県', '建築状況', 'お名前', 'フリガナ', 'ご連絡先', 'メールアドレス', 'メールアドレス（確認用）', 'お問い合わせ内容');


//----------------------------------------------------------------------
//  自動返信メール設定(START)
//----------------------------------------------------------------------

// 差出人に送信内容確認メール（自動返信メール）を送る(送る=1, 送らない=0)
// 送る場合は、フォーム側のメール入力欄のname属性の値が上記「$Email」で指定した値と同じである必要があります
$remail = 1;

//自動返信メールの送信者欄に表示される名前　※あなたの名前や会社名など（もし自動返信メールの送信者名が文字化けする場合ここは空にしてください）
$refrom_name = "株式会社リプロハウス";

// 差出人に送信確認メールを送る場合のメールのタイトル（上記で1を設定した場合のみ）
$re_subject = "【自動返信】お問い合わせいただきありがとうございます｜【公式】注文住宅やリノベーションならリプロハウス";

//フォーム側の「名前」箇所のname属性の値　※自動返信メールの「○○様」の表示で使用します。
//指定しない、または存在しない場合は、○○様と表示されないだけです。あえて無効にしてもOK
$dsp_name = 'お名前';

//自動返信メールの冒頭の文言 ※日本語部分のみ変更可
$remail_text = <<< TEXT

この度は「土地探しからのオーダーハウス」にお問い合わせいただき、誠にありがとうございます。

以下の内容でお問い合わせを受付いたしました。
内容によりましては、返信に多少時間のかかることがありますのでご了承ください。

TEXT;


//自動返信メールに署名（フッター）を表示(する=1, しない=0)※管理者宛にも表示されます。
$mailFooterDsp = 1;

//上記で「1」を選択時に表示する署名（フッター）（FOOTER～FOOTER;の間に記述してください）
$mailSignature = <<< FOOTER

こちらは自動返信メールですので、返信は不要です。

このメールはリプロハウス（https://reprohouse.jp/）のお問い合わせフォームから送信されました。

━━━━━━━━━━━━━━━━
株式会社リプロハウス
〒564-0052 大阪府吹田市広芝町3-29
【TEL】
0120-361-555 / 06-6170-6780 FAX 06-6170-6782
【mail】
info@reprohouse.jp
━━━━━━━━━━━━━━━━

FOOTER;


//----------------------------------------------------------------------
//  自動返信メール設定(END)
//----------------------------------------------------------------------

//メールアドレスの形式チェックを行うかどうか。(する=1, しない=0)
//※デフォルトは「する」。特に理由がなければ変更しないで下さい。メール入力欄のname属性の値が上記「$Email」で指定した値である必要があります。
$mail_check = 1;

//全角英数字→半角変換を行うかどうか。(する=1, しない=0)
$hankaku = 1;

//全角英数字→半角変換を行う項目のname属性の値（name="○○"の「○○」部分）
//※複数の場合にはカンマで区切って下さい。（上記で「1」を指定した場合のみ有効）
//配列の形「name="○○[]"」の場合には必ず後ろの[]を取ったものを指定して下さい。
$hankaku_array = array('電話番号', '金額');

//-fオプションによるエンベロープFrom（Return-Path）の設定(する=1, しない=0)　
//※宛先不明（間違いなどで存在しないアドレス）の場合に 管理者宛に「Mail Delivery System」から「Undelivered Mail Returned to Sender」というメールが届きます。
//サーバーによっては稀にこの設定が必須の場合もあります。
//設置サーバーでPHPがセーフモードで動作している場合は使用できませんので送信時にエラーが出たりメールが届かない場合は「0」（OFF）として下さい。
$use_envelope = 0;

//機種依存文字の変換
/*たとえば㈱（かっこ株）や①（丸1）、その他特殊な記号や特殊な漢字などは変換できずに「？」と表示されます。それを回避するための機能です。
確認画面表示時に置換処理されます。「変換前の文字」が「変換後の文字」に変換され、送信メール内でも変換された状態で送信されます。（たとえば「㈱」の場合、「（株）」に変換されます） 
必要に応じて自由に追加して下さい。ただし、変換前の文字と変換後の文字の順番と数は必ず合わせる必要がありますのでご注意下さい。*/

//変換前の文字
$replaceStr['before'] = array('①', '②', '③', '④', '⑤', '⑥', '⑦', '⑧', '⑨', '⑩', '№', '㈲', '㈱', '髙');
//変換後の文字
$replaceStr['after'] = array('(1)', '(2)', '(3)', '(4)', '(5)', '(6)', '(7)', '(8)', '(9)', '(10)', 'No.', '（有）', '（株）', '高');

//------------------------------- 任意設定ここまで ---------------------------------------------


// 以下の変更は知識のある方のみ自己責任でお願いします。

//----------------------------------------------------------------------
//  関数実行、変数初期化
//----------------------------------------------------------------------
//トークンチェック用のセッションスタート
if ($useToken == 1 && $confirmDsp == 1) {
  session_name('PHPMAILFORMSYSTEM');
  session_start();
}
$encode = "UTF-8"; //このファイルの文字コード定義（変更不可）
if (isset($_GET)) $_GET = sanitize($_GET); //NULLバイト除去//
if (isset($_POST)) $_POST = sanitize($_POST); //NULLバイト除去//
if (isset($_COOKIE)) $_COOKIE = sanitize($_COOKIE); //NULLバイト除去//
if ($encode == 'SJIS') $_POST = sjisReplace($_POST, $encode); //Shift-JISの場合に誤変換文字の置換実行
$funcRefererCheck = refererCheck($Referer_check, $Referer_check_domain); //リファラチェック実行

//変数初期化
$sendmail = 0;
$empty_flag = 0;
$post_mail = '';
$errm = '';
$header = '';

if ($requireCheck == 1) {
  $requireResArray = requireCheck($require); //必須チェック実行し返り値を受け取る
  $errm = $requireResArray['errm'];
  $empty_flag = $requireResArray['empty_flag'];
}
//メールアドレスチェック
if (empty($errm)) {
  foreach ($_POST as $key => $val) {
    if ($val == "confirm_submit") $sendmail = 1;
    if ($key == $Email) $post_mail = h($val);
    if ($key == $Email && $mail_check == 1 && !empty($val)) {
      if (!checkMail($val)) {
        $errm .= "<p class=\"error_messe\">【" . $key . "】はメールアドレスの形式が正しくありません。</p>\n";
        $empty_flag = 1;
      }
    }
  }
}

if (($confirmDsp == 0 || $sendmail == 1) && $empty_flag != 1) {

  //トークンチェック（CSRF対策）※確認画面がONの場合のみ実施
  if ($useToken == 1 && $confirmDsp == 1) {
    if (empty($_SESSION['mailform_token']) || ($_SESSION['mailform_token'] !== $_POST['mailform_token'])) {
      exit('ページ遷移が不正です');
    }
    if (isset($_SESSION['mailform_token'])) unset($_SESSION['mailform_token']); //トークン破棄
    if (isset($_POST['mailform_token'])) unset($_POST['mailform_token']); //トークン破棄
  }

  //差出人に届くメールをセット
  if ($remail == 1) {
    $userBody = mailToUser($_POST, $dsp_name, $remail_text, $mailFooterDsp, $mailSignature, $encode);
    $reheader = userHeader($refrom_name, $from, $encode);
    $re_subject = "=?iso-2022-jp?B?" . base64_encode(mb_convert_encoding($re_subject, "JIS", $encode)) . "?=";
  }
  //管理者宛に届くメールをセット
  $adminBody = mailToAdmin($_POST, $dsp_name, $subject, $mailFooterDsp, $encode, $confirmDsp);
  $header = adminHeader($post_mail, $BccMail);
  $subject = "=?iso-2022-jp?B?" . base64_encode(mb_convert_encoding($subject, "JIS", $encode)) . "?=";

  //-fオプションによるエンベロープFrom（Return-Path）の設定(safe_modeがOFFの場合かつ上記設定がONの場合のみ実施)
  if ($use_envelope == 0) {
    mail($to, $subject, $adminBody, $header);
    if ($remail == 1 && !empty($post_mail)) mail($post_mail, $re_subject, $userBody, $reheader);
  } else {
    mail($to, $subject, $adminBody, $header, '-f' . $from);
    if ($remail == 1 && !empty($post_mail)) mail($post_mail, $re_subject, $userBody, $reheader, '-f' . $from);
  }
} else if ($confirmDsp == 1) {

  /*　▼▼▼送信確認画面のレイアウト※編集可　オリジナルのデザインも適用可能▼▼▼　*/
?>
<!DOCTYPE html>
<html lang="ja">

<head prefix="og: http://ogp.me/ns# ">
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <!-- 電話番号の自動検出と変換を無効にする -->
  <meta name="format-detection" content="telephone=no" />

  <title>入力確認｜【公式】注文住宅やリノベーションならリプロハウス</title>
  <meta name="description"
    content="リプロハウスでは、お客様のご要望に応じた土地探しから資金計画、設計デザインや建築施工、そしてアフターメンテナンスまでワンストップで、お客様にご満足いただける家創りをトータルサポートさせていただきます。" />
  <meta name="keywords" content="リプロハウス,新築、注文住宅,リノベーション,リフォーム,サンワカンパニー,アソリエ,平家,木の家,天井の高い家,3階建ての家" />

  <!-- ogp -->
  <meta property="og:title" content="入力確認｜【公式】注文住宅やリノベーションならリプロハウス      " />
  <meta property="og:type" content="article" />
  <meta property="og:url" content="https://reprohouse.jp/wood-house.html" />
  <meta property="og:image" content="./images/common/ogp.png" />
  <meta property="og:site_name" content="【公式】注文住宅やリノベーションならリプロハウス" />
  <meta property="og:description"
    content="リプロハウスでは、お客様のご要望に応じた土地探しから資金計画、設計デザインや建築施工、そしてアフターメンテナンスまでワンストップで、お客様にご満足いただける家創りをトータルサポートさせていただきます。" />
  <!-- ファビコン -->
  <link rel="icon" href="./images/common/favicon.ico" />
  <link rel="apple-touch-icon" href="./images/common/favicon.ico" />
  <!-- フォント -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/yakuhanjp@3.4.1/dist/css/yakuhanjp-noto.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Montserrat&family=Roboto:wght@500&display=swap"
    rel="stylesheet" />
  <!-- CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />
  <link rel="stylesheet" href="./css/style.css" />
  <!-- script -->
</head>

<body>
  <header class="header layout-header page-header js-header">
    <div class="header__inner">
      <div class="header__overview-bg overview-bg js-overview"></div>
      <div class="header__logo">
        <a href="index.html">
          <img src="./images/common/logo.svg" alt="LOGO" />
        </a>
      </div>
      <button class="header__hamburger js-hamburger">
        <span></span>
        <span></span>
        <span></span>
      </button>
      <div class="header__drawer js-drawer drawer">
        <nav class="drawer-nav">
          <ul class="drawer-items--top">
            <li class="drawer-item drawer-item--big">
              <a href="order-house.html"><span>-&emsp;注文住宅・オーダーハウス</span></a>
            </li>
            <li class="drawer-item drawer-item--small">
              <a href="wood-house.html"><span>-&emsp;木の家</span></a>
            </li>
            <li class="drawer-item drawer-item--small">
              <a href="one-story-house.html"><span>-&emsp;平屋の家</span></a>
            </li>
            <li class="drawer-item drawer-item--small">
              <a href="high-house-in-the-sky.html"><span>-&emsp;天井の高い家</span></a>
            </li>
            <li class="drawer-item drawer-item--small">
              <a href="three-story-house.html"><span>-&emsp;3階建ての家</span></a>
            </li>
          </ul>
          <ul class="drawer-items--bottom">
            <li class="drawer-item drawer-item--big">
              <a href="renovation-house.html"><span>-&emsp;リフォーム・リノベーション</span></a>
            </li>
            <li class="drawer-item drawer-item--big">
              <a href="asolie-house.html"><span>-&emsp;アソリエ・サンワカンパニー</span></a>
            </li>
            <li class="drawer-item drawer-item--big">
              <a href="about.html"><span>-&emsp;リプロハウスについて</span></a>
            </li>
            <li class="drawer-item drawer-item--big">
              <a href="company.html"><span>-&emsp;会社概要</span></a>
            </li>
            <li class="drawer-item drawer-item--big">
              <a href="coming.html"><span>-&emsp;モデルハウスのお申し込み</span></a>
            </li>
          </ul>
          <ul class="drawer__btn-list form-btn__list">
            <li class="form-btn1">
              <a href="coming.html"><span>モデルハウス見学会<br />お申し込み</span></a>
            </li>
            <li class="form-btn2">
              <a href="contact.html"><span>土地探しから<br />オーダーハウスの<br />ご相談</span></a>
            </li>
            <li class="form-btn3 drawer__form-btn3">
              <a href="contact.html"><span>中古物件から<br />リノベーションの<br />ご相談</span></a>
            </li>
          </ul>
        </nav>
      </div>
    </div>
  </header>

  <!-- ▲ Headerやその他コンテンツなど　※自由に編集可 ▲-->

  <!-- ▼************ 送信内容表示部　※編集は自己責任で ************ ▼-->
  <main>
    <div id="wrapper">
      <section class="form form-confirm layout-form--confirm">
        <div class="form-confirm__inner inner">
          <div class="form-confirm__title">
            <div class="page-mv-title__en page-mv-title__en--uppercase">
              confirm
            </div>
            <h1 class="page-mv-title__ja">確認画面</h1>
          </div>



          <div id="formWrap">
            <?php if ($empty_flag == 1) { ?>
            <div>
              <p class="form-confirm__announce">入力にエラーがあります。下記をご確認の上「戻る」ボタンにて修正をお願い致します。</p>
              <?php echo $errm; ?><br /><br /><input type="button" value=" 前画面に戻る " onClick="history.back()">
            </div>
            <?php } else { ?>
            <p class="form-confirm__announce">以下の内容で間違いがなければ、<br class="sp-view">「送信する」ボタンを押してください。</p>
            <form class="form-confirm__form" action="<?php echo h($_SERVER['SCRIPT_NAME']); ?>" method="POST">
              <table class="formTable">
                <?php echo confirmOutput($_POST); //入力内容を表示
                    ?>
              </table>
              <div class="form-confirm__btn-wrap"><input type="hidden" name="mail_set" value="confirm_submit">
                <input type="hidden" name="httpReferer" value="<?php echo h($_SERVER['HTTP_REFERER']); ?>">
                <input type="button" value="入力画面に戻る" onClick="history.back()">
                <input type="submit" value="　送信する　">
              </div>
            </form>
            <?php } ?>
          </div><!-- /formWrap -->

        </div>
      </section>
    </div>
  </main>

  <!-- ▲ *********** 送信内容確認部　※編集は自己責任で ************ ▲-->

  <!-- ▼ Footerその他コンテンツなど　※編集可 ▼-->
  <footer class="footer layout-footer">
    <div class="footer__bg">
      <div class="footer__inner inner">
        <ul class="footer__sns-list">
          <li class="footer__sns-item">
            <a href="coming.html">
              <img src="./images/common/instagram_logo.svg" alt="instagram_logo" />
            </a>
          </li>
          <li class="footer__sns-item">
            <a href="coming.html">
              <img src="./images/common/X_logo.svg" alt="X_logo" />
            </a>
          </li>
        </ul>
        <div class="footer__address-list">
          <div class="footer__logo">
            <a href="index.html">
              <img src="./images/common/logo.svg" alt="REPRO-logo" />
            </a>
          </div>
          <div class="footer__address-texts">
            <div class="footer__address-texts-flex">
              <p>本&emsp;&emsp;社&emsp;</p>
              <p>
                〒564-0052 大阪府吹田市広芝町3-29&emsp;<br class="sp-view" />TEL 06-6170-6780 FAX 06-6170-6782
              </p>
            </div>
            <div class="footer__address-texts-flex">
              <p>京都支店&emsp;</p>
              <p>
                〒601-8414 京都府京都市南区西九条蔵王町53-1&emsp;<br class="sp-view" />TEL 075-874-6669 FAX 075-874-6569
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <nav class="footer__nav js-footer-nav">
      <div class="footer__nav-bg">
        <div class="footer__nav-wrap">
          <ul class="footer__nav-list--left">
            <li class="footer__nav-item--big">
              <a href="order-house.html">-&emsp;&emsp;注文住宅・オーダーハウス</a>
            </li>
            <li class="footer__nav-item--small">
              <a href="wood-house.html">-&emsp;&emsp; 木の家</a>
            </li>
            <li class="footer__nav-item--small">
              <a href="one-story-house.html">-&emsp;&emsp; 平屋の家</a>
            </li>
            <li class="footer__nav-item--small">
              <a href="high-house-in-the-sky.html">-&emsp;&emsp; 天井の高い家</a>
            </li>
            <li class="footer__nav-item--small">
              <a href="three-story-house.html">-&emsp;&emsp; 3階建ての家</a>
            </li>
          </ul>
          <ul class="footer__nav-list--right">
            <li class="footer__nav-item--big">
              <a href="renovation-house.html">-&emsp;&emsp;リフォーム・リノベーション</a>
            </li>
            <li class="footer__nav-item--big">
              <a href="asolie-house.html">-&emsp;&emsp;アソリエ・サンワカンパニー</a>
            </li>
            <li class="footer__nav-item--big">
              <a href="about.html">-&emsp;&emsp;リプロハウスについて</a>
            </li>
            <li class="footer__nav-item--big">
              <a href="company.html">-&emsp;&emsp;会社概要</a>
            </li>
            <li class="footer__nav-item--big">
              <a href="coming.html">-&emsp;&emsp;モデルハウスのお申し込み</a>
              >
            </li>
          </ul>
        </div>
        <div class="footer__nav-btn">
          <a href="coming.html" class="footer__nav-btn-bg"><span>ご相談</span></a>
        </div>
        <div class="footer__copyright">
          <small>&copy;2024 REPROHOUSE co.,ltd.</small>
        </div>
      </div>
    </nav>
    <div class="layout-page-top js-page-top">
      <a href="#" class="page-top"></a>
    </div>
  </footer>
  <!-- JS -->
  <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
  <script defer src="./js/script.js"></script>

</body>

</html>
<?php
  /* ▲▲▲送信確認画面のレイアウト　※オリジナルのデザインも適用可能▲▲▲　*/
}

if (($jumpPage == 0 && $sendmail == 1) || ($jumpPage == 0 && ($confirmDsp == 0 && $sendmail == 0))) {

  /* ▼▼▼送信完了画面のレイアウト　編集可 ※送信完了後に指定のページに移動しない場合のみ表示▼▼▼　*/
?>
<!DOCTYPE HTML>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
  <meta name="format-detection" content="telephone=no">
  <title>完了画面</title>
</head>

<body>

  <div align="center">
    <?php if ($empty_flag == 1) { ?>
    <h4>入力にエラーがあります。下記をご確認の上「戻る」ボタンにて修正をお願い致します。</h4>
    <div style="color:red"><?php echo $errm; ?></div>
    <br /><br /><input type="button" value=" 前画面に戻る " onClick="history.back()">
  </div>
</body>

</html>
<?php } else { ?>
送信ありがとうございました。<br />
送信は正常に完了しました。<br /><br />
<a href="<?php echo $site_top; ?>">トップページへ戻る&raquo;</a>
</div>
<?php copyright(); ?>
<!--  CV率を計測する場合ここにAnalyticsコードを貼り付け -->
</body>

</html>
<?php
        /* ▲▲▲送信完了画面のレイアウト 編集可 ※送信完了後に指定のページに移動しない場合のみ表示▲▲▲　*/
      }
    }
    //確認画面無しの場合の表示、指定のページに移動する設定の場合、エラーチェックで問題が無ければ指定ページヘリダイレクト
    else if (($jumpPage == 1 && $sendmail == 1) || $confirmDsp == 0) {
      if ($empty_flag == 1) { ?>
<div align="center">
  <h4>入力にエラーがあります。下記をご確認の上「戻る」ボタンにて修正をお願い致します。</h4>
  <div style="color:red"><?php echo $errm; ?></div><br /><br /><input type="button" value=" 前画面に戻る "
    onClick="history.back()">
</div>
<?php
      } else {
        header("Location: " . $thanksPage);
      }
    }

    // 以下の変更は知識のある方のみ自己責任でお願いします。

    //----------------------------------------------------------------------
    //  関数定義(START)
    //----------------------------------------------------------------------
    function checkMail($str)
    {
      $mailaddress_array = explode('@', $str);
      if (preg_match("/^[\.!#%&\-_0-9a-zA-Z\?\/\+]+\@[!#%&\-_0-9a-zA-Z]+(\.[!#%&\-_0-9a-zA-Z]+)+$/", "$str") && count($mailaddress_array) == 2) {
        return true;
      } else {
        return false;
      }
    }
    function h($string)
    {
      global $encode;
      return htmlspecialchars($string, ENT_QUOTES, $encode);
    }
    function sanitize($arr)
    {
      if (is_array($arr)) {
        return array_map('sanitize', $arr);
      }
      return str_replace("\0", "", $arr);
    }
    //Shift-JISの場合に誤変換文字の置換関数
    function sjisReplace($arr, $encode)
    {
      foreach ($arr as $key => $val) {
        $key = str_replace('＼', 'ー', $key);
        $resArray[$key] = $val;
      }
      return $resArray;
    }
    //送信メールにPOSTデータをセットする関数
    function postToMail($arr)
    {
      global $hankaku, $hankaku_array;
      $resArray = '';
      foreach ($arr as $key => $val) {
        $out = '';
        if (is_array($val)) {
          foreach ($val as $key02 => $item) {
            //連結項目の処理
            if (is_array($item)) {
              $out .= connect2val($item);
            } else {
              $out .= $item . ', ';
            }
          }
          $out = rtrim($out, ', ');
        } else {
          $out = $val;
        } //チェックボックス（配列）追記ここまで

        if (version_compare(PHP_VERSION, '5.1.0', '<=')) { //PHP5.1.0以下の場合のみ実行（7.4でget_magic_quotes_gpcが非推奨になったため）
          if (get_magic_quotes_gpc()) {
            $out = stripslashes($out);
          }
        }

        //全角→半角変換
        if ($hankaku == 1) {
          $out = zenkaku2hankaku($key, $out, $hankaku_array);
        }
        if ($out != "confirm_submit" && $key != "httpReferer") {
          $resArray .= "【 " . h($key) . " 】 " . h($out) . "\n";
        }
      }
      return $resArray;
    }
    //確認画面の入力内容出力用関数
    function confirmOutput($arr)
    {
      global $hankaku, $hankaku_array, $useToken, $confirmDsp, $replaceStr;
      $html = '';
      foreach ($arr as $key => $val) {
        $out = '';
        if (is_array($val)) {
          foreach ($val as $key02 => $item) {
            //連結項目の処理
            if (is_array($item)) {
              $out .= connect2val($item);
            } else {
              $out .= $item . ', ';
            }
          }
          $out = rtrim($out, ', ');
        } else {
          $out = $val;
        } //チェックボックス（配列）追記ここまで

        if (version_compare(PHP_VERSION, '5.1.0', '<=')) { //PHP5.1.0以下の場合のみ実行（7.4でget_magic_quotes_gpcが非推奨になったため）
          if (get_magic_quotes_gpc()) {
            $out = stripslashes($out);
          }
        }

        //全角→半角変換
        if ($hankaku == 1) {
          $out = zenkaku2hankaku($key, $out, $hankaku_array);
        }

        $out = nl2br(h($out)); //※追記 改行コードを<br>タグに変換
        $key = h($key);
        $out = str_replace($replaceStr['before'], $replaceStr['after'], $out); //機種依存文字の置換処理

        $html .= "<tr><th>" . $key . "</th><td>" . $out;
        $html .= '<input type="hidden" name="' . $key . '" value="' . str_replace(array("<br />", "<br>"), "", $out) . '" />';
        $html .= "</td></tr>\n";
      }
      //トークンをセット
      if ($useToken == 1 && $confirmDsp == 1) {
        $token = sha1(uniqid(mt_rand(), true));
        $_SESSION['mailform_token'] = $token;
        $html .= '<input type="hidden" name="mailform_token" value="' . $token . '" />';
      }

      return $html;
    }

    //全角→半角変換
    function zenkaku2hankaku($key, $out, $hankaku_array)
    {
      global $encode;
      if (is_array($hankaku_array) && function_exists('mb_convert_kana')) {
        foreach ($hankaku_array as $hankaku_array_val) {
          if ($key == $hankaku_array_val) {
            $out = mb_convert_kana($out, 'a', $encode);
          }
        }
      }
      return $out;
    }
    //配列連結の処理
    function connect2val($arr)
    {
      $out = '';
      foreach ($arr as $key => $val) {
        if ($key === 0 || $val == '') { //配列が未記入（0）、または内容が空のの場合には連結文字を付加しない（型まで調べる必要あり）
          $key = '';
        } elseif (strpos($key, "円") !== false && $val != '' && preg_match("/^[0-9]+$/", $val)) {
          $val = number_format($val); //金額の場合には3桁ごとにカンマを追加
        }
        $out .= $val . $key;
      }
      return $out;
    }

    //管理者宛送信メールヘッダ
    function adminHeader($post_mail, $BccMail)
    {
      global $from;
      $header = "From: $from\n";
      if ($BccMail != '') {
        $header .= "Bcc: $BccMail\n";
      }
      if (!empty($post_mail)) {
        $header .= "Reply-To: " . $post_mail . "\n";
      }
      $header .= "Content-Type:text/plain;charset=iso-2022-jp\nX-Mailer: PHP/" . phpversion();
      return $header;
    }
    //管理者宛送信メールボディ
    function mailToAdmin($arr, $dsp_name, $encode)
    {
      $adminBody .= $userBody = h($arr[$dsp_name]) . " 様より\n\n";
      $adminBody .= "以下の内容でお問い合わせが入りました。\n";
      $adminBody .= "ご対応よろしくお願いいたします。\n\n\n";
      $adminBody .= "＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝\n\n\n";
      $adminBody .= postToMail($arr); //POSTデータを関数からセット
      $adminBody .= "\n\n＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝\n\n\n";
      $adminBody .= "このメールはリプロハウス（https://reprohouse.jp/）から送信されました。";
      return mb_convert_encoding($adminBody, "JIS", $encode);
    }

    //ユーザ宛送信メールヘッダ
    function userHeader($refrom_name, $to, $encode)
    {
      $reheader = "From: ";
      if (!empty($refrom_name)) {
        $default_internal_encode = mb_internal_encoding();
        if ($default_internal_encode != $encode) {
          mb_internal_encoding($encode);
        }
        $reheader .= mb_encode_mimeheader($refrom_name) . " <" . $to . ">\nReply-To: " . $to;
      } else {
        $reheader .= "$to\nReply-To: " . $to;
      }
      $reheader .= "\nContent-Type: text/plain;charset=iso-2022-jp\nX-Mailer: PHP/" . phpversion();
      return $reheader;
    }
    //ユーザ宛送信メールボディ
    function mailToUser($arr, $dsp_name, $remail_text, $mailFooterDsp, $mailSignature, $encode)
    {
      $userBody = '';
      if (isset($arr[$dsp_name])) $userBody = h($arr[$dsp_name]) . " 様\n";
      $userBody .= $remail_text;
      $userBody .= "\n＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝\n\n\n";
      $userBody .= postToMail($arr); //POSTデータを関数からセット
      $userBody .= "\n\n＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝\n\n";
      if ($mailFooterDsp == 1) $userBody .= $mailSignature;
      return mb_convert_encoding($userBody, "JIS", $encode);
    }
    //必須チェック関数
    function requireCheck($require)
    {
      $res['errm'] = '';
      $res['empty_flag'] = 0;
      foreach ($require as $requireVal) {
        $existsFalg = '';
        foreach ($_POST as $key => $val) {
          if ($key == $requireVal) {

            //連結指定の項目（配列）のための必須チェック
            if (is_array($val)) {
              $connectEmpty = 0;
              foreach ($val as $kk => $vv) {
                if (is_array($vv)) {
                  foreach ($vv as $kk02 => $vv02) {
                    if ($vv02 == '') {
                      $connectEmpty++;
                    }
                  }
                }
              }
              if ($connectEmpty > 0) {
                $res['errm'] .= "<p class=\"error_messe\">【" . h($key) . "】は必須項目です。</p>\n";
                $res['empty_flag'] = 1;
              }
            }
            //デフォルト必須チェック
            elseif ($val == '') {
              $res['errm'] .= "<p class=\"error_messe\">【" . h($key) . "】は必須項目です。</p>\n";
              $res['empty_flag'] = 1;
            }

            $existsFalg = 1;
            break;
          }
        }
        if ($existsFalg != 1) {
          $res['errm'] .= "<p class=\"error_messe\">【" . $requireVal . "】が未選択です。</p>\n";
          $res['empty_flag'] = 1;
        }
      }

      return $res;
    }
    //リファラチェック
    function refererCheck($Referer_check, $Referer_check_domain)
    {
      if ($Referer_check == 1 && !empty($Referer_check_domain)) {
        if (strpos($_SERVER['HTTP_REFERER'], $Referer_check_domain) === false) {
          return exit('<p align="center">リファラチェックエラー。フォームページのドメインとこのファイルのドメインが一致しません</p>');
        }
      }
    }
    function copyright()
    {
      echo '<a style="display:block;text-align:center;margin:15px 0;font-size:11px;color:#aaa;text-decoration:none" href="http://www.php-factory.net/" target="_blank">- PHP工房 -</a>';
    }
    //----------------------------------------------------------------------
    //  関数定義(END)
    //----------------------------------------------------------------------
?>