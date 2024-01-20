jQuery(function ($) {
  // スムーズスクロール
  $('a[href^="#"]').click(function () {
    var href = $(this).attr("href");
    var target = $(href);
    var position = target.offset().top;
    var headerHeight = $("header").outerHeight();
    $("body,html")
      .stop()
      .animate({ scrollTop: position - headerHeight }, 300);
    return false;
  });

  // ハンバーガーメニュー
  $(function () {
    $(".js-hamburger").on("click", function () {
      $(this).toggleClass("is-open");
      if ($(this).hasClass("is-open")) {
        openDrawer();
      } else {
        closeDrawer();
      }
    });

    // backgroundまたはページ内リンクをクリックで閉じる
    $(".js-drawer a[href]").on("click", function () {
      closeDrawer();
    });
    $(".js-overview").on("click", function () {
      closeDrawer();
    });
  });

  function openDrawer() {
    $("body").addClass("is-open");
    $(".js-hamburger").addClass("is-open");
    $(".js-drawer").fadeIn();
    $(".js-overview").addClass("is-open");
  }

  function closeDrawer() {
    $("body").removeClass("is-open");
    $(".js-hamburger").removeClass("is-open");
    $(".js-drawer").fadeOut();
    $(".js-overview").removeClass("is-open");
  }

  // MV過ぎたらヘッダー背景色変化,トップボタン出現
  const pageTop = $(".js-page-top");
  pageTop.hide();
  $(window).on("scroll", function () {
    mvHeight = $(".js-mv").height();
    if ($(window).scrollTop() > mvHeight) {
      $(".js-header").addClass("is-scroll");
      $(".js-header .header__nav-item a").addClass("is-scroll");
      $(".js-page-top").addClass("is-scroll");
      $(".js-page-top").fadeIn();
    } else {
      $(".js-header").removeClass("is-scroll");
      $(".js-header .header__nav-item a").removeClass("is-scroll");
      $(".js-page-top").removeClass("is-scroll");
      $(".js-page-top").fadeOut();
    }
  });

  // TOPに戻るボタン
  $(".js-page-top").on("click", function () {
    $("body,html").animate(
      {
        scrollTop: 0,
      },
      500
    );
    return false;
  });

  //フッターの指定位置で止まる
  $(window).on("scroll", function () {
    var scroll = $(window).scrollTop();
    var btnHeight = $(".js-page-top").height();
    var wH = window.innerHeight;
    var footerPos = $(".js-footer-nav").offset().top;
    if (scroll + wH >= footerPos + btnHeight + 15) {
      var pos = scroll + wH - footerPos - btnHeight;
      $(".js-page-top").css("bottom", pos);
    } else {
      if ($(".layout-page-top").hasClass("is-scroll")) {
        $(".js-page-top").css("bottom", "15px");
      }
    }
  });

  // news-swiper
  var news_swiper = new Swiper(".js-news-swiper", {
    // loop: true,
    speed: 1000,
    direction: "vertical",
    autoplay: {
      delay: 3000,
      disableOnInteraction: false,
    },
    pagination: {
      el: ".swiper-pagination",
      type: "fraction",
      clickable: true,
    },
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
  });

  //画面に要素が入ったらアクション
  // const fadeIn = $(".js-fadeIn");
  // fadeIn.hide();
  $(window).on("scroll", function () {
    $(".js-fadeIn").each(function () {
      let elemPos = $(this).offset().top;
      let scroll = $(window).scrollTop();
      let windowHeight = $(window).height();

      if (scroll >= elemPos - windowHeight) {
        $(this).addClass("active");
      } else {
      }
    });
  });

  //タブの実装
  $(function () {
    $(".contact-tab .tab__menu-item").click(function () {
      var index = $(".contact-tab .tab__menu-item").index(this);
      $(".contact-tab .tab__menu-item, .form .js-tab-panel").removeClass(
        "tab-active"
      );
      $(this).addClass("tab-active");
      $(".form .js-tab-panel").eq(index).addClass("tab-active");
    });
  });

  //タブへダイレクトリンクの実装
  $(function () {
    //location.hashで#以下を取得 変数hashに格納
    var hash = location.hash;
    //hashの中に#tab-panel～が存在するか調べる。
    hash = (hash.match(/^#tab-panel-\d+$/) || [])[0];

    //hashに要素が存在する場合、hashで取得した文字列（#tab2,#tab3等）から#より後を取得(tab2,tab3)
    if (hash.length) {
      var tabname = hash.slice(1);
    } else {
      // 要素が存在しなければtabnameにtab-panel-1を代入する
      var tabname = "tab-panel-1";
    }
    console.log(tabname);
    console.log(hash.length);

    //コンテンツを一度すべて非表示にし、
    // $(".form__form--contact").css("display", "none");
    //一度タブについているクラスtab-activeを消し、
    $(".form__form--contact").removeClass("tab-active");

    var tabno = $("section.form form#" + tabname).index();
    console.log(tabno);


    //クリックされたタブと同じ順番のコンテンツを表示します。
    $(".form__form--contact").eq(tabno).fadeIn();

    //クリックされたタブのみにクラスtab-activeをつけます。
    $("section.form form").eq(tabno).addClass("tab-active");
    $("ul.contact-tab__menu li").eq(tabno).addClass("tab-active");


  });
});
