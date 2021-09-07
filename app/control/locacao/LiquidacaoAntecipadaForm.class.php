<?php
/**
 * LiquidacaoAntecipada Listing
 * @author  <your name here>
 */
class LiquidacaoAntecipadaForm extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $formgrid;
    
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
        $contrato_id = new TSeekButton('contrato_id');
        $cliente_id = new TEntry('cliente_id');
        $cliente = new TEntry('cliente');
        $cliente2_id = new TEntry('cliente2_id');
        $cliente2 = new TEntry('cliente2');
        $parcela_de = new TEntry('parcela_de');
        
        $obj = new ContratoSelectionList;
        $action = new TAction(array($obj, 'onReload'));
        $contrato_id->setAction($action);
        
        // Campos Não Editáveis
        $cliente_id->setEditable(FALSE);
        $cliente->setEditable(FALSE);
        $cliente2_id->setEditable(FALSE);
        $cliente2->setEditable(FALSE);
        
        // Tamanho dos Campos no formulário
        $cliente_id->setSize(50);
        $cliente->setSize(277);
        $cliente2_id->setSize(50);
        $cliente2->setSize(277);
        
        $contrato_id->setExitAction(new TAction(array($this, 'onExitContrato')));

        // add the fields
        $this->form->addQuickField('Contrato', $contrato_id,  50 );
        $this->form->addQuickFields('Locatário', array($cliente_id, $cliente) );
        $this->form->addQuickFields('Locatário 2', array($cliente2_id, $cliente2) );
        $this->form->addQuickField('Excluir parcelas a partir da: ', $parcela_de, 50 );
        
        // keep the form filled during navigation with session data
        //$this->form->setData( TSession::getValue('LiquidacaoAntecipada_filter_data') );
        
        // add the search form actions
        $this->form->addQuickAction('Listar Parcelas em Aberto', new TAction(array($this, 'onSearch')), 'fa:list-ul blue');
        $this->form->addQuickAction('Liquidar Contrato', new TAction(array($this, 'onLiquida')),'fa:money green');
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');        

        // creates the datagrid columns
        $column_numero = new TDataGridColumn('numero', 'Parcela', 'left');
        $column_dtvencto = new TDataGridColumn('dtvencto', 'Vencimento', 'right');
        $column_valor = new TDataGridColumn('valor', 'Valor', 'right');

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_numero);
        $this->datagrid->addColumn($column_dtvencto);
        $this->datagrid->addColumn($column_valor);
        
        // define the transformer method over image
        $column_dtvencto->setTransformer( function($value, $object, $row) {
            $date = new DateTime($value);
            return $date->format('d/m/Y');
        });
        
        // define the transformer method over image
        $column_valor->setTransformer( function($value, $object, $row) {
            return 'R$ ' . number_format($value, 2, ',', '.');
        });
        
        // create the datagrid model
        $this->datagrid->createModel();  
        
        $this->formgrid = new TQuickForm();
        $this->formgrid->class = 'tform'; // change CSS class        
        $this->formgrid->style = 'display: table;width:100%'; // change style
        $this->formgrid->add($this->datagrid);
        
        $gridpack = new TVBox;
        $gridpack->style = 'width: 100%';
        $gridpack->add($this->formgrid);

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        //$container->add($this->datagrid);
        $container->add($gridpack);
        
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
            $filter = new TFilter('contrato_id', '=', "{$data->contrato_id}"); // create the filter
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
            TTransaction::open(TSession::getValue('banco'));
            
            // creates a repository for LiquidacaoAntecipada
            $repository = new TRepository('ParcelaReceber');
            //$limit = 10;
            // creates a criteria
            $criteria = new TCriteria;
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'numero';
                $param['direction'] = 'asc';
            }
            $criteria->setProperties($param); // order, offset
            //$criteria->setProperty('limit', $limit);

            if (TSession::getValue('LiquidacaoAntecipada_filter_contrato_id')) {
                $criteria->add(TSession::getValue('LiquidacaoAntecipada_filter_contrato_id')); // add the session filter
            }
            
            $criteria->add(new TFilter('vlpago','<=','0'));
            
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
            //$this->loaded = true;
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            // undo all pending operations
            TTransaction::rollback();
        }
    }
    
    public static function onExitContrato($param)
    {
        $contrato_id = isset($param['contrato_id']) ? $param['contrato_id'] : NULL;
        
        if ($contrato_id !== NULL)
        {            
            try
            {                
                TTransaction::open(TSession::getValue('banco'));
                
                $contrato = new Contrato($contrato_id);
                $cliente = new Cliente($contrato->cliente_id);
                $cliente2 = new Cliente($contrato->cliente2_id);
                
                $obj = new StdClass;
                $obj->cliente_id = $contrato->cliente_id;  
                $obj->cliente = $cliente->nome;
                $obj->cliente2_id = $contrato->cliente2_id;
                $obj->cliente2 = $cliente2->nome;         
                                         
                TForm::sendData('form_LiquidacaoAntecipada', $obj);
                
                TTransaction::close();
            }
            catch (Exception $e)
            {
                new TMessage('error', $e->getMessage()); // shows the exception error message
                TTransaction::rollback(); // undo all pending operations       
            }
        }
    }
    
    public function onLiquida($param)
    {              
        $data = $this->form->getData();
        
        $contrato_id = isset($param['contrato_id']) ? $param['contrato_id'] : NULL;
        $parcela_de = isset($param['parcela_de']) ? $param['parcela_de'] : NULL;
        
        if ($contrato_id !== NULL && $contrato_id != '' && $parcela_de !== NULL && $parcela_de != '')
        {            
            try
            {                
                TTransaction::open(TSession::getValue('banco'));
                
                $contrato = new Contrato($contrato_id);
                
                for ($i = $param['parcela_de']; $i<= $contrato->qtdeparc; $i++) 
                {
                    $repository = new TRepository('ParcelaReceber');
                    $criteria = new TCriteria;
                    $criteria->add(new TFilter('contrato_id', '=', $contrato_id));
                    $criteria->add(new TFilter('numero', '=', $i));
                    
                    $objects = $repository->load($criteria, FALSE);
                    
                    if ($objects)
                    {
                        foreach ($objects as $object)
                        {
                            $parcela = new ParcelaReceber($object->id);
                            $parcela->delete();
                        }
                    }
                }
                
                new TMessage('info', 'Contrato Liquidado');
                
                TTransaction::close();
            }
            catch (Exception $e)
            {
                new TMessage('error', $e->getMessage()); // shows the exception error message
                TTransaction::rollback(); // undo all pending operations       
            }
        }
        
        $this->form->setData($data);
    }
}
?>