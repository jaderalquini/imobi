<?php
/**
 * LiquidacaoAntecipada Listing
 * @author  <your name here>
 */
class LiquidacaoAntecipada extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $formgrid;
    private $loaded;
    
    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new TQuickForm('form_LiquidacaoAntecipada');
        $this->form->class = 'tform'; // change CSS class        
        $this->form->style = 'display: table;width:100%'; // change style
        //$this->form->setFormTitle('LiquidacaoAntecipada');
        $this->form->setFieldsByRow(2);

        // create the form fields
        $contrato_id = new TEntry('contrato_id');
        $cliente_id = new TEntry('cliente_id');
        $cliente2_id = new TEntry('cliente2_id');

        // add the fields
        $this->form->addQuickField('Contrato Id', $contrato_id,  200 );
        $this->form->addQuickField('Cliente Id', $cliente_id,  200 );
        $this->form->addQuickField('Cliente2 Id', $cliente2_id,  200 );
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('LiquidacaoAntecipada_filter_data') );
        
        // add the search form actions
        $this->form->addQuickAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');        

        // creates the datagrid columns
        $column_numero = new TDataGridColumn('numero', 'Numero', 'left');
        $column_dtvencto = new TDataGridColumn('dtvencto', 'Dtvencto', 'left');
        $column_vlacrescido = new TDataGridColumn('vlacrescido', 'Vlacrescido', 'left');

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_numero);
        $this->datagrid->addColumn($column_dtvencto);
        $this->datagrid->addColumn($column_vlacrescido);
        
        // create the datagrid model
        $this->datagrid->createModel();      

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add($this->datagrid);
        
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
        TSession::setValue('LiquidacaoAntecipada_filter_contrato_id',   NULL);


        if (isset($data->contrato_id) AND ($data->contrato_id)) {
            $filter = new TFilter('contrato_id', 'like', "%{$data->contrato_id}%"); // create the filter
            TSession::setValue('LiquidacaoAntecipada_filter_contrato_id',   $filter); // stores the filter in the session
        }
        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('LiquidacaoAntecipada_filter_data', $data);
        
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
            // open a transaction with database 'menegotti'
            TTransaction::open('menegotti');
            
            // creates a repository for LiquidacaoAntecipada
            $repository = new TRepository('ParcelaReceber');
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'contrato_id';
                $param['direction'] = 'asc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);


            if (TSession::getValue('LiquidacaoAntecipada_filter_contrato_id')) {
                $criteria->add(TSession::getValue('LiquidacaoAntecipada_filter_contrato_id')); // add the session filter
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
            TTransaction::open('menegotti'); // open a transaction with database
            $object = new LiquidacaoAntecipada($key, FALSE); // instantiates the Active Record
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
            /*if (func_num_args() > 0)
            {
                $this->onReload( func_get_arg(0) );
            }
            else
            {
                $this->onReload();
            }*/
        }
        parent::show();
    }
}
?>