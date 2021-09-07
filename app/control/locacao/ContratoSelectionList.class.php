<?php
/**
 * ContratoSelectionList Record selection
 * @author  <your name here>
 */
class ContratoSelectionList extends TWindow
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
        $this->form = new TForm('form_select_Contrato');
        // creates a new table
        $table = new TTable;
        $table->{'width'} = '100%';
        // adds the table into the form
        $this->form->add($table);
        
        // create the form fields
        $id = new TEntry('id');        
        $bem_id = new TSeekButton('bem_id');
        $bem = new TEntry('bem');
        $criteria = new TCriteria;
        $criteria->setProperty('order', 'nome');
        $cliente_id = new TDBSeekButton('cliente_id',TSession::getValue('banco'),'form_select_Contrato','Cliente','nome','cliente_id','cliente',$criteria);
        $cliente = new TEntry('cliente');
        
        $obj = new BemSelectionList;
        $action = new TAction(array($obj, 'onReload'));
        $bem_id->setAction($action);
        
        // keeps the field's value
        $id->setValue( TSession::getValue('ContratoSelection_filter_id') );
        $bem_id->setValue( TSession::getValue('ContratoSelection_filter_bem_id') );
        $cliente_id->setValue( TSession::getValue('ContratoSelection_filter_cliente_id') );
        
        // Campos Não Editáveis
        $bem->setEditable(FALSE);
        $cliente->setEditable(FALSE);
        
        // Tamanho dos Campos no formulário
        $id->setSize(50);
        $bem_id->setSize(50);
        $bem->setSize(277);
        $cliente_id->setSize(50);
        $cliente->setSize(277);   
        
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
        $table->addRowSet( new TLabel(_t('ID')), $id, new TLabel('Bem'), array($bem_id,$bem));
        $table->addRowSet( new TLabel('Locador 1'), array($cliente_id, $cliente) );
        $row = $table->addRowSet($find_button, $clear_button);
        
        // define wich are the form fields
        $this->form->setFields(array($id, $bem_id, $bem, $cliente_id, $cliente, $find_button, $clear_button));
        
        // creates a new datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->{'style'} = 'width: 100%';
        
        // create two datagrid columns
        $column_id = new TDataGridColumn('id', _t('ID'), 'right');
        $column_bem_id = new TDataGridColumn('bem', 'Bem', 'left');
        $column_cliente_id = new TDataGridColumn('cliente', 'Locador 1', 'left');
        $column_cliente2_id = new TDataGridColumn('cliente2', 'Locador 2', 'left');        
        $column_dtinicio = new TDataGridColumn('dtinicio', 'Data Início', 'left');
        $column_dtfim = new TDataGridColumn('dtfim', 'Data Fim', 'right');
        $column_vlacrescido = new TDataGridColumn('vlacrescido', 'Valor Acrescido', 'right');
        $column_percdesc = new TDataGridColumn('percdesc', 'Percentual Desconto', 'right');
        $column_valor = new TDataGridColumn('valor', 'Valor Líquido', 'right');
        $column_vldesc = new TDataGridColumn('vldesc', 'Valor Desconto', 'right');
        $column_qtdeparcdesc = new TDataGridColumn('qtdeparcdesc', 'Quantidade Parcelas Desconto', 'right');
        $column_avalista_id = new TDataGridColumn('avalista', 'Avalista', 'left');
        $column_avalista2_id = new TDataGridColumn('avalista2', 'Avalista2', 'left');
        $column_vlseguro = new TDataGridColumn('vlseguro', 'Valor Seguro', 'right');
        $column_qtdeparc = new TDataGridColumn('qtdeparc', 'Quantidade Parcelas', 'right');
        $column_diavencto = new TDataGridColumn('diavencto', 'Dia Vencimento', 'right');
        $column_dtcadastro = new TDataGridColumn('dtcadastro', 'Data Cadastro', 'right');
        $column_liquidado = new TDataGridColumn('liquidado', 'Liquidado', 'left');
        $column_system_user_id = new TDataGridColumn('system_user_id', 'Corretor', 'right');
        
        // add the columns to the datagrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_bem_id);
        $this->datagrid->addColumn($column_cliente_id);
        //$this->datagrid->addColumn($column_cliente2_id);        
        $this->datagrid->addColumn($column_dtinicio);
        $this->datagrid->addColumn($column_dtfim);
        /*$this->datagrid->addColumn($column_vlacrescido);
        $this->datagrid->addColumn($column_percdesc);*/
        $this->datagrid->addColumn($column_valor);
        /*$this->datagrid->addColumn($column_vldesc);
        $this->datagrid->addColumn($column_qtdeparcdesc);
        $this->datagrid->addColumn($column_avalista_id);
        $this->datagrid->addColumn($column_avalista2_id);
        $this->datagrid->addColumn($column_vlseguro);
        $this->datagrid->addColumn($column_qtdeparc);
        $this->datagrid->addColumn($column_diavencto);
        $this->datagrid->addColumn($column_dtcadastro);*/
        $this->datagrid->addColumn($column_liquidado);
        //$this->datagrid->addColumn($column_system_user_id);
        
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
        
        if (isset($data->id) && ($data->id))
        {
            $filter = new TFilter('id', 'like', "%{$data->id}%");           
            TSession::setValue('ContratoSelection_filter_id',   $data->id);           
        }
        
        if (isset($data->bem_id) && ($data->bem_id))
        {
            $filter = new TFilter('bem_id', '=', "{$data->bem_id}");
            TSession::setValue('ContratoSelection_filter_bem_id',   $data->bem_id);   
        }
        
        if (isset($data->cliente_id) && ($data->cliente_id))
        {
            $filter = new TFilter('cliente_id', '=', "{$data->cliente_id}");
            TSession::setValue('ContratoSelection_filter_cliente_id',   $data->cliente_id);   
        }        
        
        // fill the form with data again
        $this->form->setData($data);

        // stores the filter in the session
        TSession::setValue('ContratoSelection_filter', $filter);
        
        // redefine the parameters for reload method
        $param=array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    
    public function onClear( $param )
    {
        $this->form->clear();
                
        TSession::setValue('ContratoSelection_filter_id',   NULL);
        TSession::setValue('ContratoSelection_filter_bem_id',   NULL);
        TSession::setValue('ContratoSelection_filter_cliente_id',   NULL);
        
        TSession::setValue('ContratoSelection_filter', NULL);
        
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
            $repository = new TRepository('Contrato');
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;
            
            // default order
            if (!isset($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'desc';
            }
            
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit); 
            
            if (TSession::getValue('ContratoSelection_filter'))
            {
                // add the filter stored in the session to the criteria
                $criteria->add(TSession::getValue('ContratoSelection_filter'));
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
            $key = $param['key'];
            TTransaction::open(TSession::getValue('banco'));
            
            // load the active record
            $contrato = new Contrato($key);
            
            // closes the transaction
            TTransaction::close();
            
            $object = new StdClass;
            $object->contrato_id = $contrato->id;
            
            TForm::sendData('form_ParcelaReceber', $object);
            TForm::sendData('form_ParcelaPagar', $object);
            TForm::sendData('form_ParcelaReceberCollection', $object);
            TForm::sendData('form_ParcelaPagarCollection', $object);
            TForm::sendData('form_LiquidacaoAntecipada', $object);
            parent::closeWindow(); // closes the window
        }
        catch (Exception $e) // em caso de exceção
        {
            // clear fields
            $object = new StdClass;
            $object->contrato_id   = '';
            TForm::sendData('form_ParcelaReceber', $object);
            TForm::sendData('form_ParcelaPagar', $object);
            TForm::sendData('form_ParcelaReceberCollection', $object);
            TForm::sendData('form_ParcelaPagarCollection', $object);
            TForm::sendData('form_LiquidacaoAntecipada', $object);
            
            // undo pending operations
            TTransaction::rollback();
        }
    }
}
?>