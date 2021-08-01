<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/*
 * Output the common HTML blocks
 */

if (!defined('XLITE_INSTALL_MODE')) {
    die('Incorrect call of the script. Stopping.');
}


function show_install_html_header() {

    if (!headers_sent()) {
        header('Content-Type: text/html; charset=utf-8');
    }
?>
<head>
  <title>X-Cart v.<?php echo LC_VERSION; ?> <?php echo xtr('Installation Wizard'); ?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta http-equiv="Content-Script-Type" content="type/javascript" />
  <meta name="ROBOTS" content="NOINDEX" />
  <meta name="ROBOTS" content="NOFOLLOW" />
  <link rel="shortcut icon" href="public/favicon.ico" type="image/x-icon" />
  <link rel="stylesheet" href="skins/common/css/font-awesome/font-awesome.min.css">
  <link rel="stylesheet" href="skins/common/bootstrap/css/bootstrap.min.css">
<?php

}

function show_install_css() {

    global $skinsDir;

?>

  <style type="text/css">

    /**
      * Clear styles
      */

    html, body, div, span, applet, object, iframe, h1, h2, h3, h4, h5, h6, p, blockquote, pre, a, abbr, acronym, address, big, cite, code, del, dfn, em, font, img, ins, kbd, q, s, samp, small, strike, strong, sub, sup, tt, var, b, u, i, center, dl, dt, dd, ol, ul, li, fieldset, label, legend, caption, input, textarea {
      margin: 0;
      padding: 0;
      border: 0;
      outline: 0;
    }

    html, body {
      min-height: 100%;
    }

    label {
      font-weight: normal;
    }

    ol, ul {
      list-style: none;
    }

    blockquote, q {
      quotes: none;
    }

    blockquote:before,
    blockquote:after,
    q:before,
    q:after {
      content: '';
      content: none;
    }

    :focus {
      outline: 0;
    }



    /**
      * Common styles
      */

    body,
    p,
    div,
    th,
    td,
    p,
    input,
    span,
    textarea,
    button {
      color: #141414;
      font-size: 14px;
      font-family: 'Open Sans', Arial, sans-serif;
      line-height: 24px;
    }

    body {
      background-color: #ffffff;
    }

    p {
      margin: 0 0 24px;
      line-height: 24px;
      padding: 0;
    }

    a {
      color: #0f8dd0;
      text-decoration: none;
    }

    h1,
    h2,
    h3 {
      color: #141414;
      font-family: 'Open Sans', Arial, sans-serif;
    }

    h1 {
      font-size: 30px;
      line-height: 36px;
      margin-bottom: 20px;
      margin: 10px 0 20px;
    }

    h2 {
      font-size: 24px;
      margin: 18px 0;
    }

    h3 {
      font-size: 18px;
      margin: 12px 0;
    }

    code {
      color: #6bb670;
      background-color: transparent;
      font-size: 14px;
    }

    /**
      * Form elements styles
      */

    input[type=radio],
    input[type=checkbox] {
      width: 16px;
      height: 16px;
      margin: 4px 0;
    }

    input[type=text],
    input[type=password] {
      height: 40px;
      line-height: 24px;
      font-size: 14px;
      width: 245px;
      margin: 4px 0;
    }

    input[type=text]:focus,
    input[type=password]:focus,
    select:focus,
    textarea:focus {
      border: solid 1px #999;
      font-size: 14px;
    }

    select {
      line-height: 24px;
    }

    input[type="submit"].next-button,
    input[type="button"].next-button,
    input[type="reset"].next-button,
    button.next-button {
      margin-left: -20px;
    }

    input[type="submit"].disabled-button,
    input[type="button"].disabled-button,
    input[type="reset"].disabled-button,
    button.disabled-button {
      background: #dfdfdf;
      border: 1px solid #dfdfdf;
      color: white;
      cursor: default;
    }

    input.error,
    select.error,
    input.error:focus,
    select.error:focus {
      border-color: #ff0000;
    }

    button span {
      vertical-align: middle;
    }

    button:hover {
      border-color: #b1c9e0;
    }

    button.main {
      padding-left: 10px;
      padding-right: 10px;
    }

    button.main span {
      font-size: 18px;
      line-height: 18px;
    }

    button.invert {
      background: url(<?php echo $skinsDir; ?>images/button_bg_blue.png) repeat 0 0;
      border-color: transparent;
    }

    button.invert span {
      color: #fff;
    }

    button.invert:hover {
      background-color: transparent;
      background-image: url(<?php echo $skinsDir; ?>images/button_bg_blue_hover.png);
    }

    td.next-button-layer {
      width: 151px;
      height: 114px;
    }


    /**
      * Layout
      */

    html,
    body {
      min-width: 800px;
      height: 100%;
    }

    #content,
    #sub-section,
    #footer {
      overflow: hidden;
    }

    #page-container {
      min-height: 100%;
      position: relative;
    }

    #page-container {
      vertical-align: top;
      width: 100%;
    }

    #header {
      width: 100%;
      position: absolute;
      top: 0;
      left: 0;
    }

    #header .logo {
      background: url(<?php echo $skinsDir; ?>images/logo.svg) no-repeat left center;
      background-size: 60px 60px;
      height: 96px;
      width: 72px;
      padding: 18px 0;
      box-sizing: border-box;
      float: left
    }

    .sw-version {
      position: absolute;
      left: 72px;
      top: 24px;
      line-height: 24px;
      width: 888px;
    }

    .current {
      color: #141414;
      font-size: 14px;
      margin: 0;
      display: inline-block;
    }

    .upgrade-note {
      margin-right: 0;
      text-align: right;
      float: right;
      white-space: nowrap;
    }

    .upgrade-note a {
      text-decoration: none;
      padding-left: 5px;
    }

    /**
      * Page content styles
      */
    div.install-page {
      width: 960px !important;
      margin: 0 auto;
    }

    div.install-page #header, #menu {
      background: transparent none;
    }

    div.install-page h1 {
      margin: 36px 0 0 72px;
      line-height: 48px;
      font-family: 'Open Sans', Arial, sans-serif;
      font-size: 30px;
    }

    div.install-page #content {
      background: transparent none;
      border-top: 0 none;
    }

    div.content {
      position: absolute;
      top: 144px;
      width: 100%;
      text-align: center;
      padding-top: 24px;
    }

    #email {
      margin: 20px 0 20px;
      text-align: left;
    }

    #email input {
      display: inline-block;
      max-width: 300px;
    }

    #copyright_notice {
      border: 1px solid #ccc;
      border-radius: 4px;
      font-family: 'Open Sans', Arial, sans-serif;
      font-size: 14px;
      height: 300px;
      margin-bottom: 12px;
      padding: 12px;
      overflow: auto;
      text-align: left;
      width: 100%;
      line-height: 24px;
      box-sizing: border-box;
    }

    .status-report-box .permissions-list {
      border: 1px solid #999999;
      font-family: "Courier New", monospace;
      font-size: 11px;
      font-style: italic;
      max-height: 250px;
      margin-bottom: 10px;
      padding: 5px;
      padding-bottom: 20px;
      overflow: auto;
      text-align: left;
      width: 320px;
    }

    .status-report-box .copy2clipboard {
      cursor: pointer;
      float: right;
      margin-bottom: 5px;
    }

    .error-text.file_permissions .copy2clipboard-alert {
      position: absolute;
      padding: 6px;
      margin-top: 24px;
      width: 290px;
      margin-left: 6px;
    }

    .text-left {
      align-items: left;
    }

    .field-label {
      font-size: 14px;
      font-weight: normal;
      text-align: left;
      margin-right: 0;
      vertical-align: baseline;
      line-height: 24px;
      min-height: 40px;
    }

    #auth-code {
      padding-right: 12px;
    }

    .field-label .required {
      color: #a94461;
      width: 24px;
      padding: 0;
      display: inline-block;
      text-align: center;
      line-height: 24px;
    }

    .checkbox-field {
      display: inline-block;
      font-size: 14px;
      text-align: left;
      color: #141414;
      line-height: 24px;
      vertical-align: baseline;
      font-weight: normal;
      margin-top: 0;
    }

    #install-form .checkbox-field {
      display: flex;
      justify-content: center;
    }

    #install-form table + .checkbox-field {
      margin-top: 12px;
    }

    .checkbox-field label {
      display: inline;
      padding-left: 12px;
      white-space: nowrap;
    }

    .field-notice {
      font-size: 12px;
      text-align: left;
      color: #808080;
      font-style: normal;
      line-height: 18px;
      margin-top: 0;
      margin-bottom: 6px;
    }

    td.field-notice {
      padding-left: 10px;
      text-align: left;
    }

    /**
      * Common styles
      */

    .status-ok {
      color: #6bb670;
    }

    .status-failed {
      color: #a94461;
    }

    .iframe-div + .status-ok ,
    .iframe-div + .status-failed {
      display: block;
      margin-bottom: 12px;
    }

    .status-failed-link {
      color: #a94461;
      text-decoration: underline;
    }

    .status-failed-link-active {
      color: #a94461;
      text-decoration: none;
      cursor: default;
    }

    .status-skipped {
      color: #145d8f;
    }

    .status-already-exists {
      color: #145d8f;
    }


    /**
      * Requirements checking page styles
      */

    .clear {
      clear: both;
    }

    div.requirements-report {
    }

    div.requirements-list {
      float: left;
      width: 60%;
      display: flex;
      flex-direction: column;
    }

    div.requirements-list .section-title:first-child {
      padding-top: 0;
    }

    div.requirements-notes {
      float: right;
      width: 40%;
      line-height: 24px;
    }

    div.section-title {
      font-size: 20px;
      text-align: left;
      padding-top: 24px;
      padding-bottom: 24px;
      line-height: 24px;
    }

    .color-1 {
      background: transparent;
    }

    .color-2 {
      background: white;
    }

    .section-requirements .list-row {
      padding-top: 6px;
      display: flex;
    }

    .section-requirements .list-row:first-child {
      padding-top: 0;
    }

    div.field-left {
      float: left;
      text-align: left;
      width: 70%;
    }

    div.field-right {
      float: right;
      text-align: right;
      width: 30%;
      white-space: nowrap;
    }

    .error-title {
      text-align: left;
      font-size: 20px;
      color: #a94461;
      margin-left: 24px;
    }

    .error-text {
      text-align: left;
      padding-top: 10px;
      margin-left: 24px;
    }

    div.requirements-warning-text {
      padding-top: 25px;
      padding-bottom: 25px;
      font-size: 12px;
      color: #333333;
    }

    div.status-report-box {
      border-radius: 4px;
      background-color: #ffc5c6;
      padding: 12px;
      margin-top: 12px;
      margin-left: 24px;
      text-align: left;
    }

    div.status-report-box.warning {
      background-color: #fcebb0;
    }

    div.status-report-box-text {
      text-align: left;
      padding-bottom: 24px;
    }

    div.status-report-box-text em,
    .fatal-error .note em,
    .fatal-error .additional-note em {
      text-decoration: none;
      font-style: normal;
      font-weight: bold;
    }

    input[type="button"].active-button {
      background: linear-gradient(to bottom, #f59f57 0%, #f3923c 100%);
      border-radius: 5px;
      padding: 6px 10px;
      border: 1px solid #e88d42;
      color: white;
      font-size: 14px;
      font-weight: normal;
      margin: 0;
    }

    #status-report .buttons {
      display: flex;
    }

    .status-report-box .error-text {
      padding: 12px 0 24px 0;
      margin: 0;
      border-top: solid 1px #dedede;
      line-height: 24px;
    }

    .status-report-box .error-text:first-child {
      border-top: none;
      padding-top: 0;
    }

    .status-report-box .error-text.php_version b {
      color: #a94461;
    }

    .status-report-box input {
      margin: 4px 0;
    }

    .status-report-box #re-check-button {
      margin-right: 24px;
    }

    .cloud-box {
      margin-top: 12px;
      margin-left: 24px;
      text-align: center;
    }

    .cloud-box .grey-line {
      display: inline-block;
      border-bottom: solid 1px #dedede;
      width: 360px;
      height: 24px;
    }

    .cloud-box .or-cloud {
      display: inline-block;
      position: relative;
      top: 12px;
      background-color: white;
    }

    .cloud-box .or-cloud span {
      color: white;
      padding: 9px 6.5px;
      background: #dedede;
      border-radius: 17px;
    }

    .cloud-box .cloud-header {
      font-size: 20px;
      margin-top: 24px;
      margin-bottom: 24px;
    }

    .cloud-box .cloud-text {
      font-size: 14px;
      margin: auto;
      padding-bottom: 12px;
    }

    .link-expanded {
      margin-top: -3px;
      margin-right: -9px;
    }

    .requirements-success {
      padding-top: 45px;
      padding-left: 30px;
      text-align: center;
      font-family: Arial, Helvetica, sans-serif;
      font-size: 36px;
      color: #51924a;
    }

    .requirements-success-image {
      padding-left: 35px;
    }

    /**
      * Step bar styles definition
      */

    div.steps-bar {
      position: absolute;
      top: 96px;
    }

    .steps {
      border-style: none;
      margin: 0;
    }

    .step-row {
      background: #0f8dd0;
      float: left;
      list-style: none outside none;
      height: 40px;
      font-family: 'Open Sans', Arial, sans-serif;
      font-size: 14px;
      color: white;
      line-height: 40px;
      padding-left: 10px;
      padding-right: 12px;
      position: relative;
      margin: 4px 0;
    }

    .first {
      border-radius: 4px 0 0 4px;
      padding-left: 24px;
    }

    .last {
      border-radius: 0 6px 6px 0;
      padding-right: 24px;
    }

    .next {
      background: #eff8fe;
      color: #0f8dd0;
    }

    .prev-prev {
      background: url(<?php echo $skinsDir; ?>images/arrow_dark.png) no-repeat scroll center center transparent;
    }

    .prev-next {
      width: 24px;
      background: url(<?php echo $skinsDir; ?>images/arrow_dark_grey.png) no-repeat scroll center center transparent;
    }

    .prev-next:after {
      border-width: 20px 0 20px 12px;
      content: "";
      width: 12px;
      height: 40px;
      position: absolute;
      border-style: solid;
      border-top-color: #eff8fe;
      border-left-color: #fff;
      border-bottom-color: #eff8fe;
      right: 0;
      top: 0;
    }

    .prev-next:before {
      border-width: 20px 0 20px 12px;
      content: "";
      width: 12px;
      height: 40px;
      position: absolute;
      border-bottom-color: #fff;
      border-style: solid;
      border-top-color: #fff;
      border-left-color: #0f8dd0;
      border-right-color: #fff;
      left: 0;
      top: 0;
    }

    .next-next {
      background: url(<?php echo $skinsDir; ?>images/arrow_grey.png) no-repeat scroll center center transparent;
    }

    .next-next, .prev-prev {
      width: 24px;
      padding: 0;
    }

    .next-next:after, .prev-prev:after {
      border-width: 20px 0 20px 12px;
      content: "";
      width: 12px;
      height: 40px;
      position: absolute;
      border-style: solid;
      border-top-color: #eff8fe;
      border-left-color: #fff;
      border-bottom-color: #eff8fe;
      right: 0;
      top: 0;
    }

    .next-next:before, .prev-prev:before {
      border-width: 20px 0 20px 12px;
      content: "";
      width: 12px;
      height: 40px;
      position: absolute;
      border-bottom-color: #fff;
      border-style: solid;
      border-top-color: #fff;
      border-left-color: #eff8fe;
      border-right-color: #fff;
      left: 0;
      top: 0;
    }

    .prev-prev:before {
      border-left-color: #0f8dd0;
    }

    .prev-prev:after {
      border-top-color: #0f8dd0;
      border-bottom-color: #0f8dd0;
    }

    /**
      * /end of step bar styles definition
      */

    .full-width {
      width: 100%;
    }

    #process_iframe {
      padding: 12px;
      border: 1px solid #cccccc;
      border-radius: 4px;
    }

    .cache-error {
      font-size: 16px;
      text-align: left;
      margin-bottom: 20px;
    }

    .cache-error span {
      font-size: 16px;
      color: #a94461;
    }

    .keyhole-icon {
      margin-right: 50px;
    }

    .report-layer {
      background: rgba(17, 17, 17, .8);
      filter: none;
      left: 0;
      position: fixed;
      top: 0;
      width: 900px;
      z-index: 1003;
      height: 100%;
      width: 100%;
    }

    .report-window {
      border: 1px solid #c5c5c5;
      background: white;
      width: 830px;
      margin: 60px auto;
      padding: 24px;
      z-index: 1004;
      border-radius: 5px;
      overflow: visible;
      position: relative;
    }

    .report-title {
      font-size: 24px;
      line-height: 48px;
      margin-bottom: 24px;
      margin-top: 0;
      font-weight: normal;
      width: auto;
      white-space: normal;
      max-width: 680px;
      color: #141414;
    }

    .report-buttons {
      padding: 4px 0;
      margin-top: 24px;
    }

    .form-control {
      color: #141414;
      padding: 7px 12px;
      height: 40px;
    }

    select.form-control {
      width: 245px;
    }

    textarea.form-control.report-details {
      font-family: "Courier New", monospace;
      font-size: 14px;
      height: 90px;
      width: 100%;
      line-height: 24px;
      background: #fff;
    }

    textarea.report-notes {
      height: 90px;
      width: 400px;
    }

    a.report-close {
      position: absolute;
      display: block;
      top: 24px;
      right: 24px;
      outline: none;
      border: none;
      z-index: 10;
      background: none;
      margin: 4px;
      width: 20px;
      height: 20px;
    }

    a.report-close:before,
    a.report-close:after {
      position: absolute;
      left: 10px;
      top: -2px;
      content: '';
      height: 24px;
      width: 1px;
      background-color: #000;
    }

    a.report-close:before {
      transform: rotate(45deg);
    }

    a.report-close:after {
      transform: rotate(-45deg);
    }

    .hidden {
      display: none;
    }

    .fatal-error,
    .warning-text {
      font-size: 14px;
      text-align: left;
      border: none;
      border-radius: 4px;
      width: 100%;
    }

    .warning-text {
      background: #fcebb0;
      padding: 12px;
    }

    .fatal-error {
      color: #a94461;
      font-size: 14px;
    }

    .fatal-error > div {
      font-size: 14px;
      color: #a94461;
      text-align: left;
    }

    .fatal-error > div.additional-note {
      margin-top: 12px;
      font-size: 14px;
      color: #141414;
    }

    .fatal-error .note {
      margin-top: 24px;
      margin-bottom: 24px;
      color: #141414;
      font-size: 14px;
    }

    .fatal-error input.active-button {
      padding: 12px 20px;
      font-size: 16px;
    }

    td.table-left-column {
      text-align: right;
      width: 35%;
      padding: 0;
      border: none;
      vertical-align: baseline;
    }

    .table-left-column .field-label {
      padding: 12px 24px 12px 0;
      position: relative;
      text-align: right;
    }

    .table-left-column .field-label .required {
      position: absolute;
      top: 12px;
      right: 0;
    }

    td.table-right-column {
      text-align: left;
      width: 65%;
      border: none;
    }

    td.table-right-column input[type=checkbox] {
      margin: 17px 0;
    }

    table tr.section-title td {
      text-align: right;
      padding-right: 24px;
    }

    table tr.section-title td span {
      display: inline-block;
      font-size: 14px;
      cursor: pointer;
      margin: 24px 0px 11px;
      border-bottom: dashed 1px #0f8dd0;
      color: #0f8dd0;
    }

    table tr.section {
      display: none;
    }

    .buttons-bar {
      margin: 24px auto 0;
    }

    .install_dirs .buttons-bar {
      margin-top: 18px;
    }

    .buttons-bar td {
      padding: 4px 12px;
    }

    .buttons-bar td:first-child {
      padding-left: 12px;
    }

    .cfg_create_admin .buttons-bar,
    .cfg_install_db .buttons-bar {
      margin-left: 35%;
    }

    .cfg_create_admin .fatal-error ~ .buttons-bar,
    .cfg_install_db .fatal-error.extended ~ .buttons-bar {
      margin-left: auto;
      margin-right: auto;
    }

    .cfg_create_admin .buttons-bar td:first-child,
    .cfg_install_db .buttons-bar td:first-child {
      padding-left: 0;
    }

    .pdo-details {
      font-size: 14px;
      cursor: pointer;
      border-bottom: dashed 1px #0f8dd0;
      color: #0f8dd0;
      margin-left: 5px;
    }

    .section.section-pdo-error {
      display: none;
    }

    .btn.disabled,
    .btn[disabled],
    fieldset[disabled] .btn {
      opacity: 0.2;
    }

    .fatal-error.extended {
      border: none;
      margin: 0;
      padding: 0;
      width: 530px;
    }

    .fatal-error .header {
      font-size: 36px;
      line-height: 48px;
      margin-bottom: 24px;
    }

    .fatal-error.extended .cloud-box {
      border: 1px solid #cccccc;
      border-radius: 4px;
      position: absolute;
      top: 0;
      right: 0;
      margin-top: 36px;
      padding: 11px;
      text-align: center;
      max-width: 400px;
    }

    .cloud-box .svg-icon {
      display: none;
    }

    .cloud-box .svg-icon img {
      width: 48px;
      height: 48px;
    }

    .fatal-error.extended .cloud-box .svg-icon {
      display: block;
      margin-top: 0;
    }

    .fatal-error.extended .cloud-box .grey-line {
      display: none;
    }

    .fatal-error.extended .cloud-box .cloud-header {
      margin-top: 0;
      font-size: 20px;
    }

    .fatal-error.extended .cloud-box .cloud-text {
      font-size: 14px;
      padding-bottom: 12px;
    }

    #create-online-store {
      margin: 4px 0;
    }

    table.display-help {
      float: left;
    }
    
    .cfg_install_db table.display-help {
      float: none;
    }

    .btn-lg:hover {
      font-size: 14px;
      background-color: #eff8fe;
      border-color: #c9dcea;
      color: #0f8dd0;
    }

    .btn-lg, .btn-group-lg > .btn {
      padding: 0 12px;
      height: 40px;
      border-radius: 4px;
      line-height: 24px;
      color: #0f8dd0;
      font-size: 14px;
    }

    .btn-warning {
      background-color: #efad4e;
      border-color: #eda237;
      color: #fff;
      font-size: 14px;
    }

    .btn-warning:hover {
      background-color: #ed9c29;
      border-color: #eda237;
      color: #fff;
    }

    .install-help-box {
      display: table;
      position: absolute;
      top: 96px;
      right: 0;
    }

    .install-help-box img {
      display: table-cell;
      vertical-align: middle;
      margin-top: 3px;
      width: 32px;
      height: 32px;
    }

    .install-help-box div {
      display: table-cell;
      vertical-align: middle;
      padding-left: 0;
      line-height: 24px;
    }

    .install_done ul.permissions-list {
      border: none;
      border-radius: 4px;
      background-color: #cce5ff;
      font-size: 14px;
      padding: 12px;
      margin: 0;
      margin-top: 12px;
      display: inline-block;
    }

    .install_done ul.permissions-list li:before {
      content: "$";
      padding-right: 6px;
      color: #808080;
    }

    .install_done .clipbrd {
      margin-top: 12px;
      margin-bottom: 12px;
    }

    input[type="button"].copy2clipboard {
      padding: 9px 11px;
      color: #0f8dd0;
      margin: 4px 0;
      border-color: #cccccc;
    }

    input[type="button"].copy2clipboard:hover {
      background-color: #eff8fe;
      border-color: #c9dcea;
    }

    .install_done .copy2clipboard-alert {
      display: inline-block;
      border-radius: 3px;
      padding: 6px;
      width: 290px;
      margin-left: 10px;
    }

    .install_done .second-title {
      margin-bottom: 12px;
    }

    .install_done p.customer-link {
      margin-bottom: 12px;
    }

    a:hover {
      color: #0f8dd0;
      text-decoration: underline;
    }

    .process-iframe-header {
      margin-bottom: 12px
    }

    .building-cache-notice {
      margin-top: 17px;
    }

  </style>

    <?php

}
