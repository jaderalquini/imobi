<?php
require_once 'init.php';

$theme = 'alquiniweb';

new TSession;

$status = $ini['status'];

if ($status == 0)
{
    $content = file_get_contents("app/templates/{$theme}/suspended.html");    
    $content = str_replace('{LIBRARIES}', file_get_contents("app/templates/{$theme}/libraries.html"), $content);
    $css     = TPage::getLoadedCSS();
    $js      = TPage::getLoadedJS();
    $content = str_replace('{HEAD}', $css.$js, $content);
    
    echo $content;
}

if ($status == 1)
{
    if ( TSession::getValue('logged') )
    {
        $content     = file_get_contents("app/templates/{$theme}/layout.html");
        $menu_string = AdiantiMenuBuilder::parse('menu.xml', $theme);
        $content     = str_replace('{MENU}', $menu_string, $content);
        
        TTransaction::open('permission');            
        $empresa = new Empresa(TSession::getValue('empresa'));
        TTransaction::close();
        
        $content     = str_replace('{EMPRESA}',$empresa->nome,$content);
    }
    else
    {
        $content = file_get_contents("app/templates/{$theme}/login.html");
    }
    
    // $content  = TApplicationTranslator::translateTemplate($content);
    $content  = str_replace('{LIBRARIES}', file_get_contents("app/templates/{$theme}/libraries.html"), $content);
    $content  = str_replace('{class}', isset($_REQUEST['class']) ? $_REQUEST['class'] : '', $content);
    $content  = str_replace('{template}', $theme, $content);
    $content  = str_replace('{username}', TSession::getValue('username'), $content);
    $content  = str_replace('{frontpage}', TSession::getValue('frontpage'), $content);
    $content  = str_replace('{query_string}', $_SERVER["QUERY_STRING"], $content);
    $css      = TPage::getLoadedCSS();
    $js       = TPage::getLoadedJS();
    $content  = str_replace('{HEAD}', $css.$js, $content);
    
    echo $content;
    
    if (TSession::getValue('logged'))
    {
        if (isset($_REQUEST['class']))
        {
            $method = isset($_REQUEST['method']) ? $_REQUEST['method'] : NULL;
            AdiantiCoreApplication::loadPage($_REQUEST['class'], $method, $_REQUEST);
        }
    }
    else
    {
        AdiantiCoreApplication::loadPage('LoginForm', '', $_REQUEST);
    }
}

if ($status == 2)
{
    $content = file_get_contents("app/templates/{$theme}/maintenance.html");    
    $content = str_replace('{LIBRARIES}', file_get_contents("app/templates/{$theme}/libraries.html"), $content);
    $css     = TPage::getLoadedCSS();
    $js      = TPage::getLoadedJS();
    $content = str_replace('{HEAD}', $css.$js, $content);
    
    echo $content;
}
?>
