<?php
/**
 * Language by namespace: Set the UI language by page language, or by namespace, or by browser language
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Viktor Söderqvist <viktor@zuiderkwast.se>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'action.php');

class action_plugin_uilanguage extends DokuWiki_Action_Plugin {

    /**
     * Register the events
     */
    public function register(Doku_Event_Handler $controller) {
        $controller->register_hook('DOKUWIKI_STARTED', 'BEFORE', $this, 'changeUiLanguage');
        $controller->register_hook('TPL_METAHEADER_OUTPUT', 'BEFORE', $this, 'handleMetaheaderOutput');
    }

    /**
     * Sets the internal language and reloads the language files.
     */
    public function changeUiLanguage($event, $args) {
        global $conf, $INFO, $ID, $lang;
        $old_language = $conf['lang'];

        do { // search for a usable language; break when found

            // 1. Language metadata of the page - the best choice if set
            if (!empty($INFO['meta']['language']) && $this->langOK($INFO['meta']['language'])) {
                $conf['lang'] = $INFO['meta']['language'];
                break;
            }
            // 2. Language by namespace (first part)
            if (strlen($ID) > 3 && $ID[2] == ':') {
                $l = substr($ID, 0, 2);
                if ($this->langOK($l)) {
                    $conf['lang'] = $l;
                    break;
                }
            }
            // 3. Set the UI language to one of the browser's accepted languages
            $languages = explode(',', preg_replace('/\(;q=\d+\.\d+\)/i', '', getenv('HTTP_ACCEPT_LANGUAGE')));
            foreach ($languages as $l) {
                if ($this->langOK($l)) {
                    $conf['lang'] = $l;
                    break;
                }
            }
        } while(false);

        // Rebuild the language array if necessary
        if ($old_language != $conf['lang']) {
            $lang = array();
            require(DOKU_INC.'inc/lang/en/lang.php');
            if ($conf['lang'] && $conf['lang'] != 'en') {
                require(DOKU_INC.'inc/lang/'.$conf['lang'].'/lang.php');
            }
        }
    }

    /** Check if a string is a valid language code, using the languages of DokuWiki itself */
    private function langOK($lang) {
        return file_exists(DOKU_INC."inc/lang/$lang/lang.php");
    }

    /**
     * Intercept the CSS links. Since we might have changed the language direction to RTL, we might
     * want to include RTL styles in the CSS.
     */
    public function handleMetaheaderOutput($event, $param) {
        global $lang, $conf;
        if ($lang['direction'] != 'rtl') return;
        $head = & $event->data;
        $links = & $head['link'];
        $pluginname = $this->getPluginName();
        for ($i=0; $i < count($links); $i++) {
            $link = & $links[$i];
            //if ($link['rel']=='stylesheet') msg('Media '.$link['media'].': '.$link['href']);
            if ($link['rel']=='stylesheet' && $link['type']=='text/css') {
                $link['href'] = DOKU_BASE.'lib/plugins/'.$pluginname.'/rtlcss.php?s='.$link['media'].'&t='.$conf['template'];
            }
        }
    }
}
