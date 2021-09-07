<?php
/**
 * ChequeForm Master/Detail
 * @author  <your name here>
 */
class ChequeForm extends TPage
{
    protected $form; // form
    protected $formFields;
    protected $detail_list;
    
    /**
     * Page constructor
     */
    public function __construct()
    {
        parent::__construct();
        parent::include_js('app/lib/include/application.js');
        
        // creates the form
        $this->form = new TForm('form_Cheque');
        $this->form->class = 'tform'; // CSS class
        $this->form->style = 'max-width:700px'; // style
        parent::include_css('app/resources/custom-frame.css');
        
        $table_master = new TTable;
        $table_master->width = '100%';
        
        //$table_master->addRowSet( new TLabel('Cheque'), '', '')->class = 'tformtitle';
        
        // add a table inside form
        $table_general = new TTable;
        $table_detail  = new TTable;
        $table_general-> width = '100%';
        $table_detail-> width  = '100%';
        
        $frame_general = new TFrame;
        //$frame_general->setLegend('Cheque');
        $frame_general->style = 'background:whiteSmoke';
        $frame_general->add($table_general);
        
        $table_master->addRow()->addCell( $frame_general )->colspan=2;
        $row = $table_master->addRow();
        $row->addCell( $table_detail );
        
        $this->form->add($table_master);
        
        // master fields
        $id = new THidden('id');
        $dtcheque = new TDate('dtcheque');
        $banco_id = new TSeekButton('banco_id');
        $banco = new TEntry('banco');
        $contacorrente_id = new TEntry('contacorrente_id');
        $numero = new TEntry('numero');
        $valor = new TEntry('valor');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('id', '>', 0));
        $criteria->setProperty('order', 'nome');
        $nominal_id = new TDBSeekButton('nominal_id',TSession::getValue('banco'),'form_Cheque','Cliente','nome','nominal_id','nominal',$criteria);
        $nominal = new TEntry('nominal');
        $dtprepago = new TDate('dtprepago');
        
        $obj = new BancoSelectionList;
        $action = new TAction(array($obj, 'onReload'));
        $banco_id->setAction($action);
        
        // Campos Não Editáveis
        $id->setEditable(FALSE);
        $banco->setEditable(FALSE);
        
        // Máscaras
        $dtcheque->setMask('dd/mm/yyyy');
        $dtprepago->setMask('dd/mm/yyyy');
        $valor->setNumericMask(2, ',', '.');
        
        // Tamanho dos Campos no formulário
        $dtcheque->setSize(100);
        $banco_id->setSize(50);
        $banco->setSize(277);
        $contacorrente_id->setSize(100);
        $numero->setSize(50);
        $nominal_id->setSize(50);
        $nominal->setSize(277);
        $dtprepago->setSize(100);
        $id->setSize(50);
        
        // Número de Caracteres permitidos dentro dos campos
        
        
        // Adiciona validação aos campos
        $dtcheque->addValidation('Data', new TRequiredValidator);
        $banco_id->addValidation('Banco', new TRequiredValidator);
        $contacorrente_id->addValidation('Conta', new TRequiredValidator);
        $numero->addValidation('Número', new TRequiredValidator);
        $valor->addValidation('Valor', new TRequiredValidator);
        $dtprepago->addValidation('Pré-Pago', new TRequiredValidator);
        
        // Formatações Adicionais
        $id->style = 'background: whiteSmoke; color: #fff; border: 0; height: 0px;';
        
        // detail fields
        $detail_id = new THidden('detail_id');
        $detail_banco_id = new THidden('detail_banco_id');
        $detail_contacorrente_id = new THidden('detail_contacorrente_id');
        $detail_sequencia = new TEntry('detail_sequencia');
        $detail_vltitulo = new TEntry('detail_vltitulo');
        $detail_vlpago = new TEntry('detail_vlpago');
        $detail_variacao = new TEntry('detail_variacao');
        $detail_descricao = new TEntry('detail_descricao');
        
        // Tamanho dos Campos no formulário
        $detail_sequencia->setSize(50);
        $detail_variacao->setSize(100);
        $detail_descricao->setSize(300);
        
        // Formatação para Valores Monetário
        $detail_vltitulo->setNumericMask(2, ',', '.');
        $detail_vlpago->setNumericMask(2, ',', '.');

        /** samples
         $this->form->addQuickFields('Date', array($date1, new TLabel('to'), $date2)); // side by side fields
         $fieldX->addValidation( 'Field X', new TRequiredValidator ); // add validation
         $fieldX->setSize( 100, 40 ); // set size
         **/
        
        $label_dtcheque = new TLabel('Data');
        $label_dtcheque->setSize(100);
        $label_banco = new TLabel('Banco');
        $label_banco->setSize(100);
        
        // master        
        $coluna = new TLabel('');
        $coluna->setSize(350);
        $table_general->addRowSet( $label_dtcheque, $dtcheque, $label_banco, array($banco_id, $banco) );
        $table_general->addRowSet( new TLabel('Conta Corrente'), $contacorrente_id, new TLabel('Número'), $numero );
        $table_general->addRowSet( new TLabel('Valor'), $valor, new TLabel('Nominal'),array($nominal_id,  $nominal) );
        $table_general->addRowSet( new TLabel('Pré-Pago'), $dtprepago, new TLabel(''), $coluna );
        $table_general->addRowSet( new TLabel('') , $id );
        
         // detail
        $frame_details = new TFrame();
        $frame_details->setLegend('Itens');
        $row = $table_detail->addRow();
        $row->addCell($frame_details);
        
        $btn_save_detail = new TButton('btn_save_detail');
        $btn_save_detail->setAction(new TAction(array($this, 'onSaveDetail')), 'Salvar');
        $btn_save_detail->setImage('fa:save');
        
        $table_details = new TTable;
        $frame_details->add($table_details);
        
        $label_sequencia = new TLabel('Sequência');
        $label_sequencia->setSize(100);
        $label_vltitulo = new TLabel('Valor Título');
        $label_vltitulo->setSize(100);
        
        $table_details->addRowSet( $label_sequencia, $detail_sequencia, $label_vltitulo, $detail_vltitulo );
        $table_details->addRowSet( new TLabel('Valor Pago'), $detail_vlpago, new TLabel('Variação'), $detail_variacao );
        $table_details->addRowSet( new TLabel('Descrição'), $detail_descricao );
        $table_details->addRowSet( '', $detail_id );
        $table_details->addRowSet( '', $detail_banco_id );
        $table_details->addRowSet( '', $detail_contacorrente_id );
        
        $table_details->addRowSet( $btn_save_detail );
        
        $this->detail_list = new TQuickGrid;
        $this->detail_list->setHeight( 175 );
        $this->detail_list->makeScrollable();
        $this->detail_list->disableDefaultClick();
        $this->detail_list->addQuickColumn('', 'edit', 'left', 50);
        $this->detail_list->addQuickColumn('', 'delete', 'left', 50);
        
        // items        
        $detail_column_banco_id = new TDataGridColumn('banco_id', 'Banco Id',  'left', 200);
        $detail_column_contacorrente_id = new TDataGridColumn('contacorrente_id', 'Contacorrente Id', 'left', 200);
        $detail_column_sequencia = new TDataGridColumn('sequencia', 'Sequência', 'left', 100);
        $detail_column_vltitulo = new TDataGridColumn('vltitulo', 'Valor Título', 'left', 200);
        $detail_column_vlpago = new TDataGridColumn('vlpago', 'Valor Pago', 'left', 200);
        $detail_column_variacao = new TDataGridColumn('variacao', 'Variação', 'left', 100);
        $detail_column_descricao = new TDataGridColumn('descricao', 'Descrição', 'left', 200);
        
        $detail_column_vltitulo->setTransformer(function($value, $object, $row) {
            return 'R$ ' . number_format($value, 2, ',', '.');                                                        
        });
        
        $detail_column_vlpago->setTransformer(function($value, $object, $row) {
            return 'R$ ' . number_format($value, 2, ',', '.');                                                        
        });
        
        /*$this->detail_list->addColumn($detail_column_banco_id);
        $this->detail_list->addColumn($detail_column_contacorrente_id);*/
        $this->detail_list->addColumn($detail_column_sequencia);
        $this->detail_list->addColumn($detail_column_vltitulo);
        $this->detail_list->addColumn($detail_column_vlpago);
        $this->detail_list->addColumn($detail_column_variacao);
        $this->detail_list->addColumn($detail_column_descricao);
        $this->detail_list->createModel();
        
        $row = $table_detail->addRow();
        $row->addCell($this->detail_list);
        
        // create an action button (save)
        $save_button=new TButton('save');
        $save_button->setAction(new TAction(array($this, 'onSave')), _t('Save'));
        $save_button->setImage('fa:floppy-o');

        // create an new button (edit with no parameters)
        $new_button=new TButton('new');
        $new_button->setAction(new TAction(array($this, 'onClear')), _t('New'));
        $new_button->setImage('bs:plus-sign green');
        
        // create an list button
        $list_button=new TButton('list');
        $list_button->setAction(new TAction(array('ChequeList', 'onReload')), _t('Back to the listing'));
        $list_button->setImage('fa:table blue');
        
        // define form fields
        $this->formFields   = array($id,$dtcheque,$banco_id,$contacorrente_id,$numero,$valor,$nominal_id,$nominal,$dtprepago,$detail_banco_id,$detail_contacorrente_id,$detail_sequencia,$detail_vltitulo,$detail_vlpago,$detail_variacao,$detail_descricao);
        $this->formFields[] = $btn_save_detail;
        $this->formFields[] = $save_button;
        $this->formFields[] = $new_button;
        $this->formFields[] = $list_button;
        $this->formFields[] = $detail_id;
        $this->form->setFields( $this->formFields );
        
        $table_master->addRowSet( array($save_button, $new_button, $list_button), '', '')->class = 'tformaction'; // CSS class
        
        // create the page container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', 'ChequeList'));
        $container->add($this->form);
        parent::add($container);
    }
    
    
    /**
     * Clear form
     * @param $param URL parameters
     */
    public function onClear($param)
    {
        $this->form->clear();
        TSession::setValue(__CLASS__.'_items', array());
        $this->onReload( $param );
    }
    
    /**
     * Save an item from form to session list
     * @param $param URL parameters
     */
    public function onSaveDetail( $param )
    {
        try
        {
            TTransaction::open(TSession::getValue('banco'));
            $data = $this->form->getData();
            
            /** validation sample
            if (! $data->fieldX)
                throw new Exception('The field fieldX is required');
            **/
            
            $items = TSession::getValue(__CLASS__.'_items');
            $key = empty($data->detail_id) ? 'X'.mt_rand(1000000000, 1999999999) : $data->detail_id;
            
            $items[ $key ] = array();
            $items[ $key ]['id'] = $key;
            $items[ $key ]['banco_id'] = $data->detail_banco_id;
            $items[ $key ]['contacorrente_id'] = $data->detail_contacorrente_id;
            $items[ $key ]['sequencia'] = $data->detail_sequencia;
            $items[ $key ]['vltitulo'] = FuncoesExtras::retiraFormatacao($data->detail_vltitulo);
            $items[ $key ]['vlpago'] = FuncoesExtras::retiraFormatacao($data->detail_vlpago);
            $items[ $key ]['variacao'] = $data->detail_variacao;
            $items[ $key ]['descricao'] = $data->detail_descricao;
            
            TSession::setValue(__CLASS__.'_items', $items);
            
            // clear detail form fields
            $data->detail_id = '';
            $data->detail_banco_id = '';
            $data->detail_contacorrente_id = '';
            $data->detail_sequencia = '';
            $data->detail_vltitulo = '';
            $data->detail_vlpago = '';
            $data->detail_variacao = '';
            $data->detail_descricao = '';
            
            TTransaction::close();
            $this->form->setData($data);
            
            $this->onReload( $param ); // reload the items
        }
        catch (Exception $e)
        {
            $this->form->setData( $this->form->getData());
            new TMessage('error', $e->getMessage());
        }
    }
    
    /**
     * Load an item from session list to detail form
     * @param $param URL parameters
     */
    public function onEditDetail( $param )
    {
        $data = $this->form->getData();
        
        // read session items
        $items = TSession::getValue(__CLASS__.'_items');
        
        // get the session item
        $item = $items[ $param['item_key'] ];
        
        $item['vltitulo'] = number_format($item['vltitulo'], 2, ',', '.');
        $item['vlpago'] = number_format($item['vlpago'], 2, ',', '.');
        
        $data->detail_id = $item['id'];
        $data->detail_banco_id = $item['banco_id'];
        $data->detail_contacorrente_id = $item['contacorrente_id'];
        $data->detail_sequencia = $item['sequencia'];
        $data->detail_vltitulo = $item['vltitulo'];
        $data->detail_vlpago = $item['vlpago'];
        $data->detail_variacao = $item['variacao'];
        $data->detail_descricao = $item['descricao'];
        
        // fill detail fields
        TForm::sendData('form_Cheque', $data);
    
        $this->onReload( $param );
    }
    
    /**
     * Delete an item from session list
     * @param $param URL parameters
     */
    public function onDeleteDetail( $param )
    {
        $data = $this->form->getData();
        
        // reset items
            $data->detail_banco_id = '';
            $data->detail_contacorrente_id = '';
            $data->detail_sequencia = '';
            $data->detail_vltitulo = '';
            $data->detail_vlpago = '';
            $data->detail_variacao = '';
            $data->detail_descricao = '';
        
        // clear form data
        $this->form->setData( $data );
        
        // read session items
        $items = TSession::getValue(__CLASS__.'_items');
        
        // delete the item from session
        unset($items[ $param['item_key'] ] );
        TSession::setValue(__CLASS__.'_items', $items);
        
        // reload items
        $this->onReload( $param );
    }
    
    /**
     * Load the items list from session
     * @param $param URL parameters
     */
    public function onReload($param)
    {
        // read session items
        $items = TSession::getValue(__CLASS__.'_items');
        
        $this->detail_list->clear(); // clear detail list
        $data = $this->form->getData();
        
        if ($items)
        {
            $cont = 1;
            foreach ($items as $list_item_key => $list_item)
            {
                $item_name = 'prod_' . $cont++;
                $item = new StdClass;
                
                // create action buttons
                $action_del = new TAction(array($this, 'onDeleteDetail'));
                $action_del->setParameter('item_key', $list_item_key);
                
                $action_edi = new TAction(array($this, 'onEditDetail'));
                $action_edi->setParameter('item_key', $list_item_key);
                
                $button_del = new TButton('delete_detail'.$cont);
                $button_del->class = 'btn btn-default btn-sm';
                $button_del->setAction( $action_del, '' );
                $button_del->setImage('fa:trash-o red fa-lg');
                
                $button_edi = new TButton('edit_detail'.$cont);
                $button_edi->class = 'btn btn-default btn-sm';
                $button_edi->setAction( $action_edi, '' );
                $button_edi->setImage('fa:edit blue fa-lg');
                
                $item->edit   = $button_edi;
                $item->delete = $button_del;
                
                $this->formFields[ $item_name.'_edit' ] = $item->edit;
                $this->formFields[ $item_name.'_delete' ] = $item->delete;
                
                // items
                $item->id = $list_item['id'];
                $item->banco_id = $list_item['banco_id'];
                $item->contacorrente_id = $list_item['contacorrente_id'];
                $item->sequencia = $list_item['sequencia'];
                $item->vltitulo = $list_item['vltitulo'];
                $item->vlpago = $list_item['vlpago'];
                $item->variacao = $list_item['variacao'];
                $item->descricao = $list_item['descricao'];
                
                $row = $this->detail_list->addItem( $item );
                $row->onmouseover='';
                $row->onmouseout='';
            }

            $this->form->setFields( $this->formFields );
        }
        
        $this->loaded = TRUE;
    }
    
    /**
     * Load Master/Detail data from database to form/session
     */
    public function onEdit($param)
    {
        try
        {
            TTransaction::open(TSession::getValue('banco'));
            
            if (isset($param['key']))
            {
                $key = $param['key'];
                
                $object = new Cheque($key);
                $items  = ChequeItem::where('cheque_id', '=', $key)->load();
                
                $session_items = array();
                foreach( $items as $item )
                {
                    $item_key = $item->id;
                    $session_items[$item_key] = $item->toArray();
                    $session_items[$item_key]['id'] = $item->id;
                    $session_items[$item_key]['banco_id'] = $item->banco_id;
                    $session_items[$item_key]['contacorrente_id'] = $item->contacorrente_id;
                    $session_items[$item_key]['sequencia'] = $item->sequencia;
                    $session_items[$item_key]['vltitulo'] = $item->vltitulo;
                    $session_items[$item_key]['vlpago'] = $item->vlpago;
                    $session_items[$item_key]['variacao'] = $item->variacao;
                    $session_items[$item_key]['descricao'] = $item->descricao;
                }
                TSession::setValue(__CLASS__.'_items', $session_items);
                
                $bancos  = Banco::where('codigo', '=', $object->banco_id)->load();
                
                $obj = new StdClass;
                
                foreach ($bancos as $banco)
                {
                    $obj->banco = $banco->nome;
                }
                
                TForm::sendData('form_Cheque', $obj);
                
                if ($object->dtcheque !== NULL) {
                    $date = new DateTime($object->dtcheque);
                    $object->dtcheque = $date->format('d/m/Y');
                }
                
                $object->valor = number_format($object->valor, 2, ',', '.');
                
                if ($object->dtprepago !== NULL) {
                    $date = new DateTime($object->dtprepago);
                    $object->dtprepago = $date->format('d/m/Y');
                }
                
                $this->form->setData($object); // fill the form with the active record data
                $this->onReload( $param ); // reload items list
                TTransaction::close(); // close transaction
            }
            else
            {
                $this->form->clear();                
                TSession::setValue(__CLASS__.'_items', null);
                $this->onReload( $param );
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    /**
     * Save the Master/Detail data from form/session to database
     */
    public function onSave()
    {
        try
        {
            // open a transaction with database
            TTransaction::open(TSession::getValue('banco'));
            
            $data = $this->form->getData();
            
            $data->dtcheque = TDate::date2us($data->dtcheque);
            $data->valor = FuncoesExtras::retiraFormatacao($data->valor);            
            $data->dtprepago = TDate::date2us($data->dtprepago);
            
            $master = new Cheque;
            $master->fromArray( (array) $data);
            $this->form->validate(); // form validation
            
            $master->store(); // save master object
            // delete details
            $old_items = ChequeItem::where('cheque_id', '=', $master->id)->load();
            
            $keep_items = array();
            
            // get session items
            $items = TSession::getValue(__CLASS__.'_items');
            
            if( $items )
            {
                foreach( $items as $item )
                {
                    if (substr($item['id'],0,1) == 'X' ) // new record
                    {
                        $detail = new ChequeItem;
                    }
                    else
                    {
                        $detail = ChequeItem::find($item['id']);
                    }
                    $detail->banco_id  = $item['banco_id'];
                    $detail->contacorrente_id  = $item['contacorrente_id'];
                    $detail->sequencia  = $item['sequencia'];
                    $detail->vltitulo  = $item['vltitulo'];
                    $detail->vlpago  = $item['vlpago'];
                    $detail->variacao  = $item['variacao'];
                    $detail->descricao  = $item['descricao'];
                    $detail->cheque_id = $master->id;
                    $detail->store();
                    
                    $keep_items[] = $detail->id;
                }
            }
            
            if ($old_items)
            {
                foreach ($old_items as $old_item)
                {
                    if (!in_array( $old_item->id, $keep_items))
                    {
                        $old_item->delete();
                    }
                }
            }
            TTransaction::close(); // close the transaction
            
            // reload form and session items
            $this->onEdit(array('key'=>$master->id));
            
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback();
        }
    }
    
    /**
     * Show the page
     */
    public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR $_GET['method'] !== 'onReload') )
        {
            $this->onReload( func_get_arg(0) );
        }
        parent::show();
    }
}
?>