<?php
/**
 * HomeView
 *
 * @version    1.0
 * @package    samples
 * @subpackage tutor
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class HomeView extends TPage
{
    private $label;
    private $source;
    
    public function __construct()
    {
        // parent classs constructor
        parent::__construct();
        
        $xml = simplexml_load_file('menu.xml');
        foreach ($xml as $xmlElement)
        {
            $atts   = $xmlElement->attributes();
            $index  = (string) $atts['label'];
            
            if ($xmlElement->menu)
            {
                foreach ($xmlElement->menu->menuitem as $subXmlElement)
                {
                    $subindex = (string) $subXmlElement['label'];
                    $subatts   = $subXmlElement->attributes();
                    $items = array();
                    if ($subXmlElement->menu)
                    {
                        foreach ($subXmlElement->menu->menuitem as $option)
                        {
                            $optatts   = $option->attributes();
                            $label  = (string) $option['label'];
                            $action = (string) $option-> action;
                            $icon   = str_replace('fa:', 'fa fa-', (string) $option-> icon);
                            $items[] = array('label'  => $label,
                                              'action' => str_replace('#', '&', $action),
                                              'icon'   => $icon);
                        }
                    }
                    
                    $section = new THtmlRenderer('app/resources/menupart.html');
                    $section->enableSection('main', array('subindex' => (string) $subindex));
                    $section->enableSection('items', $items, TRUE);
                    
                    $replaces[$index][] = array('options' => $section);
                }
            }
        }
        $this->html = new THtmlRenderer('app/resources/home.html');
        $this->html->enableSection('main');
        $this->html->enableSection('menu',  $replaces, TRUE);
        parent::add($this->html);
    }
}
