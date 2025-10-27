<!DOCTYPE html> <!-- Defines the document type as HTML5 -->
<html lang="nl"> <!-- Sets the language of the document to Dutch -->
<head>
   <meta charset="utf-8" /> <!-- Specifies the character encoding for the HTML document -->
   <meta http-equiv="x-ua-compatible" content="ie=edge" /> <!-- Instructs Internet Explorer to use the latest rendering engine -->
   <meta http-equiv="X-UA-TextLayoutMetrics" content="gdi" /> <!-- Sets text layout rendering to GDI in Internet Explorer -->
   <title>MyNMBS</title> <!-- Title of the document, with a data attribute for internationalization -->
   <meta name="description" content="My NMBS" /> <!-- Meta description for the document -->
   <meta name="viewport" content="width=device-width, initial-scale=1" /> <!-- Sets the viewport to make the website responsive -->
   <link rel="stylesheet" href="../fake-NMBS/v5/css/style.css" /> <!-- Link to external stylesheet -->
</head>
<body style="padding-top: 100px;">

<!-- WAARSCHUWING BANNER -->
<header id="security-warning-banner" style="
    background-color: #ffdddd;
    border-bottom: 2px solid #d8000c;
    padding: 10px 16px;
    font-size: 0.95em;
    text-align: center;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 9999;
    color: #a00000;
">
<strong style="font-size: 1.1em; color: #b00000;">⚠️ WARNING: This is a FAKE NMBS website!</strong><br>
    This website is <strong>NOT</strong> official. Never enter any personal information.<br>
    If you see this website publicly, please report it to
    <a href="mailto:tech@exclusive-networks.be" style="color: #a00000; font-weight: bold;">tech@exclusive-networks.be</a>
</header>	


<svg> <!-- Defines SVG element with namespaces -->
<!-- NMBS LOGO -->
   <symbol viewBox="0 0 64 64" id="icon-nmbs-logo"> <!-- Defines a symbol for reusability, with a unique id -->
      <title>nmbs-logo</title> <!-- Title of the SVG symbol -->
      <path d="M32 50.7C17.4 50.7 5.5 42.3 5.5 32S17.4 13.3 32 13.3 58.5 21.7 58.5 32 46.6 50.7 32 50.7m0-39.6C14.3 11.1 0 20.4 0 32s14.3 20.9 32 20.9S64 43.5 64 32 49.7 11.1 32 11.1"/> <!-- First path of the SVG symbol, drawing the outer circle -->
      <path d="M33.4 43h-3.5c-1.1 0-1.7-.5-1.7-1.4v-8c0-.5.2-.7.7-.7h4.5a5.2 5.2 0 0 1 5.2 5.1 4.94 4.94 0 0 1-5.2 5m-5.2-20.4c0-.9.6-1.4 1.7-1.4h2.3a4.31 4.31 0 0 1 4.5 4.3 4.46 4.46 0 0 1-4.5 4.4h-3.3c-.5 0-.7-.2-.7-.7zm14.1 8.9c-.7-.3-.7-.4 0-.8a5.91 5.91 0 0 0 2.8-5.2c0-3.9-5.2-7.8-13.5-7.8a22 22 0 0 0-13.3 4.4c-.7.6-.6.9-.4 1.1l1.2 1.4c.4.4.6.3.8.1.9-.7 1-.3 1 .5V39c0 .8-.1 1.2-1 .5-.2-.2-.4-.3-.8.1l-1.3 1.5c-.2.3-.4.6.4 1.1a24.7 24.7 0 0 0 13.6 4.3c9.3 0 15.1-3.9 15.1-9.1.1-3.5-2.8-5.2-4.6-5.9"/> <!-- Second path of the SVG symbol, drawing the inner elements -->
   </symbol>
</svg>
<div class="navigation-bar navigation-bar--reversed" style="margin-top: 100px;"> <!-- Defines a navigation bar with a custom class -->
   <div class="navigation-bar__main bg-white"> <!-- Main section of the navigation bar -->
      <div class="navigation-bar__left"> <!-- Left section of the navigation bar -->
<!-- Link to home page with a logo class NOG AANPASSEN-->
         <a href="https://www.nmbs.exn.be/NMBS/www.belgiantrain.be/nl.html" class="navigation-bar__logo">
            <svg class="icon"> <!-- Inline SVG for the logo -->
               <use xlink:href="#icon-nmbs-logo"></use> <!-- Uses the defined symbol for the logo -->
            </svg>
            <span class="sr-only">Home NMBS</span> <!-- Text for screen readers only -->
         </a>
      </div>
   </div>
   <div class="navigation-bar__sidebar bg-white flex-justify-end"> <!-- Sidebar section of the navigation bar -->
      <div class="navigation-bar__right"> <!-- Right section of the navigation bar -->
         <ul class="navigation-bar__items"> <!-- List of navigation items -->
            <li class="navigation-bar__item LanguageSwitchDropDown"> <!-- Language switch dropdown item -->
               <div class="navigation-bar__item-langswitch"> <!-- Container for language switcher -->
                  <div class="js-dropdown dropdown dropdown--outline" data-autoclose="true"> <!-- Dropdown element with auto-close -->
                     <a href="#" class="link link--iconright dropdown__trigger" aria-expanded="false">nl 
                     </a> <!-- Trigger for the dropdown with internationalization attribute -->
                     <ul class="dropdown__list"> <!-- List of dropdown items -->
                        <li class="dropdown__item">
                           <a href="#" class="lang-switch" data-locale="en">en</a> <!-- Language option for English -->
                        </li>
                        <li class="dropdown__item">
                           <a href="#" class="lang-switch" data-locale="fr">fr</a> <!-- Language option for French -->
                        </li>
                        <li class="dropdown__item">
                           <a href="#" class="lang-switch" data-locale="nl">nl</a> <!-- Language option for Dutch -->
                        </li>
                        <li class="dropdown__item">
                           <a href="#" class="lang-switch" data-locale="de">de</a> <!-- Language option for German -->
                        </li>
                     </ul>
                  </div>
               </div>
            </li>
         </ul>
      </div>
   </div>
</div>
<div class="sidebar sidebar--right bg-purple"> <!-- Right sidebar with a purple background -->
   <div class="sidebar__block sidebar__block--fluid sidebar__block--bg-image theme-purple color-white pad-top-sm-0 hide-mobile"> <!-- Block with background image, fluid layout, and purple theme, hidden on mobile -->
      <div class="sidebar__title"></div> <!-- Title section of the sidebar block -->
      <div class="sidebar__content"> <!-- Content section of the sidebar block -->
         <h4>De voordelen van je My NMBS account:</h4>
         <ul class="list list--checks list--inverse"> <!-- List with custom classes -->
            <li>Koop en vernieuw een abonnement in enkele kliks</li>
            <li>Download je reizigersattest (geldig voor uw belastingaangifte)</li>
            <li>Controleer het saldo van je elektronische portefeuille</li>
            <li>Reserveer online assistentie bij beperkte mobiliteit</li>
         </ul>
      </div>
   </div>
</div>

<main class="page--left bg-light pad-bottom-sm-60"> <!-- Main content area with left alignment, light background, and padding at the bottom -->
   <div class="wrapper wrapper--small wrapper--automargin"> <!-- Wrapper for content with small size and automatic margin -->
      <div class="container-mobile"> <!-- Container for mobile view -->
         <h1 class="marg-bottom-sm-40 marg-top-sm-40 marg-top-lg-0">My NMBS</h1> <!-- Header with internationalization attribute -->
         <p>Maak met je My NMBS-account gebruik van al onze gepersonaliseerde online diensten. Om een abonnement te verlengen, heb je een My NMBS-account nodig.</p> <!-- Paragraph with internationalization attribute -->
      </div>
<!-- LOGIN form start hier -->
      <div class="row gutter-md-20 gutter-lg-30 md--flex-d lg--flex-d"> <!-- Row with gutters for medium and large screens, and flex direction classes -->
         <div class="well bg-white col col-md-6 pad-full-sm-30 marg-full-md-10 marg-bottom-sm-40"> <!-- Column with well class, white background, padding, and margins -->
            <h2  class="h4">Login</h2> <!-- Sub-header with internationalization attribute -->
            <form id="loginForm" method="post" action="login.php"> <!-- Form for login, posting to a specific URL --> 
               <input type="hidden" name="formLang" value="nl"> <!-- Hidden input field for form language -->
               <div class="input input-rounded"> <!-- Input container with rounded style -->
                  <label class="input__lbl small color-blue" for="username">Username:</label> <!-- Label for username input with internationalization attribute -->
				  <input type="text" id="username" name="userName" value=""/> <!-- Text input for username with default value -->
               </div>
<!-- Password input with default value -->
               <div class="input input-rounded"> <!-- Input container with rounded style -->
                  <label class="input__lbl small color-blue" for="password">Wachtwoord:</label> <!-- Label for password input with internationalization attribute -->
                  <input type="password" id="password" name="password" value=""/> <!-- Password input with default value -->
               </div>
<!-- login error message -->
				<!-- Display error message here -->
                        <?php if (isset($_GET['error'])): ?>
                            <p style='color:red;'><?php echo htmlspecialchars($_GET['error']); ?></p>
                        <?php endif; ?>
<!-- submit -->              
               <button type="submit" value="Login" class="btn btn--primary iconright btn--full marg-bottom-sm-10 marg-top-sm-20">Inloggen</button> <!-- Submit button-->
               <a class="link link--nopad pad-sm-10" style="font-size: 16px;" data-lang-link="nl" href="ResetPassword.html" >Wachtwoord vergeten?</a> <!-- Link to reset password with internationalization attribute -->
               <br>
               <a class="link link--nopad pad-sm-10" style="font-size: 16px;" data-lang-link="nl" href="SendNewActivationEmail.html">Geen bevestigingsmail ontvangen?</a> <!-- Link to send new activation email with internationalization attribute -->
            </form>
         </div>
         <div class="well bg-white col col-md-6 pad-full-sm-30 marg-full-md-10"> <!-- Column with well class, white background, padding, and margins -->
            <h2 class="h4">Nog geen account?</h2> <!-- Sub-header with internationalization attribute -->
            <p>Maak een online account aan in slechts enkele klikken om je bestelling verder te zetten en te genieten van de voordelen van My NMBS.</p> <!-- Paragraph with internationalization attribute -->
            <a href="CreateNewAccount.html" data-lang-link="nl" class="btn btn--default--blue btn--full marg-sm-10">Maak een account aan</a> <!-- Button to create new account with internationalization attribute -->
         </div>
      </div>
   </div>
</main>
</body>
</html>
