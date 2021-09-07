<?php
/**
 * ContacorrenteList Listing
 * @author  <your name here>
 */
class ContacorrenteList extends TPage
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
        $this->form = new TQuickForm('form_search_Contacorrente');
        $this->form->class = 'tform'; // change CSS class        
        $this->form->style = 'display: table;width:100%'; // change style
        //$this->form->setFormTitle('Contacorrente'); 
        $this->form->setFieldsByRow(2);       

        // create the form fields
        $id = new TEntry('id');
        $banco_id = new TSeekButton('banco_id');
        $banco = new TEntry('banco');
        $id = new TEntry('id');
        $digito = new TEntry('digito');
        $descricao = new TEntry('descricao');
        
        $obj = new BancoSelectionList;
        $action = new TAction(array($obj, 'onReload'));
        $banco_id->setAction($action);
        
        // Campos Não Editáveis
        $banco->setEditable(FALSE);
        
        // Tamanho dos Campos no formulário
        $banco_id->setSize(50);
        $banco->setSize(277);
        
        // Número de Caracteres permitidos dentro dos campos
        $banco_id->setMaxLength(3);
        $id->setMaxLength(6);

        // add the fields
        $this->form->addQuickFields('Banco', array($banco_id, $banco) );
        $this->form->addQuickField('Conta', $id,  100 );
        $this->form->addQuickField('Dígito', $digito,  50 );
        $this->form->addQuickField('Descrição', $descricao,  300 );
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('Contacorrente_filter_data') );
        
        // add the search form actions
        $this->form->addQuickAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        $this->form->addQuickAction('Limpar Filtro',  new TAction(array($this, 'onClear')), 'bs:ban-circle red');
        $this->form->addQuickAction(_t('New'),  new TAction(array('ContacorrenteForm', 'onEdit')), 'bs:plus-sign green');
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');        

        // creates the datagrid columns
        $column_banco_id = new TDataGridColumn('banco', 'Banco', 'left');
        $column_id = new TDataGridColumn('id', 'Conta', 'right');
        $column_digito = new TDataGridColumn('digito', 'Dígito', 'right');
        $column_descricao = new TDataGridColumn('descricao', 'Descrição', 'left');

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_banco_id);
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_digito);
        $this->datagrid->addColumn($column_descricao);

        // creates the datagrid column actions
        /*$order_banco_id = new TAction(array($this, 'onReload'));
        $order_banco_id->setParameter('order', 'banco');
        $column_banco_id->setAction($order_banco_id);
        
        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);
        
        $order_digito = new TAction(array($this, 'onReload'));
        $order_digito->setParameter('order', 'digito');
        $column_digito->setAction($order_digito);
        
        $order_descricao = new TAction(array($this, 'onReload'));
        $order_descricao->setParameter('order', 'descricao');
        $column_descricao->setAction($order_descricao);*/       
        
        // create EDIT action
        $action_edit = new TDataGridAction(array('ContacorrenteForm', 'onEdit'));
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
        TSession::setValue('ContacorrenteList_filter_banco_id',   NULL);
        TSession::setValue('ContacorrenteList_filter_id',   NULL);
        TSession::setValue('ContacorrenteList_filter_digito',   NULL);
        TSession::setValue('ContacorrenteList_filter_descricao',   NULL);

        if (isset($data->banco_id) AND ($data->banco_id)) {
            $filter = new TFilter('banco_id', 'like', "%{$data->banco_id}%"); // create the filter
            TSession::setValue('ContacorrenteList_filter_banco_id',   $filter); // stores the filter in the session
        }


        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', 'like', "%{$data->id}%"); // create the filter
            TSession::setValue('ContacorrenteList_filter_id',   $filter); // stores the filter in the session
        }


        if (isset($data->digito) AND ($data->digito)) {
            $filter = new TFilter('digito', 'like', "%{$data->digito}%"); // create the filter
            TSession::setValue('ContacorrenteList_filter_digito',   $filter); // stores the filter in the session
        }


        if (isset($data->descricao) AND ($data->descricao)) {
            $filter = new TFilter('descricao', 'like', "%{$data->descricao}%"); // create the filter
            TSession::setValue('ContacorrenteList_filter_descricao',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('Contacorrente_filter_data', $data);
        
        $param=array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    
    public function onClear( $param )
    {
        $this->form->clear();
        
        TSession::setValue('ContacorrenteList_filter_banco_id',   NULL);
        TSession::setValue('ContacorrenteList_filter_id',   NULL);
        TSession::setValue('ContacorrenteList_filter_digito',   NULL);
        TSession::setValue('ContacorrenteList_filter_descricao',   NULL);
        
        TSession::setValue('Contacorrente_filter_data', NULL);
        
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
            
            // creates a repository for Contacorrente
            $repository = new TRepository('Contacorrente');
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

            if (TSession::getValue('ContacorrenteList_filter_banco_id')) {
                $criteria->add(TSession::getValue('ContacorrenteList_filter_banco_id')); // add the session filter
            }


            if (TSession::getValue('ContacorrenteList_filter_id')) {
                $criteria->add(TSession::getValue('ContacorrenteList_filter_id')); // add the session filter
            }


            if (TSession::getValue('ContacorrenteList_filter_digito')) {
                $criteria->add(TSession::getValue('ContacorrenteList_filter_digito')); // add the session filter
            }


            if (TSession::getValue('ContacorrenteList_filter_descricao')) {
                $criteria->add(TSession::getValue('ContacorrenteList_filter_descricao')); // add the session filter
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
            $object = new Contacorrente($key, FALSE); // instantiates the Active Record
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