<?php
/**
 * ModeloContratoList Listing
 * @author  <your name here>
 */
class ModeloContratoList extends TStandardList
{
    protected $form;     // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    protected $formgrid;
    protected $deleteButton;
    protected $transformCallback;
    
    /**
     * Page constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        parent::setDatabase('seam');            // defines the database
        parent::setActiveRecord('ModeloContrato');   // defines the active record
        parent::setDefaultOrder('id', 'asc');         // defines the default order
        // parent::setCriteria($criteria) // define a standard filter

        parent::addFilterField('id', 'like', 'id'); // filterField, operator, formField
        parent::addFilterField('descricao', 'like', 'descricao'); // filterField, operator, formField
        parent::addFilterField('rescom', 'like', 'rescom'); // filterField, operator, formField
        parent::addFilterField('tipogarantia_id', 'like', 'tipogarantia_id'); // filterField, operator, formField
        parent::addFilterField('qtdeparc', 'like', 'qtdeparc'); // filterField, operator, formField
        parent::addFilterField('conteudo', 'like', 'conteudo'); // filterField, operator, formField
        parent::addFilterField('tipocontrato_id', 'like', 'tipocontrato_id'); // filterField, operator, formField
        parent::addFilterField('percomissao', 'like', 'percomissao'); // filterField, operator, formField
        
        // creates the form
        $this->form = new TQuickForm('form_search_ModeloContrato');
        $this->form->class = 'tform'; // change CSS class
        
        $this->form->style = 'display: table;width:100%'; // change style
        //$this->form->setFormTitle('ModeloContrato');
        $this->form->setFieldsByRow(2);

        // create the form fields
        $id = new TEntry('id');
        $descricao = new TEntry('descricao');
        $tipocontrato_id = new TDBCombo('tipocontrato_id',TSession::getValue('banco'),'Tipocontrato','id','descricao');
        $percomissao = new TEntry('percomissao');
        $rescom = new TRadioGroup('rescom');
        $tipogarantia_id = new TDBCombo('tipogarantia_id',TSession::getValue('banco'),'Tipograntia','id','descricao');
        $qtdeparc = new TEntry('qtdeparc');
        $conteudo = new TEntry('conteudo');
        
        // Adiciona Items aos campos RadioGroup e ComboBox
        $rescom->addItems(array('R' => ' Residencial', 'C' => ' Comercial'));
        
        // Define Layout dos campos RadioGroup e ComboBox
        $rescom->setLayout('horizontal');

        // add the fields
        $this->form->addQuickField(_t('ID'), $id, 100 );
        $this->form->addQuickField('Descrição', $descricao, 400 );
        $this->form->addQuickField('Tipo de Contrato', $tipocontrato_id, 200 );
        //$this->form->addQuickField('Percentual Comissão', $percomissao, 200 );
        $this->form->addQuickField('Residencial/Comercial', $rescom, 200 );
        $this->form->addQuickField('Tipo de Garantia', $tipogarantia_id, 200 );
        //$this->form->addQuickField('Quantidade Parcelas', $qtdeparc, 50 );
        //$this->form->addQuickField('Conteúdo', $conteudo );
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('ModeloContrato_filter_data') );
        
        // add the search form actions
        $this->form->addQuickAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        $this->form->addQuickAction('Limpar Filtro',  new TAction(array($this, 'onClear')), 'bs:ban-circle red');
        $this->form->addQuickAction(_t('New'),  new TAction(array('ModeloContratoForm', 'onEdit')), 'bs:plus-sign green');
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');     

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', _t('ID'), 'left');
        $column_descricao = new TDataGridColumn('descricao', 'Descrição', 'left');
        $column_tipocontrato_id = new TDataGridColumn('tipocontrato', 'Tipo de Contrato', 'left');
        $column_percomissao = new TDataGridColumn('percomissao', 'Percentual Comissão', 'right');
        $column_rescom = new TDataGridColumn('rescom', 'Residencial/Comercial', 'left');
        $column_tipogarantia_id = new TDataGridColumn('tipogarantia', 'Tipo de Garantia', 'left');
        $column_qtdeparc = new TDataGridColumn('qtdeparc', 'Quantidade Parcelas', 'right');
        $column_conteudo = new TDataGridColumn('conteudo', 'Conteúdo', 'left');

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_descricao);
        $this->datagrid->addColumn($column_tipocontrato_id);
        $this->datagrid->addColumn($column_percomissao);
        $this->datagrid->addColumn($column_rescom);
        $this->datagrid->addColumn($column_tipogarantia_id);
        $this->datagrid->addColumn($column_qtdeparc);
        //$this->datagrid->addColumn($column_conteudo);
        
        // create EDIT action
        $action_edit = new TDataGridAction(array('ModeloContratoForm', 'onEdit'));
        //$action_edit->setUseButton(TRUE);
        $action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('fa:pencil-square-o blue fa-lg');
        $action_edit->setField('id');
        $this->datagrid->addAction($action_edit);
        
        // creates the datagrid column actions
        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);
        
        $order_descricao = new TAction(array($this, 'onReload'));
        $order_descricao->setParameter('order', 'descricao');
        $column_descricao->setAction($order_descricao);
        
        $order_tipocontrato_id = new TAction(array($this, 'onReload'));
        $order_tipocontrato_id->setParameter('order', 'tipocontrato_id');
        $column_tipocontrato_id->setAction($order_tipocontrato_id);
        
        $order_percomissao = new TAction(array($this, 'onReload'));
        $order_percomissao->setParameter('order', 'percomissao');
        $column_percomissao->setAction($order_percomissao);
        
        $order_rescom = new TAction(array($this, 'onReload'));
        $order_rescom->setParameter('order', 'rescom');
        $column_rescom->setAction($order_rescom);
        
        $order_tipogarantia_id = new TAction(array($this, 'onReload'));
        $order_tipogarantia_id->setParameter('order', 'tipogarantia_id');
        $column_tipogarantia_id->setAction($order_tipogarantia_id);
        
        $order_qtdeparc = new TAction(array($this, 'onReload'));
        $order_qtdeparc->setParameter('order', 'qtdeparc');
        $column_qtdeparc->setAction($order_qtdeparc);
        
        // define the transformer method over image
        $column_percomissao->setTransformer( function($value, $object, $row) {
            return number_format($value, 2, ',', '.');
        });
        
        // define the transformer method over image
        $column_rescom->setTransformer( function($value, $object, $row) {
            if ($value == 'C') {
                return 'COMERCIAL';
            }
            elseif ($value == 'R') {
                return 'RESIDENCIAL';
            }
        });
        
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
        
        // create the page navigation
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
}