<?php
/**
 * ContacorrenteClienteList Listing
 * @author  <your name here>
 */
class ContacorrenteClienteList extends TPage
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
        $this->form = new TQuickForm('form_search_ContacorrenteCliente');
        $this->form->class = 'tform'; // change CSS class        
        $this->form->style = 'display: table;width:100%'; // change style
        //$this->form->setFormTitle('Contaa Correntes (Clientes)');
        $this->form->setFieldsByRow(2);        

        // create the form fields
        $id = new TEntry('id');
        $criteria = new TCriteria;
        $criteria->setProperty('order', 'nome');    
        $cliente_id = new TDBSeekButton('cliente_id',TSession::getValue('banco'),'form_search_ContacorrenteCliente','Cliente','nome','cliente_id','cliente',$criteria);
        $cliente = new TEntry('cliente');
        $banco = new TEntry('banco');
        $praca = new TEntry('praca');
        $numero = new TEntry('numero');
        $digito = new TEntry('digito');
        $agencia = new TEntry('agencia');
        $digitoagencia = new TEntry('digitoagencia');
        $nomedep = new TEntry('nomedep');
        
        // Campos Não Editáveis
        $cliente->setEditable(FALSE);
        
        // Tamanho dos Campos no formulário
        $cliente_id->setSize(50);
        $cliente->setSize(277);
        
        // Número de Caracteres permitidos dentro dos campos

        // add the fields
        //$this->form->addQuickField(_t('ID'), $id,  50 );
        $this->form->addQuickFields('Cliente', array($cliente_id, $cliente) );
        $this->form->addQuickField('Banco', $banco,  350 );
        $this->form->addQuickField('Praça', $praca,  350 );
        $this->form->addQuickField('N° Conta', $numero,  100 );
        $this->form->addQuickField('Dígito', $digito,  50 );
        $this->form->addQuickField('Agência', $agencia,  100 );
        $this->form->addQuickField('Dígito Agência', $digitoagencia,  50 );
        $this->form->addQuickField('Nome Depositante', $nomedep,  350 );
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('ContacorrenteCliente_filter_data') );
        
        // add the search form actions
        $this->form->addQuickAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        $this->form->addQuickAction('Limpar Filtro',  new TAction(array($this, 'onClear')), 'bs:ban-circle red');
        $this->form->addQuickAction(_t('New'),  new TAction(array('ContacorrenteClienteForm', 'onEdit')), 'bs:plus-sign green');
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', _t('ID'), 'right');
        $column_cliente_id = new TDataGridColumn('cliente', 'Cliente', 'left');
        $column_banco = new TDataGridColumn('banco', 'Banco', 'left');
        $column_praca = new TDataGridColumn('praca', 'Praça', 'left');
        $column_numero = new TDataGridColumn('numero', 'N° Conta', 'right');
        $column_digito = new TDataGridColumn('digito', 'Dígito', 'right');
        $column_agencia = new TDataGridColumn('agencia', 'Agência', 'right');
        $column_digitoagencia = new TDataGridColumn('digitoagencia', 'Dígito', 'right');
        $column_nomedep = new TDataGridColumn('nomedep', 'Nome Depositante', 'left');

        // add the columns to the DataGrid
        //$this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_cliente_id);
        $this->datagrid->addColumn($column_banco);
        $this->datagrid->addColumn($column_praca);
        $this->datagrid->addColumn($column_numero);
        $this->datagrid->addColumn($column_digito);
        $this->datagrid->addColumn($column_agencia);
        $this->datagrid->addColumn($column_digitoagencia);
        $this->datagrid->addColumn($column_nomedep);

        // creates the datagrid column actions
        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);
        
        $order_cliente_id = new TAction(array($this, 'onReload'));
        $order_cliente_id->setParameter('order', 'cliente_id');
        $column_cliente_id->setAction($order_cliente_id);
        
        $order_banco = new TAction(array($this, 'onReload'));
        $order_banco->setParameter('order', 'banco');
        $column_banco->setAction($order_banco);
        
        $order_praca = new TAction(array($this, 'onReload'));
        $order_praca->setParameter('order', 'praca');
        $column_praca->setAction($order_praca);
        
        /*$order_numero = new TAction(array($this, 'onReload'));
        $order_numero->setParameter('order', 'numero');
        $column_numero->setAction($order_numero);
        
        $order_digito = new TAction(array($this, 'onReload'));
        $order_digito->setParameter('order', 'digito');
        $column_digito->setAction($order_digito);
        
        $order_agencia = new TAction(array($this, 'onReload'));
        $order_agencia->setParameter('order', 'agencia');
        $column_agencia->setAction($order_agencia);
        
        $order_digitoagencia = new TAction(array($this, 'onReload'));
        $order_digitoagencia->setParameter('order', 'digitoagencia');
        $column_digitoagencia->setAction($order_digitoagencia);
        
        $order_nomedep = new TAction(array($this, 'onReload'));
        $order_nomedep->setParameter('order', 'nomedep');
        $column_nomedep->setAction($order_nomedep);*/
        
        // create EDIT action
        $action_edit = new TDataGridAction(array('ContacorrenteClienteForm', 'onEdit'));
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
        TSession::setValue('ContacorrenteClienteList_filter_id',   NULL);
        TSession::setValue('ContacorrenteClienteList_filter_cliente_id',   NULL);
        TSession::setValue('ContacorrenteClienteList_filter_banco',   NULL);
        TSession::setValue('ContacorrenteClienteList_filter_praca',   NULL);
        TSession::setValue('ContacorrenteClienteList_filter_numero',   NULL);
        TSession::setValue('ContacorrenteClienteList_filter_digito',   NULL);
        TSession::setValue('ContacorrenteClienteList_filter_agencia',   NULL);
        TSession::setValue('ContacorrenteClienteList_filter_digitoagencia',   NULL);
        TSession::setValue('ContacorrenteClienteList_filter_nomedep',   NULL);
    
        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', 'like', "%{$data->id}%"); // create the filter
            TSession::setValue('ContacorrenteClienteList_filter_id',   $filter); // stores the filter in the session
        }


        if (isset($data->cliente_id) AND ($data->cliente_id)) {
            $filter = new TFilter('cliente_id', '=', "{$data->cliente_id}"); // create the filter
            TSession::setValue('ContacorrenteClienteList_filter_cliente_id',   $filter); // stores the filter in the session
        }


        if (isset($data->banco) AND ($data->banco)) {
            $filter = new TFilter('banco', 'like', "%{$data->banco}%"); // create the filter
            TSession::setValue('ContacorrenteClienteList_filter_banco',   $filter); // stores the filter in the session
        }


        if (isset($data->praca) AND ($data->praca)) {
            $filter = new TFilter('praca', 'like', "%{$data->praca}%"); // create the filter
            TSession::setValue('ContacorrenteClienteList_filter_praca',   $filter); // stores the filter in the session
        }


        if (isset($data->numero) AND ($data->numero)) {
            $filter = new TFilter('numero', 'like', "%{$data->numero}%"); // create the filter
            TSession::setValue('ContacorrenteClienteList_filter_numero',   $filter); // stores the filter in the session
        }


        if (isset($data->digito) AND ($data->digito)) {
            $filter = new TFilter('digito', 'like', "%{$data->digito}%"); // create the filter
            TSession::setValue('ContacorrenteClienteList_filter_digito',   $filter); // stores the filter in the session
        }


        if (isset($data->agencia) AND ($data->agencia)) {
            $filter = new TFilter('agencia', 'like', "%{$data->agencia}%"); // create the filter
            TSession::setValue('ContacorrenteClienteList_filter_agencia',   $filter); // stores the filter in the session
        }


        if (isset($data->digitoagencia) AND ($data->digitoagencia)) {
            $filter = new TFilter('digitoagencia', 'like', "%{$data->digitoagencia}%"); // create the filter
            TSession::setValue('ContacorrenteClienteList_filter_digitoagencia',   $filter); // stores the filter in the session
        }


        if (isset($data->nomedep) AND ($data->nomedep)) {
            $filter = new TFilter('nomedep', 'like', "%{$data->nomedep}%"); // create the filter
            TSession::setValue('ContacorrenteClienteList_filter_nomedep',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('ContacorrenteCliente_filter_data', $data);
        
        $param=array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    
    public function onClear( $param )
    {
        $this->form->clear();
        
        TSession::setValue('ContacorrenteClienteList_filter_id',   NULL);
        TSession::setValue('ContacorrenteClienteList_filter_cliente_id',   NULL);
        TSession::setValue('ContacorrenteClienteList_filter_banco_id',   NULL);
        TSession::setValue('ContacorrenteClienteList_filter_praca',   NULL);
        TSession::setValue('ContacorrenteClienteList_filter_numero',   NULL);
        TSession::setValue('ContacorrenteClienteList_filter_digito',   NULL);
        TSession::setValue('ContacorrenteClienteList_filter_agencia',   NULL);
        TSession::setValue('ContacorrenteClienteList_filter_digitoagencia',   NULL);
        TSession::setValue('ContacorrenteClienteList_filter_nomedep',   NULL);
        
        TSession::setValue('ContacorrenteCliente_filter_data', NULL);
        
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
            
            // creates a repository for ContacorrenteCliente
            $repository = new TRepository('ContacorrenteCliente');
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

            if (TSession::getValue('ContacorrenteClienteList_filter_id')) {
                $criteria->add(TSession::getValue('ContacorrenteClienteList_filter_id')); // add the session filter
            }


            if (TSession::getValue('ContacorrenteClienteList_filter_cliente_id')) {
                $criteria->add(TSession::getValue('ContacorrenteClienteList_filter_cliente_id')); // add the session filter
            }


            if (TSession::getValue('ContacorrenteClienteList_filter_banco')) {
                $criteria->add(TSession::getValue('ContacorrenteClienteList_filter_banco')); // add the session filter
            }


            if (TSession::getValue('ContacorrenteClienteList_filter_praca')) {
                $criteria->add(TSession::getValue('ContacorrenteClienteList_filter_praca')); // add the session filter
            }


            if (TSession::getValue('ContacorrenteClienteList_filter_numero')) {
                $criteria->add(TSession::getValue('ContacorrenteClienteList_filter_numero')); // add the session filter
            }


            if (TSession::getValue('ContacorrenteClienteList_filter_digito')) {
                $criteria->add(TSession::getValue('ContacorrenteClienteList_filter_digito')); // add the session filter
            }


            if (TSession::getValue('ContacorrenteClienteList_filter_agencia')) {
                $criteria->add(TSession::getValue('ContacorrenteClienteList_filter_agencia')); // add the session filter
            }


            if (TSession::getValue('ContacorrenteClienteList_filter_digitoagencia')) {
                $criteria->add(TSession::getValue('ContacorrenteClienteList_filter_digitoagencia')); // add the session filter
            }


            if (TSession::getValue('ContacorrenteClienteList_filter_nomedep')) {
                $criteria->add(TSession::getValue('ContacorrenteClienteList_filter_nomedep')); // add the session filter
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
            $object = new ContacorrenteCliente($key, FALSE); // instantiates the Active Record
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