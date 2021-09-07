<?php
/**
 * BemList Listing
 * @author  <your name here>
 */
class BemList extends TPage
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
        $this->form = new TQuickForm('form_search_Bem');
        $this->form->class = 'tform'; // change CSS class        
        $this->form->style = 'display: table;width:100%'; // change style
        //$this->form->setFormTitle('Bem');
        $this->form->setFieldsByRow(2);

        // create the form fields
        $id = new TEntry('id');
        $descricao = new TEntry('descricao');
        $endereco = new TEntry('endereco');
        $complemento = new TEntry('complemento');
        $criteria = new TCriteria;
        $criteria->setProperty('order', 'nome');
        $municipio_id = new TDBSeekButton('municipio_id',TSession::getValue('banco'),'form_search_Bem','Municipio','nome','municipio_id','municipio',$criteria);
        $municipio = new TEntry('municipio');
        $bairro = new TEntry('bairro');
        $cep = new TEntry('cep');
        $uf_id = new TDBCombo('uf_id',TSession::getValue('banco'),'UF','id','nome');
        $criteria = new TCriteria;
        $criteria->setProperty('order', 'descricao'); 
        $localizacao_id = new TDBSeekButton('localizacao_id',TSession::getValue('banco'),'form_search_Bem','Localizacao','descricao','localizacao_id','localizacao',$criteria);
        $localizacao = new TEntry('localizacao');
        $tipobem_id = new TDBCombo('tipobem_id',TSession::getValue('banco'),'Tipobem','id','descricao');
        $criteria = new TCriteria;
        $criteria->setProperty('order', 'nome');
        $proprietario_id = new TDBSeekButton('proprietario_id',TSession::getValue('banco'),'form_search_Bem','Cliente','nome','proprietario_id','proprietario',$criteria);
        $proprietario = new TEntry('proprietario');
        $rescom = new TRadioGroup('rescom');
        $pagtogar = new TRadioGroup('pagtogar');
        $diapagto = new TEntry('diapagto');
        $vlaluguel = new TEntry('vlaluguel');
        $vldesc = new TEntry('vldesc');
        $qtdemes = new TEntry('qtdemes');
        $percomissao = new TEntry('percomissao');
        $criteria = new TCriteria;
        $criteria->setProperty('order', 'nome');    
        $cliente_id = new TDBSeekButton('cliente_id',TSession::getValue('banco'),'form_search_Bem','Cliente','nome','cliente_id','cliente',$criteria);
        $cliente = new TEntry('cliente');
        $contrato_id = new TEntry('contrato_id');
        $reservar = new TRadioGroup('reservar');
        $obs = new TText('obs');
        $urbrural = new TRadioGroup('urbrural');
        $criteria = new TCriteria;
        $criteria->setProperty('order', 'nome');
        $cliente2_id = new TDBSeekButton('cliente2_id',TSession::getValue('banco'),'form_search_Bem','Cliente','nome','cliente2_id','cliente2',$criteria);
        $cliente2 = new TEntry('cliente2');
        
        // Campos Não Editáveis
        $localizacao->setEditable(FALSE);
        $municipio->setEditable(FALSE);
        $proprietario->setEditable(FALSE); 
        $cliente->setEditable(FALSE);
        $cliente2->setEditable(FALSE);      
        
        // Máscaras
        $cep->setMask('99999-999');
        
        // Formatação para Valores Monetário
        $vlaluguel->setNumericMask(2, ',', '.');
        $vldesc->setNumericMask(2, ',', '.');
        $percomissao->setNumericMask(2, ',' ,'.');
        
        // Tamanho dos Campos no formulário
        $localizacao_id->setSize(50);
        $localizacao->setSize(277);        
        $proprietario_id->setSize(50);
        $proprietario->setSize(277);
        $municipio_id->setSize(50);
        $municipio->setSize(277);
        $cliente_id->setSize(50);
        $cliente->setSize(277);
        $cliente2_id->setSize(50);
        $cliente2->setSize(277);
        
        // Número de Caracteres permitidos dentro dos campos
        
        // Adiciona Items aos campos RadioGroup e ComboBox                
        $rescom->addItems(array('R' => ' Residencial', 'C' => ' Comercial'));
        $pagtogar->addItems(array('N' => ' Não', 'S' => ' Sim'));
        $reservar->addItems(array('N' => ' Não', 'S' => ' Sim'));
        $urbrural->addItems(array('U' => ' Urbano', 'R' => ' Rural'));
        
        // Define Layout dos campos RadioGroup e ComboBox
        $rescom->setLayout('horizontal');        
        $pagtogar->setLayout('horizontal');     
        $reservar->setLayout('horizontal');        
        $urbrural->setLayout('horizontal');

        // add the fields
        $this->form->addQuickField(_t('ID'), $id,  50 );
        $this->form->addQuickField('Descrição', $descricao,  350 );
        /*$this->form->addQuickField('CEP', $cep,  100 );
        $this->form->addQuickField('Endereço', $endereco,  300 );
        $this->form->addQuickField('Complemento', $complemento,  200 );
        $this->form->addQuickField('Bairro', $bairro,  300 );
        $this->form->addQuickFields('Municipio', array($municipio_id, $municipio)  );
        $this->form->addQuickField('UF', $uf_id,  200 );*/
        $this->form->addQuickFields('Localização', array($localizacao_id, $localizacao) );
        /*$this->form->addQuickField('Tipo de Bem', $tipobem_id,  150 );
        $this->form->addQuickField('Urbano/Rural', $urbrural,  200 );*/
        $this->form->addQuickFields('Locador', array($proprietario_id, $proprietario) );
        /*$this->form->addQuickField('Residencial/Comercial', $rescom,  200 );
        $this->form->addQuickField('Pagamento Garantido', $pagtogar,  200 );
        $this->form->addQuickField('Dia Pagamento', $diapagto,  50 );
        $this->form->addQuickField('Percentual Comissão', $percomissao,  50 );*/
        $this->form->addQuickFields('Locatário 1', array($cliente_id, $cliente) );
        /*$this->form->addQuickField('Valor Aluguel', $vlaluguel,  200 );
        $this->form->addQuickField('Valor Desconto', $vldesc,  200 );
        $this->form->addQuickField('Qtde. Meses', $qtdemes,  50 );*/
        $this->form->addQuickField('Contrato', $contrato_id,  100 );
        /*$this->form->addQuickField('Reservar', $reservar,  200 );
        $this->form->addQuickField('Observação', $obs,  200 );
        $this->form->addQuickFields('Locatário 2', array($cliente2_id, $cliente2) );*/
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('Bem_filter_data') );
        
        // add the search form actions
        $this->form->addQuickAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        $this->form->addQuickAction('Limpar Filtro',  new TAction(array($this, 'onClear')), 'bs:ban-circle red');
        $this->form->addQuickAction(_t('New'),  new TAction(array('BemForm', 'onEdit')), 'bs:plus-sign green');
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', _t('ID'), 'right');
        $column_descricao = new TDataGridColumn('descricao', 'Descrição', 'left');
        $column_endereco = new TDataGridColumn('endereco', 'Endereço', 'left');
        $column_complemento = new TDataGridColumn('complemento', 'Complemento', 'left');
        $column_municipio_id = new TDataGridColumn('municipio_id', 'Município', 'left');
        $column_municipio = new TDataGridColumn('municipio', 'Município', 'left');
        $column_bairro = new TDataGridColumn('bairro', 'Bairro', 'left');
        $column_cep = new TDataGridColumn('cep', 'CEP', 'right');
        $column_uf_id = new TDataGridColumn('uf_id', 'UF', 'left');
        $column_localizacao_id = new TDataGridColumn('localizacao', 'Localização', 'left');
        $column_tipobem_id = new TDataGridColumn('tipobem_id', 'Tipo de Bem', 'left');
        $column_proprietario_id = new TDataGridColumn('proprietario', 'Locador', 'left');
        $column_rescom = new TDataGridColumn('rescom', 'Res./Com.', 'left');
        $column_pagtogar = new TDataGridColumn('pagtogar', 'Pagto. Gar.', 'left');
        $column_diapagto = new TDataGridColumn('diapagto', 'Dia Pagto.', 'right');
        $column_vlaluguel = new TDataGridColumn('vlaluguel', 'Valor Aluguel', 'right');
        $column_vldesc = new TDataGridColumn('vldesc', 'Valor Desconto', 'right');
        $column_qtdemes = new TDataGridColumn('qtdemes', 'Qtde. Meses', 'left');
        $column_percomissao = new TDataGridColumn('percomissao', 'Perc. Comissão', 'right');
        $column_cliente_id = new TDataGridColumn('cliented', 'Locatário 1', 'left');
        $column_contrato_id = new TDataGridColumn('contrato_id', 'Contrato', 'right');
        $column_reservar = new TDataGridColumn('reservar', 'Reservar', 'left');
        $column_obs = new TDataGridColumn('obs', 'Observação', 'left');
        $column_urbrural = new TDataGridColumn('urbrural', 'Urb./Rural', 'left');
        $column_cliente2_id = new TDataGridColumn('cliente2_id', 'Locatário 2', 'left');

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_descricao);
        /*$this->datagrid->addColumn($column_endereco);
        $this->datagrid->addColumn($column_complemento);
        $this->datagrid->addColumn($column_municipio_id);
        $this->datagrid->addColumn($column_municipio);
        $this->datagrid->addColumn($column_bairro);
        $this->datagrid->addColumn($column_cep);
        $this->datagrid->addColumn($column_uf_id);*/
        $this->datagrid->addColumn($column_localizacao_id);
        //$this->datagrid->addColumn($column_tipobem_id);
        $this->datagrid->addColumn($column_proprietario_id);
        /*$this->datagrid->addColumn($column_rescom);
        $this->datagrid->addColumn($column_pagtogar);
        $this->datagrid->addColumn($column_diapagto);
        $this->datagrid->addColumn($column_vlaluguel);
        $this->datagrid->addColumn($column_vldesc);
        $this->datagrid->addColumn($column_qtdemes);
        $this->datagrid->addColumn($column_percomissao);*/
        $this->datagrid->addColumn($column_cliente_id);
        $this->datagrid->addColumn($column_contrato_id);
        /*$this->datagrid->addColumn($column_reservar);
        $this->datagrid->addColumn($column_obs);
        $this->datagrid->addColumn($column_urbrural);
        $this->datagrid->addColumn($column_cliente2_id);*/

        // creates the datagrid column actions
        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);
        
        $order_descricao = new TAction(array($this, 'onReload'));
        $order_descricao->setParameter('order', 'descricao');
        $column_descricao->setAction($order_descricao);
        
        $order_endereco = new TAction(array($this, 'onReload'));
        $order_endereco->setParameter('order', 'endereco');
        $column_endereco->setAction($order_endereco);
        
        $order_municipio = new TAction(array($this, 'onReload'));
        $order_municipio->setParameter('order', 'municipio');
        $column_municipio->setAction($order_municipio);
        
        $order_bairro = new TAction(array($this, 'onReload'));
        $order_bairro->setParameter('order', 'bairro');
        $column_bairro->setAction($order_bairro);
        
        $order_uf_id = new TAction(array($this, 'onReload'));
        $order_uf_id->setParameter('order', 'uf_id');
        $column_uf_id->setAction($order_uf_id);
        
        $order_localizacao_id = new TAction(array($this, 'onReload'));
        $order_localizacao_id->setParameter('order', 'localizacao_id');
        $column_localizacao_id->setAction($order_localizacao_id);
        
        $order_tipobem_id = new TAction(array($this, 'onReload'));
        $order_tipobem_id->setParameter('order', 'tipobem_id');
        $column_tipobem_id->setAction($order_tipobem_id);
        
        $order_proprietario_id = new TAction(array($this, 'onReload'));
        $order_proprietario_id->setParameter('order', 'proprietario_id');
        $column_proprietario_id->setAction($order_proprietario_id);
        
        $order_rescom = new TAction(array($this, 'onReload'));
        $order_rescom->setParameter('order', 'rescom');
        $column_rescom->setAction($order_rescom);
        
        $order_pagtogar = new TAction(array($this, 'onReload'));
        $order_pagtogar->setParameter('order', 'pagtogar');
        $column_pagtogar->setAction($order_pagtogar);
        
        $order_diapagto = new TAction(array($this, 'onReload'));
        $order_diapagto->setParameter('order', 'diapagto');
        $column_diapagto->setAction($order_diapagto);
        
        $order_vlaluguel = new TAction(array($this, 'onReload'));
        $order_vlaluguel->setParameter('order', 'vlaluguel');
        $column_vlaluguel->setAction($order_vlaluguel);
        
        $order_vldesc = new TAction(array($this, 'onReload'));
        $order_vldesc->setParameter('order', 'vldesc');
        $column_vldesc->setAction($order_vldesc);
        
        $order_qtdemes = new TAction(array($this, 'onReload'));
        $order_qtdemes->setParameter('order', 'qtdemes');
        $column_qtdemes->setAction($order_qtdemes);
        
        $order_percomissao = new TAction(array($this, 'onReload'));
        $order_percomissao->setParameter('order', 'percomissao');
        $column_percomissao->setAction($order_percomissao);
        
        $order_cliente_id = new TAction(array($this, 'onReload'));
        $order_cliente_id->setParameter('order', 'cliente_id');
        $column_cliente_id->setAction($order_cliente_id);
        
        $order_contrato_id = new TAction(array($this, 'onReload'));
        $order_contrato_id->setParameter('order', 'contrato_id');
        $column_contrato_id->setAction($order_contrato_id);
        
        $order_reservar = new TAction(array($this, 'onReload'));
        $order_reservar->setParameter('order', 'reservar');
        $column_reservar->setAction($order_reservar);
        
        $order_urbrural = new TAction(array($this, 'onReload'));
        $order_urbrural->setParameter('order', 'urbrural');
        $column_urbrural->setAction($order_urbrural);
        
        $order_cliente2_id = new TAction(array($this, 'onReload'));
        $order_cliente2_id->setParameter('order', 'cliente2_id');
        $column_cliente2_id->setAction($order_cliente2_id);        

        // define the transformer method over image
        $column_vlaluguel->setTransformer( function($value, $object, $row) {
            return 'R$ ' . number_format($value, 2, ',', '.');
        });

        // define the transformer method over image
        $column_vldesc->setTransformer( function($value, $object, $row) {
            return 'R$ ' . number_format($value, 2, ',', '.');
        });
        
        // create EDIT action
        $action_edit = new TDataGridAction(array('BemForm', 'onEdit'));
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
        $action_print->setButtonClass('btn btn-default');
        $action_print->setLabel('Imprimir Contrato');
        $action_print->setImage('fa:print black fa-lg');
        $action_print->setField('id');
        $this->datagrid->addAction($action_print);
        
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
        TSession::setValue('BemList_filter_id',   NULL);
        TSession::setValue('BemList_filter_descricao',   NULL);
        TSession::setValue('BemList_filter_endereco',   NULL);
        TSession::setValue('BemList_filter_complemento',   NULL);
        TSession::setValue('BemList_filter_municipio_id',   NULL);
        TSession::setValue('BemList_filter_municipio',   NULL);
        TSession::setValue('BemList_filter_bairro',   NULL);
        TSession::setValue('BemList_filter_cep',   NULL);
        TSession::setValue('BemList_filter_uf_id',   NULL);
        TSession::setValue('BemList_filter_localizacao_id',   NULL);
        TSession::setValue('BemList_filter_tipobem_id',   NULL);
        TSession::setValue('BemList_filter_proprietario_id',   NULL);
        TSession::setValue('BemList_filter_rescom',   NULL);
        TSession::setValue('BemList_filter_pagtogar',   NULL);
        TSession::setValue('BemList_filter_diapagto',   NULL);
        TSession::setValue('BemList_filter_vlaluguel',   NULL);
        TSession::setValue('BemList_filter_vldesc',   NULL);
        TSession::setValue('BemList_filter_qtdemes',   NULL);
        TSession::setValue('BemList_filter_percomissao',   NULL);
        TSession::setValue('BemList_filter_cliente_id',   NULL);
        TSession::setValue('BemList_filter_contrato_id',   NULL);
        TSession::setValue('BemList_filter_reservar',   NULL);
        TSession::setValue('BemList_filter_obs',   NULL);
        TSession::setValue('BemList_filter_urbrural',   NULL);
        TSession::setValue('BemList_filter_cliente2_id',   NULL);

        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', 'like', "%{$data->id}%"); // create the filter
            TSession::setValue('BemList_filter_id',   $filter); // stores the filter in the session
        }


        if (isset($data->descricao) AND ($data->descricao)) {
            $filter = new TFilter('descricao', 'like', "%{$data->descricao}%"); // create the filter
            TSession::setValue('BemList_filter_descricao',   $filter); // stores the filter in the session
        }


        if (isset($data->endereco) AND ($data->endereco)) {
            $filter = new TFilter('endereco', 'like', "%{$data->endereco}%"); // create the filter
            TSession::setValue('BemList_filter_endereco',   $filter); // stores the filter in the session
        }


        if (isset($data->complemento) AND ($data->complemento)) {
            $filter = new TFilter('complemento', 'like', "%{$data->complemento}%"); // create the filter
            TSession::setValue('BemList_filter_complemento',   $filter); // stores the filter in the session
        }


        if (isset($data->municipio_id) AND ($data->municipio_id)) {
            $filter = new TFilter('municipio_id', 'like', "%{$data->municipio_id}%"); // create the filter
            TSession::setValue('BemList_filter_municipio_id',   $filter); // stores the filter in the session
        }


        if (isset($data->municipio) AND ($data->municipio)) {
            $filter = new TFilter('municipio', 'like', "%{$data->municipio}%"); // create the filter
            TSession::setValue('BemList_filter_municipio',   $filter); // stores the filter in the session
        }


        if (isset($data->bairro) AND ($data->bairro)) {
            $filter = new TFilter('bairro', 'like', "%{$data->bairro}%"); // create the filter
            TSession::setValue('BemList_filter_bairro',   $filter); // stores the filter in the session
        }


        if (isset($data->cep) AND ($data->cep)) {
            $filter = new TFilter('cep', 'like', "%{$data->cep}%"); // create the filter
            TSession::setValue('BemList_filter_cep',   $filter); // stores the filter in the session
        }


        if (isset($data->uf_id) AND ($data->uf_id)) {
            $filter = new TFilter('uf_id', 'like', "%{$data->uf_id}%"); // create the filter
            TSession::setValue('BemList_filter_uf_id',   $filter); // stores the filter in the session
        }


        if (isset($data->localizacao_id) AND ($data->localizacao_id)) {
            $filter = new TFilter('localizacao_id', 'like', "%{$data->localizacao_id}%"); // create the filter
            TSession::setValue('BemList_filter_localizacao_id',   $filter); // stores the filter in the session
        }


        if (isset($data->tipobem_id) AND ($data->tipobem_id)) {
            $filter = new TFilter('tipobem_id', 'like', "%{$data->tipobem_id}%"); // create the filter
            TSession::setValue('BemList_filter_tipobem_id',   $filter); // stores the filter in the session
        }


        if (isset($data->proprietario_id) AND ($data->proprietario_id)) {
            $filter = new TFilter('proprietario_id', 'like', "%{$data->proprietario_id}%"); // create the filter
            TSession::setValue('BemList_filter_proprietario_id',   $filter); // stores the filter in the session
        }


        if (isset($data->rescom) AND ($data->rescom)) {
            $filter = new TFilter('rescom', 'like', "%{$data->rescom}%"); // create the filter
            TSession::setValue('BemList_filter_rescom',   $filter); // stores the filter in the session
        }


        if (isset($data->pagtogar) AND ($data->pagtogar)) {
            $filter = new TFilter('pagtogar', 'like', "%{$data->pagtogar}%"); // create the filter
            TSession::setValue('BemList_filter_pagtogar',   $filter); // stores the filter in the session
        }


        if (isset($data->diapagto) AND ($data->diapagto)) {
            $filter = new TFilter('diapagto', 'like', "%{$data->diapagto}%"); // create the filter
            TSession::setValue('BemList_filter_diapagto',   $filter); // stores the filter in the session
        }


        if (isset($data->vlaluguel) AND ($data->vlaluguel)) {
            $filter = new TFilter('vlaluguel', 'like', "%{$data->vlaluguel}%"); // create the filter
            TSession::setValue('BemList_filter_vlaluguel',   $filter); // stores the filter in the session
        }


        if (isset($data->vldesc) AND ($data->vldesc)) {
            $filter = new TFilter('vldesc', 'like', "%{$data->vldesc}%"); // create the filter
            TSession::setValue('BemList_filter_vldesc',   $filter); // stores the filter in the session
        }


        if (isset($data->qtdemes) AND ($data->qtdemes)) {
            $filter = new TFilter('qtdemes', 'like', "%{$data->qtdemes}%"); // create the filter
            TSession::setValue('BemList_filter_qtdemes',   $filter); // stores the filter in the session
        }


        if (isset($data->percomissao) AND ($data->percomissao)) {
            $filter = new TFilter('percomissao', 'like', "%{$data->percomissao}%"); // create the filter
            TSession::setValue('BemList_filter_percomissao',   $filter); // stores the filter in the session
        }


        if (isset($data->cliente_id) AND ($data->cliente_id)) {
            $filter = new TFilter('cliente_id', 'like', "%{$data->cliente_id}%"); // create the filter
            TSession::setValue('BemList_filter_cliente_id',   $filter); // stores the filter in the session
        }


        if (isset($data->contrato_id) AND ($data->contrato_id)) {
            $filter = new TFilter('contrato_id', 'like', "%{$data->contrato_id}%"); // create the filter
            TSession::setValue('BemList_filter_contrato_id',   $filter); // stores the filter in the session
        }


        if (isset($data->reservar) AND ($data->reservar)) {
            $filter = new TFilter('reservar', 'like', "%{$data->reservar}%"); // create the filter
            TSession::setValue('BemList_filter_reservar',   $filter); // stores the filter in the session
        }


        if (isset($data->obs) AND ($data->obs)) {
            $filter = new TFilter('obs', 'like', "%{$data->obs}%"); // create the filter
            TSession::setValue('BemList_filter_obs',   $filter); // stores the filter in the session
        }


        if (isset($data->urbrural) AND ($data->urbrural)) {
            $filter = new TFilter('urbrural', 'like', "%{$data->urbrural}%"); // create the filter
            TSession::setValue('BemList_filter_urbrural',   $filter); // stores the filter in the session
        }


        if (isset($data->cliente2_id) AND ($data->cliente2_id)) {
            $filter = new TFilter('cliente2_id', 'like', "%{$data->cliente2_id}%"); // create the filter
            TSession::setValue('BemList_filter_cliente2_id',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('Bem_filter_data', $data);
        
        $param=array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    
    public function onClear( $param )
    {
        $this->form->clear();
        
        TSession::setValue('BemList_filter_id',   NULL);
        TSession::setValue('BemList_filter_descricao',   NULL);
        TSession::setValue('BemList_filter_endereco',   NULL);
        TSession::setValue('BemList_filter_complemento',   NULL);
        TSession::setValue('BemList_filter_municipio_id',   NULL);
        TSession::setValue('BemList_filter_municipio',   NULL);
        TSession::setValue('BemList_filter_bairro',   NULL);
        TSession::setValue('BemList_filter_cep',   NULL);
        TSession::setValue('BemList_filter_uf_id',   NULL);
        TSession::setValue('BemList_filter_localizacao_id',   NULL);
        TSession::setValue('BemList_filter_tipobem_id',   NULL);
        TSession::setValue('BemList_filter_proprietario_id',   NULL);
        TSession::setValue('BemList_filter_rescom',   NULL);
        TSession::setValue('BemList_filter_pagtogar',   NULL);
        TSession::setValue('BemList_filter_diapagto',   NULL);
        TSession::setValue('BemList_filter_vlaluguel',   NULL);
        TSession::setValue('BemList_filter_vldesc',   NULL);
        TSession::setValue('BemList_filter_qtdemes',   NULL);
        TSession::setValue('BemList_filter_percomissao',   NULL);
        TSession::setValue('BemList_filter_cliente_id',   NULL);
        TSession::setValue('BemList_filter_contrato_id',   NULL);
        TSession::setValue('BemList_filter_reservar',   NULL);
        TSession::setValue('BemList_filter_obs',   NULL);
        TSession::setValue('BemList_filter_urbrural',   NULL);
        TSession::setValue('BemList_filter_cliente2_id',   NULL);
        
        TSession::setValue('Bem_filter_data', NULL);
        
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
            
            // creates a repository for Bem
            $repository = new TRepository('Bem');
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

            if (TSession::getValue('BemList_filter_id')) {
                $criteria->add(TSession::getValue('BemList_filter_id')); // add the session filter
            }


            if (TSession::getValue('BemList_filter_descricao')) {
                $criteria->add(TSession::getValue('BemList_filter_descricao')); // add the session filter
            }


            if (TSession::getValue('BemList_filter_endereco')) {
                $criteria->add(TSession::getValue('BemList_filter_endereco')); // add the session filter
            }


            if (TSession::getValue('BemList_filter_complemento')) {
                $criteria->add(TSession::getValue('BemList_filter_complemento')); // add the session filter
            }


            if (TSession::getValue('BemList_filter_municipio_id')) {
                $criteria->add(TSession::getValue('BemList_filter_municipio_id')); // add the session filter
            }


            if (TSession::getValue('BemList_filter_municipio')) {
                $criteria->add(TSession::getValue('BemList_filter_municipio')); // add the session filter
            }


            if (TSession::getValue('BemList_filter_bairro')) {
                $criteria->add(TSession::getValue('BemList_filter_bairro')); // add the session filter
            }


            if (TSession::getValue('BemList_filter_cep')) {
                $criteria->add(TSession::getValue('BemList_filter_cep')); // add the session filter
            }


            if (TSession::getValue('BemList_filter_uf_id')) {
                $criteria->add(TSession::getValue('BemList_filter_uf_id')); // add the session filter
            }


            if (TSession::getValue('BemList_filter_localizacao_id')) {
                $criteria->add(TSession::getValue('BemList_filter_localizacao_id')); // add the session filter
            }


            if (TSession::getValue('BemList_filter_tipobem_id')) {
                $criteria->add(TSession::getValue('BemList_filter_tipobem_id')); // add the session filter
            }


            if (TSession::getValue('BemList_filter_proprietario_id')) {
                $criteria->add(TSession::getValue('BemList_filter_proprietario_id')); // add the session filter
            }


            if (TSession::getValue('BemList_filter_rescom')) {
                $criteria->add(TSession::getValue('BemList_filter_rescom')); // add the session filter
            }


            if (TSession::getValue('BemList_filter_pagtogar')) {
                $criteria->add(TSession::getValue('BemList_filter_pagtogar')); // add the session filter
            }


            if (TSession::getValue('BemList_filter_diapagto')) {
                $criteria->add(TSession::getValue('BemList_filter_diapagto')); // add the session filter
            }


            if (TSession::getValue('BemList_filter_vlaluguel')) {
                $criteria->add(TSession::getValue('BemList_filter_vlaluguel')); // add the session filter
            }


            if (TSession::getValue('BemList_filter_vldesc')) {
                $criteria->add(TSession::getValue('BemList_filter_vldesc')); // add the session filter
            }


            if (TSession::getValue('BemList_filter_qtdemes')) {
                $criteria->add(TSession::getValue('BemList_filter_qtdemes')); // add the session filter
            }


            if (TSession::getValue('BemList_filter_percomissao')) {
                $criteria->add(TSession::getValue('BemList_filter_percomissao')); // add the session filter
            }


            if (TSession::getValue('BemList_filter_cliente_id')) {
                $criteria->add(TSession::getValue('BemList_filter_cliente_id')); // add the session filter
            }


            if (TSession::getValue('BemList_filter_contrato_id')) {
                $criteria->add(TSession::getValue('BemList_filter_contrato_id')); // add the session filter
            }


            if (TSession::getValue('BemList_filter_reservar')) {
                $criteria->add(TSession::getValue('BemList_filter_reservar')); // add the session filter
            }


            if (TSession::getValue('BemList_filter_obs')) {
                $criteria->add(TSession::getValue('BemList_filter_obs')); // add the session filter
            }


            if (TSession::getValue('BemList_filter_urbrural')) {
                $criteria->add(TSession::getValue('BemList_filter_urbrural')); // add the session filter
            }


            if (TSession::getValue('BemList_filter_cliente2_id')) {
                $criteria->add(TSession::getValue('BemList_filter_cliente2_id')); // add the session filter
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
            $object = new Bem($key, FALSE); // instantiates the Active Record
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
        $bem = new Bem($id);        
        $repository = new TRepository('ModeloContrato');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('percomissao', '=', "{$bem->percomissao}"));
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
