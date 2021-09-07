<?php
/**
 * ChequeList Listing
 * @author  <your name here>
 */
class ChequeList extends TPage
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
        $this->form = new TQuickForm('form_search_Cheque');
        $this->form->class = 'tform'; // change CSS class        
        $this->form->style = 'display: table;width:100%'; // change style
        //$this->form->setFormTitle('Cheque');
        $this->form->setFieldsByRow(2);

        // create the form fields
        $id = new TEntry('id');
        $dtcheque = new TDate('dtcheque');
        $banco_id = new TSeekButton('banco_id');
        $banco = new TEntry('banco');
        $conta = new TEntry('conta');
        $numero = new TEntry('numero');
        $valor = new TEntry('valor');
        $nominal = new TEntry('nominal');
        $dtprepago = new TDate('dtprepago');
        
        $obj = new BancoSelectionList;
        $action = new TAction(array($obj, 'onReload'));
        $banco_id->setAction($action);
        
        // Campos Não Editáveis
        $banco->setEditable(FALSE);
        
        // Máscaras
        $dtcheque->setMask('dd/mm/yyyy');        
        $dtprepago->setMask('dd/mm/yyyy');
        
        // Tamanho dos Campos no formulário
        $banco_id->setSize(50);
        $banco->setSize(327);

        // add the fields
        //$this->form->addQuickField(_t('ID'), $id,  50 );
        $this->form->addQuickField('Data', $dtcheque,  100 );
        $this->form->addQuickFields('Banco', array($banco_id, $banco) );
        $this->form->addQuickField('Conta', $conta,  100 );
        $this->form->addQuickField('Número', $numero,  100 );
        $this->form->addQuickField('Valor', $valor,  200 );
        $this->form->addQuickField('Nominal', $nominal,  300 );
        $this->form->addQuickField('Pré-Pago', $dtprepago,  100 );
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('Cheque_filter_data') );
        
        // add the search form actions
        $this->form->addQuickAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        $this->form->addQuickAction('Limpar Filtro',  new TAction(array($this, 'onClear')), 'bs:ban-circle red');
        $this->form->addQuickAction(_t('New'),  new TAction(array('ChequeForm', 'onEdit')), 'bs:plus-sign green');
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'left');
        $column_dtcheque = new TDataGridColumn('dtcheque', 'Data', 'left');
        $column_banco_id = new TDataGridColumn('banco', 'Banco', 'left');
        $column_contacorrente_id = new TDataGridColumn('contacorrente_id', 'Conta', 'left');
        $column_numero = new TDataGridColumn('numero', 'Número', 'left');
        $column_valor = new TDataGridColumn('valor', 'Valor', 'left');
        $column_nominal = new TDataGridColumn('nominal', 'Nominal', 'left');
        $column_dtprepago = new TDataGridColumn('dtprepago', 'Pré-Pago', 'left');

        // add the columns to the DataGrid
        //$this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_dtcheque);
        $this->datagrid->addColumn($column_banco_id);
        $this->datagrid->addColumn($column_contacorrente_id);
        $this->datagrid->addColumn($column_numero);
        $this->datagrid->addColumn($column_valor);
        $this->datagrid->addColumn($column_nominal);
        $this->datagrid->addColumn($column_dtprepago);
        
        // creates the datagrid column actions
        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);
        
        $order_dtcheque = new TAction(array($this, 'onReload'));
        $order_dtcheque->setParameter('order', 'dtcheque');
        $column_dtcheque->setAction($order_dtcheque);
        
        $order_banco_id = new TAction(array($this, 'onReload'));
        $order_banco_id->setParameter('order', 'banco_id');
        $column_banco_id->setAction($order_banco_id);
        
        $order_contacorrente_id = new TAction(array($this, 'onReload'));
        $order_contacorrente_id->setParameter('order', 'conta');
        $column_contacorrente_id->setAction($order_contacorrente_id);
        
        $order_numero = new TAction(array($this, 'onReload'));
        $order_numero->setParameter('order', 'numero');
        $column_numero->setAction($order_numero);
        
        $order_valor = new TAction(array($this, 'onReload'));
        $order_valor->setParameter('order', 'valor');
        $column_valor->setAction($order_valor);
        
        $order_nominal = new TAction(array($this, 'onReload'));
        $order_nominal->setParameter('order', 'nominal');
        $column_nominal->setAction($order_nominal);
        
        $order_dtprepago = new TAction(array($this, 'onReload'));
        $order_dtprepago->setParameter('order', 'dtprepago');
        $column_dtprepago->setAction($order_dtprepago);        

        // define the transformer method over image
        $column_dtcheque->setTransformer( function($value, $object, $row) {
            $date = new DateTime($value);
            return $date->format('d/m/Y');
        });

        // define the transformer method over image
        $column_valor->setTransformer( function($value, $object, $row) {
            return 'R$ ' . number_format($value, 2, ',', '.');
        });

        // define the transformer method over image
        $column_dtprepago->setTransformer( function($value, $object, $row) {
            $date = new DateTime($value);
            return $date->format('d/m/Y');
        });
        
        // create EDIT action
        $action_edit = new TDataGridAction(array('ChequeForm', 'onEdit'));
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
        
        // create PRINT action
        $action_print = new TDataGridAction(array($this, 'onPrint'));
        //$action_print->setUseButton(TRUE);
        $action_print->setButtonClass('btn btn-default');
        $action_print->setLabel('Imprimir Cheque');
        $action_print->setImage('fa:print black fa-lg');
        $action_print->setField('id');
        $this->datagrid->addAction($action_print);
        
        // create ITENS action
        /*$action_itens = new TDataGridAction(array('ChequeItemList', 'onSetCheque'));
        $action_itens->setUseButton(TRUE);
        $action_itens->setButtonClass('btn btn-default');
        $action_itens->setImage('fa:list blue fa-lg');
        $action_itens->setField('id');
        $this->datagrid->addAction($action_itens);*/
        
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
        TSession::setValue('ChequeList_filter_id',   NULL);
        TSession::setValue('ChequeList_filter_dtcheque',   NULL);
        TSession::setValue('ChequeList_filter_banco_id',   NULL);
        TSession::setValue('ChequeList_filter_contacorrente_id',   NULL);
        TSession::setValue('ChequeList_filter_numero',   NULL);
        TSession::setValue('ChequeList_filter_valor',   NULL);
        TSession::setValue('ChequeList_filter_nominal',   NULL);
        TSession::setValue('ChequeList_filter_dtprepago',   NULL);

        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', 'like', "%{$data->id}%"); // create the filter
            TSession::setValue('ChequeList_filter_id',   $filter); // stores the filter in the session
        }


        if (isset($data->dtcheque) AND ($data->dtcheque)) {
            $filter = new TFilter('dtcheque', 'like', "%{$data->dtcheque}%"); // create the filter
            TSession::setValue('ChequeList_filter_dtcheque',   $filter); // stores the filter in the session
        }


        if (isset($data->banco_id) AND ($data->banco_id)) {
            $filter = new TFilter('banco_id', 'like', "%{$data->banco_id}%"); // create the filter
            TSession::setValue('ChequeList_filter_banco_id',   $filter); // stores the filter in the session
        }


        if (isset($data->contacorrente_id) AND ($data->contacorrente_id)) {
            $filter = new TFilter('contacorrente_id', 'like', "%{$data->contacorrente_id}%"); // create the filter
            TSession::setValue('ChequeList_filter_contacorrente_id',   $filter); // stores the filter in the session
        }


        if (isset($data->numero) AND ($data->numero)) {
            $filter = new TFilter('numero', 'like', "%{$data->numero}%"); // create the filter
            TSession::setValue('ChequeList_filter_numero',   $filter); // stores the filter in the session
        }


        if (isset($data->valor) AND ($data->valor)) {
            $filter = new TFilter('valor', 'like', "%{$data->valor}%"); // create the filter
            TSession::setValue('ChequeList_filter_valor',   $filter); // stores the filter in the session
        }


        if (isset($data->nominal) AND ($data->nominal)) {
            $filter = new TFilter('nominal', 'like', "%{$data->nominal}%"); // create the filter
            TSession::setValue('ChequeList_filter_nominal',   $filter); // stores the filter in the session
        }


        if (isset($data->dtprepago) AND ($data->dtprepago)) {
            $filter = new TFilter('dtprepago', 'like', "%{$data->dtprepago}%"); // create the filter
            TSession::setValue('ChequeList_filter_dtprepago',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('Cheque_filter_data', $data);
        
        $param=array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    
    public function onClear( $param )
    {
        $this->form->clear();
        
        TSession::setValue('ChequeList_filter_id',   NULL);
        TSession::setValue('ChequeList_filter_dtcheque',   NULL);
        TSession::setValue('ChequeList_filter_banco_id',   NULL);
        TSession::setValue('ChequeList_filter_conta',   NULL);
        TSession::setValue('ChequeList_filter_numero',   NULL);
        TSession::setValue('ChequeList_filter_valor',   NULL);
        TSession::setValue('ChequeList_filter_nominal',   NULL);
        TSession::setValue('ChequeList_filter_dtprepago',   NULL);
        
        TSession::setValue('Cheque_filter_data', NULL);
        
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
            
            // creates a repository for Cheque
            $repository = new TRepository('Cheque');
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

            if (TSession::getValue('ChequeList_filter_id')) {
                $criteria->add(TSession::getValue('ChequeList_filter_id')); // add the session filter
            }


            if (TSession::getValue('ChequeList_filter_dtcheque')) {
                $criteria->add(TSession::getValue('ChequeList_filter_dtcheque')); // add the session filter
            }


            if (TSession::getValue('ChequeList_filter_banco_id')) {
                $criteria->add(TSession::getValue('ChequeList_filter_banco_id')); // add the session filter
            }


            if (TSession::getValue('ChequeList_filter_contacorrente_id')) {
                $criteria->add(TSession::getValue('ChequeList_filter_contacorrente_id')); // add the session filter
            }


            if (TSession::getValue('ChequeList_filter_numero')) {
                $criteria->add(TSession::getValue('ChequeList_filter_numero')); // add the session filter
            }


            if (TSession::getValue('ChequeList_filter_valor')) {
                $criteria->add(TSession::getValue('ChequeList_filter_valor')); // add the session filter
            }


            if (TSession::getValue('ChequeList_filter_nominal')) {
                $criteria->add(TSession::getValue('ChequeList_filter_nominal')); // add the session filter
            }


            if (TSession::getValue('ChequeList_filter_dtprepago')) {
                $criteria->add(TSession::getValue('ChequeList_filter_dtprepago')); // add the session filter
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
            $object = new Cheque($key, FALSE); // instantiates the Active Record
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
    
    public function onPrint()
    {
        
    }
}
?>