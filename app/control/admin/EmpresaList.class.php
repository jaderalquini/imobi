<?php
/**
 * EmpresaList Listing
 * @author  <your name here>
 */
class EmpresaList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $formgrid;
    private $loaded;
    private $deleteButton;
    
    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();
        parent::include_js('app/lib/include/application.js');
        
        // creates the form
        $this->form = new TQuickForm('form_search_Empresa');
        $this->form->class = 'tform'; // change CSS class        
        $this->form->style = 'display: table;width:100%'; // change style
        //$this->form->setFormTitle('Empresas');
        $this->form->setFieldsByRow(2);

        // create the form fields
        $id = new TEntry('id');
        $nome = new TEntry('nome');
        $cnpj = new TEntry('cnpj');
        $ie = new TEntry('ie');
        $nrjucesc = new TEntry('nrjucesc');
        $creci = new TEntry('creci');
        $cep = new TEntry('cep');
        $uf_id = new TDBCombo('uf_id',TSession::getValue('banco'), 'UF', 'id', 'nome');
        $municipio = new TEntry('municipio');
        $bairro = new TEntry('bairro');
        $endereco = new TEntry('endereco');
        $fone = new TEntry('fone');
        $fax = new TEntry('fax');
        $site = new TEntry('site');
        $email = new TEntry('email');
        $representante_nome = new TEntry('representante_nome');
        $representante_cpf = new TEntry('representante_cpf');
        $logo = new TEntry('logo');
        $banco = new TEntry('banco');
        
        $cnpj->setMask('99.999.999/9999-99');
        $cep->setMask('99999-999');
        $fone->setMask('(999)9999-9999');
        $fax->setMask('(999)9999-9999');

        // add the fields
        $this->form->addQuickField(_t('ID'), $id,  50 );
        $this->form->addQuickField('Nome', $nome,  400 );
        $this->form->addQuickField('CNPJ', $cnpj,  150 );
        /*$this->form->addQuickField('IE', $ie,  150 );
        $this->form->addQuickField('Jucesc', $nrjucesc,  100 );
        $this->form->addQuickField('Creci', $creci,  100 );
        $this->form->addQuickField('CEP', $cep,  100 );*/
        $this->form->addQuickField('Endereço', $endereco,  400 );
        $this->form->addQuickField('Bairro', $bairro,  400 );
        /*$this->form->addQuickField('Município', $municipio,  400 );
        $this->form->addQuickField('UF', $uf_id,  200 );        
        $this->form->addQuickField('Fone', $fone,  100 );
        $this->form->addQuickField('Fax', $fax,  100 );
        $this->form->addQuickField('Site', $site,  400 );
        $this->form->addQuickField('Email', $email,  400 );
        $this->form->addQuickField('Representante', $representante_nome,  400 );
        $this->form->addQuickField('CPF', $representante_cpf,  150 );
        $this->form->addQuickField('Logo', $logo,  200 );
        $this->form->addQuickField('Logo', $banco,  200 );*/
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('Empresa_filter_data') );
        
        // add the search form actions
        $this->form->addQuickAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        $this->form->addQuickAction('Limpar Filtro',  new TAction(array($this, 'onClear')), 'bs:ban-circle red');
        $this->form->addQuickAction(_t('New'),  new TAction(array('EmpresaForm', 'onEdit')), 'bs:plus-sign green');
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;   
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);     
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', _t('ID'), 'right');
        $column_nome = new TDataGridColumn('nome', 'Nome', 'left');
        $column_cnpj = new TDataGridColumn('cnpj', 'CNPJ', 'left');
        $column_ie = new TDataGridColumn('ie', 'IE', 'left');
        $column_nrjucesc = new TDataGridColumn('nrjucesc', 'Jucesc', 'left');
        $column_creci = new TDataGridColumn('creci', 'Creci', 'left');
        $column_cep = new TDataGridColumn('cep', 'CEP', 'left');
        $column_uf_id = new TDataGridColumn('uf_id', 'UF', 'left');
        $column_municipio = new TDataGridColumn('municipio', 'Município', 'left');
        $column_bairro = new TDataGridColumn('bairro', 'Bairro', 'left');
        $column_endereco = new TDataGridColumn('endereco', 'Endereço', 'left');
        $column_fone = new TDataGridColumn('fone', 'Fone', 'left');
        $column_fax = new TDataGridColumn('fax', 'Fax', 'left');
        $column_site = new TDataGridColumn('site', 'Site', 'left');
        $column_email = new TDataGridColumn('email', 'Email', 'left');
        $column_representante_nome = new TDataGridColumn('representante_nome', 'Representante', 'left');
        $column_representante_cpf = new TDataGridColumn('representante_cpf', 'CPF', 'left');
        $column_logo = new TDataGridColumn('logo', 'Logo', 'left');
        $column_banco = new TDataGridColumn('banco', 'Banco', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_cnpj);
        /*$this->datagrid->addColumn($column_ie);
        $this->datagrid->addColumn($column_nrjucesc);
        $this->datagrid->addColumn($column_creci);
        $this->datagrid->addColumn($column_cep);*/
        $this->datagrid->addColumn($column_endereco);
        $this->datagrid->addColumn($column_bairro);
        /*$this->datagrid->addColumn($column_municipio);
        $this->datagrid->addColumn($column_uf_id);
        $this->datagrid->addColumn($column_fone);
        $this->datagrid->addColumn($column_fax);
        $this->datagrid->addColumn($column_site);
        $this->datagrid->addColumn($column_email);
        $this->datagrid->addColumn($column_representante_nome);
        $this->datagrid->addColumn($column_representante_cpf);
        $this->datagrid->addColumn($column_logo);
        $this->datagrid->addColumn($column_banco);*/


        // creates the datagrid column actions
        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);
        
        $order_nome = new TAction(array($this, 'onReload'));
        $order_nome->setParameter('order', 'nome');
        $column_nome->setAction($order_nome);
        
        /*$order_cnpj = new TAction(array($this, 'onReload'));
        $order_cnpj->setParameter('order', 'cnpj');
        $column_cnpj->setAction($order_cnpj);
        
        $order_ie = new TAction(array($this, 'onReload'));
        $order_ie->setParameter('order', 'ie');
        $column_ie->setAction($order_ie);
        
        $order_nrjucesc = new TAction(array($this, 'onReload'));
        $order_nrjucesc->setParameter('order', 'nrjucesc');
        $column_nrjucesc->setAction($order_nrjucesc);
        
        $order_creci = new TAction(array($this, 'onReload'));
        $order_creci->setParameter('order', 'creci');
        $column_creci->setAction($order_creci);
        
        $order_cep = new TAction(array($this, 'onReload'));
        $order_cep->setParameter('order', 'cep');
        $column_cep->setAction($order_cep);
        
        $order_uf_id = new TAction(array($this, 'onReload'));
        $order_uf_id->setParameter('order', 'uf_id');
        $column_uf_id->setAction($order_uf_id);
        
        $order_municipio = new TAction(array($this, 'onReload'));
        $order_municipio->setParameter('order', 'municipio');
        $column_municipio->setAction($order_municipio);
        
        $order_bairro = new TAction(array($this, 'onReload'));
        $order_bairro->setParameter('order', 'bairro');
        $column_bairro->setAction($order_bairro);
        
        $order_endereco = new TAction(array($this, 'onReload'));
        $order_endereco->setParameter('order', 'endereco');
        $column_endereco->setAction($order_endereco);
        
        $order_fone = new TAction(array($this, 'onReload'));
        $order_fone->setParameter('order', 'fone');
        $column_fone->setAction($order_fone);
        
        $order_fax = new TAction(array($this, 'onReload'));
        $order_fax->setParameter('order', 'fax');
        $column_fax->setAction($order_fax);
        
        $order_site = new TAction(array($this, 'onReload'));
        $order_site->setParameter('order', 'site');
        $column_site->setAction($order_site);
        
        $order_email = new TAction(array($this, 'onReload'));
        $order_email->setParameter('order', 'email');
        $column_email->setAction($order_email);
        
        $order_representante_nome = new TAction(array($this, 'onReload'));
        $order_representante_nome->setParameter('order', 'representante_nome');
        $column_representante_nome->setAction($order_representante_nome);
        
        $order_representante_cpf = new TAction(array($this, 'onReload'));
        $order_representante_cpf->setParameter('order', 'representante_cpf');
        $column_representante_cpf->setAction($order_representante_cpf);
        
        $order_logo = new TAction(array($this, 'onReload'));
        $order_logo->setParameter('order', 'logo');
        $column_logo->setAction($order_logo);
        
        $order_banco = new TAction(array($this, 'onReload'));
        $order_banco->setParameter('order', 'banco');
        $column_banco->setAction($order_banco);*/        

        // define the transformer method over image
        $column_logo->setTransformer( function($value, $object, $row) {
            if (file_exists($value)) {
                return new TImage($value);
            }
        });
        
        // create EDIT action
        $action_edit = new TDataGridAction(array('EmpresaForm', 'onEdit'));
        //$action_edit->setUseButton(TRUE);
        $action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('fa:pencil-square-o blue fa-lg');
        $action_edit->setField('id');
        $this->datagrid->addAction($action_edit);
        
        // create DELETE action
        $action_del = new TDataGridAction(array($this, 'onDelete'));
        //$action_del->setUseButton(TRUE);
        $action_del->setButtonClass('btn btn-default');
        $action_del->setLabel(_t('Delete'));
        $action_del->setImage('fa:trash-o red fa-lg');
        $action_del->setField('id');
        $this->datagrid->addAction($action_del);
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());        

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add($this->datagrid);
        $container->add($this->pageNavigation);
        
        parent::add($container);
    }
    
    /**
     * Register the filter in the session
     */
    public function onSearch()
    {
        // get the search form data
        $data = $this->form->getData();
        
        // clear session filters
        TSession::setValue('EmpresaList_filter_id',   NULL);
        TSession::setValue('EmpresaList_filter_nome',   NULL);
        TSession::setValue('EmpresaList_filter_cnpj',   NULL);
        TSession::setValue('EmpresaList_filter_ie',   NULL);
        TSession::setValue('EmpresaList_filter_nrjucesc',   NULL);
        TSession::setValue('EmpresaList_filter_creci',   NULL);
        TSession::setValue('EmpresaList_filter_cep',   NULL);
        TSession::setValue('EmpresaList_filter_uf_id',   NULL);
        TSession::setValue('EmpresaList_filter_municipio',   NULL);
        TSession::setValue('EmpresaList_filter_bairro',   NULL);
        TSession::setValue('EmpresaList_filter_endereco',   NULL);
        TSession::setValue('EmpresaList_filter_fone',   NULL);
        TSession::setValue('EmpresaList_filter_fax',   NULL);
        TSession::setValue('EmpresaList_filter_site',   NULL);
        TSession::setValue('EmpresaList_filter_email',   NULL);
        TSession::setValue('EmpresaList_filter_representante_nome',   NULL);
        TSession::setValue('EmpresaList_filter_representante_cpf',   NULL);
        TSession::setValue('EmpresaList_filter_logo',   NULL);

        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', 'like', "%{$data->id}%"); // create the filter
            TSession::setValue('EmpresaList_filter_id',   $filter); // stores the filter in the session
        }


        if (isset($data->nome) AND ($data->nome)) {
            $filter = new TFilter('nome', 'like', "%{$data->nome}%"); // create the filter
            TSession::setValue('EmpresaList_filter_nome',   $filter); // stores the filter in the session
        }


        if (isset($data->cnpj) AND ($data->cnpj)) {
            $filter = new TFilter('cnpj', 'like', "%{$data->cnpj}%"); // create the filter
            TSession::setValue('EmpresaList_filter_cnpj',   $filter); // stores the filter in the session
        }


        if (isset($data->ie) AND ($data->ie)) {
            $filter = new TFilter('ie', 'like', "%{$data->ie}%"); // create the filter
            TSession::setValue('EmpresaList_filter_ie',   $filter); // stores the filter in the session
        }


        if (isset($data->nrjucesc) AND ($data->nrjucesc)) {
            $filter = new TFilter('nrjucesc', 'like', "%{$data->nrjucesc}%"); // create the filter
            TSession::setValue('EmpresaList_filter_nrjucesc',   $filter); // stores the filter in the session
        }


        if (isset($data->creci) AND ($data->creci)) {
            $filter = new TFilter('creci', 'like', "%{$data->creci}%"); // create the filter
            TSession::setValue('EmpresaList_filter_creci',   $filter); // stores the filter in the session
        }


        if (isset($data->cep) AND ($data->cep)) {
            $filter = new TFilter('cep', 'like', "%{$data->cep}%"); // create the filter
            TSession::setValue('EmpresaList_filter_cep',   $filter); // stores the filter in the session
        }


        if (isset($data->uf_id) AND ($data->uf_id)) {
            $filter = new TFilter('uf_id', 'like', "%{$data->uf_id}%"); // create the filter
            TSession::setValue('EmpresaList_filter_uf_id',   $filter); // stores the filter in the session
        }


        if (isset($data->municipio) AND ($data->municipio)) {
            $filter = new TFilter('municipio', 'like', "%{$data->municipio}%"); // create the filter
            TSession::setValue('EmpresaList_filter_municipio',   $filter); // stores the filter in the session
        }


        if (isset($data->bairro) AND ($data->bairro)) {
            $filter = new TFilter('bairro', 'like', "%{$data->bairro}%"); // create the filter
            TSession::setValue('EmpresaList_filter_bairro',   $filter); // stores the filter in the session
        }


        if (isset($data->endereco) AND ($data->endereco)) {
            $filter = new TFilter('endereco', 'like', "%{$data->endereco}%"); // create the filter
            TSession::setValue('EmpresaList_filter_endereco',   $filter); // stores the filter in the session
        }


        if (isset($data->fone) AND ($data->fone)) {
            $filter = new TFilter('fone', 'like', "%{$data->fone}%"); // create the filter
            TSession::setValue('EmpresaList_filter_fone',   $filter); // stores the filter in the session
        }


        if (isset($data->fax) AND ($data->fax)) {
            $filter = new TFilter('fax', 'like', "%{$data->fax}%"); // create the filter
            TSession::setValue('EmpresaList_filter_fax',   $filter); // stores the filter in the session
        }


        if (isset($data->site) AND ($data->site)) {
            $filter = new TFilter('site', 'like', "%{$data->site}%"); // create the filter
            TSession::setValue('EmpresaList_filter_site',   $filter); // stores the filter in the session
        }


        if (isset($data->email) AND ($data->email)) {
            $filter = new TFilter('email', 'like', "%{$data->email}%"); // create the filter
            TSession::setValue('EmpresaList_filter_email',   $filter); // stores the filter in the session
        }


        if (isset($data->representante_nome) AND ($data->representante_nome)) {
            $filter = new TFilter('representante_nome', 'like', "%{$data->representante_nome}%"); // create the filter
            TSession::setValue('EmpresaList_filter_representante_nome',   $filter); // stores the filter in the session
        }


        if (isset($data->representante_cpf) AND ($data->representante_cpf)) {
            $filter = new TFilter('representante_cpf', 'like', "%{$data->representante_cpf}%"); // create the filter
            TSession::setValue('EmpresaList_filter_representante_cpf',   $filter); // stores the filter in the session
        }


        if (isset($data->logo) AND ($data->logo)) {
            $filter = new TFilter('logo', 'like', "%{$data->logo}%"); // create the filter
            TSession::setValue('EmpresaList_filter_logo',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('Empresa_filter_data', $data);
        
        $param=array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    
    public function onClear( $param )
    {
        $this->form->clear();
        
        TSession::setValue('EmpresaList_filter_id',   NULL);
        TSession::setValue('EmpresaList_filter_nome',   NULL);
        TSession::setValue('EmpresaList_filter_cnpj',   NULL);
        TSession::setValue('EmpresaList_filter_ie',   NULL);
        TSession::setValue('EmpresaList_filter_nrjucesc',   NULL);
        TSession::setValue('EmpresaList_filter_creci',   NULL);
        TSession::setValue('EmpresaList_filter_cep',   NULL);
        TSession::setValue('EmpresaList_filter_uf_id',   NULL);
        TSession::setValue('EmpresaList_filter_municipio',   NULL);
        TSession::setValue('EmpresaList_filter_bairro',   NULL);
        TSession::setValue('EmpresaList_filter_endereco',   NULL);
        TSession::setValue('EmpresaList_filter_fone',   NULL);
        TSession::setValue('EmpresaList_filter_fax',   NULL);
        TSession::setValue('EmpresaList_filter_site',   NULL);
        TSession::setValue('EmpresaList_filter_email',   NULL);
        TSession::setValue('EmpresaList_filter_representante_nome',   NULL);
        TSession::setValue('EmpresaList_filter_representante_cpf',   NULL);
        TSession::setValue('EmpresaList_filter_logo',   NULL);
        
        TSession::setValue('Empresa_filter_data', NULL);
        
        $param=array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    
    /**
     * Load the datagrid with data
     */
    public function onReload($param = NULL)
    {
        try
        {
            // open a transaction with database 'imobi'
            TTransaction::open('permission');
            
            // creates a repository for Empresa
            $repository = new TRepository('Empresa');
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue('EmpresaList_filter_id')) {
                $criteria->add(TSession::getValue('EmpresaList_filter_id')); // add the session filter
            }


            if (TSession::getValue('EmpresaList_filter_nome')) {
                $criteria->add(TSession::getValue('EmpresaList_filter_nome')); // add the session filter
            }


            if (TSession::getValue('EmpresaList_filter_cnpj')) {
                $criteria->add(TSession::getValue('EmpresaList_filter_cnpj')); // add the session filter
            }


            if (TSession::getValue('EmpresaList_filter_ie')) {
                $criteria->add(TSession::getValue('EmpresaList_filter_ie')); // add the session filter
            }


            if (TSession::getValue('EmpresaList_filter_nrjucesc')) {
                $criteria->add(TSession::getValue('EmpresaList_filter_nrjucesc')); // add the session filter
            }


            if (TSession::getValue('EmpresaList_filter_creci')) {
                $criteria->add(TSession::getValue('EmpresaList_filter_creci')); // add the session filter
            }


            if (TSession::getValue('EmpresaList_filter_cep')) {
                $criteria->add(TSession::getValue('EmpresaList_filter_cep')); // add the session filter
            }


            if (TSession::getValue('EmpresaList_filter_uf_id')) {
                $criteria->add(TSession::getValue('EmpresaList_filter_uf_id')); // add the session filter
            }


            if (TSession::getValue('EmpresaList_filter_municipio')) {
                $criteria->add(TSession::getValue('EmpresaList_filter_municipio')); // add the session filter
            }


            if (TSession::getValue('EmpresaList_filter_bairro')) {
                $criteria->add(TSession::getValue('EmpresaList_filter_bairro')); // add the session filter
            }


            if (TSession::getValue('EmpresaList_filter_endereco')) {
                $criteria->add(TSession::getValue('EmpresaList_filter_endereco')); // add the session filter
            }


            if (TSession::getValue('EmpresaList_filter_fone')) {
                $criteria->add(TSession::getValue('EmpresaList_filter_fone')); // add the session filter
            }


            if (TSession::getValue('EmpresaList_filter_fax')) {
                $criteria->add(TSession::getValue('EmpresaList_filter_fax')); // add the session filter
            }


            if (TSession::getValue('EmpresaList_filter_site')) {
                $criteria->add(TSession::getValue('EmpresaList_filter_site')); // add the session filter
            }


            if (TSession::getValue('EmpresaList_filter_email')) {
                $criteria->add(TSession::getValue('EmpresaList_filter_email')); // add the session filter
            }


            if (TSession::getValue('EmpresaList_filter_representante_nome')) {
                $criteria->add(TSession::getValue('EmpresaList_filter_representante_nome')); // add the session filter
            }


            if (TSession::getValue('EmpresaList_filter_representante_cpf')) {
                $criteria->add(TSession::getValue('EmpresaList_filter_representante_cpf')); // add the session filter
            }


            if (TSession::getValue('EmpresaList_filter_logo')) {
                $criteria->add(TSession::getValue('EmpresaList_filter_logo')); // add the session filter
            }

            
            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);
            
            if (is_callable($this->transformCallback))
            {
                call_user_func($this->transformCallback, $objects, $param);
            }
            
            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {
                    // add the object inside the datagrid
                    $this->datagrid->addItem($object);
                }
            }
            
            // reset the criteria for record count
            $criteria->resetProperties();
            $count= $repository->count($criteria);
            
            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($limit); // limit
            
            // close the transaction
            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            // undo all pending operations
            TTransaction::rollback();
        }
    }
    
    /**
     * Ask before deletion
     */
    public function onDelete($param)
    {
        // define the delete action
        $action = new TAction(array($this, 'Delete'));
        $action->setParameters($param); // pass the key parameter ahead
        
        // shows a dialog to the user
        new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    
    /**
     * Delete a record
     */
    public function Delete($param)
    {
        try
        {
            $key=$param['key']; // get the parameter $key
            TTransaction::open('permission'); // open a transaction with database
            $object = new Empresa($key, FALSE); // instantiates the Active Record
            $object->delete(); // deletes the object from the database
            TTransaction::close(); // close the transaction
            $this->onReload( $param ); // reload the listing
            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted')); // success message
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    



    
    /**
     * method show()
     * Shows the page
     */
    public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  array('onReload', 'onSearch')))) )
        {
            if (func_num_args() > 0)
            {
                $this->onReload( func_get_arg(0) );
            }
            else
            {
                $this->onReload();
            }
        }
        parent::show();
    }
}
