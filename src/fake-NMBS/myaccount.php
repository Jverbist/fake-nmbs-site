<?php
// myaccount.php √ë runs the intentionally vulnerable query and renders results nicely

session_start();

// Avoid mysqli throwing fatal exceptions; we want to capture the SQL error text.
mysqli_report(MYSQLI_REPORT_OFF);

// Defaults so header/avatar don√ït break when payload returns no rows.
$firstName = '';
$lastName  = '';

$host   = 'mysql';   // Docker service name
$dbUser = 'admin';
$dbPass = 'admin';
$dbName = 'mydb';

$conn = new mysqli($host, $dbUser, $dbPass, $dbName);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/**
 * Take raw payload from login.php (kept intentionally un-sanitized).
 * The crafted comment --  allows classic injections exactly as in your steps.
 */
$payload = $_SESSION['userName'] ?? '';
$sql = "SELECT * FROM users WHERE email = '$payload' -- '";

// Run vulnerable query
$result = $conn->query($sql);

// Build HTML block shown in the white area
$sqliDumpHtml = '';
if ($result === false) {
    // Step 1 expectation: a lone ' shows a SQL error
    $sqliDumpHtml = '
        <div class="sqli-dump sqli-error">
            <h2>Injected query error</h2>
            <div class="sqli-tip">Payload: <code>'.htmlspecialchars($payload).'</code></div>
            <pre>'.htmlspecialchars($conn->error).'</pre>
        </div>';
} else {
    if ($result->num_rows > 0) {
        // Use first row to populate welcome text
        $firstRow  = $result->fetch_assoc();
        $firstName = $firstRow['first_name'] ?? '';
        $lastName  = $firstRow['last_name']  ?? '';

        // Render a nice scrollable table with all returned rows
        $cols = array_keys($firstRow);
        $thead = '';
        foreach ($cols as $c) { $thead .= '<th>'.htmlspecialchars($c).'</th>'; }

        $tbody = '<tr>';
        foreach ($cols as $c) { $tbody .= '<td>'.htmlspecialchars((string)$firstRow[$c]).'</td>'; }
        $tbody .= '</tr>';

        while ($row = $result->fetch_assoc()) {
            $tbody .= '<tr>';
            foreach ($cols as $c) {
                $tbody .= '<td>'.htmlspecialchars((string)$row[$c]).'</td>';
            }
            $tbody .= '</tr>';
        }

        $sqliDumpHtml = '
            <div class="sqli-dump">
                <h2> <style>
    body{font:16px/1.45 system-ui,Segoe UI,Roboto,Helvetica,Arial,sans-serif;margin:0;background:#fff;color:#222}
    header{padding:24px 16px;border-bottom:1px solid #eee;position:sticky;top:0;background:#fff}
    .container{max-width:1200px;margin:0 auto;padding:0 16px}
    .welcome{font-size:28px;margin:0}
    .account__name{display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:50%;background:#6b2ea1;color:#fff;font-weight:700;margin-right:8px}
    /* SQLi dump panel */
    .sqli-dump{max-width:1100px;margin:24px auto;padding:16px;background:#fff;border:1px solid #e8e8e8;border-radius:10px}
    @media (min-width: 992px){ .sqli-dump{ margin-left:320px; margin-right:32px; } } /* clear fixed purple sidebar */
    .sqli-error{border-color:#ffb3b3;background:#fff7f7}
    .sqli-tip{font-size:13px;color:#666;margin:6px 0 10px}
    .sqli-tablewrap{overflow:auto;max-height:60vh}
    .sqli-table{width:100%;border-collapse:collapse}
    .sqli-table th,.sqli-table td{border:1px solid #ddd;padding:8px;vertical-align:top;font-size:14px}
    .sqli-table th{background:#fafafa;position:sticky;top:0}
    .sqli-empty{color:#666}
    .sqli-note{font-size:12px;color:#666;margin-top:10px}
  </style>ult</h2>
                <div class="sqli-tip">
                    Payload: <code>'.htmlspecialchars($payload).'</code>
                </div>
                <div class="sqli-tablewrap">
                    <table class="sqli-table">
                        <thead><tr>'.$thead.'</tr></thead>
                        <tbody>'.$tbody.'</tbody>
                    </table>
                </div>
                <p class="sqli-note">
                    
                </p>
            </div>';
    } else {
        $sqliDumpHtml = '
            <div class="sqli-dump">
                <h2></h2>
                <div class="sqli-tip">Payload: <code>'.htmlspecialchars($payload).'</code></div>
                <div class="sqli-empty">No rows returned.</div>
            </div>';
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="nl" xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>My NMBS: Je klantenprofiel | NMBS</title>
<style>
    body{font:16px/1.45 system-ui,Segoe UI,Roboto,Helvetica,Arial,sans-serif;margin:0;background:#fff;color:#222}
    header{padding:24px 16px;border-bottom:1px solid #eee;position:sticky;top:0;background:#fff}
    .container{max-width:1200px;margin:0 auto;padding:0 16px}
    .welcome{font-size:28px;margin:0}
    .account__name{display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:50%;background:#6b2ea1;color:#fff;font-weight:700;margin-right:8px}
    /* SQLi dump panel */
    .sqli-dump{max-width:1100px;margin:24px auto;padding:16px;background:#fff;border:1px solid #e8e8e8;border-radius:10px}
    @media (min-width: 992px){ .sqli-dump{ margin-left:320px; margin-right:32px; } } /* clear fixed purple sidebar */
    .sqli-error{border-color:#ffb3b3;background:#fff7f7}
    .sqli-tip{font-size:13px;color:#666;margin:6px 0 10px}
    .sqli-tablewrap{overflow:auto;max-height:60vh}
    .sqli-table{width:100%;border-collapse:collapse}
    .sqli-table th,.sqli-table td{border:1px solid #ddd;padding:8px;vertical-align:top;font-size:14px}
    .sqli-table th{background:#fafafa;position:sticky;top:0}
    .sqli-empty{color:#666}
    .sqli-note{font-size:12px;color:#666;margin-top:10px}
  </style>
<!-- Adobe Static Data Layer -->
<script>
dataLayer = [{event: "pageload", environment: "www.belgiantrain.be", platform: "desktop", page: {
       path: "/my-account", language: "nl", siteSections: "[my-account]", query: "",
    httpStatus: "200", httpError: "False" }, profile: { identifier: "hK/zRDDJHnhOKNb+WaB1HV/diD5KkVVHzyPORzj355E=", heid: "EF753EA35B80F30D33D4EEA8E3C4D592E16819CE9481047A859CAE14B948FA2D" }}] || [];
</script>
<!-- End Adobe Static Data Layer -->
<script type="text/javascript">try {window.didomiConfig = {languages: {default: document.documentElement.lang, enabled: [document.documentElement.lang]}}} catch (e) {window.didomiConfig = {languages: {default: 'en', enabled: ['nl', 'fr', 'en', 'de']}}};
window.gdprAppliesGlobally=true;(function(){function a(e){if(!window.frames[e]){if(document.body&&document.body.firstChild){var t=document.body;var n=document.createElement("iframe");n.style.display="none";n.name=e;n.title=e;t.insertBefore(n,t.firstChild)}
else{setTimeout(function(){a(e)},5)}}}function e(n,r,o,c,s){function e(e,t,n,a){if(typeof n!=="function"){return}if(!window[r]){window[r]=[]}var i=false;if(s){i=s(e,t,n)}if(!i){window[r].push({command:e,parameter:t,callback:n,version:a})}}e.stub=true;function t(a){if(!window[n]||window[n].stub!==true){return}if(!a.data){return}
var i=typeof a.data==="string";var e;try{e=i?JSON.parse(a.data):a.data}catch(t){return}if(e[o]){var r=e[o];window[n](r.command,r.parameter,function(e,t){var n={};n[c]={returnValue:e,success:t,callId:r.callId};a.source.postMessage(i?JSON.stringify(n):n,"*")},r.version)}}
if(typeof window[n]!=="function"){window[n]=e;if(window.addEventListener){window.addEventListener("message",t,false)}else{window.attachEvent("onmessage",t)}}}e("__tcfapi","__tcfapiBuffer","__tcfapiCall","__tcfapiReturn");a("__tcfapiLocator");(function(e){
  var t=document.createElement("script");t.id="spcloader";t.type="text/javascript";t.async=true;t.src="https://sdk.privacy-center.org/"+e+"/loader.js?target="+document.location.hostname;t.charset="utf-8";var n=document.getElementsByTagName("script")[0];n.parentNode.insertBefore(t,n)})("41d652ec-4b4f-4722-8de8-53f39705d783")})();</script>
<link rel="preconnect" href="https://www.googletagmanager.com">
<link rel="preconnect" href="https://www.google-analytics.com">
<link rel="preconnect" href="https://vars.hotjar.com">
<link rel="preconnect" href="https://script.hotjar.com">
<link rel="preconnect" href="https://static.hotjar.com">
<link rel="preload" href="https://www.belgiantrain.be/content/public/fonts/CircularStd-Book.woff" as="font" crossorigin="anonymous">
<link rel="preload" href="https://www.belgiantrain.be/content/public/fonts/CircularStd-Bold.woff2" as="font" crossorigin="anonymous">
<link rel="preload" href="https://www.belgiantrain.be/content/public/fonts/CircularStd-Medium.woff2" as="font" crossorigin="anonymous">
<link rel="preload" href="https://www.belgiantrain.be/content/public/fonts/CircularStd-Book.woff2" as="font" crossorigin="anonymous">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="x-ua-compatible" content="ie=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Toegang tot uw Mijn NMBS-account hier: verleng je abonnement, profiteer van gratis wifi in het station of download je passagierscertificaten." />
    <link rel="canonical" href="https://www.belgiantrain.be/nl/my-account" />
<meta name="robots" content="index, follow" />
<meta property="og:url" content="https://www.belgiantrain.be:443/nl/my-account"/>
<meta property="og:type" content="website" />
<meta property="og:locale" content="nl-NL" />
<meta property="og:title" content="My NMBS: Je klantenprofiel | NMBS"/>
<meta property="og:description" />
<meta property="article:author" />
<meta property="twitter:url" content="https://www.belgiantrain.be:443/nl/my-account" />
<meta property="twitter:description" />
<meta property="twitter:creator" />
<meta property="twitter:hashtags" content=""/>
<link rel="stylesheet" href="https://www.belgiantrain.be/content/public/css/main.css?v=638539742520000000">
<!-- Support application insights to measure client side info. -->
<script type="text/plain">
    var appInsights=window.appInsights||function(a){
    function b(a){c[a]=function(){var b=arguments;c.queue.push(function(){c[a].apply(c,b)})}}var c={config:a},d=document,e=window;setTimeout(function(){var b=d.createElement("script");b.src=a.url||"https://az416426.vo.msecnd.net/scripts/a/ai.0.js",d.getElementsByTagName("script")[0].parentNode.appendChild(b)});try{c.cookie=d.cookie}catch(a){}c.queue=[];for(var f=["Event","Exception","Metric","PageView","Trace","Dependency"];f.length;)b("track"+f.pop());if(b("setAuthenticatedUserContext"),b("clearAuthenticatedUserContext"),b("startTrackEvent"),b("stopTrackEvent"),b("startTrackPage"),b("stopTrackPage"),b("flush"),!a.disableExceptionTracking){f="onerror",b("_"+f);var g=e[f];e[f]=function(a,b,d,e,h){var i=g&&g(a,b,d,e,h);return!0!==i&&c["_"+f](a,b,d,e,h),i}}return c
    }({
    instrumentationKey:"fe4f475d-133c-4939-a0bf-82f94c281ec7"
    });
    window.appInsights=appInsights,appInsights.queue&&0===appInsights.queue.length&&appInsights.trackPageView();
</script>
</head>
<body class="">
    <input id="facebookShareUrl" name="FacebookShareUrl" type="hidden" value="https://www.facebook.com/dialog/share" />
<input id="facebookShareAppId" name="FacebookShareAppId" type="hidden" value="3526921284007626" />
<input id="twitterShareUrl" name="TwitterShareUrl" type="hidden" value="https://twitter.com/intent/tweet" />
<header id="top-navigation-bar" class="navigation-bar" style="">
    <div class="navigation-bar__sidebar bg-purple">
<a href="https://www.nmbs.exn.be/NMBS/www.belgiantrain.be/nl.html" class="navigation-bar__logo">
    <svg class="icon" data-id="{E42B7874-C7FD-48A9-85B5-301F11A48923}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-nmbs-logo" />
</svg>
    <span class="sr-only">Home NMBS</span>
</a>
    </div>
    <div class="navigation-bar__main">
        <div class="navigation-bar__left">
<ul class="navigation-bar__items">
                <li class="navigation-bar__item ">
                    <a class="navigation-bar__item-link " href="/nl/search">ticket kopen</a>
                </li>
                <li class="navigation-bar__item ">
                    <a class="navigation-bar__item-link " href="/nl/support/customer-service">klantendienst</a>
                </li>
                <li class="navigation-bar__item ">
                    <a class="navigation-bar__item-link " href="/nl/mobility-for-business/for-employers">business</a>
                </li>
                <li class="navigation-bar__item ">
                    <a href="https://jobs.belgiantrain.be/?locale=nl_NL" rel="noopener noreferrer" class="navigation-bar__item-link " target="_blank">jobs</a>
                </li>
</ul>
        </div>
<div class="navigation-bar__right">
    <ul class="navigation-bar__items">
                <li class="navigation-bar__item LoginStatus">
<a class="button navigation-bar__btn account account--loggedin" title="My&#32;NMBS" href="../fake-NMBS/myaccount.php">    
    <div class="account__name theme-purple">
        <?php echo strtoupper($firstName[0]); ?>
    </div>
    <div class="account__label">
        My NMBS
    </div>
</a>
                </li>
                <li class="navigation-bar__item InbentaSearchNavigationButton">
<div class="navigation-bar__btn navigation-bar__search">
    <a href="/nl/support/search-website" title="Zoek op de website" class="color-shade-dark">
        <svg class="icon" data-id="{7638E5EA-45EF-422B-ABDB-89D5119A191B}" focusable="false" role="img">
  <title>Zoek op de website</title>
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-search" />
</svg>
        <span class="sr-only">Zoek op de website</span>
    </a>
</div>
                </li>
                <li class="navigation-bar__item BasketButton">
<a class="navigation-bar__btn basket-btn" href="/nl/search" title="Winkelmandje">
    <svg class="icon" data-id="{E0EDF48D-D199-478A-8F3D-2B57DE5B4BA1}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-basket" />
</svg>
    <span class="sr-only">Winkelmandje</span>
    <div class="basket-status">
    </div>
</a>
                </li>
                <li class="navigation-bar__item LanguageSwitchDropDown">
<div class="navigation-bar__item-langswitch">
    <div class="js-dropdown dropdown dropdown--outline" data-autoclose="true">
        <a href="#" class="link link--iconright dropdown__trigger" aria-expanded="false">
            <svg class="icon icon--12 icon--dropdown" data-id="{2C0D660B-B0ED-405A-A664-82F692588137}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-down" />
</svg>
nl        </a>
        <ul class="dropdown__list">
                <li class="dropdown__item">
                    <a href="/fr/my-account">fr</a>
                </li>
                <li class="dropdown__item">
                    <a href="/en/my-account">en</a>
                </li>
                <li class="dropdown__item">
                    <a href="/de/my-account">de</a>
                </li>
        </ul>
    </div>
</div>
                </li>
                <li class="navigation-bar__item MainMenuButton">
                    <button class="navigation-bar__btn menu js-open-navigation" title="Open het menu">
    <div class="menu__label">Menu</div>
    <svg class="icon menu__icon" data-id="{71012470-03F3-4B9E-9618-BFB1E91C4175}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-menu" />
</svg>
</button>
                </li>
    </ul>
</div>
    </div>
</header>
 <!-- ===== SQL DUMP GOES RIGHT AFTER THE WELCOME BANNER ===== -->
  <div class="nmbs-sqli">
    <?php if (!empty($rows)): ?>
      <div class="scroll">
        <table>
          <thead>
            <tr>
              <?php foreach (array_keys($rows[0]) as $col): ?>
                <th><?= htmlentities($col) ?></th>
              <?php endforeach; ?>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($rows as $r): ?>
              <tr>
                <?php foreach ($r as $c): ?>
                  <td><?= htmlentities((string)$c) ?></td>
                <?php endforeach; ?>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php elseif (!empty($sqlError) && isset($_GET['debug'])): ?>
      <pre class="sqlerr"><?= htmlentities($sqlError) ?></pre>
    <?php endif; ?>
  </div>
  <!-- ===== end dump ===== -->
<div class="nav-sidebar__container nav-sidebar--navigation " style="">
    <div class="nav-sidebar__header">
        <div class="nav-sidebar__logo nav-sidebar--show-close">
<a href="https://www.nmbs.exn.be/NMBS/www.belgiantrain.be/nl.html" class="navigation-bar__logo">
    <svg class="icon" data-id="{E42B7874-C7FD-48A9-85B5-301F11A48923}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-nmbs-logo" />
</svg>
    <span class="sr-only">Home NMBS</span>
</a>
<a href="#" class="nav-sidebar__btn-close link link--iconright">
    <svg class="icon  icon--12" data-id="{5FD39E00-7D22-44B2-8210-5CFDACCFA9E9}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-close" />
</svg>
    Sluiten
</a>
<a href="#" class="nav-sidebar__btn-back link">
    <svg class="icon  icon--12" data-id="{52732675-6DB6-43F4-9B1D-7076D1FB19D2}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-left" />
</svg>
    Terug
</a>
        </div>
<div class="nav-sidebar__cta only--small theme-blue">
<div class="inbenta-nmbs">
    <!-- PE html -->
        <!-- Element where the SDK and KM will be displayed -->
        <div id="inbenta">
            <div id="search-boxsh"></div>
<div id="autocompletersh"></div>
<div id="resultssh"></div>
        </div>
        <input type="hidden" class="hdn_inbenta_css_popular" />
</div>
<a class="link  " href="/nl/search"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>                    <span>koop ticket</span>
</a><a class="link  " href="https://m.me/NMBS"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>                    <span>hulp nodig?</span>
</a></div>
    </div>
<aside class="nav-sidebar">
    <div class="nav-sidebar__block">
        <div class="nav-sidebar__content">
            <ul class="navigation__list">
                    <li class="active">
                        <a href="#" data-id="Reisinfo">Reisinfo</a>
                    </li>
                    <li >
                        <a href="#" data-id="Tickets &amp; abonnementen">Tickets &amp; abonnementen</a>
                    </li>
                    <li >
                        <a href="#" data-id="Stationsinformatie">Stationsinformatie</a>
                    </li>
                    <li >
                        <a href="#" data-id="Reisidee√É¬´n">Reisidee√É¬´n</a>
                    </li>
                    <li >
                        <a href="#" data-id="Voor werkgevers en werknemers">Voor werkgevers en werknemers</a>
                    </li>
                    <li >
                        <a href="#" data-id="Internationale reizen">Internationale reizen</a>
                    </li>
                    <li >
                        <a href="#" data-id="Klantendienst">Klantendienst</a>
                    </li>
                    <li >
                        <a href="#" data-id="Diensten voor derden en RRS">Diensten voor derden en RRS</a>
                    </li>
                    <li >
                        <a href="#" data-id="Jobs">Jobs</a>
                    </li>
                    <li >
                        <a href="#" data-id="Over NMBS">Over NMBS</a>
                    </li>
            </ul>
        </div>

        <div class="nav-sidebar__footer">
            <div class="nav-sidebar__baseline nav-sidebar__baseline--white"></div>
        </div>
    </div>
</aside>

<section class="nav-sidebar-panel  nav-sidebar-panel--navigation  js-panel  nav-sidebar-panel--large  nav-sidebar-panel--fixed-open ">
    <a href="javascript:void(0);" class="link  link--iconright  nav-sidebar-panel__close  sidebar__btn-close">
        <svg class="icon icon--12" data-id="{5FD39E00-7D22-44B2-8210-5CFDACCFA9E9}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-close" />
</svg>
        Sluiten
    </a>
    <div class="nav-sidebar-panel__content js-panel-content">
<div class="inbenta-nmbs">

    <!-- PE html -->
        <!-- Element where the SDK and KM will be displayed -->
        <div id="inbenta">
            <div id="search-boxsh"></div>
<div id="autocompletersh"></div>
<div id="resultssh"></div>

        </div>
        <input type="hidden" class="hdn_inbenta_css_popular" />
</div>
                    <div class="navigation__panel-item active" data-id="Reisinfo" style=opacity:1>
                        <div class="nav-sidebar-panel__header">
                            <div class="nav-sidebar-panel__header-title row">
                                <h4 class="h1">Reisinfo </h4>
                            </div>
                        </div>
                        <div class="row gutter-lg-40">
                            <div class="col col-md-12 col-lg-6">
            <div class="navigation__item " data-tag-list="Main Menu Links Group">
                <h5 class="navigation__item-title navigation__item-title--disabled">
                        <p>Actueel</p>
                </h5>
                <ul class="navigation__links">
                                <li class="navigation__links-item ">
                                    <a href="/nl/travel-info/current/ongoing-disturbances-and-works" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>storingen en werken</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/travel-info/current/current-departure-times" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>realtime dienstregeling</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="https://www.belgiantrain.be/nl/support/customer-service/delay-certificate" class="link"><svg class="icon icon--12" data-id="{9AD7865D-2AAF-48BC-92CF-F5C2F69DADC9}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-external" />
</svg>vertragingsattest</a>
                                </li>
                </ul>
            </div>
            <div class="navigation__item " data-tag-list="Main Menu Links Group">
                <h5 class="navigation__item-title navigation__item-title--disabled">
                        <p>Diensten in de trein</p>
                </h5>
                <ul class="navigation__links">
                                <li class="navigation__links-item ">
                                    <a href="/nl/tickets-and-railcards/bike-ticket" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>met de fiets op de trein</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/travel-info/services-in-the-train/first-or-second-class" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>reizen in 1e of 2e klas</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/travel-info/services-in-the-train/luggage-and-pets" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>reizen met bagage en huisdieren</a>
                                </li>
                </ul>
            </div>
                            </div>
                            <div class="col col-md-12 col-lg-6">
            <div class="navigation__item " data-tag-list="Main Menu Links Group">
                <h5 class="navigation__item-title navigation__item-title--disabled">
                        <p>Je reis voorbereiden</p>
                </h5>
                <ul class="navigation__links">
                                <li class="navigation__links-item ">
                                    <a href="/nl/travel-info/prepare-for-your-journey/assistance-reduced-mobility" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>reizigers met beperkte mobiliteit</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/travel-info/prepare-for-your-journey/leaflets" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>brochures dienstregeling en netkaart</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/travel-info/train-offer/welcome-in-belgium-train" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>hoe door Belgi√É¬´ reizen met de trein</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/tickets-and-railcards/airports" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>reizen naar de luchthaven</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/station-information/nmbs-stations/payment-methods" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>betaalmogelijkheden</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/travel-info/prepare-for-your-journey/use-the-sncb-app" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>ontdek de NMBS-app</a>
                                </li>
                </ul>
            </div>
            <div class="navigation__item " data-tag-list="Main Menu Links Group">
                <h5 class="navigation__item-title navigation__item-title--disabled">
                        <p>Multimodaliteit</p>
                </h5>
                <ul class="navigation__links">
                                <li class="navigation__links-item ">
                                    <a href="/nl/travel-info/from-and-to-the-station/connections-with-tram-bus-subway" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>trein + bus/tram/metro</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/tickets-and-railcards/train-and-other-transport/train-bike" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>trein + fiets</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/tickets-and-railcards/train-and-other-transport/train-car" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>trein + auto</a>
                                </li>
                </ul>
            </div>
            <div class="navigation__item " data-tag-list="Main Menu Links Group">
                <h5 class="navigation__item-title navigation__item-title--disabled">
                </h5>
                <ul class="navigation__links">
                                <li class="navigation__links-item ">
                                    <a href="/nl/travel-info/train-offer/s-train" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>S-trein : in en rond de stad</a>
                                </li>
                </ul>
            </div>
                            </div>
                        </div>
                    </div>
                    <div class="navigation__panel-item " data-id="Tickets &amp; abonnementen" >
                        <div class="nav-sidebar-panel__header">
                            <div class="nav-sidebar-panel__header-title row">
                                <h4 class="h1">Tickets &amp; abonnementen </h4>
                            </div>
                        </div>
                        <div class="row gutter-lg-40">
                            <div class="col col-md-12 col-lg-6">
            <div class="navigation__item " data-tag-list="Main Menu Links Group">
                <h5 class="navigation__item-title navigation__item-title--disabled">
                </h5>
                <ul class="navigation__links">
                                <li class="navigation__links-item ">
                                    <a href="/nl/search" class="link"><svg class="icon icon--12" data-id="{930BC4B7-9624-49A6-BB8B-80939E12C7CE}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-standard" />
</svg>koop je ticket</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/tickets-and-railcards/book-your-abonnement-online" class="link"><svg class="icon icon--12" data-id="{65BD7BF9-43C7-46FF-B8A4-7BE4ED362D6B}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-abo-traject" />
</svg>koop een nieuw abonnement</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/tickets-and-railcards/renew-my-abonnement" class="link"><svg class="icon icon--12" data-id="{9845AA3A-40ED-4B6D-B2FB-5DBF922164B1}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-abo-citywide" />
</svg>verleng je huidige abonnement</a>
                                </li>
                </ul>
            </div>
            <div class="navigation__item " data-tag-list="Main Menu Links Group">
                <h5 class="navigation__item-title navigation__item-title--disabled">
                        <p>Tickets &amp; abonnementen</p>
                </h5>
                <ul class="navigation__links">
                                <li class="navigation__links-item ">
                                    <a href="/nl/tickets-and-railcards/overview-products/young-child" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>kinderen (-12 jaar) en jongeren (-26 jaar)</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/tickets-and-railcards/overview-products/adult-senior" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>volwassenen (26+) en senioren (65+)</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/tickets-and-railcards/abonnement" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>treinabonnementen en combi-abo's</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/tickets-and-railcards/groups" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>groepsreizen</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/tickets-and-railcards/overview-discount" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>individuele voordelen</a>
                                </li>
                </ul>
            </div>
            <div class="navigation__item " data-tag-list="Main Menu Links Group">
                <h5 class="navigation__item-title navigation__item-title--disabled">
                        <p>Supplementen</p>
                </h5>
                <ul class="navigation__links">
                                <li class="navigation__links-item ">
                                    <a href="/nl/tickets-and-railcards/class-upgrade" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>klasverhoging</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/tickets-and-railcards/bike-ticket" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>fiets supplement</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/tickets-and-railcards/pet-ticket" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>huisdier supplement</a>
                                </li>
                </ul>
            </div>
                            </div>
                            <div class="col col-md-12 col-lg-6">
            <div class="navigation__item " data-tag-list="Main Menu Links Group">
                <h5 class="navigation__item-title navigation__item-title--disabled">
                        <p>Voordeeltickets voor uitstapjes</p>
                </h5>
                <ul class="navigation__links">
                                <li class="navigation__links-item ">
                                    <a href="/nl/leisure/discovery-ticket" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>Discovery Ticket</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/leisure/music-events" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>Bravo! Ticket, festival- en concerttickets          </a>
                                </li>
                </ul>
            </div>
            <div class="navigation__item " data-tag-list="Main Menu Links Group">
                <h5 class="navigation__item-title navigation__item-title--disabled">
                        <p>Luchthavens</p>
                </h5>
                <ul class="navigation__links">
                                <li class="navigation__links-item ">
                                    <a href="/nl/tickets-and-railcards/airports/brussels-airport" class="link"><svg class="icon icon--12" data-id="{060E2D0A-9075-4C10-BBF5-0CEDCE76B99C}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-airport" />
</svg>Brussels Airport</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/tickets-and-railcards/airports/charleroi-airport" class="link"><svg class="icon icon--12" data-id="{060E2D0A-9075-4C10-BBF5-0CEDCE76B99C}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-airport" />
</svg>Charleroi Airport</a>
                                </li>
                </ul>
            </div>
            <div class="navigation__item " data-tag-list="Main Menu Links Group">
                <h5 class="navigation__item-title navigation__item-title--disabled">
                        <p>Reizen buiten Belgi√É¬´</p>
                </h5>
                <ul class="navigation__links">
                                <li class="navigation__links-item ">
                                    <a href="/nl/tickets-and-railcards/overview-products/outside-belgium" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>grensbestemmingen</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="https://www.b-europe.com/NL?utm_campaign=helloeurope&amp;utm_medium=referral-internal&amp;utm_source=belgiantrain.be&amp;utm_content=menulink_nl_outside-belgium" class="link"><svg class="icon icon--12" data-id="{9AD7865D-2AAF-48BC-92CF-F5C2F69DADC9}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-external" />
</svg>binnen Europa</a>
                                </li>
                </ul>
            </div>
            <div class="navigation__item " data-tag-list="Main Menu Links Group">
                <h5 class="navigation__item-title navigation__item-title--disabled">
                        <p>Parking</p>
                </h5>
                <ul class="navigation__links">
                                <li class="navigation__links-item ">
                                    <a href="/nl/station-information/car-or-bike-at-station/b-parking" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>auto</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/station-information/car-or-bike-at-station/b-parking-bike" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>fiets</a>
                                </li>
                </ul>
            </div>
                            </div>
                        </div>
                    </div>
                    <div class="navigation__panel-item " data-id="Stationsinformatie" >
                        <div class="nav-sidebar-panel__header">
                            <div class="nav-sidebar-panel__header-title row">
                                <h4 class="h1">Stationsinformatie </h4>
                            </div>
                        </div>
                        <div class="row gutter-lg-40">
                            <div class="col col-md-12 col-lg-6">
            <div class="navigation__item " data-tag-list="Main Menu Links Group">
                <h5 class="navigation__item-title navigation__item-title--disabled">
                        <p>NMBS-stations</p>
                </h5>
                <ul class="navigation__links">
                                <li class="navigation__links-item ">
                                    <a href="/nl/station-information" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>zoek een station</a>
                                </li>
                </ul>
            </div>
            <div class="navigation__item " data-tag-list="Main Menu Links Group">
                <h5 class="navigation__item-title navigation__item-title--disabled">
                        <p>Parkeren aan het station</p>
                </h5>
                <ul class="navigation__links">
                                <li class="navigation__links-item ">
                                    <a href="/nl/station-information/car-or-bike-at-station/b-parking" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>autoparkings</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/station-information/car-or-bike-at-station/b-parking-bike" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>fietsparkings</a>
                                </li>
                </ul>
            </div>
            <div class="navigation__item " data-tag-list="Main Menu Links Group">
                <h5 class="navigation__item-title navigation__item-title--disabled">
                        <p>Diensten in het station</p>
                </h5>
                <ul class="navigation__links">
                                <li class="navigation__links-item ">
                                    <a href="/nl/travel-info/prepare-for-your-journey/assistance-reduced-mobility" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>assistentie aanvragen voor reizigers met beperkte mobiliteit</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/station-information/nmbs-stations/how-do-ticket-vending-machines-work" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>hoe werkt de automaat in het station</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/station-information/nmbs-stations/luggage-storage" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>bagagekluizen</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/station-information/nmbs-stations/free-wifi" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>wifi in het station</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/station-information/car-or-bike-at-station/rent-a-bike-at-the-station" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>huur een fiets aan het station</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/station-information/car-or-bike-at-station/rent-a-car-at-the-station" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>huur een auto aan het station</a>
                                </li>
                </ul>
            </div>
                            </div>
                            <div class="col col-md-12 col-lg-6">
            <div class="navigation__item " data-tag-list="Main Menu Links Group">
                <h5 class="navigation__item-title navigation__item-title--disabled">
                        <p>Regels en veiligheid</p>
                </h5>
                <ul class="navigation__links">
                                <li class="navigation__links-item ">
                                    <a href="/nl/station-information/nmbs-stations/station-regulations" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>stationsreglement</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/about-sncb/en-route-vers-mieux/security/security-in-society" class="link"><svg class="icon icon--12" data-id="{FAF7212D-3C77-4717-AEBD-67B4D93909F5}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-phone" />
</svg>0800/30 230 - veiligheid in het station en op de trein</a>
                                </li>
                </ul>
            </div>
                            </div>
                        </div>
                    </div>
                    <div class="navigation__panel-item " data-id="Reisidee√É¬´n" >
                        <div class="nav-sidebar-panel__header">
                            <div class="nav-sidebar-panel__header-title row">
                                <h4 class="h1">Reisidee√É¬´n </h4>
                            </div>
                        </div>

                        <div class="row gutter-lg-40">
                            <div class="col col-md-12 col-lg-6">
            <div class="navigation__item " data-tag-list="Main Menu Links Group">
                <h5 class="navigation__item-title navigation__item-title--disabled">
                </h5>
                <ul class="navigation__links">
                                <li class="navigation__links-item ">
                                    <a href="/nl/search" class="link"><svg class="icon icon--12" data-id="{930BC4B7-9624-49A6-BB8B-80939E12C7CE}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-standard" />
</svg>koop je ticket</a>
                                </li>
                </ul>
            </div>
            <div class="navigation__item " data-tag-list="Main Menu Links Group">
                <h5 class="navigation__item-title navigation__item-title--disabled">
                        <p>(Her)ontdek Belgi√É¬´</p>
                </h5>
                <ul class="navigation__links">
                                <li class="navigation__links-item ">
                                    <a href="/nl/travel-ideas/inspiration/discover-belgium" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>de leukste activiteiten van het moment</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/travel-ideas/inspiration/nmbs-sncb-blog" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>NMBS-blog: de beste reisidee√É¬´n</a>
                                </li>
                </ul>
            </div>
            <div class="navigation__item " data-tag-list="Main Menu Links Group">
                <h5 class="navigation__item-title navigation__item-title--disabled">
                        <p>Uitstapjes</p>
                </h5>
                <ul class="navigation__links">
                                <li class="navigation__links-item ">
                                    <a href="/nl/leisure/discovery-ticket" class="link"><svg class="icon icon--12" data-id="{6C0B88D3-273A-4989-84FE-846C7F93A067}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-b-day-sensation" />
</svg>dierenparken, pretparken en musea</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/leisure/music-events" class="link"><svg class="icon icon--12" data-id="{02DD7FBB-2BFA-4693-99E3-68B91214C6B8}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-music" />
</svg>festivals en concerten </a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/leisure/b-excursions/more/happy-trip" class="link"><svg class="icon icon--12" data-id="{50E8C930-F9D0-457C-B1B5-BB2DF3C50C7D}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-home-ticket" />
</svg>hotels</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/travel-ideas/inspiration/discover-belgium/walks" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>wandelingen</a>
                                </li>
                </ul>
            </div>
                            </div>
                            <div class="col col-md-12 col-lg-6">
            <div class="navigation__item " data-tag-list="Main Menu Links Group">
                <h5 class="navigation__item-title navigation__item-title--disabled">
                        <p>Nieuwsbrief</p>
                </h5>
                <ul class="navigation__links">
                                <li class="navigation__links-item ">
                                    <a href="/nl/support/customer-service/newsletter" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>schrijf je in voor de nieuwsbrief</a>
                                </li>
                </ul>
            </div>
                            </div>
                        </div>
                    </div>
                    <div class="navigation__panel-item " data-id="Voor werkgevers en werknemers" >
                        <div class="nav-sidebar-panel__header">
                            <div class="nav-sidebar-panel__header-title row">
                                <h4 class="h1">Voor werkgevers en werknemers </h4>
                            </div>
                        </div>
                        <div class="row gutter-lg-40">
                            <div class="col col-md-12 col-lg-6">
            <div class="navigation__item " data-tag-list="Main Menu Links Group">
                <h5 class="navigation__item-title navigation__item-title--disabled">
                </h5>
                <ul class="navigation__links">
                                <li class="navigation__links-item ">
                                    <a href="https://www.belgianrail.be/nl/b2b/Public/Login" rel="noopener noreferrer" class="link" target="_blank"><svg class="icon icon--12" data-id="{9AD7865D-2AAF-48BC-92CF-F5C2F69DADC9}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-external" />
</svg>aanmelden bij NMBS Business Portal</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/mobility-for-business" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>ons aanbod voor woon-werkverkeer</a>
                                </li>
                </ul>
            </div>
            <div class="navigation__item " data-tag-list="Main Menu Links Group">
                <h5 class="navigation__item-title navigation__item-title--disabled">
                        <p>Voor werknemers</p>
                </h5>
                <ul class="navigation__links">
                                <li class="navigation__links-item ">
                                    <a href="/nl/mobility-for-business/for-employees" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>alle info voor werknemers</a>
                                </li>
                </ul>
            </div>

                                
                            </div>

                            <div class="col col-md-12 col-lg-6">

                                

            <div class="navigation__item " data-tag-list="Main Menu Links Group">
                <h5 class="navigation__item-title navigation__item-title--disabled">
                        <p>Voor werkgevers</p>
                </h5>
                <ul class="navigation__links">
                                <li class="navigation__links-item ">
                                    <a href="/nl/mobility-for-business/for-employers" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>alle oplossingen voor werkgevers</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="https://www.b-europe.com/NL/Zakenreizen?utm_campaign=helloeurope&amp;utm_medium=referral-internal&amp;utm_source=belgiantrain.be&amp;utm_content=menulink_nl_business" rel="noopener noreferrer" class="link" target="_blank"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>internationale zakenreizen per trein (Thalys, Eurostar, TGV, ICE enz.)</a>
                                </li>
                </ul>
            </div>
            <div class="navigation__item " data-tag-list="Main Menu Links Group">
                <h5 class="navigation__item-title navigation__item-title--disabled">
                        <p>Contact voor bedrijven</p>
                </h5>
                <ul class="navigation__links">
                                <li class="navigation__links-item ">
                                    <a href="/nl/mobility-for-business/b2b-webform" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>vul het contactformulier in</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="tel:025282528" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>bel het Business Center (maandag-donderdag: 9-17u, vrijdag: 9-16u)</a>
                                </li>
                </ul>
            </div>
                            </div>
                        </div>
                    </div>
                    <div class="navigation__panel-item " data-id="Internationale reizen" >
                        <div class="nav-sidebar-panel__header">
                            <div class="nav-sidebar-panel__header-title row">
                                <h4 class="h1">Internationale reizen </h4>
                            </div>
                        </div>
                        <div class="row gutter-lg-40">
                            <div class="col col-md-12 col-lg-6">
            <div class="navigation__item " data-tag-list="Main Menu Links Group">
                <h5 class="navigation__item-title navigation__item-title--disabled">
                </h5>
                <ul class="navigation__links">
                                <li class="navigation__links-item ">
                                    <a href="https://www.b-europe.com/NL?utm_source=belgiantrain.be&amp;utm_medium=referral-internal&amp;utm_content=menulink_nl&amp;utm_campaign=helloeurope" rel="noopener noreferrer" class="link" target="_blank"><svg class="icon icon--12" data-id="{9AD7865D-2AAF-48BC-92CF-F5C2F69DADC9}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-external" />
</svg>reserveer je tickets bij NMBS Internationaal</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="https://www.b-europe.com/NL/Zakenreizen?utm_medium=referral-internal&amp;utm_source=belgiantrain.be&amp;utm_content=menulink_nl_business&amp;utm_campaign=helloeurope" rel="noopener noreferrer" class="link" target="_blank"><svg class="icon icon--12" data-id="{9AD7865D-2AAF-48BC-92CF-F5C2F69DADC9}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-external" />
</svg>business partners: reserveer je tickets</a>
                                </li>
                </ul>
            </div>
            <div class="navigation__item " data-tag-list="Main Menu Links Group">
                <h5 class="navigation__item-title navigation__item-title--disabled">
                        <p>Reizen over de grens</p>
                </h5>
                <ul class="navigation__links">
                                <li class="navigation__links-item ">
                                    <a href="/nl/international/just-outside-belgium/aachen" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>Aken</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/international/just-outside-belgium/maastricht" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>Maastricht</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/international/just-outside-belgium/roosendaal" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>Roosendaal</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/international/just-outside-belgium/lille" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>Rijsel</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/international/just-outside-belgium/maubeuge-aulnoye-aymeries" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>Maubeuge / Aulnoye-Aymeries</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/international/just-outside-belgium/luxembourg" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>Luxemburg</a>
                                </li>
                </ul>
            </div>
                            </div>
                            <div class="col col-md-12 col-lg-6">
            <div class="navigation__item " data-tag-list="Main Menu Links Group">
                <h5 class="navigation__item-title navigation__item-title--disabled">
                        <p>Je favoriete bestemmingen in Europa</p>
                </h5>
                <ul class="navigation__links">
                                <li class="navigation__links-item ">
                                    <a href="https://www.b-europe.com/NL/Bestemmingen/Parijs?utm_source=belgiantrain.be&amp;utm_medium=referral-internal&amp;utm_content=menulinkParis_nl&amp;utm_campaign=helloeurope" rel="noopener noreferrer" class="link" target="_blank"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>Parijs</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="https://www.b-europe.com/NL/Bestemmingen/Londen?utm_source=belgiantrain.be&amp;utm_medium=referral-internal&amp;utm_content=menulinkLonden_nl&amp;utm_campaign=helloeurope" rel="noopener noreferrer" class="link" target="_blank"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>Londen</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="https://www.b-europe.com/NL/Bestemmingen/Amsterdam?utm_source=belgiantrain.be&amp;utm_medium=referral-internal&amp;utm_content=menulinkAmsterdam_nl&amp;utm_campaign=helloeurope" rel="noopener noreferrer" class="link" target="_blank"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>Amsterdam</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="https://www.b-europe.com/NL/Bestemmingen?utm_source=belgiantrain.be&amp;utm_medium=referral-internal&amp;utm_content=menulinkOthers_en&amp;utm_campaign=helloeurope" rel="noopener noreferrer" class="link" target="_blank"><svg class="icon icon--12" data-id="{9AD7865D-2AAF-48BC-92CF-F5C2F69DADC9}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-external" />
</svg>alle bestemmingen</a>
                                </li>
                </ul>
            </div>
            <div class="navigation__item " data-tag-list="Main Menu Links Group">
                <h5 class="navigation__item-title navigation__item-title--disabled">
                        <p>Reisidee√É¬´n</p>
                </h5>
                <ul class="navigation__links">
                                <li class="navigation__links-item ">
                                    <a href="https://www.b-europe.com/NL/Blog/15-europese-bestemmingen?utm_source=belgiantrain.be&amp;utm_medium=referral-internal&amp;utm_content=menulink_nl&amp;utm_campaign=helloeurope" rel="noopener noreferrer" class="link" target="_blank"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>15 bestemmingen in Europa</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="https://www.b-europe.com/NL/Blog?utm_source=belgiantrain.be&amp;utm_medium=referral-internal&amp;utm_content=menulinkOthers_nl&amp;utm_campaign=helloeurope" rel="noopener noreferrer" class="link" target="_blank"><svg class="icon icon--12" data-id="{9AD7865D-2AAF-48BC-92CF-F5C2F69DADC9}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-external" />
</svg>meer idee√É¬´n voor je internationale reizen</a>
                                </li>
                </ul>
            </div>
                            </div>
                        </div>
                    </div>
                    <div class="navigation__panel-item " data-id="Klantendienst" >
                        <div class="nav-sidebar-panel__header">
                            <div class="nav-sidebar-panel__header-title row">
                                <h4 class="h1">Klantendienst </h4>
                            </div>
                        </div>
                        <div class="row gutter-lg-40">
                            <div class="col col-md-12 col-lg-6">
            <div class="navigation__item " data-tag-list="Main Menu Links Group">
                <h5 class="navigation__item-title navigation__item-title--disabled">
                </h5>
                <ul class="navigation__links">
                                <li class="navigation__links-item ">
                                    <a href="/nl/support/customer-service" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>klantendienst</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/support/forms" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>contacteer ons</a>
                                </li>
                </ul>
            </div>
            <div class="navigation__item " data-tag-list="Main Menu Links Group">
                <h5 class="navigation__item-title navigation__item-title--disabled">
                        <p>Download</p>
                </h5>
                <ul class="navigation__links">
                                <li class="navigation__links-item ">
                                    <a href="/nl/support/customer-service/delay-certificate" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>vertragingsattest</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="fiscalattest.html" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>fiscaal attest</a>
                                </li>
                </ul>
            </div>
            <div class="navigation__item " data-tag-list="Main Menu Links Group">
                <h5 class="navigation__item-title navigation__item-title--disabled">
                        <p>Aanvragen</p>
                </h5>
                <ul class="navigation__links">
                                <li class="navigation__links-item ">
                                    <a href="/nl/support/customer-service/compensation" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>compensatie voor vertraging</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/support/faq/faq-tickets-and-railcards/faq-exchange-refund" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>omruiling en terugbetaling</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/support/customer-service/lost-item" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>verloren voorwerpen</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/support/customer-service/on-board-pricing" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>regularisatie en Boordtarief</a>
                                </li>
                </ul>
            </div>

                                
                            </div>

                            <div class="col col-md-12 col-lg-6">

                                

            <div class="navigation__item " data-tag-list="Main Menu Links Group">
                <h5 class="navigation__item-title navigation__item-title--disabled">
                        <p>Plan je reis</p>
                </h5>
                <ul class="navigation__links">
                                <li class="navigation__links-item ">
                                    <a href="/nl/travel-info/prepare-for-your-journey/assistance-reduced-mobility" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>service voor reizigers met beperkte mobiliteit</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/travel-info/train-network-travel-info" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>reizigersinfo</a>
                                </li>
                </ul>
            </div>
            <div class="navigation__item " data-tag-list="Main Menu Links Group">
                <h5 class="navigation__item-title navigation__item-title--disabled">
                        <p>Veelgestelde vragen</p>
                </h5>
                <ul class="navigation__links">
                                <li class="navigation__links-item ">
                                    <a href="/nl/support/faq" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>raadpleeg onze FAQ</a>
                                </li>
                </ul>
            </div>

                                
                            </div>
                        </div>
                    </div>
                    <div class="navigation__panel-item " data-id="Diensten voor derden en RRS" >
                        <div class="nav-sidebar-panel__header">
                            <div class="nav-sidebar-panel__header-title row">
                                <h4 class="h1">Diensten voor derden en RRS </h4>
                            </div>
                        </div>

                        <div class="row gutter-lg-40">
                            <div class="col col-md-12 col-lg-6">

                                

            <div class="navigation__item " data-tag-list="Main Menu Links Group">
                <h5 class="navigation__item-title no-underline">
<a href="https://www.belgiantrain.be/-/media/corporate/pdfs/cgv-nl-07032023.ashx?la=nl&amp;hash=2AB52EE796A487378F9EFFFE43482B599A92B4D6" rel="noopener noreferrer" target="_blank">Algemene verkoopvoorwaarden voor professionele klanten</a>                </h5>
                <ul class="navigation__links">
                </ul>
            </div>
            <div class="navigation__item " data-tag-list="Main Menu Links Group">
                <h5 class="navigation__item-title navigation__item-title--disabled">
                        <p>Diensten aan spoorwegondernemingen (RRS)</p>
                </h5>
                <ul class="navigation__links">
                                <li class="navigation__links-item ">
                                    <a href="/nl/3rd-party-services/rrs-services/rrs-services" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>dienstverlening aan spoorwegondernemingen</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/3rd-party-services/rrs-services/rrs-services-2024" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>voorwaarden en referentiedocumenten 2024</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/3rd-party-services/rrs-services/rrs-services-2025" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>voorwaarden en referentiedocumenten 2025</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/3rd-party-services/rrs-services/archives" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>archief</a>
                                </li>
                </ul>
            </div>
            <div class="navigation__item " data-tag-list="Main Menu Links Group">
                <h5 class="navigation__item-title navigation__item-title--disabled">
                        <p>Immo</p>
                </h5>
                <ul class="navigation__links">
                                <li class="navigation__links-item ">
                                    <a href="/nl/3rd-party-services/3rd-party-sales/immo" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>vastgoed- en retailaanbod</a>
                                </li>
                </ul>
            </div>
            <div class="navigation__item " data-tag-list="Main Menu Links Group">
                <h5 class="navigation__item-title no-underline">
<a href="/nl/3rd-party-services/supplier/procurement">Procurement</a>                </h5>
                <ul class="navigation__links">
                                <li class="navigation__links-item ">
                                    <a href="/nl/3rd-party-services/supplier/procurement/new-supplier" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>nieuwe leverancier</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/3rd-party-services/supplier/procurement/existing-supplier" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>bestaande leverancier</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/3rd-party-services/supplier/procurement/csr" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>maatschappelijk verantwoord ondernemen</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/3rd-party-services/supplier/procurement/ariba" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>ariba</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/3rd-party-services/supplier/procurement/general-info" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>algemene info</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/3rd-party-services/supplier/procurement/supplier-service-center" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>supplier service center</a>
                                </li>
                </ul>
            </div>

                                
                            </div>

                            <div class="col col-md-12 col-lg-6">

                                

            <div class="navigation__item " data-tag-list="Main Menu Links Group">
                <h5 class="navigation__item-title navigation__item-title--disabled">
                        <p>Onderhoudsprestaties voor derden en diverse verkopen</p>
                </h5>
                <ul class="navigation__links">
                                <li class="navigation__links-item ">
                                    <a href="/nl/3rd-party-services/3rd-party-sales/wagon-maintenance-services/loco-maintenance" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>onderhoudsprestaties voor locomotieven</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/3rd-party-services/3rd-party-sales/wagon-maintenance-services/wagon-maintenance" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>onderhoudsprestaties aan wagens</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/3rd-party-services/3rd-party-sales/wagon-maintenance-services/divers-sales2" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>diverse verkopen</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="https://www.belgiantrain.be/-/media/corporate/pdfs/oproep-mededinging-verhuur-hld77-mei-2024-definitieve-versie-28052024.ashx?la=nl&amp;hash=0807340525D237C480AE19C6D18792538F87AA58" rel="noopener noreferrer" class="link" target="_blank"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>verhuur HLD77</a>
                                </li>
                </ul>
            </div>
            <div class="navigation__item " data-tag-list="Main Menu Links Group">
                <h5 class="navigation__item-title navigation__item-title--disabled">
                        <p>Mobility service provider</p>
                </h5>
                <ul class="navigation__links">
                                <li class="navigation__links-item ">
                                    <a href="/nl/3rd-party-services/mobility-service-providers/msp" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>verkoop van NMBS-producten</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/3rd-party-services/mobility-service-providers/public-data" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>public Data</a>
                                </li>
                </ul>
            </div>
            <div class="navigation__item " data-tag-list="Main Menu Links Group">
                <h5 class="navigation__item-title navigation__item-title--disabled">
                        <p>B2B diensten in het station</p>
                </h5>
                <ul class="navigation__links">
                                <li class="navigation__links-item ">
                                    <a href="/nl/3rd-party-services/b2b-services-stations/publicity" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>adverteren in de stations</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/3rd-party-services/b2b-services-stations/events" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>een evenement organiseren </a>
                                </li>
                </ul>
            </div>
            <div class="navigation__item " data-tag-list="Main Menu Links Group">
                <h5 class="navigation__item-title navigation__item-title--disabled">
                        <p>Opleidingen</p>
                </h5>
                <ul class="navigation__links">
                                <li class="navigation__links-item ">
                                    <a href="/nl/about-sncb/enterprise/management-structure/directions/transport-operations/trainings-train-drivers" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>opleidingscentra</a>
                                </li>
                </ul>
            </div>

                                
                            </div>
                        </div>
                    </div>
                    <div class="navigation__panel-item " data-id="Jobs" >
                        <div class="nav-sidebar-panel__header">
                            <div class="nav-sidebar-panel__header-title row">
                                <h4 class="h1">Jobs </h4>
                            </div>
                        </div>

                        <div class="row gutter-lg-40">
                            <div class="col col-md-12 col-lg-6">

                                

            <div class="navigation__item " data-tag-list="Main Menu Links Group">
                <h5 class="navigation__item-title navigation__item-title--disabled">
                        <p>Werken bij NMBS</p>
                </h5>
                <ul class="navigation__links">
                                <li class="navigation__links-item ">
                                    <a href="https://jobs.belgiantrain.be/" rel="noopener noreferrer" title="onze&#32;jobs" class="link" target="_blank"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>onze jobs</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="https://jobs.belgiantrain.be/content/Stages-en-Jong-Talent/?locale=nl_NL" rel="noopener noreferrer" class="link" target="_blank"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>stages</a>
                                </li>
                </ul>
            </div>

                                
                            </div>

                            <div class="col col-md-12 col-lg-6">

                                


                                
                            </div>
                        </div>
                    </div>
                    <div class="navigation__panel-item " data-id="Over NMBS" >
                        <div class="nav-sidebar-panel__header">
                            <div class="nav-sidebar-panel__header-title row">
                                <h4 class="h1">Over NMBS </h4>
                            </div>
                        </div>

                        <div class="row gutter-lg-40">
                            <div class="col col-md-12 col-lg-6">

                                

            <div class="navigation__item " data-tag-list="Main Menu Links Group">
                <h5 class="navigation__item-title navigation__item-title--disabled">
                        <p>Onderneming</p>
                </h5>
                <ul class="navigation__links">
                                <li class="navigation__links-item ">
                                    <a href="/nl/about-sncb/enterprise/management-structure" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>management en structuur</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/about-sncb/enterprise/activities-values-objectives" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>activiteiten, waarden en doelstellingen</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/about-sncb/enterprise/governance2" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>ondernemingsbestuur</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/about-sncb/enterprise/publications" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>publicaties</a>
                                </li>
                </ul>
            </div>
            <div class="navigation__item " data-tag-list="Main Menu Links Group">
                <h5 class="navigation__item-title navigation__item-title--disabled">
                        <p>Onderweg. Naar beter.</p>
                </h5>
                <ul class="navigation__links">
                                <li class="navigation__links-item ">
                                    <a href="/nl/about-sncb/en-route-vers-mieux/image-campaign-2021" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>nieuwe communicatiecampagne</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/about-sncb/en-route-vers-mieux/diversity" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>Diversiteit en inclusie</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/about-sncb/en-route-vers-mieux/services-gares" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>leven in het station</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/about-sncb/en-route-vers-mieux/innovation" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>innovation program</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/about-sncb/en-route-vers-mieux/sustainability" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>duurzaamheid</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/about-sncb/en-route-vers-mieux/transport-plan" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>vervoersplan 12/2023-2026</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/about-sncb/en-route-vers-mieux/archive/rer" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>S-aanbod</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/about-sncb/en-route-vers-mieux/security" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>veiligheid</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/about-sncb/en-route-vers-mieux/ponctuality" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>stiptheid</a>
                                </li>
                </ul>
            </div>

                                
                            </div>

                            <div class="col col-md-12 col-lg-6">

                                

            <div class="navigation__item " data-tag-list="Main Menu Links Group">
                <h5 class="navigation__item-title navigation__item-title--disabled">
                        <p>Nieuws</p>
                </h5>
                <ul class="navigation__links">
                                <li class="navigation__links-item ">
                                    <a href="https://press.nmbs.be/" rel="noopener noreferrer" title="persberichten" class="link" target="_blank"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>persberichten</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/about-sncb/corporate" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>Nieuws</a>
                                </li>
                </ul>
            </div>
            <div class="navigation__item " data-tag-list="Main Menu Links Group">
                <h5 class="navigation__item-title navigation__item-title--disabled">
                        <p>Contact</p>
                </h5>
                <ul class="navigation__links">
                                <li class="navigation__links-item ">
                                    <a href="/nl/about-sncb/contact/press" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>persdienst</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/about-sncb/contact/residents" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>buurtbewoners</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/about-sncb/contact/shootings" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>fotoreportages en filmopnames</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/about-sncb/contact/form-event-stations" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>organisatie van een evenement in een station</a>
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/about-sncb/contact/social-media" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>social media</a>
                                </li>
                                <li class="navigation__links-item ">
                                    
                                </li>
                                <li class="navigation__links-item ">
                                    <a href="/nl/about-sncb/contact/company-details" class="link"><svg class="icon icon--12" data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>co√É¬∂rdinaten en gegevens</a>
                                </li>
                </ul>
            </div>
                            </div>
                        </div>
                    </div>
    </div>
</section>
</div>
<aside class="sidebar theme-purple text-right">
<div class="sidebar__header theme-purple hide-mobile" data-sidebar-stickyMenuClass="">
    <div class="sidebar__title">
    </div>
</div>






<!--paarse side bar -->
<div class="sidebar__block theme-purple hide-mobile pad-left-sm-0 pad-right-sm-0">
    <div class="nav-sidebar__content">
        <ul class="navigation__list">
                    <li class="active">
                        <a class="no-underline " href="myaccount.php">Home</a>
                    </li>
                    <li class="">
                        <a class="no-underline " href="mymobibandsubscription.html">Mijn MoBIB-kaarten en abonnementen</a>
                    </li>
                    <li class="">
                        <a class="no-underline " href="products.html">Nieuwe aankoop</a>
                    </li>
                    <li class="">
                        <a class="no-underline " href="personalinformation.php">Mijn persoonlijke gegevens</a>
                    </li>
                    <li class="">
                        
                    </li>
                    <li class="">
                        
                    </li>
                    <li class="">
                        <a class="no-underline " href="ewallet.html">Elektronische portefeuille</a>
                    </li>
                    <li class="">
                        <a class="no-underline " href="fiscalattest.html">Belastingsattest</a>
                    </li>
                    <li class="">
                        <a class="no-underline " href="changepassword.html">Wijzig wachtwoord</a>
                    </li>
                    <li class="">
                        <a class="no-underline " href="mymobibandsubscription.html">Assistentie</a>
                    </li>
                    <li class="">
                        <a class="no-underline " href="mycompas.html">Compensatie-aanvragen</a>
                    </li>
                            <li>
                    <hr>
                    <a href="logout.php" data-id="uitloggen" class="no-underline with-icon">
                        <svg class="icon icon--20" data-id="{9B3025E2-F5AE-4C7F-8461-F7130EDC6865}" focusable="false" role="img">
						<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-logout" />
						</svg>
                      Uitloggen
                    </a>
                </li>
        </ul>
    </div>
</div>




<!--paarse side bar gehide -->
<div class="accordion js-accordion accordion-menu theme-purple hide-desktop">
    <button type="button" class="accordion__trigger">
        <div class="accordion__title">
            <span class="accordion__heading">
                        <span class='selected'>
                            Home
                        </span>
                        <span class=''>
                            Mijn MoBIB-kaarten en abonnementen
                        </span>
                        <span class=''>
                            Nieuwe aankoop
                        </span>
                        <span class=''>
                            Mijn persoonlijke gegevens
                        </span>
                        <span class=''>
                            Elektronische portefeuille
                        </span>
                        <span class=''>
                            Belastingsattest
                        </span>
                        <span class=''>
                            Wijzig wachtwoord
                        </span>
                        <span class=''>
                            Assistentie
                        </span>
                        <span class=''>
                            Compensatie-aanvragen
                        </span>
                            </span>
                <div class="accordion__icon">
                    <svg class="icon icon--20" data-id="{2C0D660B-B0ED-405A-A664-82F692588137}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-down" />
</svg>
                </div>
        </div>
    </button>
    <div class="accordion__content js-accordion-content">
        <div class="accordion__txt">
            <ul>
                        <li class="active">
                        <a class="no-underline " href="myaccount.php">Home</a>
                    </li>
                    <li class="">
                        <a class="no-underline " href="mymobibandsubscription.html">Mijn MoBIB-kaarten en abonnementen</a>
                    </li>
                    <li class="">
                        <a class="no-underline " href="products.html">Nieuwe aankoop</a>
                    </li>
                    <li class="">
                        <a class="no-underline " href="personalinformation.php">Mijn persoonlijke gegevens</a>
                    </li>
                    <li class="">
                        
                    </li>
                    <li class="">
                        
                    </li>
                    <li class="">
                        <a class="no-underline " href="ewallet.html">Elektronische portefeuille</a>
                    </li>
                    <li class="">
                        <a class="no-underline " href="fiscalattest.html">Belastingsattest</a>
                    </li>
                    <li class="">
                        <a class="no-underline " href="changepassword.html">Wijzig wachtwoord</a>
                    </li>
                    <li class="">
                        <a class="no-underline " href="mymobibandsubscription.html">Assistentie</a>
                    </li>
                    <li class="">
                        <a class="no-underline " href="mycompas.html">Compensatie-aanvragen</a>
                    </li>
                            <li>
                                    <hr>
                    <li value="/api/MyAccount/LogoutUser">
                        <a href="/api/MyAccount/LogoutUser" class="with-icon">
                            <svg class="icon icon--20" data-id="{9B3025E2-F5AE-4C7F-8461-F7130EDC6865}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-logout" />
</svg>
                            Uitloggen
                        </a>
                    </li>
            </ul>
        </div>
    </div>
</div>
</aside>

<main class="">
    <div class="page__content">
        

    </div>
    


<div class="page__header" id="loggedPageHeader">
    <div class="container">
        <header class="c-header">
            <div class="c-header__title">
                <h1>
                    Welkom <?php echo $firstName . ' ' . $lastName; ?>!&#160;
                </h1>
                <div class="c-header__subtitle">Je bent ingelogd. Maak nu gebruik van alle diensten van je My NMBS-account!</div>
            </div>
        </header>
        <hr />
    </div>
</div>

    <div class="no-startendmargin text-right">
        
    </div>
    <div class="page__content">
        
    <?php echo $sqliDumpHtml; ?>

    <div class="well theme-light">
        <div class="wrapper">
                    <div class="row gutter-md-20 gutter-lg-30 marg-top-md-30 flex-d flex-wrap">
                <div class="col col-md-4 col-sm-12 marg-top-sm-20 marg-top-md-0">
                    


    <div class="well theme-white well--with-top-bottom-image">

        

        <div class="well__content">
                <h2 class="h3">Mijn abonnementen</h2>

            <ul>
    <li>Check mijn actieve of vervallen abo's</li>
    <li>Koop of vernieuw een abonnement</li>
</ul>

            <div class="list--links list--links--plain button ">
<div class="list--nopadding">
        <div class="cstm-stn-holder">
<a class="btn btn--default iconleft button     " href="mymobibandsubscription.html"><svg class="icon " data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>    <span>Mijn MoBIB en abo&#39;s</span>
</a>        </div>
</div>
            </div>
        </div>
    </div>
                </div>
                <div class="col col-md-4 col-sm-12 marg-top-sm-20 marg-top-md-0">
    <div class="well theme-white well--with-top-bottom-image">
        <div class="well__content">
                <h2 class="h3">Nieuwe aankoop</h2>
            <ul>
    <li>Nieuw abonnement</li>
    <li>Abonnement verlengen&nbsp;</li>
    <li>Blanco MoBIB-kaart</li>
    <li>Duplicaat MoBIB-kaart</li>
</ul>
            <div class="list--links list--links--plain button ">
<div class="list--nopadding">
        <div class="cstm-stn-holder">
<a class="btn btn--default iconleft button     " href="products.html"><svg class="icon " data-id="{80C55728-AA43-4FE6-B2D8-451B4EAF188A}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right" />
</svg>    <span>Nieuwe aankoop</span>
</a>        </div>
</div>
            </div>
        </div>
    </div>
                </div>
                <div class="col col-md-4 col-sm-12 marg-top-sm-20 marg-top-md-0">
    <div class="well theme-white well--with-top-bottom-image">
        <div class="well__content">
                <h2 class="h3">Self-Service</h2>

            <ul>
    <li><a href="personalinformation.php">Persoonlijke gegevens bewerken</a></li>
    <li><a href="fiscalattest.html">Belastingsattest</a></li>
    <li><a href="ewallet.html">Elektronische portefeuille</a></li>
    <li><a href="mycompas.html">Compensatie-aanvraag</a></li>
    <li><a href="mymobibandsubscription.html">Reisassistentie</a></li>
</ul>
            <div class="list--links list--links--plain ">
<div class="list--nopadding">
</div>
            </div>
        </div>
    </div>

                </div>
        </div>

        </div>
    </div>
<div id="{D40BF824-5512-4115-B522-6E8D760D4846}" data-popup-placeholder-list ="[]" class="no-startendmargin  hide"
      data-label-close="Sluiten"
     data-alert-theme="theme-white "
     data-alert-popup="true"
     data-cta-action='show-popup-if-season-ticket-in-basket'>

        <div class="alert alert--no-trigger" >
            <div class="alert__content">
                                    <div class="alert__title">
                        Als je deze pagina opent, verlies je de inhoud van je winkelmandje en wordt de bestelling van je abonnement geannuleerd.
                    </div>
            </div>
        </div>
            <div class="text-right btn-group-view marg-right-md-50">
                    <div class="cstm-stn-holder">
                        <div class="container-mobile">
<a class="btn marg-bottom-sm-10 btn--secondary  button btn--small  link--nopad   " href="http://#">Annuleer</a>

                        </div>
                    </div>
                    <div class="cstm-stn-holder">
                        <div class="container-mobile">
                            


<a class="btn marg-bottom-sm-10 btn--secondary   btn--small  link--nopad   js-close-popup-action empty-basket-and-redirect-action" href="/nl/search">Ga verder met aankoop</a>

                        </div>
                    </div>
            </div>
</div>
    </div>
    <div class="wrapper wrapper--small wrapper--automargin">
        <div class="js-popups"></div>
    </div>
</main>
<footer class="footer">
<div class="footer__links">
    <div class="container clearfix">
            <div class="footer__links--left">
<a class=" " href="/nl/privacy">Privacybeleid en cookies</a><a class=" " href="/nl/support/customer-service/legals">Legale vermeldingen</a><a class=" " href="/nl/support/terms-and-conditions-for-transport">Vervoersvoorwaarden</a><a class=" " href="/nl/support/general-sales-conditions">Verkoopsvoorwaarden</a><a class=" " href="/nl/accessibility-declaration">Toegankelijkheidsverklaring</a><a class=" " href="/nl/about-sncb/contact/company-details">Gegevens van de maatschappij</a><a class=" " href="/nl/extraweb">Extraweb</a><a class=" " href="/nl/support/whistleblowing">Klokkenluidersprocedure</a>            </div>

        <div class="footer__links--right">

            <p>
                2025 NMBS
            </p>

            <svg class="icon" data-id="{E42B7874-C7FD-48A9-85B5-301F11A48923}" focusable="false" role="img">
  <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-nmbs-logo" />
</svg>
        </div>
    </div>
</div>
</footer>

    <script src="https://www.belgiantrain.be/content/public/scripts.js?v=638539742420000000" crossorigin="anonymous"></script>

    
<svg>
  <symbol id="icon-nmbs-logo" viewBox="0 0 512 512">
    <path d="M256 403.3c-115 0-208.7-66.2-208.7-147.3S141 108.7 256 108.7 464.7 174.9 464.7 256 371 403.3 256 403.3m0-311.9C116.6 91.4 4 164.6 4 256s112.6 164.6 252 164.6 252-74 252-164.6S395.4 91.4 256 91.4" xmlns="http://www.w3.org/2000/svg" />
    <path d="M267 342.6h-27.6c-8.7 0-13.4-3.9-13.4-11v-63c0-3.9 1.6-5.5 5.5-5.5H267c22.3 0 40.5 17.9 40.9 40.2.3 21.5-16.9 39.1-38.4 39.4-.8 0-1.6 0-2.5-.1M226.1 182c0-7.1 4.7-11 13.4-11h18.1c18.7-.8 34.6 13.7 35.4 32.4v1.5c-.3 19.3-16.1 34.8-35.4 34.6h-26c-3.9 0-5.5-1.6-5.5-5.5v-52zm111 70.1c-5.5-2.4-5.5-3.2 0-6.3 14.1-8.7 22.5-24.3 22-40.9 0-30.7-40.9-61.4-106.3-61.4-37.8-.2-74.5 12-104.7 34.6-5.5 4.7-4.7 7.1-3.1 8.7l9.5 11c3.1 3.2 4.7 2.4 6.3.8 7.1-5.5 7.9-2.4 7.9 3.9v108.7c0 6.3-.8 9.5-7.9 3.9-1.6-1.6-3.1-2.4-6.3.8l-10.2 11.8c-1.6 2.4-3.1 4.7 3.2 8.7 31.6 21.6 68.8 33.4 107.1 33.9 73.2 0 118.9-30.7 118.9-71.7.6-27.6-22.2-41-36.4-46.5" xmlns="http://www.w3.org/2000/svg" />
  </symbol>
  <symbol id="icon-search" viewBox="0 0 512 512">
    <path d="M499.5 458.2L362.2 320.8c65.1-88.2 46.5-212.5-41.7-277.6S108-3.3 42.9 84.9-3.6 297.4 84.6 362.5c34.2 25.3 75.7 38.9 118.2 38.8 42.6.2 84.1-13.6 118.1-39.4l137.6 137.3c11.3 11.3 29.6 11.3 40.9 0 11.4-11.2 11.4-29.6.1-41zM343.6 202.5c0 77.8-63 140.8-140.7 140.9-77.8 0-140.8-63-140.9-140.7 0-77.8 63-140.8 140.7-140.9h.1c77.7.1 140.7 63 140.8 140.7z" xmlns="http://www.w3.org/2000/svg" />
  </symbol>
  <symbol id="icon-basket" viewBox="0 0 512 512">
    <path d="M400.3 454.7H112.7l-.7-1.9-64.3-232.6H464l-63.7 234.5zm105-265.2c-6.3-6.9-15.5-10.5-24.8-9.7h-23.1c-.4-.9-.9-1.6-1.5-2.4L334.6 24.9c-7.2-9.1-20.3-10.7-29.4-3.7-8.9 6.6-10.8 19.1-4.2 28 .2.2.3.4.4.6l103.7 129.9H105.6L212.7 50.1c7.1-8.5 6-21.1-2.5-28.2-.3-.2-.4-.3-.7-.5-9.1-7.4-22.3-6.1-29.7 3l-.2.2L53.8 176.5c-.6.8-1.2 1.6-1.7 2.5H31.8c-9.3-.4-18.2 3.5-24.3 10.6-5.6 7.2-7.4 16.8-4.6 25.5l68.6 248c2.1 7.6 10.6 32.4 31.8 32.4h305.6c16.2 0 27.6-15.2 31.8-30.3l68.3-252c2.8-7.9 1.4-16.9-3.7-23.7z" xmlns="http://www.w3.org/2000/svg" />
    <path d="M161.7 393.5c11.5 0 20.8-9.1 21.2-20.4v-81.4c0-11.7-9.5-21.2-21.2-21.2s-21.2 9.5-21.2 21.2v81.5c.4 11.3 9.8 20.3 21.2 20.3zm188.9 0c11.5 0 20.8-9.1 21.2-20.4v-81.4c0-11.7-9.5-21.2-21.2-21.2s-21.2 9.5-21.2 21.2v81.5c.5 11.3 9.8 20.3 21.2 20.3zm-94.5 0c11.5 0 20.8-9.1 21.2-20.4v-81.4c0-11.7-9.5-21.2-21.2-21.2s-21.2 9.5-21.2 21.2v81.5c.5 11.3 9.8 20.3 21.2 20.3z" xmlns="http://www.w3.org/2000/svg" />
  </symbol>
  <symbol id="icon-arrow-down" viewBox="0 0 512 512">
    <path d="M3 150.6c0-23.3 18.9-42.2 42.1-42.2 11.2 0 21.9 4.4 29.8 12.4l181.1 181 181-181c16.5-16.5 43.2-16.4 59.7 0 16.5 16.5 16.4 43.2 0 59.7L285.8 391.2c-16.5 16.5-43.1 16.5-59.6 0L15.3 180.4C7.4 172.5 3 161.8 3 150.6z" xmlns="http://www.w3.org/2000/svg" />
  </symbol>
  <symbol id="icon-menu" viewBox="0 0 512 512">
    <path d="M492.2 119.3H19.8c-9.4-.7-16.4-8.9-15.8-18.3-.6-9.4 6.4-17.5 15.7-18.3h472.4c9.4.8 16.4 8.9 15.7 18.3.8 9.4-6.2 17.6-15.6 18.3zm0 155H19.8c-9.4-.8-16.4-8.9-15.8-18.3-.6-9.4 6.4-17.5 15.7-18.3h472.4c9.4.8 16.4 8.9 15.7 18.3.8 9.4-6.2 17.5-15.6 18.3zm0 154.9H19.8c-9.4-.7-16.4-8.9-15.8-18.2-.6-9.4 6.4-17.5 15.7-18.3h472.4c9.4.8 16.4 8.9 15.7 18.3.8 9.3-6.2 17.5-15.6 18.2z" xmlns="http://www.w3.org/2000/svg" />
  </symbol>
  <symbol id="icon-close" viewBox="0 0 512 512">
    <path d="M315 256L493.8 77.1c7.8-7.8 12.2-18.4 12.2-29.5 0-23-18.7-41.6-41.7-41.6-11.1 0-21.7 4.4-29.5 12.2L256 197.2 77.2 18.3C60.9 2 34.5 2 18.2 18.2s-16.3 42.7 0 58.9L197 256 18.2 434.9C10.4 442.7 6 453.3 6 464.4c0 23 18.7 41.6 41.7 41.6 11.1 0 21.7-4.4 29.5-12.2l178.8-179 178.8 178.9c16.3 16.3 42.7 16.3 58.9 0 16.3-16.3 16.3-42.7 0-58.9L315 256z" xmlns="http://www.w3.org/2000/svg" />
  </symbol>
  <symbol id="icon-close-sm" viewBox="0 0 30 30">
    <path d="M 7 4 C 6.744125 4 6.4879687 4.0974687 6.2929688 4.2929688 L 4.2929688 6.2929688 C 3.9019687 6.6839688 3.9019687 7.3170313 4.2929688 7.7070312 L 11.585938 15 L 4.2929688 22.292969 C 3.9019687 22.683969 3.9019687 23.317031 4.2929688 23.707031 L 6.2929688 25.707031 C 6.6839688 26.098031 7.3170313 26.098031 7.7070312 25.707031 L 15 18.414062 L 22.292969 25.707031 C 22.682969 26.098031 23.317031 26.098031 23.707031 25.707031 L 25.707031 23.707031 C 26.098031 23.316031 26.098031 22.682969 25.707031 22.292969 L 18.414062 15 L 25.707031 7.7070312 C 26.098031 7.3170312 26.098031 6.6829688 25.707031 6.2929688 L 23.707031 4.2929688 C 23.316031 3.9019687 22.682969 3.9019687 22.292969 4.2929688 L 15 11.585938 L 7.7070312 4.2929688 C 7.5115312 4.0974687 7.255875 4 7 4 z" xmlns="http://www.w3.org/2000/svg" />
  </symbol>
  <symbol id="icon-arrow-left" viewBox="0 0 512 512">
    <path d="M361 4c23.2 0 42 18.8 42 42 0 11.1-4.4 21.8-12.3 29.7L210.4 256l180.3 180.3c16.4 16.4 16.4 43 0 59.4s-43 16.4-59.4 0l-209.9-210c-16.4-16.4-16.4-43 0-59.4l210-210C339.2 8.4 349.8 4 361 4z" xmlns="http://www.w3.org/2000/svg" />
  </symbol>
  <symbol id="icon-arrow-right" viewBox="0 0 512 512">
    <path d="M152.3 505c-22.9 0-41.5-18.6-41.5-41.5 0-11 4.4-21.6 12.2-29.4L301.1 256 122.9 77.9c-16.2-16.2-16.2-42.5 0-58.7s42.5-16.2 58.7 0L389 226.7c16.2 16.2 16.2 42.5 0 58.7L181.6 492.9c-7.8 7.7-18.3 12.1-29.3 12.1z" xmlns="http://www.w3.org/2000/svg" />
  </symbol>
  <symbol id="icon-external" viewBox="0 0 512 512">
    <path d="M311.7 61.1h100L137.9 334.8l39.3 39.3 273.7-273.7v100h55.7V5.4H311.7v55.7z" xmlns="http://www.w3.org/2000/svg" />
    <path d="M256 61.1V5.4H61.1C30.2 5.4 5.4 30.5 5.4 61.1v389.8c0 30.7 24.8 55.7 55.7 55.7h389.8c30.6 0 55.7-25 55.7-55.7V256h-55.7v194.9H61.1V61.1H256z" xmlns="http://www.w3.org/2000/svg" />
  </symbol>
  <symbol id="icon-standard" viewBox="0 0 512 512">
    <path d="M406.8 263.9c7.9-7.9 19.7-7.9 27.5 0l49.8-49.8c7.9-7.9 7.9-22.3 0-30.2l-156-156c-7.9-7.9-22.3-7.9-30.2 0l-49.8 49.8c7.9 7.9 7.9 19.7 0 27.5-7.9 7.9-19.7 7.9-27.5 0L27.9 297.9c-7.9 7.9-7.9 22.3 0 30.2l156 156c7.9 7.9 22.3 7.9 30.2 0l192.7-192.7c-6.6-7.9-6.6-21 0-27.5zm-139-112.8c-7.9-7.9-7.9-19.7 0-27.5 7.9-7.9 19.7-7.9 27.5 0 7.9 7.9 7.9 19.7 0 27.5-7.8 7.9-19.6 7.9-27.5 0zm45.9 47.2c-7.9-7.9-7.9-19.7 0-27.5 7.9-7.9 19.7-7.9 27.5 0 7.9 7.9 7.9 19.7 0 27.5-7.9 7.9-19.7 6.6-27.5 0zm47.2 45.9c-7.9-7.9-7.9-19.7 0-27.5 7.9-7.9 19.7-7.9 27.5 0 7.9 7.9 7.9 19.7 0 27.5-7.9 7.9-19.7 7.9-27.5 0z" xmlns="http://www.w3.org/2000/svg" />
  </symbol>
  <symbol id="icon-abo-traject" viewBox="0 0 512 512">
    <path d="M301.3 32.3c-16.7-3.6-32.2 7.2-35.8 23.9-3.6 16.7 7.2 32.2 23.9 35.8 78.7 15.5 134.8 84.7 134.8 164.6 0 93-75.1 168.2-168.2 168.2S87.8 349.6 87.8 256.5c0-49.4 21.6-95.4 57.2-126.8v17.1c0 16.7 14.3 31 31 31s31-13.1 29.8-31V57.4c0-8.3-2.4-15.5-8.3-21.5-6-6-13.1-9.5-21.5-9.5H86.6c-17.9 0-31 14.3-31 31s14.3 31 31 31h13.8C54.4 131.3 27 192 27 256.5c0 126.4 102.6 229 229 229s229-102.6 229-229C485 148 408.7 53.8 301.3 32.3z" xmlns="http://www.w3.org/2000/svg" />
  </symbol>
  <symbol id="icon-abo-citywide" viewBox="0 0 512 512">
    <path d="M300.2 37.8c-16.3-3.5-31.4 7-34.9 23.3s7 31.4 23.3 34.9C365.4 111.1 420 178.6 420 256.5c0 90.7-73.3 164-164 164s-164-73.3-164-164c0-48.2 21.1-93 55.8-123.7v16.7c0 16.3 14 30.2 30.2 30.2 16.3 0 30.2-12.8 29.1-30.2V62.2c0-8.1-2.3-15.1-8.1-20.9S186.2 32 178.1 32H90.8c-17.5 0-30.2 14-30.2 30.2 0 16.3 14 30.2 30.2 30.2h13.5c-44.9 41.8-71.6 101.1-71.6 164 0 123.3 100.1 223.4 223.4 223.4s223.4-100.1 223.4-223.4c-.1-105.7-74.6-197.6-179.3-218.6z" xmlns="http://www.w3.org/2000/svg" />
  </symbol>
  <symbol id="icon-airport" viewBox="0 0 512 512">
    <path d="M469.3 448l-77.2-243c0-1.4-1.4-2.9-2.9-4.3l84.3-78.6c24.3-24.3 21.4-61.5 0-84.3-22.9-22.9-60-24.3-84.3 0L312 123.5c-1.4-1.4-2.9-2.9-5.7-2.9l-243-77.2c-15.7-7.1-31.4-4.3-37.2 4.3l-2.9 5.7c-7.1 11.4 1.4 30 20 40l191.6 115.8-131.5 145.9-1.4-1.4s-48.6-17.2-64.3 0l-2.9 2.9c-10 10-4.3 20 4.3 30l87.2 87.2c10 10 20 14.3 30 4.3l2.9-2.9c14.3-14.3 2.9-55.8 0-62.9l144.4-132.9 114.4 190.1c11.4 18.6 28.6 27.2 40 20l5.7-2.9c10-7.2 12.8-22.9 5.7-38.6z" xmlns="http://www.w3.org/2000/svg" />
  </symbol>
  <symbol id="icon-phone" viewBox="0 0 512 512">
    <path d="M391.9 323.4c.6 4.4-.7 8.2-4 11.5l-38.1 37.8c-1.7 1.9-4 3.5-6.8 4.9-2.8 1.3-5.5 2.2-8.2 2.6-.2 0-.7.1-1.7.2s-2.2.2-3.7.2c-3.7 0-9.5-.6-17.6-1.8-8.2-1.2-18-4.3-29.8-9.2-11.7-4.9-25.1-12.2-39.9-22-14.8-9.7-30.7-23.2-47.5-40.2-13.3-13.2-24.5-25.8-33.2-37.8-8.8-12-15.9-23.2-21.2-33.5-5.4-10.2-9.3-19.5-12-27.8-2.7-8.3-4.4-15.5-5.5-21.5-1-6-1.3-10.7-1.1-14.2.2-3.4.3-5.4.3-5.7.4-2.7 1.2-5.4 2.6-8.2 1.3-2.8 3-5.1 4.9-6.8l38.1-38.1c2.7-2.7 5.7-4 9.2-4 2.4 0 4.7.7 6.6 2.1 1.9 1.5 3.5 3.2 4.9 5.3l30.6 58.2c1.7 3 2.2 6.4 1.5 10-.7 3.7-2.4 6.7-4.9 9.2l-14.1 14c-.4.4-.7 1-1 1.8-.3.9-.4 1.6-.4 2.1.7 4 2.4 8.6 5.2 13.8 2.3 4.6 5.8 10.2 10.5 16.7 4.7 6.6 11.6 14.2 20.3 22.8 8.6 8.8 16.2 15.6 22.9 20.5 6.7 4.9 12.3 8.5 16.7 10.7 4.4 2.3 7.9 3.7 10.3 4.2l3.6.7c.4 0 1-.2 1.8-.4s1.5-.6 1.8-1l16.4-16.5c3.4-3 7.4-4.6 12-4.6 3.2 0 5.8.6 7.8 1.7h.3l55.2 32.7c4 2.8 6.4 5.8 7.2 9.6z" xmlns="http://www.w3.org/2000/svg" />
  </symbol>
  <symbol id="icon-tl-smartphone" viewBox="0 0 512 512">
    <path d="M365.5 5.7h-219c-25.4 0-46.9 21.5-46.9 46.9v406.7c0 25.4 21.5 46.9 46.9 46.9h219c25.4 0 46.9-21.5 46.9-46.9V52.6c0-25.4-21.5-46.9-46.9-46.9zM193.4 29.2h125.2v15.6H193.4V29.2zM256 475c-17.6 0-31.3-13.7-31.3-31.3s13.7-31.3 31.3-31.3 31.3 13.7 31.3 31.3S273.6 475 256 475zm125.2-93.9H130.9V68.3h250.3v312.8z" xmlns="http://www.w3.org/2000/svg" />
  </symbol>
  <symbol id="icon-b-day-sensation" viewBox="0 0 512 512">
    <path d="M498.5 290.4c-6.8-56.3-69.8-176-185-176-80.5 0-134.6 43.7-170.4 137.5-34.9 91.4-112.2 86.1-113.8 86.2-9.7.9-16.8 9.4-16 19.2.9 9.1 8.5 16.1 17.5 16.1.5 0 1.1 0 1.6-.1 6.1-.5 60.1 1.3 119.4-67.5v93.6c0 9.7 7.9 17.6 17.6 17.6s17.6-7.9 17.6-17.6v-161c11.2-23.2 23.8-41.6 38.3-55.5v213.4c0 9.7 7.9 17.6 17.6 17.6 9.7 0 17.6-7.9 17.6-17.6V159.5c11.8-5 24.4-8.1 38.2-9.3v246.1c0 9.7 7.9 17.6 17.6 17.6 9.8 0 17.6-7.9 17.6-17.6V151.1c14.6 2.2 27.3 6.8 38.3 12.9-.1.5-.1 1.1-.1 1.6v230.5c0 9.7 7.9 17.6 17.6 17.6 9.8 0 17.6-7.9 17.6-17.6V194.4c29.3 36.9 55.4 97 55.8 100.2 1.1 9.6 10.1 16.6 19.6 15.4 10-1.1 17-9.9 15.8-19.6z" xmlns="http://www.w3.org/2000/svg" />
    <path d="M174.1 156.9c4.5 0 9-1.7 12.4-5.1 9.4-9.4 20.4-17.2 32.4-23.4 8.7-4.4 12.1-15.1 7.7-23.7-4.4-8.7-15.1-12.1-23.7-7.7-15.3 7.8-29.1 17.8-41.2 29.7-6.9 6.9-7 18-.1 24.9 3.4 3.5 8 5.3 12.5 5.3z" xmlns="http://www.w3.org/2000/svg" />
  </symbol>
  <symbol id="icon-music" viewBox="0 0 512 512">
    <path d="M152.5 98.8v101.9l1.5 143.2c-11.8-3-25.1-4.4-39.9-1.5-42.8 7.4-76.8 39.9-76.8 73.8 0 32.5 34 53.2 76.8 45.8s76.8-39.9 76.8-73.8V193.3l242.2-38.4v144.7c-11.8-3-25.1-4.4-39.9-1.5-42.8 7.4-76.8 39.9-76.8 73.8 0 32.5 34 53.2 76.8 45.8 42.8-7.4 76.8-39.9 76.8-73.8V48.6L152.5 98.8z" xmlns="http://www.w3.org/2000/svg" />
  </symbol>
  <symbol id="icon-home-ticket" viewBox="0 0 512 512">
    <path d="M205.8 469.3V318.8h100.4v150.6h125.5V268.5H507L256 42.7 5 268.5h75.3v200.8h125.5z" xmlns="http://www.w3.org/2000/svg" />
  </symbol>
  <symbol id="icon-logout" viewBox="0 0 512 512">
	<path d="M12.2,272.5l167.2,167.2c14.9,14.9,40.8,4.5,40.8-16.9v-95.5h135.3c13.2,0,23.9-10.6,23.9-23.9v-95.5   c0-13.2-10.6-23.9-23.9-23.9H220.2V88.5c0-21.3-25.8-31.8-40.8-16.9L12.2,238.7C3,248,3,263.2,12.2,272.5z M315.7,434.7v-39.8   c0-6.6,5.4-11.9,11.9-11.9h83.6c17.6,0,31.8-14.2,31.8-31.8v-191c0-17.6-14.2-31.8-31.8-31.8h-83.6c-6.6,0-11.9-5.4-11.9-11.9V76.5   c0-6.6,5.4-11.9,11.9-11.9h83.6c52.7,0,95.5,42.8,95.5,95.5v191c0,52.7-42.8,95.5-95.5,95.5h-83.6   C321.1,446.6,315.7,441.3,315.7,434.7z" xmlns="http://www.w3.org/2000/svg" />
</symbol>
</svg>
    


<div class="modal-overlay js-session-timeout-popup js-timeout-modal" data-extendSessionPeriodInSeconds="60" data-extendSessionPeriodInMilliSeconds="60000" data-timeout="3600000" data-editor="0">
    <div class="modal theme-white">
        <div class="modal-content">
            <input type="hidden" id="sessionExpiredTitle" value="Je sessie is verlopen" />
            <input type="hidden" id="extendSessionTitle" value="Je sessie verloopt in" />
            <input type="hidden" id="extendSessionTimeMessage" value="{0} seconden" />
            <div class="h1" id="sessionModelTitle">Je sessie is verlopen</div>
            <div class="h1" id="extendSessionTimeText">{0} seconden</div>
            <div></div>
            <div class="btn-container">
                <a class="btn btn--secondary js-session-timeout-extend-page" href="javascript:void(0);">Pagina actief houden</a>
                    <a class="btn btn--secondary js-session-timeout-reload-page" href="javascript:void(0);">Herlaad de pagina</a>
                

            </div>
        </div>
    </div>
</div>


    
<div class="loader-nmbs" id="action-wait-loader">
    <div class="loader__img">
<img src="https://www.belgiantrain.be/-/media/project/host/sharedcontrols/loader-sober.ashx?h=200&amp;la=nl&amp;w=300&amp;hash=C26FAC6F71145C95B5117E32FBBFD52B0D169921" alt="" />    </div>
</div>
<style>
    .loader-animated {
        -webkit-animation-duration: 6s;
        animation-duration:  6s;
        -webkit-animation-fill-mode: both;
        animation-fill-mode: both;
    }

    @-webkit-keyframes fadeIn {
        0% {opacity: 0;}
        100% {opacity: 1;}
    }

    @keyframes fadeIn {
        0% {opacity: 0;}
        100% {opacity: 1;}
    }

    .fadeIn {
        -webkit-animation-name: fadeIn;
        animation-name: fadeIn;
    }
</style>
</body>
</html>
