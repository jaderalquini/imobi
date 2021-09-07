<?php
/**
 * BemSelectionList Record selection
 * @author  <your name here>
 */
class BemSelectionList extends TWindow
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
        $this->form = new TForm('form_select_Bem');
        // creates a new table
        $table = new TTable;
        $table->{'width'} = '100%';
        // adds the table into the form
        $this->form->add($table);
        
        // create the form fields
        $descricao= new TEntry('descricao');
        $criteria = new TCriteria;
        $criteria->setProperty('order', 'nome');
        $proprietario_id = new TDBSeekButton('proprietario_id',TSession::getValue('banco'),'form_select_Bem','Cliente','nome','proprietario_id','proprietario',$criteria);
        $proprietario = new TEntry('proprietario');
        
        // keeps the field's value
        $descricao->setValue( TSession::getValue('BemSelection_filter_descricao') );
        $proprietario_id->setValue( TSession::getValue('BemSelection_filter_proprietario_id') );
        $proprietario->setValue( TSession::getValue('BemSelection_filter_proprietario') );
        
        // Campos Não Editáveis
        $proprietario->setEditable(FALSE);
        
        // Tamanho dos Campos no formulário
        $descricao->setSize(200);
        $proprietario_id->setSize(50);
        $proprietario->setSize(227);
        
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
        $table->addRowSet( new TLabel('Descrição: '), $descricao, new TLabel('Proprietário: '), array($proprietario_id,$proprietario), $find_button, $clear_button);
        
        // define wich are the form fields
        $this->form->setFields(array($descricao, $proprietario_id, $proprietario, $find_button, $clear_button));
        
        // creates a new datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->{'style'} = 'width: 100%';
        
        // create two datagrid columns
        $column_id = new TDataGridColumn('id', _t('ID'),  'right', '10%');
        $column_descricao = new TDataGridColumn('descricao', 'Descrição', 'left');
        $column_proprietario = new TDataGridColumn('proprietario', 'Proprietário', 'left');
        
        // add the columns to the datagrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_descricao);
        $this->datagrid->addColumn($column_proprietario);
        
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
        
        if (isset($data->descricao) && ($data->descricao))
        {
            $filter = new TFilter('descricao', 'like', "%{$data->descricao}%");           
            TSession::setValue('BemSelection_filter_descricao',   $data->descricao);           
        }
        
        if (isset($data->proprietario_id) && ($data->proprietario_id))
        {
            $filter = new TFilter('proprietario_id', '=', "{$data->proprietario_id}");
            TSession::setValue('BemSelection_filter_proprietario_id',   $data->proprietario_id);   
        }        
        
        // fill the form with data again
        $this->form->setData($data);

        // stores the filter in the session
        TSession::setValue('BemSelection_filter', $filter);
        
        // redefine the parameters for reload method
        $param=array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    
    public function onClear( $param )
    {
        $this->form->clear();
                
        TSession::setValue('BemSelection_filter_descricao',   NULL);
        TSession::setValue('BemSelection_filter_proprietario_id',   NULL);
        
        TSession::setValue('BemSelection_filter', NULL);
        
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
            $repository = new TRepository('Bem');
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;
            
            // default order
            if (!isset($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);            
            $criteria->add(new TFilter('contrato_id', '=', '0'));
            
            if (TSession::getValue('BemSelection_filter'))
            {
                // add the filter stored in the session to the criteria
                $criteria->add(TSession::getValue('BemSelection_filter'));
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
            $bem = new Bem($key);
            
            // closes the transaction
            TTransaction::close();
            
            $object = new StdClass;
            $object->bem_id = $bem->id;
            $object->bem = $bem->descricao;
            
            TForm::sendData('form_search_Contrato', $object);
            TForm::sendData('form_select_Contrato', $object);
            TForm::sendData('form_Contrato', $object);
            parent::closeWindow(); // closes the window
        }
        catch (Exception $e) // em caso de exceção
        {
            // clear fields
            $object = new StdClass;
            $object->bem_id   = '';
            $object->bem = '';
            TForm::sendData('form_search_Contrato', $object);
            TForm::sendData('form_select_Contrato', $object);
            TForm::sendData('form_Contrato', $object);
            
            // undo pending operations
            TTransaction::rollback();
        }
    }
}
?>