<?php
/**
 * BancoSelectionList Record selection
 * @author  <your name here>
 */
class BancoSelectionList extends TWindow
{
    private $form;      // search form
    private $datagrid;  // listing
    private $pageNavigation;
    private $parentForm;
    
    /**
     * Page constructor
     */
    public function __construct()
    {
        parent::__construct();
        parent::include_js('app/lib/include/application.js');
        parent::setTitle( AdiantiCoreTranslator::translate('Search record') );
        parent::setSize(0.7, 640);
        
        // creates a new form
        $this->form = new TForm('form_select_Banco');
        // creates a new table
        $table = new TTable;
        $table->{'width'} = '100%';
        // adds the table into the form
        $this->form->add($table);
        
        // create the form fields
        $nome = new TEntry('nome');
        
        // keeps the field's value
        $nome->setValue( TSession::getValue('BancoSelection_filter_nome') );
        
        // create the action button
        $find_button = new TButton('busca');
        // define the button action
        $find_button->setAction(new TAction(array($this, 'onSearch')), AdiantiCoreTranslator::translate('Search'));
        $find_button->setImage('fa:search blue');
        
        // create the action button
        $clear_button = new TButton('clear');
        // define the button action
        $clear_button->setAction(new TAction(array($this, 'onClear')), 'Limpar Filtro');
        $clear_button->setImage('bs:ban-circle red');
        
        // add a row for the filter field
        $table->addRowSet( new TLabel(_t('Search').': '), $nome, $find_button, $clear_button);
        
        // define wich are the form fields
        $this->form->setFields(array($nome, $find_button, $clear_button));
        
        // creates a new datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->{'style'} = 'width: 100%';
        
        // create two datagrid columns
        $column_id = new TDataGridColumn('codigo', _t('ID'),  'right', '10%');
        $column_agencia = new TDataGridColumn('agencia', 'Agência', 'left');
        $column_nome = new TDataGridColumn('nome', 'Nome', 'left');
        
        // add the columns to the datagrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_agencia);
        $this->datagrid->addColumn($column_nome);
        
        // create a datagrid action
        $action1 = new TDataGridAction(array($this, 'onSelect'));
        $action1->setLabel(AdiantiCoreTranslator::translate('Select'));
        $action1->setImage('fa:check-circle-o green');
        $action1->setUseButton(TRUE);
        $action1->setField('id');
        
        // add the actions to the datagrid
        $this->datagrid->addAction($action1);
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the paginator
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        $panel = new TPanelGroup();
        $panel->add($this->form);
        
        // creates the container
        $vbox = new TVBox;
        $vbox->add($panel);
        $vbox->add($this->datagrid);
        $vbox->add($this->pageNavigation);
        $vbox->{'style'} = 'width: 100%';
        
        // add the container to the page
        parent::add($vbox);
    }
    
    /**
     * Register the user filter in the section
     */
    public function onSearch()
    {
        // get the search form data
        $data = $this->form->getData();        
        
        if (isset($data->nome) && ($data->nome))
        {
            $filter = new TFilter('nome', 'like', "%{$data->nome}%");           
            TSession::setValue('BancoSelection_filter_nome',   $data->nome);           
        }   
        
        // fill the form with data again
        $this->form->setData($data);

        // stores the filter in the session
        TSession::setValue('BancoSelection_filter', $filter);
        
        // redefine the parameters for reload method
        $param=array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    
    public function onClear( $param )
    {
        $this->form->clear();
                
        TSession::setValue('BancoSelection_filter_nome',   NULL);        
        TSession::setValue('BancoSelection_filter', NULL);
        
        $param=array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    
    /**
     * Load the datagrid with the active record objects
     */
    public function onReload($param = NULL)
    {
        try
        {
            // open a transaction with database 'samples'
            TTransaction::open(TSession::getValue('banco'));
            
            // creates a repository for City
            $repository = new TRepository('Banco');
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;
            
            // default order
            if (!isset($param['order']))
            {
                $param['order'] = 'nome';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            
            if (TSession::getValue('BancoSelection_filter'))
            {
                // add the filter stored in the session to the criteria
                $criteria->add(TSession::getValue('BancoSelection_filter'));
            }
            
            // load the objects according to the criteria
            $bens = $repository->load($criteria);
            $this->datagrid->clear();
            if ($bens)
            {
                foreach ($bens as $bem)
                {
                    // add the object inside the datagrid
                    $this->datagrid->addItem($bem);
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
     * Select the register by ID and return the information to the main form
     *     When using onblur signal, AJAX passes all needed parameters via GET
     *     instead of calling onSetup before.
     */
    public static function onSelect($param)
    {        
        try
        {
            $key = $param['id'];
            TTransaction::open(TSession::getValue('banco'));
            
            // load the active record
            $banco = new Banco($key);
            
            // closes the transaction
            TTransaction::close();
            
            $object = new StdClass;
            $object->banco_id = $banco->codigo;
            $object->banco = $banco->nome;
            
            TForm::sendData('form_search_Cheque', $object);
            TForm::sendData('form_Cheque', $object);
            TForm::sendData('form_search_Contacorrente', $object);
            TForm::sendData('form_Contacorrente', $object);
            TForm::sendData('form_search_Movbancaria', $object);
            TForm::sendData('form_Movbancaria', $object);
            parent::closeWindow(); // closes the window
        }
        catch (Exception $e) // em caso de exceção
        {
            // clear fields
            $object = new StdClass;
            $object->bem_id   = '';
            $object->bem = '';
            TForm::sendData('form_search_Cheque', $object);
            TForm::sendData('form_Cheque', $object);
            TForm::sendData('form_search_Contacorrente', $object);
            TForm::sendData('form_Contacorrente', $object);
            TForm::sendData('form_search_Movbancaria', $object);
            TForm::sendData('form_Movbancaria', $object);
            
            // undo pending operations
            TTransaction::rollback();
        }
    }
}
?>