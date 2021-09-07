<?php
/**
 * ClienteList Listing
 * @author  <your name here>
 */
class ClienteList extends TPage
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
        $this->form = new TQuickForm('form_search_Cliente');
        $this->form->class = 'tform'; // change CSS class        
        //$this->form->style = 'display: table;width:100%'; // change style
        //$this->form->setFormTitle('Clientes');
        $this->form->setFieldsByRow(2); 
        
        $script = new TElement('script');
        $script->type = 'text/javascript';
        $script->add('
            $(document).ready(function() {
                $(\'input[name="tipo"]\').change(function(event) {
                    tipoPessoa = $(this).val();
                    
                    if(tipoPessoa == \'F\') {
                        $(\'label:contains(CPF/CNPJ)\').text(\'CPF\');
                        $(\'label:contains(CNPJ)\').text(\'CPF\');
                        $(\'input[name="cpfcnpj"]\').val(\'\');
                        $(\'input[name="cpfcnpj"]\').attr({onkeypress:\'return tentry_mask(this,event,"999.999.999-99")\'});
                    }
                    
                    if(tipoPessoa == \'J\') {
                        $(\'label:contains(CPF/CNPJ)\').text(\'CNPJ\');
                        $(\'label:contains(CPF)\').text(\'CNPJ\');
                        $(\'input[name="cpfcnpj"]\').val(\'\');
                        $(\'input[name="cpfcnpj"]\').attr({onkeypress:\'return tentry_mask(this,event,"99.999.999/9999-99")\'});
                    }
                });

            });
        ');
        parent::add($script);          

        // create the form fields
        $id = new TEntry('id');
        $nome = new TEntry('nome');
        $cpfcnpj = new TEntry('cpfcnpj');
        $tipo = new TRadioGroup('tipo');
        $dtnasc = new TDate('dtnasc');
        $ierg = new TEntry('ierg');
        $estadocivil_id = new TDBCombo('estadocivil_id',TSession::getValue('banco'),'Estadocivil','id','descricao');
        $municipionasc = new TEntry('municipionasc');
        $ufnasc = new TDBCombo('ufnasc',TSession::getValue('banco'),'UF','id','nome');
        $nomepais = new TEntry('nomepais');
        $endereco = new TEntry('endereco');
        $bairro = new TEntry('bairro');
        $cep = new TEntry('cep');
        $municipio = new TEntry('municipio');
        $uf_id = new TDBCombo('uf_id',TSession::getValue('banco'),'UF','id','nome');
        $fone = new TEntry('fone');
        $cel = new TEntry('cel');
        $fax = new TEntry('fax');
        $tipores_id = new TDBCombo('tipores_id',TSession::getValue('banco'),'Tiporesidencia','id','descricao');
        $tempores = new TEntry('tempores');
        $proximo = new TEntry('proximo');
        $dtcadastro = new TDate('dtcadastro');
        
        // Máscaras
        $dtnasc->setMask('dd/mm/yyyy');
        $cep->setMask('99999-999');
        $fone->setMask('(99)9999-9999');
        $cel->setMask('(99)99999-9999');
        $fax->setMask('(99)9999-9999');
        
        // Número de Caracteres permitidos dentro dos campos
        
        // Adiciona Items aos campos RadioGroup e ComboBox
        $tipo->addItems(array('F' => ' Pessoa Física', 'J' => ' Pessoa Jurídica'));
        
        // Define Layout dos campos RadioGroup e ComboBox
        $tipo->setLayout('horizontal');

        // add the fields
        $this->form->addQuickField(_t('ID'), $id,  50 );        
        $this->form->addQuickField('Tipo', $tipo,  200 );
        $this->form->addQuickField('CPF/CNPJ', $cpfcnpj,  200 );        
        $this->form->addQuickField('Nome', $nome,  '95%' );
        /*$this->form->addQuickField('Data Nascimento', $dtnasc,  100 );
        $this->form->addQuickField('Município Natal', $municipionasc,  400 );
        $this->form->addQuickField('UF Natal', $ufnasc,  200 );
        $this->form->addQuickField('IE/RG', $ierg,  100 );
        $this->form->addQuickField('Estado Civil', $estadocivil_id,  150 );        
        $this->form->addQuickField('Nome Pais', $nomepais,  400 );
        $this->form->addQuickField('CEP', $cep,  100 );*/
        $this->form->addQuickField('Endereço', $endereco,  '95%' );
        /*$this->form->addQuickField('Bairro', $bairro,  400 );        
        $this->form->addQuickField('Município', $municipio,  400 );
        $this->form->addQuickField('UF', $uf_id,  200 );
        $this->form->addQuickField('Fone', $fone,  200 );
        $this->form->addQuickField('Celular', $cel,  200 );
        $this->form->addQuickField('Fax', $fax,  200 );
        $this->form->addQuickField('Tipo Residência', $tipores_id,  150 );
        $this->form->addQuickField('Tempo Residência', $tempores,  200 );
        $this->form->addQuickField('Próximo', $proximo,  200 );
        $this->form->addQuickField('Data Cadastro', $dtcadastro,  100 );*/
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('Cliente_filter_data') );
        
        // add the search form actions
        $this->form->addQuickAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        $this->form->addQuickAction('Limpar Filtro',  new TAction(array($this, 'onClear')), 'bs:ban-circle red');
        $this->form->addQuickAction(_t('New'),  new TAction(array('ClienteForm', 'onEdit')), 'bs:plus-sign green');
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', _t('ID'), 'right');
        $column_nome = new TDataGridColumn('nome', 'Nome', 'left');
        $column_cpfcnpj = new TDataGridColumn('cpfcnpj', 'CPF/CNPJ', 'left');
        $column_tipo = new TDataGridColumn('tipo', 'Tipo', 'left');
        $column_dtnasc = new TDataGridColumn('dtnasc', 'Data Nasc.', 'right');
        $column_ierg = new TDataGridColumn('ierg', 'IE/RG', 'left');
        $column_estadocivil_id = new TDataGridColumn('estadocivil_id', 'Estado Civil', 'left');
        $column_municipionasc = new TDataGridColumn('municipionasc', 'Municipio Natal', 'left');
        $column_ufnasc = new TDataGridColumn('ufnasc', 'UF Natal', 'left');
        $column_nomepais = new TDataGridColumn('nomepais', 'Nome Pais', 'left');
        $column_endereco = new TDataGridColumn('endereco', 'Endereço', 'left');
        $column_bairro = new TDataGridColumn('bairro', 'Bairro', 'left');
        $column_cep = new TDataGridColumn('cep', 'CEP', 'left');
        $column_municipio = new TDataGridColumn('municipio', 'Município', 'left');
        $column_uf_id = new TDataGridColumn('uf_id', 'UF', 'left');
        $column_fone = new TDataGridColumn('fone', 'Fone', 'left');
        $column_cel = new TDataGridColumn('cel', 'Celular', 'left');
        $column_fax = new TDataGridColumn('fax', 'Fax', 'left');
        $column_tipores_id = new TDataGridColumn('tipores_id', 'Tipo Residência', 'left');
        $column_tempores = new TDataGridColumn('tempores', 'Tempo Residência', 'left');
        $column_proximo = new TDataGridColumn('proximo', 'Próximo', 'left');
        $column_dtcadastro = new TDataGridColumn('dtcadastro', 'Data Cadastro', 'right');

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_tipo);
        $this->datagrid->addColumn($column_cpfcnpj);
        $this->datagrid->addColumn($column_nome);       
        /*$this->datagrid->addColumn($column_dtnasc);
        $this->datagrid->addColumn($column_municipionasc);
        $this->datagrid->addColumn($column_ufnasc);
        $this->datagrid->addColumn($column_ierg);
        $this->datagrid->addColumn($column_estadocivil_id);        
        $this->datagrid->addColumn($column_nomepais);
        $this->datagrid->addColumn($column_cep);*/
        $this->datagrid->addColumn($column_endereco);
        /*$this->datagrid->addColumn($column_bairro);        
        $this->datagrid->addColumn($column_municipio);
        $this->datagrid->addColumn($column_uf_id);*/
        $this->datagrid->addColumn($column_fone);
        /*$this->datagrid->addColumn($column_cel);
        $this->datagrid->addColumn($column_fax);
        $this->datagrid->addColumn($column_tipores_id);
        $this->datagrid->addColumn($column_tempores);
        $this->datagrid->addColumn($column_proximo);
        $this->datagrid->addColumn($column_dtcadastro);*/
        
        // creates the datagrid column actions
        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);
        
        $order_nome = new TAction(array($this, 'onReload'));
        $order_nome->setParameter('order', 'nome');
        $column_nome->setAction($order_nome);
        
        /*$order_cpfcnpj = new TAction(array($this, 'onReload'));
        $order_cpfcnpj->setParameter('order', 'cpfcnpj');
        $column_cpfcnpj->setAction($order_cpfcnpj);
        
        $order_tipo = new TAction(array($this, 'onReload'));
        $order_tipo->setParameter('order', 'tipo');
        $column_tipo->setAction($order_tipo);
        
        $order_dtnasc = new TAction(array($this, 'onReload'));
        $order_dtnasc->setParameter('order', 'dtnasc');
        $column_dtnasc->setAction($order_dtnasc);
        
        $order_ierg = new TAction(array($this, 'onReload'));
        $order_ierg->setParameter('order', 'ierg');
        $column_ierg->setAction($order_ierg);
        
        $order_estadocivil_id = new TAction(array($this, 'onReload'));
        $order_estadocivil_id->setParameter('order', 'estadocivil_id');
        $column_estadocivil_id->setAction($order_estadocivil_id);
        
        $order_municipionasc = new TAction(array($this, 'onReload'));
        $order_municipionasc->setParameter('order', 'municipionasc');
        $column_municipionasc->setAction($order_municipionasc);
        
        $order_ufnasc = new TAction(array($this, 'onReload'));
        $order_ufnasc->setParameter('order', 'ufnasc');
        $column_ufnasc->setAction($order_ufnasc);
        
        $order_nomepais = new TAction(array($this, 'onReload'));
        $order_nomepais->setParameter('order', 'nomepais');
        $column_nomepais->setAction($order_nomepais);
        
        $order_endereco = new TAction(array($this, 'onReload'));
        $order_endereco->setParameter('order', 'endereco');
        $column_endereco->setAction($order_endereco);*/
        
        $order_bairro = new TAction(array($this, 'onReload'));
        $order_bairro->setParameter('order', 'bairro');
        $column_bairro->setAction($order_bairro);
        
        /*$order_cep = new TAction(array($this, 'onReload'));
        $order_cep->setParameter('order', 'cep');
        $column_cep->setAction($order_cep);*/
        
        $order_municipio = new TAction(array($this, 'onReload'));
        $order_municipio->setParameter('order', 'municipio');
        $column_municipio->setAction($order_municipio);
        
        $order_uf_id = new TAction(array($this, 'onReload'));
        $order_uf_id->setParameter('order', 'uf_id');
        $column_uf_id->setAction($order_uf_id);
        
        /*$order_fone = new TAction(array($this, 'onReload'));
        $order_fone->setParameter('order', 'fone');
        $column_fone->setAction($order_fone);
        
        $order_cel = new TAction(array($this, 'onReload'));
        $order_cel->setParameter('order', 'cel');
        $column_cel->setAction($order_cel);
        
        $order_fax = new TAction(array($this, 'onReload'));
        $order_fax->setParameter('order', 'fax');
        $column_fax->setAction($order_fax);
        
        $order_tipores_id = new TAction(array($this, 'onReload'));
        $order_tipores_id->setParameter('order', 'tipores_id');
        $column_tipores_id->setAction($order_tipores_id);
        
        $order_tempores = new TAction(array($this, 'onReload'));
        $order_tempores->setParameter('order', 'tempores');
        $column_tempores->setAction($order_tempores);
        
        $order_proximo = new TAction(array($this, 'onReload'));
        $order_proximo->setParameter('order', 'proximo');
        $column_proximo->setAction($order_proximo);
        
        $order_dtcadastro = new TAction(array($this, 'onReload'));
        $order_dtcadastro->setParameter('order', 'dtcadastro');
        $column_dtcadastro->setAction($order_dtcadastro);*/      

        // define the transformer method over image
        $column_dtnasc->setTransformer( function($value, $object, $row) {
            $date = new DateTime($value);
            if ($value !== '') {
                return $date->format('d/m/Y');
            }
        });
        
        // create EDIT action
        $action_edit = new TDataGridAction(array('ClienteForm', 'onEdit'));
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
        TSession::setValue('ClienteList_filter_id',   NULL);
        TSession::setValue('ClienteList_filter_nome',   NULL);
        TSession::setValue('ClienteList_filter_cpfcnpj',   NULL);
        TSession::setValue('ClienteList_filter_tipo',   NULL);
        TSession::setValue('ClienteList_filter_dtnasc',   NULL);
        TSession::setValue('ClienteList_filter_ierg',   NULL);
        TSession::setValue('ClienteList_filter_estadocivil_id',   NULL);
        TSession::setValue('ClienteList_filter_municipionasc',   NULL);
        TSession::setValue('ClienteList_filter_ufnasc',   NULL);
        TSession::setValue('ClienteList_filter_nomepais',   NULL);
        TSession::setValue('ClienteList_filter_endereco',   NULL);
        TSession::setValue('ClienteList_filter_bairro',   NULL);
        TSession::setValue('ClienteList_filter_cep',   NULL);
        TSession::setValue('ClienteList_filter_municipio',   NULL);
        TSession::setValue('ClienteList_filter_uf_id',   NULL);
        TSession::setValue('ClienteList_filter_fone',   NULL);
        TSession::setValue('ClienteList_filter_cel',   NULL);
        TSession::setValue('ClienteList_filter_fax',   NULL);
        TSession::setValue('ClienteList_filter_tipores_id',   NULL);
        TSession::setValue('ClienteList_filter_tempores',   NULL);
        TSession::setValue('ClienteList_filter_proximo',   NULL);
        TSession::setValue('ClienteList_filter_dtcadastro',   NULL);
                
        $filter = new TFilter('id', '>', "0"); // create the filter
        TSession::setValue('ClienteList_filter_id',   $filter); // stores the filter in the session


        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', '=', "{$data->id}"); // create the filter
            TSession::setValue('ClienteList_filter_id',   $filter); // stores the filter in the session
        }


        if (isset($data->nome) AND ($data->nome)) {
            $filter = new TFilter('nome', 'like', "%{$data->nome}%"); // create the filter
            TSession::setValue('ClienteList_filter_nome',   $filter); // stores the filter in the session
        }


        if (isset($data->cpfcnpj) AND ($data->cpfcnpj)) {
            $filter = new TFilter('cpfcnpj', 'like', "%{$data->cpfcnpj}%"); // create the filter
            TSession::setValue('ClienteList_filter_cpfcnpj',   $filter); // stores the filter in the session
        }


        if (isset($data->tipo) AND ($data->tipo)) {
            $filter = new TFilter('tipo', 'like', "%{$data->tipo}%"); // create the filter
            TSession::setValue('ClienteList_filter_tipo',   $filter); // stores the filter in the session
        }


        if (isset($data->dtnasc) AND ($data->dtnasc)) {
            $filter = new TFilter('dtnasc', 'like', "%{$data->dtnasc}%"); // create the filter
            TSession::setValue('ClienteList_filter_dtnasc',   $filter); // stores the filter in the session
        }


        if (isset($data->ierg) AND ($data->ierg)) {
            $filter = new TFilter('ierg', 'like', "%{$data->ierg}%"); // create the filter
            TSession::setValue('ClienteList_filter_ierg',   $filter); // stores the filter in the session
        }


        if (isset($data->estadocivil_id) AND ($data->estadocivil_id)) {
            $filter = new TFilter('estadocivil_id', 'like', "%{$data->estadocivil_id}%"); // create the filter
            TSession::setValue('ClienteList_filter_estadocivil_id',   $filter); // stores the filter in the session
        }


        if (isset($data->municipionasc) AND ($data->municipionasc)) {
            $filter = new TFilter('municipionasc', 'like', "%{$data->municipionasc}%"); // create the filter
            TSession::setValue('ClienteList_filter_municipionasc',   $filter); // stores the filter in the session
        }


        if (isset($data->ufnasc) AND ($data->ufnasc)) {
            $filter = new TFilter('ufnasc', 'like', "%{$data->ufnasc}%"); // create the filter
            TSession::setValue('ClienteList_filter_ufnasc',   $filter); // stores the filter in the session
        }


        if (isset($data->nomepais) AND ($data->nomepais)) {
            $filter = new TFilter('nomepais', 'like', "%{$data->nomepais}%"); // create the filter
            TSession::setValue('ClienteList_filter_nomepais',   $filter); // stores the filter in the session
        }


        if (isset($data->endereco) AND ($data->endereco)) {
            $filter = new TFilter('endereco', 'like', "%{$data->endereco}%"); // create the filter
            TSession::setValue('ClienteList_filter_endereco',   $filter); // stores the filter in the session
        }


        if (isset($data->bairro) AND ($data->bairro)) {
            $filter = new TFilter('bairro', 'like', "%{$data->bairro}%"); // create the filter
            TSession::setValue('ClienteList_filter_bairro',   $filter); // stores the filter in the session
        }


        if (isset($data->cep) AND ($data->cep)) {
            $filter = new TFilter('cep', 'like', "%{$data->cep}%"); // create the filter
            TSession::setValue('ClienteList_filter_cep',   $filter); // stores the filter in the session
        }


        if (isset($data->municipio) AND ($data->municipio)) {
            $filter = new TFilter('municipio', 'like', "%{$data->municipio}%"); // create the filter
            TSession::setValue('ClienteList_filter_municipio',   $filter); // stores the filter in the session
        }


        if (isset($data->uf_id) AND ($data->uf_id)) {
            $filter = new TFilter('uf_id', 'like', "%{$data->uf_id}%"); // create the filter
            TSession::setValue('ClienteList_filter_uf_id',   $filter); // stores the filter in the session
        }


        if (isset($data->fone) AND ($data->fone)) {
            $filter = new TFilter('fone', 'like', "%{$data->fone}%"); // create the filter
            TSession::setValue('ClienteList_filter_fone',   $filter); // stores the filter in the session
        }


        if (isset($data->cel) AND ($data->cel)) {
            $filter = new TFilter('cel', 'like', "%{$data->cel}%"); // create the filter
            TSession::setValue('ClienteList_filter_cel',   $filter); // stores the filter in the session
        }


        if (isset($data->fax) AND ($data->fax)) {
            $filter = new TFilter('fax', 'like', "%{$data->fax}%"); // create the filter
            TSession::setValue('ClienteList_filter_fax',   $filter); // stores the filter in the session
        }


        if (isset($data->tipores_id) AND ($data->tipores_id)) {
            $filter = new TFilter('tipores_id', 'like', "%{$data->tipores_id}%"); // create the filter
            TSession::setValue('ClienteList_filter_tipores_id',   $filter); // stores the filter in the session
        }


        if (isset($data->tempores) AND ($data->tempores)) {
            $filter = new TFilter('tempores', 'like', "%{$data->tempores}%"); // create the filter
            TSession::setValue('ClienteList_filter_tempores',   $filter); // stores the filter in the session
        }


        if (isset($data->proximo) AND ($data->proximo)) {
            $filter = new TFilter('proximo', 'like', "%{$data->proximo}%"); // create the filter
            TSession::setValue('ClienteList_filter_proximo',   $filter); // stores the filter in the session
        }


        if (isset($data->dtcadastro) AND ($data->dtcadastro)) {
            $filter = new TFilter('dtcadastro', 'like', "%{$data->dtcadastro}%"); // create the filter
            TSession::setValue('ClienteList_filter_dtcadastro',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('Cliente_filter_data', $data);
        
        $param=array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    
    public function onClear( $param )
    {
        $this->form->clear();
        
        TSession::setValue('ClienteList_filter_id',   NULL);
        TSession::setValue('ClienteList_filter_nome',   NULL);
        TSession::setValue('ClienteList_filter_cpfcnpj',   NULL);
        TSession::setValue('ClienteList_filter_tipo',   NULL);
        TSession::setValue('ClienteList_filter_dtnasc',   NULL);
        TSession::setValue('ClienteList_filter_ierg',   NULL);
        TSession::setValue('ClienteList_filter_estadocivil_id',   NULL);
        TSession::setValue('ClienteList_filter_municipionasc',   NULL);
        TSession::setValue('ClienteList_filter_ufnasc',   NULL);
        TSession::setValue('ClienteList_filter_nomepais',   NULL);
        TSession::setValue('ClienteList_filter_endereco',   NULL);
        TSession::setValue('ClienteList_filter_bairro',   NULL);
        TSession::setValue('ClienteList_filter_cep',   NULL);
        TSession::setValue('ClienteList_filter_municipio',   NULL);
        TSession::setValue('ClienteList_filter_uf_id',   NULL);
        TSession::setValue('ClienteList_filter_fone',   NULL);
        TSession::setValue('ClienteList_filter_cel',   NULL);
        TSession::setValue('ClienteList_filter_fax',   NULL);
        TSession::setValue('ClienteList_filter_tipores_id',   NULL);
        TSession::setValue('ClienteList_filter_tempores',   NULL);
        TSession::setValue('ClienteList_filter_proximo',   NULL);
        TSession::setValue('ClienteList_filter_dtcadastro',   NULL);
        
        TSession::setValue('Cliente_filter_data', NULL);
        
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
            
            // creates a repository for Cliente
            $repository = new TRepository('Cliente');
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
            $criteria->add(new TFilter('id', '>', '0'));          
            
            if (TSession::getValue('ClienteList_filter_id')) {
                $criteria->add(TSession::getValue('ClienteList_filter_id')); // add the session filter
            }


            if (TSession::getValue('ClienteList_filter_nome')) {
                $criteria->add(TSession::getValue('ClienteList_filter_nome')); // add the session filter
            }


            if (TSession::getValue('ClienteList_filter_cpfcnpj')) {
                $criteria->add(TSession::getValue('ClienteList_filter_cpfcnpj')); // add the session filter
            }


            if (TSession::getValue('ClienteList_filter_tipo')) {
                $criteria->add(TSession::getValue('ClienteList_filter_tipo')); // add the session filter
            }


            if (TSession::getValue('ClienteList_filter_dtnasc')) {
                $criteria->add(TSession::getValue('ClienteList_filter_dtnasc')); // add the session filter
            }


            if (TSession::getValue('ClienteList_filter_ierg')) {
                $criteria->add(TSession::getValue('ClienteList_filter_ierg')); // add the session filter
            }


            if (TSession::getValue('ClienteList_filter_estadocivil_id')) {
                $criteria->add(TSession::getValue('ClienteList_filter_estadocivil_id')); // add the session filter
            }


            if (TSession::getValue('ClienteList_filter_municipionasc')) {
                $criteria->add(TSession::getValue('ClienteList_filter_municipionasc')); // add the session filter
            }


            if (TSession::getValue('ClienteList_filter_ufnasc')) {
                $criteria->add(TSession::getValue('ClienteList_filter_ufnasc')); // add the session filter
            }


            if (TSession::getValue('ClienteList_filter_nomepais')) {
                $criteria->add(TSession::getValue('ClienteList_filter_nomepais')); // add the session filter
            }


            if (TSession::getValue('ClienteList_filter_endereco')) {
                $criteria->add(TSession::getValue('ClienteList_filter_endereco')); // add the session filter
            }


            if (TSession::getValue('ClienteList_filter_bairro')) {
                $criteria->add(TSession::getValue('ClienteList_filter_bairro')); // add the session filter
            }


            if (TSession::getValue('ClienteList_filter_cep')) {
                $criteria->add(TSession::getValue('ClienteList_filter_cep')); // add the session filter
            }


            if (TSession::getValue('ClienteList_filter_municipio')) {
                $criteria->add(TSession::getValue('ClienteList_filter_municipio')); // add the session filter
            }


            if (TSession::getValue('ClienteList_filter_uf_id')) {
                $criteria->add(TSession::getValue('ClienteList_filter_uf_id')); // add the session filter
            }


            if (TSession::getValue('ClienteList_filter_fone')) {
                $criteria->add(TSession::getValue('ClienteList_filter_fone')); // add the session filter
            }


            if (TSession::getValue('ClienteList_filter_cel')) {
                $criteria->add(TSession::getValue('ClienteList_filter_cel')); // add the session filter
            }


            if (TSession::getValue('ClienteList_filter_fax')) {
                $criteria->add(TSession::getValue('ClienteList_filter_fax')); // add the session filter
            }


            if (TSession::getValue('ClienteList_filter_tipores_id')) {
                $criteria->add(TSession::getValue('ClienteList_filter_tipores_id')); // add the session filter
            }


            if (TSession::getValue('ClienteList_filter_tempores')) {
                $criteria->add(TSession::getValue('ClienteList_filter_tempores')); // add the session filter
            }


            if (TSession::getValue('ClienteList_filter_proximo')) {
                $criteria->add(TSession::getValue('ClienteList_filter_proximo')); // add the session filter
            }


            if (TSession::getValue('ClienteList_filter_dtcadastro')) {
                $criteria->add(TSession::getValue('ClienteList_filter_dtcadastro')); // add the session filter
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
            $object = new Cliente($key, FALSE); // instantiates the Active Record
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