<?php
class SystemProfileView extends TPage
{
    public function __construct()
    {
        parent::__construct();
        parent::include_js('app/lib/include/application.js');
        
        $html = new THtmlRenderer('app/resources/profile.html');
        $replaces = array();
        
        try
        {
            TTransaction::open('permission');
            
            $user= SystemUser::newFromLogin(TSession::getValue('login'));
            $replaces = $user->toArray();
            $replaces['frontpage'] = $user->frontpage_name;
            $replaces['groupnames'] = $user->getSystemUserGroupNames();
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
        
        $html->enableSection('main', $replaces);
        $html->enableTranslation();
        
        $bc = new TBreadCrumb();
        $bc->addHome();
        $bc->addItem('Perfil');
        
        $container = TVBox::pack($bc, $html);
        $container->style = 'width:100%';
        parent::add($container);
    }
}
?>