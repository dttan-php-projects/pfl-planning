<?php 
  require("../Template.php");
  if(!isset($_COOKIE["ZeroIntranet"])) header('Content-type: text/html; charset=utf-8'); 
  else header('Location: /Index.php');
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Login form</title>
  <link href="../Module/css/font-awesome.css" rel="stylesheet">
  <link rel="stylesheet" href="../Module/css/reset.min.css">
  <script src="../Module/JS/jquery-1.10.1.min.js"></script>
  <script src="../Module/JS/trianglify.min.js"></script>
</head>
  <style>
  /* cyrillic-ext */
@font-face {
  font-family: 'Open Sans';
  font-style: italic;
  font-weight: 300;
  src: local('Open Sans Light Italic'), local('OpenSans-LightItalic'), url(https://fonts.gstatic.com/s/opensans/v16/memnYaGs126MiZpBA-UFUKWyV9hmIqOjjg.woff2) format('woff2');
  unicode-range: U+0460-052F, U+1C80-1C88, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;
}
/* cyrillic */
@font-face {
  font-family: 'Open Sans';
  font-style: italic;
  font-weight: 300;
  src: local('Open Sans Light Italic'), local('OpenSans-LightItalic'), url(https://fonts.gstatic.com/s/opensans/v16/memnYaGs126MiZpBA-UFUKWyV9hvIqOjjg.woff2) format('woff2');
  unicode-range: U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
}
/* greek-ext */
@font-face {
  font-family: 'Open Sans';
  font-style: italic;
  font-weight: 300;
  src: local('Open Sans Light Italic'), local('OpenSans-LightItalic'), url(https://fonts.gstatic.com/s/opensans/v16/memnYaGs126MiZpBA-UFUKWyV9hnIqOjjg.woff2) format('woff2');
  unicode-range: U+1F00-1FFF;
}
/* greek */
@font-face {
  font-family: 'Open Sans';
  font-style: italic;
  font-weight: 300;
  src: local('Open Sans Light Italic'), local('OpenSans-LightItalic'), url(https://fonts.gstatic.com/s/opensans/v16/memnYaGs126MiZpBA-UFUKWyV9hoIqOjjg.woff2) format('woff2');
  unicode-range: U+0370-03FF;
}
/* vietnamese */
@font-face {
  font-family: 'Open Sans';
  font-style: italic;
  font-weight: 300;
  src: local('Open Sans Light Italic'), local('OpenSans-LightItalic'), url(https://fonts.gstatic.com/s/opensans/v16/memnYaGs126MiZpBA-UFUKWyV9hkIqOjjg.woff2) format('woff2');
  unicode-range: U+0102-0103, U+0110-0111, U+1EA0-1EF9, U+20AB;
}
/* latin-ext */
@font-face {
  font-family: 'Open Sans';
  font-style: italic;
  font-weight: 300;
  src: local('Open Sans Light Italic'), local('OpenSans-LightItalic'), url(https://fonts.gstatic.com/s/opensans/v16/memnYaGs126MiZpBA-UFUKWyV9hlIqOjjg.woff2) format('woff2');
  unicode-range: U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
}
/* latin */
@font-face {
  font-family: 'Open Sans';
  font-style: italic;
  font-weight: 300;
  src: local('Open Sans Light Italic'), local('OpenSans-LightItalic'), url(https://fonts.gstatic.com/s/opensans/v16/memnYaGs126MiZpBA-UFUKWyV9hrIqM.woff2) format('woff2');
  unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
}
/* cyrillic-ext */
@font-face {
  font-family: 'Open Sans';
  font-style: italic;
  font-weight: 400;
  src: local('Open Sans Italic'), local('OpenSans-Italic'), url(https://fonts.gstatic.com/s/opensans/v16/mem6YaGs126MiZpBA-UFUK0Udc1UAw.woff2) format('woff2');
  unicode-range: U+0460-052F, U+1C80-1C88, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;
}
/* cyrillic */
@font-face {
  font-family: 'Open Sans';
  font-style: italic;
  font-weight: 400;
  src: local('Open Sans Italic'), local('OpenSans-Italic'), url(https://fonts.gstatic.com/s/opensans/v16/mem6YaGs126MiZpBA-UFUK0ddc1UAw.woff2) format('woff2');
  unicode-range: U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
}
/* greek-ext */
@font-face {
  font-family: 'Open Sans';
  font-style: italic;
  font-weight: 400;
  src: local('Open Sans Italic'), local('OpenSans-Italic'), url(https://fonts.gstatic.com/s/opensans/v16/mem6YaGs126MiZpBA-UFUK0Vdc1UAw.woff2) format('woff2');
  unicode-range: U+1F00-1FFF;
}
/* greek */
@font-face {
  font-family: 'Open Sans';
  font-style: italic;
  font-weight: 400;
  src: local('Open Sans Italic'), local('OpenSans-Italic'), url(https://fonts.gstatic.com/s/opensans/v16/mem6YaGs126MiZpBA-UFUK0adc1UAw.woff2) format('woff2');
  unicode-range: U+0370-03FF;
}
/* vietnamese */
@font-face {
  font-family: 'Open Sans';
  font-style: italic;
  font-weight: 400;
  src: local('Open Sans Italic'), local('OpenSans-Italic'), url(https://fonts.gstatic.com/s/opensans/v16/mem6YaGs126MiZpBA-UFUK0Wdc1UAw.woff2) format('woff2');
  unicode-range: U+0102-0103, U+0110-0111, U+1EA0-1EF9, U+20AB;
}
/* latin-ext */
@font-face {
  font-family: 'Open Sans';
  font-style: italic;
  font-weight: 400;
  src: local('Open Sans Italic'), local('OpenSans-Italic'), url(https://fonts.gstatic.com/s/opensans/v16/mem6YaGs126MiZpBA-UFUK0Xdc1UAw.woff2) format('woff2');
  unicode-range: U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
}
/* latin */
@font-face {
  font-family: 'Open Sans';
  font-style: italic;
  font-weight: 400;
  src: local('Open Sans Italic'), local('OpenSans-Italic'), url(https://fonts.gstatic.com/s/opensans/v16/mem6YaGs126MiZpBA-UFUK0Zdc0.woff2) format('woff2');
  unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
}
/* cyrillic-ext */
@font-face {
  font-family: 'Open Sans';
  font-style: italic;
  font-weight: 700;
  src: local('Open Sans Bold Italic'), local('OpenSans-BoldItalic'), url(https://fonts.gstatic.com/s/opensans/v16/memnYaGs126MiZpBA-UFUKWiUNhmIqOjjg.woff2) format('woff2');
  unicode-range: U+0460-052F, U+1C80-1C88, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;
}
/* cyrillic */
@font-face {
  font-family: 'Open Sans';
  font-style: italic;
  font-weight: 700;
  src: local('Open Sans Bold Italic'), local('OpenSans-BoldItalic'), url(https://fonts.gstatic.com/s/opensans/v16/memnYaGs126MiZpBA-UFUKWiUNhvIqOjjg.woff2) format('woff2');
  unicode-range: U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
}
/* greek-ext */
@font-face {
  font-family: 'Open Sans';
  font-style: italic;
  font-weight: 700;
  src: local('Open Sans Bold Italic'), local('OpenSans-BoldItalic'), url(https://fonts.gstatic.com/s/opensans/v16/memnYaGs126MiZpBA-UFUKWiUNhnIqOjjg.woff2) format('woff2');
  unicode-range: U+1F00-1FFF;
}
/* greek */
@font-face {
  font-family: 'Open Sans';
  font-style: italic;
  font-weight: 700;
  src: local('Open Sans Bold Italic'), local('OpenSans-BoldItalic'), url(https://fonts.gstatic.com/s/opensans/v16/memnYaGs126MiZpBA-UFUKWiUNhoIqOjjg.woff2) format('woff2');
  unicode-range: U+0370-03FF;
}
/* vietnamese */
@font-face {
  font-family: 'Open Sans';
  font-style: italic;
  font-weight: 700;
  src: local('Open Sans Bold Italic'), local('OpenSans-BoldItalic'), url(https://fonts.gstatic.com/s/opensans/v16/memnYaGs126MiZpBA-UFUKWiUNhkIqOjjg.woff2) format('woff2');
  unicode-range: U+0102-0103, U+0110-0111, U+1EA0-1EF9, U+20AB;
}
/* latin-ext */
@font-face {
  font-family: 'Open Sans';
  font-style: italic;
  font-weight: 700;
  src: local('Open Sans Bold Italic'), local('OpenSans-BoldItalic'), url(https://fonts.gstatic.com/s/opensans/v16/memnYaGs126MiZpBA-UFUKWiUNhlIqOjjg.woff2) format('woff2');
  unicode-range: U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
}
/* latin */
@font-face {
  font-family: 'Open Sans';
  font-style: italic;
  font-weight: 700;
  src: local('Open Sans Bold Italic'), local('OpenSans-BoldItalic'), url(https://fonts.gstatic.com/s/opensans/v16/memnYaGs126MiZpBA-UFUKWiUNhrIqM.woff2) format('woff2');
  unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
}
/* cyrillic-ext */
@font-face {
  font-family: 'Open Sans';
  font-style: normal;
  font-weight: 300;
  src: local('Open Sans Light'), local('OpenSans-Light'), url(https://fonts.gstatic.com/s/opensans/v16/mem5YaGs126MiZpBA-UN_r8OX-hpOqc.woff2) format('woff2');
  unicode-range: U+0460-052F, U+1C80-1C88, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;
}
/* cyrillic */
@font-face {
  font-family: 'Open Sans';
  font-style: normal;
  font-weight: 300;
  src: local('Open Sans Light'), local('OpenSans-Light'), url(https://fonts.gstatic.com/s/opensans/v16/mem5YaGs126MiZpBA-UN_r8OVuhpOqc.woff2) format('woff2');
  unicode-range: U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
}
/* greek-ext */
@font-face {
  font-family: 'Open Sans';
  font-style: normal;
  font-weight: 300;
  src: local('Open Sans Light'), local('OpenSans-Light'), url(https://fonts.gstatic.com/s/opensans/v16/mem5YaGs126MiZpBA-UN_r8OXuhpOqc.woff2) format('woff2');
  unicode-range: U+1F00-1FFF;
}
/* greek */
@font-face {
  font-family: 'Open Sans';
  font-style: normal;
  font-weight: 300;
  src: local('Open Sans Light'), local('OpenSans-Light'), url(https://fonts.gstatic.com/s/opensans/v16/mem5YaGs126MiZpBA-UN_r8OUehpOqc.woff2) format('woff2');
  unicode-range: U+0370-03FF;
}
/* vietnamese */
@font-face {
  font-family: 'Open Sans';
  font-style: normal;
  font-weight: 300;
  src: local('Open Sans Light'), local('OpenSans-Light'), url(https://fonts.gstatic.com/s/opensans/v16/mem5YaGs126MiZpBA-UN_r8OXehpOqc.woff2) format('woff2');
  unicode-range: U+0102-0103, U+0110-0111, U+1EA0-1EF9, U+20AB;
}
/* latin-ext */
@font-face {
  font-family: 'Open Sans';
  font-style: normal;
  font-weight: 300;
  src: local('Open Sans Light'), local('OpenSans-Light'), url(https://fonts.gstatic.com/s/opensans/v16/mem5YaGs126MiZpBA-UN_r8OXOhpOqc.woff2) format('woff2');
  unicode-range: U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
}
/* latin */
@font-face {
  font-family: 'Open Sans';
  font-style: normal;
  font-weight: 300;
  src: local('Open Sans Light'), local('OpenSans-Light'), url(https://fonts.gstatic.com/s/opensans/v16/mem5YaGs126MiZpBA-UN_r8OUuhp.woff2) format('woff2');
  unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
}
/* cyrillic-ext */
@font-face {
  font-family: 'Open Sans';
  font-style: normal;
  font-weight: 400;
  src: local('Open Sans Regular'), local('OpenSans-Regular'), url(https://fonts.gstatic.com/s/opensans/v16/mem8YaGs126MiZpBA-UFWJ0bbck.woff2) format('woff2');
  unicode-range: U+0460-052F, U+1C80-1C88, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;
}
/* cyrillic */
@font-face {
  font-family: 'Open Sans';
  font-style: normal;
  font-weight: 400;
  src: local('Open Sans Regular'), local('OpenSans-Regular'), url(https://fonts.gstatic.com/s/opensans/v16/mem8YaGs126MiZpBA-UFUZ0bbck.woff2) format('woff2');
  unicode-range: U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
}
/* greek-ext */
@font-face {
  font-family: 'Open Sans';
  font-style: normal;
  font-weight: 400;
  src: local('Open Sans Regular'), local('OpenSans-Regular'), url(https://fonts.gstatic.com/s/opensans/v16/mem8YaGs126MiZpBA-UFWZ0bbck.woff2) format('woff2');
  unicode-range: U+1F00-1FFF;
}
/* greek */
@font-face {
  font-family: 'Open Sans';
  font-style: normal;
  font-weight: 400;
  src: local('Open Sans Regular'), local('OpenSans-Regular'), url(https://fonts.gstatic.com/s/opensans/v16/mem8YaGs126MiZpBA-UFVp0bbck.woff2) format('woff2');
  unicode-range: U+0370-03FF;
}
/* vietnamese */
@font-face {
  font-family: 'Open Sans';
  font-style: normal;
  font-weight: 400;
  src: local('Open Sans Regular'), local('OpenSans-Regular'), url(https://fonts.gstatic.com/s/opensans/v16/mem8YaGs126MiZpBA-UFWp0bbck.woff2) format('woff2');
  unicode-range: U+0102-0103, U+0110-0111, U+1EA0-1EF9, U+20AB;
}
/* latin-ext */
@font-face {
  font-family: 'Open Sans';
  font-style: normal;
  font-weight: 400;
  src: local('Open Sans Regular'), local('OpenSans-Regular'), url(https://fonts.gstatic.com/s/opensans/v16/mem8YaGs126MiZpBA-UFW50bbck.woff2) format('woff2');
  unicode-range: U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
}
/* latin */
@font-face {
  font-family: 'Open Sans';
  font-style: normal;
  font-weight: 400;
  src: local('Open Sans Regular'), local('OpenSans-Regular'), url(https://fonts.gstatic.com/s/opensans/v16/mem8YaGs126MiZpBA-UFVZ0b.woff2) format('woff2');
  unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
}
/* cyrillic-ext */
@font-face {
  font-family: 'Open Sans';
  font-style: normal;
  font-weight: 700;
  src: local('Open Sans Bold'), local('OpenSans-Bold'), url(https://fonts.gstatic.com/s/opensans/v16/mem5YaGs126MiZpBA-UN7rgOX-hpOqc.woff2) format('woff2');
  unicode-range: U+0460-052F, U+1C80-1C88, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;
}
/* cyrillic */
@font-face {
  font-family: 'Open Sans';
  font-style: normal;
  font-weight: 700;
  src: local('Open Sans Bold'), local('OpenSans-Bold'), url(https://fonts.gstatic.com/s/opensans/v16/mem5YaGs126MiZpBA-UN7rgOVuhpOqc.woff2) format('woff2');
  unicode-range: U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
}
/* greek-ext */
@font-face {
  font-family: 'Open Sans';
  font-style: normal;
  font-weight: 700;
  src: local('Open Sans Bold'), local('OpenSans-Bold'), url(https://fonts.gstatic.com/s/opensans/v16/mem5YaGs126MiZpBA-UN7rgOXuhpOqc.woff2) format('woff2');
  unicode-range: U+1F00-1FFF;
}
/* greek */
@font-face {
  font-family: 'Open Sans';
  font-style: normal;
  font-weight: 700;
  src: local('Open Sans Bold'), local('OpenSans-Bold'), url(https://fonts.gstatic.com/s/opensans/v16/mem5YaGs126MiZpBA-UN7rgOUehpOqc.woff2) format('woff2');
  unicode-range: U+0370-03FF;
}
/* vietnamese */
@font-face {
  font-family: 'Open Sans';
  font-style: normal;
  font-weight: 700;
  src: local('Open Sans Bold'), local('OpenSans-Bold'), url(https://fonts.gstatic.com/s/opensans/v16/mem5YaGs126MiZpBA-UN7rgOXehpOqc.woff2) format('woff2');
  unicode-range: U+0102-0103, U+0110-0111, U+1EA0-1EF9, U+20AB;
}
/* latin-ext */
@font-face {
  font-family: 'Open Sans';
  font-style: normal;
  font-weight: 700;
  src: local('Open Sans Bold'), local('OpenSans-Bold'), url(https://fonts.gstatic.com/s/opensans/v16/mem5YaGs126MiZpBA-UN7rgOXOhpOqc.woff2) format('woff2');
  unicode-range: U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
}
/* latin */
@font-face {
  font-family: 'Open Sans';
  font-style: normal;
  font-weight: 700;
  src: local('Open Sans Bold'), local('OpenSans-Bold'), url(https://fonts.gstatic.com/s/opensans/v16/mem5YaGs126MiZpBA-UN7rgOUuhp.woff2) format('woff2');
  unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
}
  body {
    font: 13px/20px "Open Sans", Tahoma, Verdana, sans-serif;
    color: black;
  }


/*Login form style*/
/* === Logo === */
.logo {
  background-position: center;
  height: 60px;
  width: 140px;
  margin: 100px auto 30px auto;
}

/* === Form === */
.form {
  width: 100%;
}
.form .field {
  position: relative;
  margin: 0 25px;
}
.form .field i {
  font-size: 18px;
  left: 0px;
  top: 0px;
  position: absolute;
  height: 44px;
  width: 44px;
  color: #f7f3eb;
  background: #676056;
  text-align: center;
  line-height: 44px;
  transition: all 0.3s ease-out;
  pointer-events: none;
}

/* === Login styles === */
.login {
  position: relative;
  margin: 10% auto;
  width: 370px;
  height: 345px;
  background: white;
  border-radius: 3px;
}
.login:before {
  content: '';
  position: absolute;
  top: -8px;
  right: -8px;
  bottom: -8px;
  left: -8px;
  z-index: -1;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 4px;
}
.login h1 {
  line-height: 55px;
  font-size: 24px;
  font-weight: bold;
  font-family: 'Open Sans', sans-serif;
  text-transform: uppercase;
  color: #fff;
  text-align: center;
  background: #1a80bc;
  border-top-left-radius: 3px;
  border-top-right-radius: 3px;
}
.login .submit {
  text-align: center;
}
.login p:first-child {
  margin-top: 30px;
}
.login p .remember {
  float: left;
}
.login p .remember label {
  color: #a7a599;
  font-size: 12px;
  cursor: pointer;
}
.login p .forgot {
  float: right;
  margin-right: 50px;
}
.login p .forgot a {
  color: #a7a599;
  font-size: 12px;
  text-decoration: none;
  font-style: italic;
  transition: all 0.3s ease-out;
}
.login p .forgot a:hover {
  color: #f2672e;
}

/*input style*/
/* === Input Form === */
::-webkit-input-placeholder {
  color: #ded9cf;
  font-family: 'Open Sans';
}

:-moz-placeholder {
  color: #ded9cf !important;
  font-family: 'Open Sans';
}

.form input[type=text], input[type=password] {
  font-family: 'Open Sans', Calibri, Arial, sans-serif;
  font-size: 14px;
  font-weight: 400;
  padding: 10px 15px 10px 55px;
  position: relative;
  width: 250px;
  height: 24px;
  color: #676056;
  border: none;
  background: #f7f3eb;
  color: #777;
  transition: color 0.3s ease-out;
}

.form input[type=text] {
  margin-bottom: 15px;
}

.form input[type=text]:hover ~ i,
.form input[type=password]:hover ~ i {
  color: #27ae60;
}

.form input[type=text]:focus ~ i,
.form input[type=password]:focus ~ i {
  color: #27ae60;
}

.form input[type=text]:focus,
.form input[type=password]:focus,
.form button[type=submit]:focus {
  outline: none;
}

.form input[type=submit] {
  margin-top: 10px;
  width: 320px;
  text-align: center;
  font-size: 14px;
  font-family: 'Open Sans',sans-serif;
  font-weight: bold;
  padding: 10px 0;
  letter-spacing: 0;
  box-shadow: inset 0px 0px 0px 0px #57a0de;
  color: #fff;
  background-color: #1a80bc;
  text-shadow: none;
  text-transform: uppercase;
  border: none;
  cursor: pointer;
  position: relative;
  margin-bottom: 0px;
  -webkit-animation: shadowFadeOut 0.4s;
  -moz-animation: shadowFadeOut 0.4s;
}

.form input[type=submit]:hover, input[type=submit]:focus {
  color: #fff;
  box-shadow: inset 0px 46px 0px 0px #57a0de;
  -webkit-animation: shadowFade 0.4s;
  -moz-animation: shadowFade 0.4s;
}

/*keyframes for input animation*/
@keyframes shadowFade {
  0% {
    box-shadow: inset 0px 0px 0px 0px #57a0de;
    color: #fff;
  }
  100% {
    box-shadow: inset 0px 46px 0px 0px #57a0de;
    color: #fff;
  }
}
@keyframes shadowFadeOut {
  0% {
    box-shadow: inset 0px 46px 0px 0px #57a0de;
    color: #fff;
  }
  100% {
    box-shadow: inset 0px 0px 0px 0px #57a0de;
    color: #fff;
  }
}
@-webkit-keyframes shadowFade {
  0% {
    box-shadow: inset 0px 0px 0px 0px #57a0de;
    color: #fff;
  }
  100% {
    box-shadow: inset 0px 46px 0px 0px #57a0de;
    color: #fff;
  }
}
@-webkit-keyframes shadowFadeOut {
  0% {
    box-shadow: inset 0px 46px 0px 0px #57a0de;
    color: #fff;
  }
  100% {
    box-shadow: inset 0px 0px 0px 0px #57a0de;
    color: #fff;
  }
}
@-moz-keyframes shadowFade {
  0% {
    box-shadow: inset 0px 0px 0px 0px #57a0de;
    color: #fff;
  }
  100% {
    box-shadow: inset 0px 46px 0px 0px #57a0de;
    color: #fff;
  }
}
@-moz-keyframes shadowFadeOut {
  0% {
    box-shadow: inset 0px 44px 0px 0px #57a0de;
    color: #fff;
  }
  100% {
    box-shadow: inset 0px 0px 0px 0px #57a0de;
    color: #fff;
  }
}
/*continued styling for input */
.form input[type="checkbox"] {
  display: none;
}

/*.form input[type="checkbox"] + label span {
    display:inline-block;
    width:16px;
    height:16px;
    margin: -2px 4px 0 50px;
    vertical-align:middle;
    background:url("../img/checkbox.png") left top no-repeat;
    cursor:pointer;
}
.form input[type="checkbox"]:checked + label span {
    background:url("../img/checkbox.png") -16px top no-repeat;
}*/
/* === Copyright === */
.copyright {
  margin-top: 30px;
  text-align: center;
}
.copyright p, .copyright a {
  color: #828078;
  font-size: 12px;
  text-decoration: none;
  transition: color 0.3s ease-out;
}


    </style>

  <script src="/Module/JS/prefixfree.min.js"></script>
	<script>

  $(document).ready(function(){	
    var pattern = Trianglify({
      width: window.innerWidth,
      height: window.innerHeight,
      variance: "1",
        // cell_size: 40
    });
    document.documentElement.style.background = "#f3f3f3 url('" + pattern.png() + "')";
    document.documentElement.style.backgroundSize = "cover";
  });


    <?php 
      if(isset($_GET["URL"])) echo 'var URL = "' . $_GET["URL"] . '"';
      else echo 'var URL = "Index.php"';
    ?>

		function login() {
			$.ajax({
				url: 'Login.php',
				type: 'GET',
				dataType: 'html',
				cache: false,
				data: {
					'USERNAME': $("#username").val(),
					'PASSWORD': $("#password").val()
					},
				success: function(string){		
					if(string =='OK') {
						alert('OK');
						window.location = "/Module/Redirect.php?PAGE=" + URL;
					} else {
						console.log(string);
						alert('Mật khẩu không đúng');
					}							
				},
				error: function (){
					alert('ERROR');
				}
			});
		}


    function loginMail() {
      // window.location.replace("https://script.google.com/a/ap.averydennison.com/macros/s/AKfycbxiPMWYc1KKPpCycqQvS8WHRB0sqDxkPAKRVDa6z_B_r0VYl8qz/exec?PAGE=PFL&IP=147.121.73.252");
      window.location.replace("https://script.google.com/a/macros/ap.averydennison.com/s/AKfycbzm_1RP_PdXYbff8xxdvUywH9nQbqGb4Mx7xB44lwbA_cwmdoS0YuiSrP9SC8_ghZ1L1g/exec?PAGE=PFL&IP=147.121.73.252");
      
    }
    $(document).ready(function(){
      var input = document.getElementById("password");
      input.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
          login();
          event.preventDefault();
        }
      });
    })
	</script>
</head>

<body>
  <div class="login">
    <h1>Zero Intranet System</h1>

    <div class="form">

      <p class="field">
        <input type="text" name="login" id="username" placeholder="Username" required/>
        <i class="fa fa-user"></i>
      </p>

      <p class="field" style="margin-bottom:10px">
        <input type="password" name="password" id="password" placeholder="Password" required/>
        <i class="fa fa-lock"></i>
      </p>

      <p class="submit"><input type="submit" name="sent" value="Login by User" onclick="login()"></p>
      <p class="submit"><input type="submit" name="sent" value="Login by Mail" onclick="loginMail()"></p>
      <p style="font-size:14pt; margin:20px 0 0 25px; font-weight: bold"><a href="/">Trở về trang chủ</a></p>
    </div>
  </div> <!--/ Login-->

  
</body>
</html>
