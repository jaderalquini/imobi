<?php
/**
 * ContratoList Listing
 * @author  <your name here>
 */
class ContratoList extends TPage
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
        $this->form = new TQuickForm('form_search_Contrato');
        $this->form->class = 'tform'; // change CSS class        
        $this->form->style = 'display: table;width:100%'; // change style
        //$this->form->setFormTitle('Contrato');
        $this->form->setFieldsByRow(2);

        // create the form fields
        $id = new TEntry('id');        
        $bem_id = new TSeekButton('bem_id');
        $bem = new TEntry('bem');
        $criteria = new TCriteria;
        $criteria->setProperty('order', 'nome');
        $cliente_id = new TDBSeekButton('cliente_id',TSession::getValue('banco'),'form_search_Contrato','Cliente','nome','cliente_id','cliente',$criteria);
        $cliente = new TEntry('cliente');
        $cliente2_id = new TDBSeekButton('cliente2_id',TSession::getValue('banco'),'form_search_Contrato','Cliente','nome','cliente2_id','cliente2',$criteria);        
        $cliente2 = new TEntry('cliente2');
        $dtinicio = new TDate('dtinicio');
        $dtfim = new TDate('dtfim');
        $vlacrescido = new TEntry('vlacrescido');
        $percdesc = new TEntry('percdesc');
        $valor = new TEntry('valor');
        $vldesc = new TEntry('vldesc');
        $qtdeparcdesc = new TEntry('qtdeparcdesc');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('id', '>', 0));
        $criteria->setProperty('order', 'nome');
        $avalista_id = new TDBSeekButton('avalista_id',TSession::getValue('banco'),'form_Contrato','Cliente','nome','avalista_id','avalista',$criteria);
        $avalista = new TEntry('avalista');
        $avalista2_id = new TDBSeekButton('avalista2_id',TSession::getValue('banco'),'form_Contrato','Cliente','nome','avalista2_id','avalista2', $criteria);
        $avalista2 = new TEntry('avalista2');
        $vlseguro = new TEntry('vlseguro');
        $qtdeparc = new TEntry('qtdeparc');
        $diavencto = new TEntry('diavencto');
        $dtcadastro = new TDate('dtcadastro');
        $liquidado = new TRadioGroup('liquidado');
        $system_user_id = new TDBSeekButton('system_user_id',TSession::getValue('banco'),'form_search_Contrato','SystemUser','name','system_user_id','system_user');
        $system_user = new TEntry('system_user');
        
        $obj = new BemSelectionList;
        $action = new TAction(array($obj, 'onReload'));
        $bem_id->setAction($action);
        
        // Campos Não Editáveis
        $bem->setEditable(FALSE);
        $cliente->setEditable(FALSE);
        $cliente2->setEditable(FALSE);        
        $avalista->setEditable(FALSE);
        $avalista2->setEditable(FALSE);
        $system_user->setEditable(FALSE);
        
        // Máscaras
        $dtinicio->setMask('dd/mm/yyyy');
        $dtfim->setMask('dd/mm/yyyy');
        $dtcadastro->setMask('dd/mm/yyyy');
        
        // Formatação para Valores Monetário
        $vlacrescido->setNumericMask(2, ',', '.');
        $valor->setNumericMask(2, ',', '.');
        $vldesc->setNumericMask(2, ',', '.');
        $vlseguro->setNumericMask(2, ',', '.');
        
        // Tamanho dos Campos no formulário
        $bem_id->setSize(50);
        $bem->setSize(277);
        $cliente_id->setSize(50);
        $cliente->setSize(277);        
        $cliente2_id->setSize(50);
        $cliente2->setSize(277);                
        $avalista_id->setSize(50);
        $avalista->setSize(277);        
        $avalista2_id->setSize(50);
        $avalista2->setSize(277);
        $system_user_id->setSize(50);
        $system_user->setSize(277);
        
        // Número de Caracteres permitidos dentro dos campos
        
        // Adiciona Items aos campos RadioGroup e ComboBox        
        $liquidado->addItems(array('S' => ' Sim', 'N' => ' Não'));
        
        // Define Layout dos campos RadioGroup e ComboBox
        $liquidado->setLayout('horizontal');

        // add the fields
        $this->form->addQuickField(_t('ID'), $id,  50 );
        $this->form->addQuickFields('Bem', array($bem_id,$bem) );
        $this->form->addQuickFields('Locatário 1', array($cliente_id,$cliente) );
        /*$this->form->addQuickFields('Locador 2', array($cliente2_id,$cliente2) );        
        $this->form->addQuickField('Data Início', $dtinicio,  100 );
        $this->form->addQuickField('Data Fim', $dtfim,  100 );
        $this->form->addQuickField('Quantidade Parcelas', $qtdeparc,  50 );
        $this->form->addQuickField('Valor Aluguel', $vlacrescido,  200 );
        $this->form->addQuickField('Percentual Desconto', $percdesc,  50 );
        $this->form->addQuickField('Valor Líquido', $valor,  100 );
        $this->form->addQuickField('Valor Desconto', $vldesc,  200 );
        $this->form->addQuickField('Quantidade Parcelas Desconto', $qtdeparcdesc,  50 );
        $this->form->addQuickField('Valor Seguro', $vlseguro,  200 );
        $this->form->addQuickFields('Avalista 1', array($avalista_id,$avalista) );
        $this->form->addQuickFields('Avalista2', array($avalista2_id,$avalista2) );                
        $this->form->addQuickField('Dia Vencimento', $diavencto,  50 );
        $this->form->addQuickField('Data Cadastro', $dtcadastro,  100 );*/
        $this->form->addQuickField('Liquidado', $liquidado,  100 );
        //$this->form->addQuickFields('Corretor', array($system_user_id,$system_user) );
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('Contrato_filter_data') );
        
        // add the search form actions
        $this->form->addQuickAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        $this->form->addQuickAction('Limpar Filtro',  new TAction(array($this, 'onClear')), 'bs:ban-circle red');
        $this->form->addQuickAction(_t('New'),  new TAction(array('ContratoForm', 'onEdit')), 'bs:plus-sign green');
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', _t('ID'), 'right');
        $column_bem_id = new TDataGridColumn('bem', 'Bem', 'left');
        $column_cliente_id = new TDataGridColumn('cliente', 'Locatário 1', 'left');
        $column_cliente2_id = new TDataGridColumn('cliente2', 'Locatário 2', 'left');        
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

        // add the columns to the DataGrid
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
        
        // creates the datagrid column actions
        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);
        
        $order_cliente_id = new TAction(array($this, 'onReload'));
        $order_cliente_id->setParameter('order', 'cliente_id');
        $column_cliente_id->setAction($order_cliente_id);
        
        /*$order_cliente2_id = new TAction(array($this, 'onReload'));
        $order_cliente2_id->setParameter('order', 'cliente2_id');
        $column_cliente2_id->setAction($order_cliente2_id);*/
        
        $order_bem_id = new TAction(array($this, 'onReload'));
        $order_bem_id->setParameter('order', 'bem_id');
        $column_bem_id->setAction($order_bem_id);
        
        $order_dtinicio = new TAction(array($this, 'onReload'));
        $order_dtinicio->setParameter('order', 'dtinicio');
        $column_dtinicio->setAction($order_dtinicio);
        
        $order_dtfim = new TAction(array($this, 'onReload'));
        $order_dtfim->setParameter('order', 'dtfim');
        $column_dtfim->setAction($order_dtfim);
        
        /*$order_vlacrescido = new TAction(array($this, 'onReload'));
        $order_vlacrescido->setParameter('order', 'vlacrescido');
        $column_vlacrescido->setAction($order_vlacrescido);
        
        $order_percdesc = new TAction(array($this, 'onReload'));
        $order_percdesc->setParameter('order', 'percdesc');
        $column_percdesc->setAction($order_percdesc);*/
        
        $order_valor = new TAction(array($this, 'onReload'));
        $order_valor->setParameter('order', 'valor');
        $column_valor->setAction($order_valor);
        
        /*$order_vldesc = new TAction(array($this, 'onReload'));
        $order_vldesc->setParameter('order', 'vldesc');
        $column_vldesc->setAction($order_vldesc);
        
        $order_qtdeparcdesc = new TAction(array($this, 'onReload'));
        $order_qtdeparcdesc->setParameter('order', 'qtdeparcdesc');
        $column_qtdeparcdesc->setAction($order_qtdeparcdesc);
        
        $order_avalista_id = new TAction(array($this, 'onReload'));
        $order_avalista_id->setParameter('order', 'avalista_id');
        $column_avalista_id->setAction($order_avalista_id);
        
        $order_avalista2_id = new TAction(array($this, 'onReload'));
        $order_avalista2_id->setParameter('order', 'avalista2_id');
        $column_avalista2_id->setAction($order_avalista2_id);
        
        $order_vlseguro = new TAction(array($this, 'onReload'));
        $order_vlseguro->setParameter('order', 'vlseguro');
        $column_vlseguro->setAction($order_vlseguro);
        
        $order_qtdeparc = new TAction(array($this, 'onReload'));
        $order_qtdeparc->setParameter('order', 'qtdeparc');
        $column_qtdeparc->setAction($order_qtdeparc);
        
        $order_diavencto = new TAction(array($this, 'onReload'));
        $order_diavencto->setParameter('order', 'diavencto');
        $column_diavencto->setAction($order_diavencto);
        
        $order_dtcadastro = new TAction(array($this, 'onReload'));
        $order_dtcadastro->setParameter('order', 'dtcadastro');
        $column_dtcadastro->setAction($order_dtcadastro);*/
        
        $order_liquidado = new TAction(array($this, 'onReload'));
        $order_liquidado->setParameter('order', 'liquidado');
        $column_liquidado->setAction($order_liquidado);
        
        /*$order_system_user_id = new TAction(array($this, 'onReload'));
        $order_system_user_id->setParameter('order', 'system_user_id');
        $column_system_user_id->setAction($order_system_user_id);*/    

        // define the transformer method over image
        $column_dtinicio->setTransformer( function($value, $object, $row) {
            $date = new DateTime($value);
            if ($value !== '') {
                return $date->format('d/m/Y');
            }
        });

        // define the transformer method over image
        $column_dtfim->setTransformer( function($value, $object, $row) {
            $date = new DateTime($value);
            if ($value !== '') {
                return $date->format('d/m/Y');
            }
        });

        // define the transformer method over image
        $column_vlacrescido->setTransformer( function($value, $object, $row) {
            return 'R$ ' . number_format($value, 2, ',', '.');
        });

        // define the transformer method over image
        $column_valor->setTransformer( function($value, $object, $row) {
            return 'R$ ' . number_format($value, 2, ',', '.');
        });

        // define the transformer method over image
        $column_vldesc->setTransformer( function($value, $object, $row) {
            return 'R$ ' . number_format($value, 2, ',', '.');
        });

        // define the transformer method over image
        $column_vlseguro->setTransformer( function($value, $object, $row) {
            return 'R$ ' . number_format($value, 2, ',', '.');
        });

        // define the transformer method over image
        $column_dtcadastro->setTransformer( function($value, $object, $row) {
            $date = new DateTime($value);
            if ($value !== '') {
                return $date->format('d/m/Y');
            }
        });
        
        // create EDIT action
        $action_edit = new TDataGridAction(array('ContratoForm', 'onEdit'));
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
        $action_print->setLabel('Imprimir Contrato');
        $action_print->setImage('fa:print black fa-lg');
        $action_print->setField('id');
        $this->datagrid->addAction($action_print);
        
        // create ParcelaReceber action
        /*$action_edit = new TDataGridAction(array('ParcelaReceberList', 'onSetContrato'));
        //$action_edit->setUseButton(TRUE);
        $action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel('Parcelas de Aluguel');
        $action_edit->setImage('fa:download green fa-lg');
        $action_edit->setField('id');
        $this->datagrid->addAction($action_edit);
        
        // create ParcelaPagar action
        $action_edit = new TDataGridAction(array('ParcelaPagarList', 'onSetContrato'));
        //$action_edit->setUseButton(TRUE);
        $action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel('Parcelas ao Locador');
        $action_edit->setImage('fa:upload red fa-lg');
        $action_edit->setField('id');
        $this->datagrid->addAction($action_edit);*/
        
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
        TSession::setValue('ContratoList_filter_id',   NULL);
        TSession::setValue('ContratoList_filter_bem_id',   NULL);
        TSession::setValue('ContratoList_filter_cliente_id',   NULL);
        TSession::setValue('ContratoList_filter_cliente2_id',   NULL);
        TSession::setValue('ContratoList_filter_dtinicio',   NULL);
        TSession::setValue('ContratoList_filter_dtfim',   NULL);
        TSession::setValue('ContratoList_filter_vlacrescido',   NULL);
        TSession::setValue('ContratoList_filter_percdesc',   NULL);
        TSession::setValue('ContratoList_filter_valor',   NULL);
        TSession::setValue('ContratoList_filter_vldesc',   NULL);
        TSession::setValue('ContratoList_filter_qtdeparcdesc',   NULL);
        TSession::setValue('ContratoList_filter_avalista_id',   NULL);
        TSession::setValue('ContratoList_filter_avalista2_id',   NULL);
        TSession::setValue('ContratoList_filter_vlseguro',   NULL);
        TSession::setValue('ContratoList_filter_qtdeparc',   NULL);
        TSession::setValue('ContratoList_filter_diavencto',   NULL);
        TSession::setValue('ContratoList_filter_dtcadastro',   NULL);
        TSession::setValue('ContratoList_filter_liquidado',   NULL);
        TSession::setValue('ContratoList_filter_system_user_id',   NULL);

        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', 'like', "%{$data->id}%"); // create the filter
            TSession::setValue('ContratoList_filter_id',   $filter); // stores the filter in the session
        }


        if (isset($data->bem_id) AND ($data->bem_id)) {
            $filter = new TFilter('bem_id', 'like', "%{$data->bem_id}%"); // create the filter
            TSession::setValue('ContratoList_filter_bem_id',   $filter); // stores the filter in the session
        }


        if (isset($data->cliente_id) AND ($data->cliente_id)) {
            $filter = new TFilter('cliente_id', 'like', "%{$data->cliente_id}%"); // create the filter
            TSession::setValue('ContratoList_filter_cliente_id',   $filter); // stores the filter in the session
        }


        if (isset($data->cliente2_id) AND ($data->cliente2_id)) {
            $filter = new TFilter('cliente2_id', 'like', "%{$data->cliente2_id}%"); // create the filter
            TSession::setValue('ContratoList_filter_cliente2_id',   $filter); // stores the filter in the session
        }


        if (isset($data->dtinicio) AND ($data->dtinicio)) {
            $filter = new TFilter('dtinicio', 'like', "%{$data->dtinicio}%"); // create the filter
            TSession::setValue('ContratoList_filter_dtinicio',   $filter); // stores the filter in the session
        }


        if (isset($data->dtfim) AND ($data->dtfim)) {
            $filter = new TFilter('dtfim', 'like', "%{$data->dtfim}%"); // create the filter
            TSession::setValue('ContratoList_filter_dtfim',   $filter); // stores the filter in the session
        }


        if (isset($data->vlacrescido) AND ($data->vlacrescido)) {
            $filter = new TFilter('vlacrescido', 'like', "%{$data->vlacrescido}%"); // create the filter
            TSession::setValue('ContratoList_filter_vlacrescido',   $filter); // stores the filter in the session
        }


        if (isset($data->percdesc) AND ($data->percdesc)) {
            $filter = new TFilter('percdesc', 'like', "%{$data->percdesc}%"); // create the filter
            TSession::setValue('ContratoList_filter_percdesc',   $filter); // stores the filter in the session
        }


        if (isset($data->valor) AND ($data->valor)) {
            $filter = new TFilter('valor', 'like', "%{$data->valor}%"); // create the filter
            TSession::setValue('ContratoList_filter_valor',   $filter); // stores the filter in the session
        }


        if (isset($data->vldesc) AND ($data->vldesc)) {
            $filter = new TFilter('vldesc', 'like', "%{$data->vldesc}%"); // create the filter
            TSession::setValue('ContratoList_filter_vldesc',   $filter); // stores the filter in the session
        }


        if (isset($data->qtdeparcdesc) AND ($data->qtdeparcdesc)) {
            $filter = new TFilter('qtdeparcdesc', 'like', "%{$data->qtdeparcdesc}%"); // create the filter
            TSession::setValue('ContratoList_filter_qtdeparcdesc',   $filter); // stores the filter in the session
        }


        if (isset($data->avalista_id) AND ($data->avalista_id)) {
            $filter = new TFilter('avalista_id', 'like', "%{$data->avalista_id}%"); // create the filter
            TSession::setValue('ContratoList_filter_avalista_id',   $filter); // stores the filter in the session
        }


        if (isset($data->avalista2_id) AND ($data->avalista2_id)) {
            $filter = new TFilter('avalista2_id', 'like', "%{$data->avalista2_id}%"); // create the filter
            TSession::setValue('ContratoList_filter_avalista2_id',   $filter); // stores the filter in the session
        }


        if (isset($data->vlseguro) AND ($data->vlseguro)) {
            $filter = new TFilter('vlseguro', 'like', "%{$data->vlseguro}%"); // create the filter
            TSession::setValue('ContratoList_filter_vlseguro',   $filter); // stores the filter in the session
        }


        if (isset($data->qtdeparc) AND ($data->qtdeparc)) {
            $filter = new TFilter('qtdeparc', 'like', "%{$data->qtdeparc}%"); // create the filter
            TSession::setValue('ContratoList_filter_qtdeparc',   $filter); // stores the filter in the session
        }


        if (isset($data->diavencto) AND ($data->diavencto)) {
            $filter = new TFilter('diavencto', 'like', "%{$data->diavencto}%"); // create the filter
            TSession::setValue('ContratoList_filter_diavencto',   $filter); // stores the filter in the session
        }


        if (isset($data->dtcadastro) AND ($data->dtcadastro)) {
            $filter = new TFilter('dtcadastro', 'like', "%{$data->dtcadastro}%"); // create the filter
            TSession::setValue('ContratoList_filter_dtcadastro',   $filter); // stores the filter in the session
        }


        if (isset($data->liquidado) AND ($data->liquidado)) {
            $filter = new TFilter('liquidado', 'like', "%{$data->liquidado}%"); // create the filter
            TSession::setValue('ContratoList_filter_liquidado',   $filter); // stores the filter in the session
        }


        if (isset($data->system_user_id) AND ($data->system_user_id)) {
            $filter = new TFilter('system_user_id', 'like', "%{$data->system_user_id}%"); // create the filter
            TSession::setValue('ContratoList_filter_system_user_id',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('Contrato_filter_data', $data);
        
        $param=array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    
    public function onClear( $param )
    {
        $this->form->clear();
        
        TSession::setValue('ContratoList_filter_id',   NULL);
        TSession::setValue('ContratoList_filter_cliente_id',   NULL);
        TSession::setValue('ContratoList_filter_cliente2_id',   NULL);
        TSession::setValue('ContratoList_filter_bem_id',   NULL);
        TSession::setValue('ContratoList_filter_dtinicio',   NULL);
        TSession::setValue('ContratoList_filter_dtfim',   NULL);
        TSession::setValue('ContratoList_filter_vlacrescido',   NULL);
        TSession::setValue('ContratoList_filter_percdesc',   NULL);
        TSession::setValue('ContratoList_filter_valor',   NULL);
        TSession::setValue('ContratoList_filter_vldesc',   NULL);
        TSession::setValue('ContratoList_filter_qtdeparcdesc',   NULL);
        TSession::setValue('ContratoList_filter_avalista_id',   NULL);
        TSession::setValue('ContratoList_filter_avalista2_id',   NULL);
        TSession::setValue('ContratoList_filter_vlseguro',   NULL);
        TSession::setValue('ContratoList_filter_qtdeparc',   NULL);
        TSession::setValue('ContratoList_filter_diavencto',   NULL);
        TSession::setValue('ContratoList_filter_dtcadastro',   NULL);
        TSession::setValue('ContratoList_filter_liquidado',   NULL);
        TSession::setValue('ContratoList_filter_system_user_id',   NULL);
        
        TSession::setValue('Contrato_filter_data', NULL);
        
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
            
            // creates a repository for Contrato
            $repository = new TRepository('Contrato');
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'desc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);

            if (TSession::getValue('ContratoList_filter_id')) {
                $criteria->add(TSession::getValue('ContratoList_filter_id')); // add the session filter
            }


            if (TSession::getValue('ContratoList_filter_bem_id')) {
                $criteria->add(TSession::getValue('ContratoList_filter_bem_id')); // add the session filter
            }


            if (TSession::getValue('ContratoList_filter_cliente_id')) {
                $criteria->add(TSession::getValue('ContratoList_filter_cliente_id')); // add the session filter
            }


            if (TSession::getValue('ContratoList_filter_cliente2_id')) {
                $criteria->add(TSession::getValue('ContratoList_filter_cliente2_id')); // add the session filter
            }


            if (TSession::getValue('ContratoList_filter_dtinicio')) {
                $criteria->add(TSession::getValue('ContratoList_filter_dtinicio')); // add the session filter
            }


            if (TSession::getValue('ContratoList_filter_dtfim')) {
                $criteria->add(TSession::getValue('ContratoList_filter_dtfim')); // add the session filter
            }


            if (TSession::getValue('ContratoList_filter_vlacrescido')) {
                $criteria->add(TSession::getValue('ContratoList_filter_vlacrescido')); // add the session filter
            }


            if (TSession::getValue('ContratoList_filter_percdesc')) {
                $criteria->add(TSession::getValue('ContratoList_filter_percdesc')); // add the session filter
            }


            if (TSession::getValue('ContratoList_filter_valor')) {
                $criteria->add(TSession::getValue('ContratoList_filter_valor')); // add the session filter
            }


            if (TSession::getValue('ContratoList_filter_vldesc')) {
                $criteria->add(TSession::getValue('ContratoList_filter_vldesc')); // add the session filter
            }


            if (TSession::getValue('ContratoList_filter_qtdeparcdesc')) {
                $criteria->add(TSession::getValue('ContratoList_filter_qtdeparcdesc')); // add the session filter
            }


            if (TSession::getValue('ContratoList_filter_avalista_id')) {
                $criteria->add(TSession::getValue('ContratoList_filter_avalista_id')); // add the session filter
            }


            if (TSession::getValue('ContratoList_filter_avalista2_id')) {
                $criteria->add(TSession::getValue('ContratoList_filter_avalista2_id')); // add the session filter
            }


            if (TSession::getValue('ContratoList_filter_vlseguro')) {
                $criteria->add(TSession::getValue('ContratoList_filter_vlseguro')); // add the session filter
            }


            if (TSession::getValue('ContratoList_filter_qtdeparc')) {
                $criteria->add(TSession::getValue('ContratoList_filter_qtdeparc')); // add the session filter
            }


            if (TSession::getValue('ContratoList_filter_diavencto')) {
                $criteria->add(TSession::getValue('ContratoList_filter_diavencto')); // add the session filter
            }


            if (TSession::getValue('ContratoList_filter_dtcadastro')) {
                $criteria->add(TSession::getValue('ContratoList_filter_dtcadastro')); // add the session filter
            }


            if (TSession::getValue('ContratoList_filter_liquidado')) {
                $criteria->add(TSession::getValue('ContratoList_filter_liquidado')); // add the session filter
            }


            if (TSession::getValue('ContratoList_filter_system_user_id')) {
                $criteria->add(TSession::getValue('ContratoList_filter_system_user_id')); // add the session filter
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
            $object = new Contrato($key, FALSE); // instantiates the Active Record
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
    
    public function onPrint ( $param )
    {   
        TTransaction::open(TSession::getValue('banco'));
        
        $id = $param['id'];
        $contrato = new Contrato($id);  
        $bem = new Bem($contrato->bem_id);        
        $repository = new TRepository('ModeloContrato');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('rescom', 'like', "%{$bem->rescom}%"));
        $criteria->add(new TFilter('tipogarantia_id', '=', $contrato->tipogarantia_id));
        if ($contrato->qtdeparc <= 12)
        {
            $qtdeparc = 12;
        }
        else
        {
            $qtdeparc = 30;
        }
        $criteria->add(new TFilter('qtdeparc', '=', $qtdeparc));
        $objects = $repository->load( $criteria );
        if ($objects)
        {
            foreach ($objects as $object)
            {
                $modelo = $object->id;
                $tipo = $object->tipocontrato_id;
            }
        }
        
        TScript::create("__adianti_open_file('contrato.php?id={$id}&tipo={$tipo}&&modelo={$modelo}')");
    }
}