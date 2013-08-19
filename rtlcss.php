<?php
/**
 * RTL StyleSheet creator
 *
 * Outputs a CSS with only RTL style, no matter what language is set in your configuration.
 * This is useful if you have plugins that change $conf['lang'] and (thus the language direction) per page.
 */

if(!defined('DOKU_INC')) define('DOKU_INC',dirname(__FILE__).'/../../../');
if(!defined('NOSESSION')) define('NOSESSION',true); // we do not use a session or authentication here (better caching)
if(!defined('DOKU_DISABLE_GZIP_OUTPUT')) define('DOKU_DISABLE_GZIP_OUTPUT',1); // we gzip ourself here
require_once(DOKU_INC.'inc/init.php');
require_once(DOKU_INC.'inc/pageutils.php');
require_once(DOKU_INC.'inc/io.php');
require_once(DOKU_INC.'inc/confutils.php');


// This will make css_out() include the rtl stuff.
$lang['direction'] = 'rtl';

// This will make css_out() use a separate cache.
$_SERVER['SERVER_PORT'] = '00'.$_SERVER['SERVER_PORT'];

// Now let css_out() work as usual.
require_once(DOKU_INC.'lib/exe/css.php');

//Setup VIM: ex: et ts=4 enc=utf-8 :
