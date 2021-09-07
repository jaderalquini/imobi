<?php
/**
 * BancoList Listing
 * @author  <your name here>
 */
class BancoList extends TPage
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
        $this->form = new TQuickForm('form_search_Banco');
        $this->form->class = 'tform'; // change CSS class        
        $this->form->style = 'display: table;width:100%'; // change style
        //$this->form->setFormTitle('Banco');
        $this->form->setFieldsByRow(2);

        // create the form fields
        $id = new TEntry('id');
        $codigo = new TEntry('codigo');
        $agencia = new TEntry('agencia');
        $digito = new TEntry('digito');
        $nome = new TEntry('nome');
        $endereco = new TEntry('endereco');
        $cep = new TEntry('cep');
        $municipio = new TEntry('municipio');
        $uf_id = new TDBCombo('uf_id',TSession::getValue('banco'),'UF','id','nome');
        $cnpj = new TEntry('cnpj');
        
        // Máscaras
        $cep->setMask('99999-999');
        $cnpj->setMask('99.999.999/9999-9');

        // add the fields
        //$this->form->addQuickField('Id', $id,  200 );
        $this->form->addQuickField('Código', $codigo,  100 );
        $this->form->addQuickField('Agência', $agencia,  100 );
        $this->form->addQuickField('Dígito', $digito,  50 );
        $this->form->addQuickField('Nome', $nome,  300 );
        $this->form->addQuickField('Endereço', $endereco,  300 );
        $this->form->addQuickField('CEP', $cep,  100 );
        $this->form->addQuickField('Município', $municipio,  300 );
        $this->form->addQuickField('UF', $uf_id,  200 );
        $this->form->addQuickField('CNPJ', $cnpj,  100 );
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('Banco_filter_data') );
        
        // add the search form actions
        $this->form->addQuickAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        $this->form->addQuickAction('Limpar Filtro',  new TAction(array($this, 'onClear')), 'bs:ban-circle red');
        $this->form->addQuickAction(_t('New'),  new TAction(array('BancoForm', 'onEdit')), 'bs:plus-sign green');
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'left');
        $column_codigo = new TDataGridColumn('codigo', 'Codigo', 'right');
        $column_agencia = new TDataGridColumn('agencia', 'Agência', 'right');
        $column_digito = new TDataGridColumn('digito', 'Dígito', 'right');
        $column_nome = new TDataGridColumn('nome', 'Nome', 'left');
        $column_endereco = new TDataGridColumn('endereco', 'Endereço', 'left');
        $column_cep = new TDataGridColumn('cep', 'CEP', 'left');
        $column_municipio = new TDataGridColumn('municipio', 'Município', 'left');
        $column_uf_id = new TDataGridColumn('uf_id', 'UF', 'left');
        $column_cnpj = new TDataGridColumn('cnpj', 'CNPJ', 'left');

        // add the columns to the DataGrid
        //$this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_codigo);
        $this->datagrid->addColumn($column_agencia);
        $this->datagrid->addColumn($column_digito);
        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_endereco);
        $this->datagrid->addColumn($column_cep);
        $this->datagrid->addColumn($column_municipio);
        $this->datagrid->addColumn($column_uf_id);
        $this->datagrid->addColumn($column_cnpj);
        
        // creates the datagrid column actions
        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);
        
        $order_agencia = new TAction(array($this, 'onReload'));
        $order_agencia->setParameter('order', 'agencia');
        $column_agencia->setAction($order_agencia);
        
        $order_digito = new TAction(array($this, 'onReload'));
        $order_digito->setParameter('order', 'digito');
        $column_digito->setAction($order_digito);
        
        $order_nome = new TAction(array($this, 'onReload'));
        $order_nome->setParameter('order', 'nome');
        $column_nome->setAction($order_nome);
        
        $order_endereco = new TAction(array($this, 'onReload'));
        $order_endereco->setParameter('order', 'endereco');
        $column_endereco->setAction($order_endereco);
        
        $order_cep = new TAction(array($this, 'onReload'));
        $order_cep->setParameter('order', 'cep');
        $column_cep->setAction($order_cep);
        
        $order_municipio = new TAction(array($this, 'onReload'));
        $order_municipio->setParameter('order', 'municipio');
        $column_municipio->setAction($order_municipio);
        
        $order_uf_id = new TAction(array($this, 'onReload'));
        $order_uf_id->setParameter('order', 'uf_id');
        $column_uf_id->setAction($order_uf_id);
        
        $order_cnpj = new TAction(array($this, 'onReload'));
        $order_cnpj->setParameter('order', 'cnpj');
        $column_cnpj->setAction($order_cnpj);
        
        // create EDIT action
        $action_edit = new TDataGridAction(array('BancoForm', 'onEdit'));
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
        TSession::setValue('BancoList_filter_id',   NULL);
        TSession::setValue('BancoList_filter_codigo',   NULL);
        TSession::setValue('BancoList_filter_agencia',   NULL);
        TSession::setValue('BancoList_filter_digito',   NULL);
        TSession::setValue('BancoList_filter_nome',   NULL);
        TSession::setValue('BancoList_filter_endereco',   NULL);
        TSession::setValue('BancoList_filter_cep',   NULL);
        TSession::setValue('BancoList_filter_municipio',   NULL);
        TSession::setValue('BancoList_filter_uf_id',   NULL);
        TSession::setValue('BancoList_filter_cnpj',   NULL);

        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', 'like', "%{$data->id}%"); // create the filter
            TSession::setValue('BancoList_filter_id',   $filter); // stores the filter in the session
        }


        if (isset($data->codigo) AND ($data->codigo)) {
            $filter = new TFilter('codigo', 'like', "%{$data->codigo}%"); // create the filter
            TSession::setValue('BancoList_filter_codigo',   $filter); // stores the filter in the session
        }


        if (isset($data->agencia) AND ($data->agencia)) {
            $filter = new TFilter('agencia', 'like', "%{$data->agencia}%"); // create the filter
            TSession::setValue('BancoList_filter_agencia',   $filter); // stores the filter in the session
        }


        if (isset($data->digito) AND ($data->digito)) {
            $filter = new TFilter('digito', 'like', "%{$data->digito}%"); // create the filter
            TSession::setValue('BancoList_filter_digito',   $filter); // stores the filter in the session
        }


        if (isset($data->nome) AND ($data->nome)) {
            $filter = new TFilter('nome', 'like', "%{$data->nome}%"); // create the filter
            TSession::setValue('BancoList_filter_nome',   $filter); // stores the filter in the session
        }


        if (isset($data->endereco) AND ($data->endereco)) {
            $filter = new TFilter('endereco', 'like', "%{$data->endereco}%"); // create the filter
            TSession::setValue('BancoList_filter_endereco',   $filter); // stores the filter in the session
        }


        if (isset($data->cep) AND ($data->cep)) {
            $filter = new TFilter('cep', 'like', "%{$data->cep}%"); // create the filter
            TSession::setValue('BancoList_filter_cep',   $filter); // stores the filter in the session
        }


        if (isset($data->municipio) AND ($data->municipio)) {
            $filter = new TFilter('municipio', 'like', "%{$data->municipio}%"); // create the filter
            TSession::setValue('BancoList_filter_municipio',   $filter); // stores the filter in the session
        }


        if (isset($data->uf_id) AND ($data->uf_id)) {
            $filter = new TFilter('uf_id', 'like', "%{$data->uf_id}%"); // create the filter
            TSession::setValue('BancoList_filter_uf_id',   $filter); // stores the filter in the session
        }


        if (isset($data->cnpj) AND ($data->cnpj)) {
            $filter = new TFilter('cnpj', 'like', "%{$data->cnpj}%"); // create the filter
            TSession::setValue('BancoList_filter_cnpj',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('Banco_filter_data', $data);
        
        $param=array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    
    public function onClear( $param )
    {
        $this->form->clear();
        
        TSession::setValue('BancoList_filter_agencia',   NULL);
        TSession::setValue('BancoList_filter_digito',   NULL);
        TSession::setValue('BancoList_filter_nome',   NULL);
        TSession::setValue('BancoList_filter_endereco',   NULL);
        TSession::setValue('BancoList_filter_cep',   NULL);
        TSession::setValue('BancoList_filter_cidade',   NULL);
        TSession::setValue('BancoList_filter_uf_id',   NULL);
        TSession::setValue('BancoList_filter_cnpj',   NULL);
        
        TSession::setValue('Banco_filter_data', NULL);
        
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
            TTransaction::open(TSession::getValue('banco'));
            
            // creates a repository for Banco
            $repository = new TRepository('Banco');
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

            if (TSession::getValue('BancoList_filter_id')) {
                $criteria->add(TSession::getValue('BancoList_filter_id')); // add the session filter
            }


            if (TSession::getValue('BancoList_filter_codigo')) {
                $criteria->add(TSession::getValue('BancoList_filter_codigo')); // add the session filter
            }


            if (TSession::getValue('BancoList_filter_agencia')) {
                $criteria->add(TSession::getValue('BancoList_filter_agencia')); // add the session filter
            }


            if (TSession::getValue('BancoList_filter_digito')) {
                $criteria->add(TSession::getValue('BancoList_filter_digito')); // add the session filter
            }


            if (TSession::getValue('BancoList_filter_nome')) {
                $criteria->add(TSession::getValue('BancoList_filter_nome')); // add the session filter
            }


            if (TSession::getValue('BancoList_filter_endereco')) {
                $criteria->add(TSession::getValue('BancoList_filter_endereco')); // add the session filter
            }


            if (TSession::getValue('BancoList_filter_cep')) {
                $criteria->add(TSession::getValue('BancoList_filter_cep')); // add the session filter
            }


            if (TSession::getValue('BancoList_filter_municipio')) {
                $criteria->add(TSession::getValue('BancoList_filter_municipio')); // add the session filter
            }


            if (TSession::getValue('BancoList_filter_uf_id')) {
                $criteria->add(TSession::getValue('BancoList_filter_uf_id')); // add the session filter
            }


            if (TSession::getValue('BancoList_filter_cnpj')) {
                $criteria->add(TSession::getValue('BancoList_filter_cnpj')); // add the session filter
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
            TTransaction::open(TSession::getValue('banco')); // open a transaction with database
            $object = new Banco($key, FALSE); // instantiates the Active Record
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
?>