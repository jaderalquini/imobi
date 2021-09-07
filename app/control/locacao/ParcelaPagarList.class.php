<?php
/**
 * ParcelaPagarList Listing
 * @author  <your name here>
 */
class ParcelaPagarList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $formgrid;
    private $loaded;
    private $deleteButton;
    private $editButton;
    
    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();
        parent::include_js('app/lib/include/application.js');
        
        // creates the form
        $this->form = new TQuickForm('form_ParcelaPagar');
        $this->form->class = 'tform'; // change CSS class        
        $this->form->style = 'display: table;width:100%'; // change style
        //$this->form->setFormTitle('ParcelaPagar');
        $this->form->setFieldsByRow(2);

        // create the form fields
        $id = new THidden('id');
        $contrato_id = new TEntry('contrato_id');
        $numero = new TEntry('numero');
        $sequencia = new TEntry('sequencia');
        $bem_id = new TEntry('bem_id');
        $bem = new TEntry('bem');
        $cliente_id = new TEntry('cliente_id');
        $proprietario_id = new TEntry('proprietario_id');
        $proprietario = new TEntry('proprietario');
        $dtinicio = new TDate('dtinicio');
        $dtfim = new TDate('dtfim');
        $dtvencto = new TDate('dtvencto');
        $valor = new TEntry('valor');
        $dtpagto = new TDate('dtpagto');
        $numrecibo = new TEntry('numrecibo');
        $vlpago = new TEntry('vlpago');
        $opcomissao = new TEntry('opcomissao');
        $vlcomissao = new TEntry('vlcomissao');
        $opoutro = new TEntry('opoutro');
        $vloutro = new TEntry('vloutro');
        $opseguro = new TEntry('opseguro');
        $vlseguro = new TEntry('vlseguro');
        $opcondominio = new TEntry('opcondominio');
        $vlcondominio = new TEntry('vlcondominio');
        $opluz = new TEntry('opluz');
        $vlluz = new TEntry('vlluz');
        $opagua = new TEntry('opagua');
        $vlagua = new TEntry('vlagua');
        $opiptu = new TEntry('opiptu');
        $vliptu = new TEntry('vliptu');
        $numrecpro = new TEntry('numrecpro');
        $opdevolucao = new TEntry('opdevolucao');
        $vldevolucao = new TEntry('vldevolucao');
        $opgas = new TEntry('opgas');
        $vlgas = new TEntry('vlgas');
        $observacao = new TText('observacao');
        
        // Campos Não Editáveis
        $contrato_id->setEditable(FALSE);
        $bem_id->setEditable(FALSE);
        $bem->setEditable(FALSE);
        $proprietario_id->setEditable(FALSE);
        $proprietario->setEditable(FALSE);
        
        // Tamanho dos Campos no formulário
        $bem_id->setSize(50);
        $bem->setSize(277);
        $proprietario_id->setSize(50);
        $proprietario->setSize(277);

        // add the fields        
        $this->form->addQuickField('Contrato', $contrato_id,  50 );
        $this->form->addQuickFields('Bem', array($bem_id, $bem) );
        //$this->form->addQuickField('Locatário 1', $cliente_id,  50 );
        $this->form->addQuickFields('Locador', array($proprietario_id, $proprietario) );
        /*$this->form->addQuickField('Numero', $numero,  200 );
        $this->form->addQuickField('Sequencia', $sequencia,  200 );
        $this->form->addQuickField('Dtinicio', $dtinicio,  200 );
        $this->form->addQuickField('Dtfim', $dtfim,  200 );
        $this->form->addQuickField('Dtvencto', $dtvencto,  200 );
        $this->form->addQuickField('Valor', $valor,  200 );
        $this->form->addQuickField('Dtpagto', $dtpagto,  200 );
        $this->form->addQuickField('Numrecibo', $numrecibo,  200 );
        $this->form->addQuickField('Vlpago', $vlpago,  200 );
        $this->form->addQuickField('Opcomissao', $opcomissao,  200 );
        $this->form->addQuickField('Vlcomissao', $vlcomissao,  200 );
        $this->form->addQuickField('Opoutro', $opoutro,  200 );
        $this->form->addQuickField('Vloutro', $vloutro,  200 );
        $this->form->addQuickField('Opseguro', $opseguro,  200 );
        $this->form->addQuickField('Vlseguro', $vlseguro,  200 );
        $this->form->addQuickField('Opcondominio', $opcondominio,  200 );
        $this->form->addQuickField('Vlcondominio', $vlcondominio,  200 );
        $this->form->addQuickField('Opluz', $opluz,  200 );
        $this->form->addQuickField('Vlluz', $vlluz,  200 );
        $this->form->addQuickField('Opagua', $opagua,  200 );
        $this->form->addQuickField('Vlagua', $vlagua,  200 );
        $this->form->addQuickField('Opiptu', $opiptu,  200 );
        $this->form->addQuickField('Vliptu', $vliptu,  200 );
        $this->form->addQuickField('Numrecpro', $numrecpro,  200 );
        $this->form->addQuickField('Opdevolucao', $opdevolucao,  200 );
        $this->form->addQuickField('Vldevolucao', $vldevolucao,  200 );
        $this->form->addQuickField('Opgas', $opgas,  200 );
        $this->form->addQuickField('Vlgas', $vlgas,  200 );
        $this->form->addQuickField('Observacao', $observacao,  200 );*/
        $this->form->addQuickField('', $id );
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('ParcelaPagar_filter_data') );
        
        // add the search form actions
        $this->form->addQuickAction('Gerar Parcelas', new TAction(array($this, 'onGerParcelas')), 'fa:cog blue');
        $this->form->addQuickAction(_t('Back to the listing'),new TAction(array('ContratoList','onReload')),'fa:table blue');
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');        

        // creates the datagrid columns
        $column_check = new TDataGridColumn('check', '', 'center');
        $column_id = new TDataGridColumn('id', 'ID', 'left');
        $column_contrato_id = new TDataGridColumn('contrato_id', 'Contrato', 'left');
        $column_numero = new TDataGridColumn('numero', 'Parcela', 'right');
        $column_sequencia = new TDataGridColumn('sequencia', 'Sequência', 'right');
        $column_bem_id = new TDataGridColumn('bem_id', 'Bem', 'left');
        $column_cliente_id = new TDataGridColumn('cliente_id', 'Locatário 1', 'left');
        $column_proprietario_id = new TDataGridColumn('proprietario_id', 'Locador', 'left');
        $column_dtinicio = new TDataGridColumn('dtinicio', 'Data Início', 'right');
        $column_dtfim = new TDataGridColumn('dtfim', 'Data Fim', 'right');
        $column_dtvencto = new TDataGridColumn('dtvencto', 'Data Vencimento', 'right');
        $column_valor = new TDataGridColumn('valor', 'Valor', 'right');
        $column_dtpagto = new TDataGridColumn('dtpagto', 'Data Pagamento', 'right');
        $column_numrecibo = new TDataGridColumn('numrecibo', 'Num. Recibo', 'right');        
        $column_vlpago = new TDataGridColumn('vlpago', 'Valor Pago', 'right');
        $column_opcomissao = new TDataGridColumn('opcomissao', 'Opcomissao', 'left');
        $column_vlcomissao = new TDataGridColumn('vlcomissao', 'Comissão', 'right');
        $column_opoutro = new TDataGridColumn('opoutro', 'Opoutro', 'left');
        $column_vloutro = new TDataGridColumn('vloutro', 'Outro', 'right');
        $column_opseguro = new TDataGridColumn('opseguro', 'Opseguro', 'left');
        $column_vlseguro = new TDataGridColumn('vlseguro', 'Seguro', 'right');
        $column_opcondominio = new TDataGridColumn('opcondominio', 'Opcondominio', 'left');
        $column_vlcondominio = new TDataGridColumn('vlcondominio', 'Condomínio', 'right');
        $column_opluz = new TDataGridColumn('opluz', 'Opluz', 'left');
        $column_vlluz = new TDataGridColumn('vlluz', 'Luz', 'right');
        $column_opagua = new TDataGridColumn('opagua', 'Opagua', 'left');
        $column_vlagua = new TDataGridColumn('vlagua', 'Água', 'right');
        $column_opiptu = new TDataGridColumn('opiptu', 'Opiptu', 'left');
        $column_vliptu = new TDataGridColumn('vliptu', 'IPTU', 'right');    
        $column_numrecpro = new TDataGridColumn('numrecpro', 'Num. Recibo.', 'right');    
        $column_opdevolucao = new TDataGridColumn('opdevolucao', 'Opdevolucao', 'left');
        $column_vldevolucao = new TDataGridColumn('vldevolucao', 'Devolução', 'right');
        $column_opgas = new TDataGridColumn('opgas', 'Opgas', 'left');
        $column_vlgas = new TDataGridColumn('vlgas', 'Gás', 'right');
        $column_observacao = new TDataGridColumn('observacao', 'Observacao', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_check);
        /*$this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_contrato_id);*/
        $this->datagrid->addColumn($column_numero);
        /*$this->datagrid->addColumn($column_sequencia);
        $this->datagrid->addColumn($column_bem_id);
        $this->datagrid->addColumn($column_cliente_id);
        $this->datagrid->addColumn($column_proprietario_id);
        $this->datagrid->addColumn($column_dtinicio);
        $this->datagrid->addColumn($column_dtfim);*/
        $this->datagrid->addColumn($column_dtvencto);
        $this->datagrid->addColumn($column_valor);
        $this->datagrid->addColumn($column_dtpagto);
        //$this->datagrid->addColumn($column_numrecibo);
        $this->datagrid->addColumn($column_vlpago);
        $this->datagrid->addColumn($column_numrecpro);
        /*$this->datagrid->addColumn($column_opcomissao);
        $this->datagrid->addColumn($column_vlcomissao);
        $this->datagrid->addColumn($column_opoutro);
        $this->datagrid->addColumn($column_vloutro);
        $this->datagrid->addColumn($column_opseguro);
        $this->datagrid->addColumn($column_vlseguro);
        $this->datagrid->addColumn($column_opcondominio);
        $this->datagrid->addColumn($column_vlcondominio);
        $this->datagrid->addColumn($column_opluz);
        $this->datagrid->addColumn($column_vlluz);
        $this->datagrid->addColumn($column_opagua);
        $this->datagrid->addColumn($column_vlagua);
        $this->datagrid->addColumn($column_opiptu);
        $this->datagrid->addColumn($column_vliptu);        
        $this->datagrid->addColumn($column_opdevolucao);
        $this->datagrid->addColumn($column_vldevolucao);
        $this->datagrid->addColumn($column_opgas);
        $this->datagrid->addColumn($column_vlgas);
        $this->datagrid->addColumn($column_observacao);*/
        
        // define the transformer method over image
        $column_dtinicio->setTransformer( function($value, $object, $row) {
            $date = new DateTime($value);
            return $date->format('d/m/Y');
        });

        // define the transformer method over image
        $column_dtfim->setTransformer( function($value, $object, $row) {
            $date = new DateTime($value);
            return $date->format('d/m/Y');
        });

        // define the transformer method over image
        $column_dtvencto->setTransformer( function($value, $object, $row) {
            $date = new DateTime($value);
            return $date->format('d/m/Y');
        });

        // define the transformer method over image
        $column_valor->setTransformer( function($value, $object, $row) {
            return 'R$ ' . number_format($value, 2, ',', '.');
        });

        // define the transformer method over image
        $column_dtpagto->setTransformer( function($value, $object, $row) {
            try
            {
                $date = new DateTime($value);
                return $date->format('d/m/Y');
            }
            catch (Exception $e) // in case of exception
            {
                return NULL;
            }
        });

        // define the transformer method over image
        $column_vlpago->setTransformer( function($value, $object, $row) {
            return 'R$ ' . number_format($value, 2, ',', '.');
        });

        // define the transformer method over image
        $column_vlcomissao->setTransformer( function($value, $object, $row) {
            return 'R$ ' . number_format($value, 2, ',', '.');
        });

        // define the transformer method over image
        $column_vloutro->setTransformer( function($value, $object, $row) {
            return 'R$ ' . number_format($value, 2, ',', '.');
        });

        // define the transformer method over image
        $column_vlseguro->setTransformer( function($value, $object, $row) {
            return 'R$ ' . number_format($value, 2, ',', '.');
        });

        // define the transformer method over image
        $column_vlcondominio->setTransformer( function($value, $object, $row) {
            return 'R$ ' . number_format($value, 2, ',', '.');
        });

        // define the transformer method over image
        $column_vlluz->setTransformer( function($value, $object, $row) {
            return 'R$ ' . number_format($value, 2, ',', '.');
        });

        // define the transformer method over image
        $column_vlagua->setTransformer( function($value, $object, $row) {
            return 'R$ ' . number_format($value, 2, ',', '.');
        });

        // define the transformer method over image
        $column_vliptu->setTransformer( function($value, $object, $row) {
            return 'R$ ' . number_format($value, 2, ',', '.');
        });

        // define the transformer method over image
        $column_vldevolucao->setTransformer( function($value, $object, $row) {
            return 'R$ ' . number_format($value, 2, ',', '.');
        });

        // define the transformer method over image
        $column_vlgas->setTransformer( function($value, $object, $row) {
            return 'R$ ' . number_format($value, 2, ',', '.');
        });
        
        // create EDIT action
        $action_edit = new TDataGridAction(array('ParcelaPagarEditForm', 'onEdit'));
        $action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('fa:pencil-square-o blue fa-lg');
        $action_edit->setField('id');
        $this->datagrid->addAction($action_edit);
        
        // create PAY action
        $action_pay = new TDataGridAction(array('ParcelaPagarPayForm', 'onEdit'));
        //$action_pay->setUseButton(TRUE);
        $action_pay->setButtonClass('btn btn-default');
        $action_pay->setLabel('Efetuar Pagamento');
        $action_pay->setImage('fa:money green fa-lg');
        $action_pay->setField('id');
        $this->datagrid->addAction($action_pay);
        
        // create DELETE action
        $action_del = new TDataGridAction(array($this, 'onDelete'));
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
        
        $this->datagrid->disableDefaultClick();
        
        // put datagrid inside a form
        /*$this->formgrid = new TForm;
        $this->formgrid->add($this->datagrid);*/
        
        $this->formgrid = new TQuickForm();
        $this->formgrid->class = 'tform'; // change CSS class        
        $this->formgrid->style = 'display: table;width:100%'; // change style
        $this->formgrid->add($this->datagrid);
        
        // creates the delete collection button
        $this->deleteButton = new TButton('delete_collection');
        $this->deleteButton->setAction(new TAction(array($this, 'onDeleteCollection')), AdiantiCoreTranslator::translate('Delete selected'));
        $this->deleteButton->setImage('fa:trash-o red');
        $this->formgrid->addField($this->deleteButton);
        
        $data = $this->formgrid->getData();
        $editCollection = new TAction(array('ParcelaPagarEditCollectionForm','onEdit'));
        $editCollection->setParameters( (array) $data);        
        
        // creates the edit collection button
        $this->editButton = new TButton('edit_collection');
        $this->editButton->setAction($editCollection);
        $this->editButton->setLabel(AdiantiCoreTranslator::translate('Edit selected'));
        $this->editButton->setImage('fa:pencil-square-o blue');
        $this->formgrid->addField($this->editButton);
        
        $buttonpack = new THbox;
        $buttonpack->style = 'width: 100%; background:whiteSmoke;border:1px solid #cccccc; padding: 3px;padding: 5px;';
        $buttonpack->add($this->editButton);
        $buttonpack->add($this->deleteButton);
        
        $gridpack = new TVBox;
        $gridpack->style = 'width: 100%';
        $gridpack->add($this->formgrid);
        $gridpack->add($buttonpack);
        
        $this->transformCallback = array($this, 'onBeforeLoad');

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu2.xml', __CLASS__));
        $container->add($this->form);
        $container->add($gridpack);
        //$container->add($this->pageNavigation);
        
        parent::add($container);
    }
    
    /**
     * Set the MASTER key
     */
    public function onSetContrato($param)
    {
        $id = isset($param['contrato_id']) ? $param['contrato_id'] : $param['key'];
        TSession::setValue('contrato_id', $id);
        
        TTransaction::open(TSession::getValue('banco'));
                
        $object = new Contrato($id); 
        $bem = new Bem($object->bem_id);
        $cliente = new Cliente($bem->proprietario_id);     
                                      
        $obj = new StdClass;
        $obj->contrato_id = $object->id;
        $obj->bem_id = $object->bem_id;
        $obj->bem = $bem->descricao;
        $obj->proprietario_id = $bem->proprietario_id;
        $obj->proprietario = $cliente->nome;
                         
        TForm::sendData('form_ParcelaPagar', $obj);  
        
        TTransaction::close();   
        
        $this->onReload();
    }
    
    /**
     * Load the datagrid with data
     */
    public function onReload($param = NULL)
    {
        try
        {
            TTransaction::open(TSession::getValue('banco'));
            
            // creates a repository for ParcelaPagar
            $repository = new TRepository('ParcelaPagar');
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;
            $criteria->setProperties($param); // order, offset
            
            // filter the master record
            $criteria->add(new TFilter('contrato_id', '=', TSession::getValue('contrato_id')));
            
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
        $id = $param['id'];
        
        TTransaction::open(TSession::getValue('banco'));
        $parcela = new ParcelaPagar($id);
        TTransaction::close();
        $param2 = array();
        $param2['contrato_id'] = $parcela->contrato_id;
        
        // define the delete action
        $action = new TAction(array($this, 'Delete'));
        $action->setParameters($param); // pass the key parameter ahead
        
        // shows a dialog to the user
        new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
                
        $this->onSetContrato($param2);
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
            $object = new ParcelaPagar($key); // instantiates the Active Record
            $param = array();
            $param['contrato_id'] = $object->contrato_id;
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
        
        $this->onSetContrato($param);
    }
    
    /**
     * Ask before delete record collection
     */
    public function onDeleteCollection( $param )
    {        
        $data = $this->formgrid->getData(); // get selected records from datagrid
        $this->formgrid->setData($data); // keep form filled
        
        if ($data)
        {
            $selected = array();
            
            // get the record id's
            foreach ($data as $index => $check)
            {
                TTransaction::open(TSession::getValue('banco'));
                if ($check == 'on')
                {
                    $id = substr($index,5);
                    $selected[] = $id;
                    $object = new ParcelaPagar($id);
                    $param2 = array();
                    $param2['contrato_id'] = $object->contrato_id;
                }
                TTransaction::close();
            }
            
            if ($selected)
            {
                // encode record id's as json
                $param['selected'] = json_encode($selected);
                
                // define the delete action
                $action = new TAction(array($this, 'deleteCollection'));
                $action->setParameters($param); // pass the key parameter ahead
                
                // shows a dialog to the user
                new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
            }
        }
        
        $this->onSetContrato($param2);
    }
    
    /**
     * method deleteCollection()
     * Delete many records
     */
    public function deleteCollection($param)
    {        
        // decode json with record id's
        $selected = json_decode($param['selected']);
        
        try
        {
            TTransaction::open(TSession::getValue('banco'));
            if ($selected)
            {
                // delete each record from collection
                foreach ($selected as $id)
                {
                    $object = new ParcelaPagar($id);
                    $param = array();
                    $param['contrato_id'] = $object->contrato_id;
                    $object->delete( $id );
                }
                $posAction = new TAction(array($this, 'onSetContrato'));
                $posAction->setParameters( $param );
                new TMessage('info', AdiantiCoreTranslator::translate('Records deleted'), $posAction);
            }
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    /**
     * Transform datagrid objects
     * Create the checkbutton as datagrid element
     */
    public function onBeforeLoad($objects, $param)
    {
        // update the action parameters to pass the current page to action
        // without this, the action will only work for the first page
        $deleteAction = $this->deleteButton->getAction();
        $deleteAction->setParameters($param); // important!
        
        $gridfields = array( $this->deleteButton );
        
        foreach ($objects as $object)
        {
            $object->check = new TCheckButton('check' . $object->id);
            $object->check->setIndexValue('on');
            $gridfields[] = $object->check; // important
        }
        
        $this->formgrid->setFields($gridfields);
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
    
    public function onGerParcelas($param)
    {        
        TTransaction::open(TSession::getValue('banco'));
        
        $contrato_id = $param['contrato_id'];
        $contrato = new Contrato($contrato_id);
        
        $repository = new TRepository('ParcelaPagar');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('contrato_id','=',$contrato->id));
        $parcelas = $repository->load($criteria, FALSE);
        
        $i = 0;
        foreach ($parcelas as $parcela)
        {
            $i++;
        }
                    
        if ($i < $contrato->qtdeparc)
        {
            $i++; 
            $qtdeparc = $contrato->qtdeparc;
            $contrato_id = $contrato->id;
            $bem_id = $contrato->bem_id;
            $cliente_id = $contrato->cliente_id;
            $dtinicio = $contrato->dtinicio;
            $dtinicio = date('Y-m-d', strtotime("+" . $i . " month", strtotime($dtinicio)));
            //echo $dtinicio;
            $dtfim = $contrato->dtfim;
            $dtfim = date('Y-m-d', strtotime("+1 month", strtotime($dtfim)));
            //echo $dtfim;
                        
            $valor = $contrato->valor;
            $vldesc = $contrato->vldesc;
            $vlseguro = $contrato->vlseguro;
            $qtdeparcdesc = $contrato->qtdeparcdesc;
            $percdesc = $contrato->percdesc;
                                
            $bem = new Bem($bem_id);
            $diapagto = $bem->diapagto;
                        
            $proprietario_id = $bem->proprietario_id;        
            $percomissao = $bem->percomissao;
                                
            $vlcomissao = $valor * $percomissao * 0.01;
            $proprietario_id = $bem->proprietario_id;
            $cliente2_id = $contrato->cliente2_id;
                                              
            for ($date = strtotime($dtinicio); $date < strtotime($dtfim); $date = strtotime("+1 month", $date))
            {
                $parcela = array();
                $parcela['contrato_id'] = $contrato_id;
                $parcela['numero'] = $i;
                $parcela['sequencia'] = $i . $qtdeparc;
                $parcela['bem_id'] = $bem_id;
                $parcela['cliente_id'] = $cliente_id;
                $parcela['dtinicio'] = $contrato->dtinicio;
                $parcela['dtfim'] = $contrato->dtfim;
                $mes = date('m', $date);
                $ano = date('Y', $date);
                $parcela['dtvencto'] = $ano . '-'. $mes . '-' . str_pad($diapagto, 2, '0', STR_PAD_LEFT);
                $parcela['dtpagto'] = '0';
                $parcela['valor'] = $valor;
                            
                $parcela['proprietario_id'] = $proprietario_id;
                $parcela['vlcomissao'] = $vlcomissao;
                $parcela['cliente2_id'] = $cliente2_id;
                            
                $ppagr = new ParcelaPagar;
                $ppagr->fromArray( (array) $parcela);
                $ppagr->store(); 
                $i++;                     
            }
        }
        
        TTransaction::close();
        
        $param = array();
        $param['contrato_id'] = $contrato_id;
        
        $this->onSetContrato($param);       
    }
}
?>