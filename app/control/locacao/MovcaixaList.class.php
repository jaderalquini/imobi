<?php
/**
 * MovcaixaList Listing
 * @author  <your name here>
 */
class MovcaixaList extends TPage
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
        $this->form = new TQuickForm('form_search_Movcaixa');
        $this->form->class = 'tform'; // change CSS class        
        $this->form->style = 'display: table;width:100%'; // change style
        //$this->form->setFormTitle('Movimentação de  Caixa');
        $this->form->setFieldsByRow(2);        

        // create the form fields
        $id = new TEntry('id');
        $dtmov = new TDate('dtmov');
        $seq = new TEntry('seq');
        $tipo = new TRadioGroup('tipo');
        $historico = new TEntry('historico');
        $valor = new TEntry('valor');

        // Máscaras
        $dtmov->setMask('dd/mm/yyyy');
        
        // Adiciona Items aos campos RadioGroup e ComboBox
        $tipo->addItems(array('E' => ' Entrada', 'S' => ' Saída'));
        
        // Define Layout dos campos RadioGroup e ComboBox
        $tipo->setLayout('horizontal');

        // add the fields
        //$this->form->addQuickField('ID', $id,  50 );
        $this->form->addQuickField('Data', $dtmov,  100 );
        $this->form->addQuickField('Sequência', $seq,  50 );
        $this->form->addQuickField('Tipo', $tipo,  100 );
        $this->form->addQuickField('Histórico', $historico,  400 );
        $this->form->addQuickField('Valor', $valor,  100 );
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('Movcaixa_filter_data') );
        
        // add the search form actions
        $this->form->addQuickAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        $this->form->addQuickAction('Limpar Filtro',  new TAction(array($this, 'onClear')), 'bs:ban-circle red');
        $this->form->addQuickAction(_t('New'),  new TAction(array('MovcaixaForm', 'onEdit')), 'bs:plus-sign green');
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'ID', 'right');
        $column_dtmov = new TDataGridColumn('dtmov', 'Data', 'left');
        $column_seq = new TDataGridColumn('seq', 'Seq.', 'right');
        $column_tipo = new TDataGridColumn('tipo', 'Tipo', 'left');
        $column_historico = new TDataGridColumn('historico', 'Histórico', 'left');
        $column_valor = new TDataGridColumn('valor', 'Valor', 'right');

        // add the columns to the DataGrid
        //$this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_dtmov);
        $this->datagrid->addColumn($column_seq);
        $this->datagrid->addColumn($column_tipo);
        $this->datagrid->addColumn($column_historico);
        $this->datagrid->addColumn($column_valor);

        // creates the datagrid column actions
        $order_dtmov = new TAction(array($this, 'onReload'));
        $order_dtmov->setParameter('order', 'dtmov');
        $column_dtmov->setAction($order_dtmov);
        
        $order_seq = new TAction(array($this, 'onReload'));
        $order_seq->setParameter('order', 'seq');
        $column_seq->setAction($order_seq);
        
        $order_tipo = new TAction(array($this, 'onReload'));
        $order_tipo->setParameter('order', 'tipo');
        $column_tipo->setAction($order_tipo);
        
        $order_historico = new TAction(array($this, 'onReload'));
        $order_historico->setParameter('order', 'historico');
        $column_historico->setAction($order_historico);
        
        $order_valor = new TAction(array($this, 'onReload'));
        $order_valor->setParameter('order', 'valor');
        $column_valor->setAction($order_valor);     

        // define the transformer method over image
        $column_dtmov->setTransformer( function($value, $object, $row) {
            $date = new DateTime($value);
            if ($value !== '') {
                return $date->format('d/m/Y');
            }
        });

        // define the transformer method over image
        $column_valor->setTransformer( function($value, $object, $row) {
            return 'R$ ' . number_format($value, 2, ',', '.');
        });
        
        // create EDIT action
        $action_edit = new TDataGridAction(array('MovcaixaForm', 'onEdit'));
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
        TSession::setValue('MovcaixaList_filter_id',   NULL);
        TSession::setValue('MovcaixaList_filter_dtmov',   NULL);
        TSession::setValue('MovcaixaList_filter_seq',   NULL);
        TSession::setValue('MovcaixaList_filter_tipo',   NULL);
        TSession::setValue('MovcaixaList_filter_historico',   NULL);
        TSession::setValue('MovcaixaList_filter_valor',   NULL);
        
        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', 'like', "%{$data->id}%"); // create the filter
            TSession::setValue('MovcaixaList_filter_id',   $filter); // stores the filter in the session
        }


        if (isset($data->dtmov) AND ($data->dtmov)) {
            $filter = new TFilter('dtmov', 'like', "%{$data->dtmov}%"); // create the filter
            TSession::setValue('MovcaixaList_filter_dtmov',   $filter); // stores the filter in the session
        }


        if (isset($data->seq) AND ($data->seq)) {
            $filter = new TFilter('seq', 'like', "%{$data->seq}%"); // create the filter
            TSession::setValue('MovcaixaList_filter_seq',   $filter); // stores the filter in the session
        }


        if (isset($data->tipo) AND ($data->tipo)) {
            $filter = new TFilter('tipo', 'like', "%{$data->tipo}%"); // create the filter
            TSession::setValue('MovcaixaList_filter_tipo',   $filter); // stores the filter in the session
        }


        if (isset($data->historico) AND ($data->historico)) {
            $filter = new TFilter('historico', 'like', "%{$data->historico}%"); // create the filter
            TSession::setValue('MovcaixaList_filter_historico',   $filter); // stores the filter in the session
        }


        if (isset($data->valor) AND ($data->valor)) {
            $filter = new TFilter('valor', 'like', "%{$data->valor}%"); // create the filter
            TSession::setValue('MovcaixaList_filter_valor',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('Movcaixa_filter_data', $data);
        
        $param=array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    
    public function onClear( $param )
    {
        $this->form->clear();
        
        TSession::setValue('MovcaixaList_filter_id',   NULL);
        TSession::setValue('MovcaixaList_filter_dtmov',   NULL);
        TSession::setValue('MovcaixaList_filter_seq',   NULL);
        TSession::setValue('MovcaixaList_filter_tipo',   NULL);
        TSession::setValue('MovcaixaList_filter_historico',   NULL);
        TSession::setValue('MovcaixaList_filter_valor',   NULL);
        
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
            
            // creates a repository for Movcaixa
            $repository = new TRepository('Movcaixa');
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

            if (TSession::getValue('MovcaixaList_filter_id')) {
                $criteria->add(TSession::getValue('MovcaixaList_filter_id')); // add the session filter
            }


            if (TSession::getValue('MovcaixaList_filter_dtmov')) {
                $criteria->add(TSession::getValue('MovcaixaList_filter_dtmov')); // add the session filter
            }


            if (TSession::getValue('MovcaixaList_filter_seq')) {
                $criteria->add(TSession::getValue('MovcaixaList_filter_seq')); // add the session filter
            }


            if (TSession::getValue('MovcaixaList_filter_tipo')) {
                $criteria->add(TSession::getValue('MovcaixaList_filter_tipo')); // add the session filter
            }


            if (TSession::getValue('MovcaixaList_filter_historico')) {
                $criteria->add(TSession::getValue('MovcaixaList_filter_historico')); // add the session filter
            }


            if (TSession::getValue('MovcaixaList_filter_valor')) {
                $criteria->add(TSession::getValue('MovcaixaList_filter_valor')); // add the session filter
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
            $object = new Movcaixa($key, FALSE); // instantiates the Active Record
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