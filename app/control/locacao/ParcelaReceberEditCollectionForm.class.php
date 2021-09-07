<?php
/**
 * ParcelaReceberForm Form
 * @author  <your name here>
 */
class ParcelaReceberEditCollectionForm extends TWindow
{
    protected $form; // form
    
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        parent::include_js('app/lib/include/application.js');
        parent::setTitle('Lançamento de Valores para Mais Parcelass');
        
        // creates the form
        $this->form = new TQuickForm('form_ParcelaReceberCollection');
        $this->form->class = 'tform'; // change CSS class        
        $this->form->style = 'display: table;width:100%'; // change style
        //$this->form->setFormTitle('ParcelaReceber');
        $this->form->setFieldsByRow(2);

        // create the form fields
        $id = new THidden('id');
        $contrato_id = new TSeekButton('contrato_id');
        $parcela_de = new TSeekButton('parcela_de');
        $parcela_ate = new TEntry('parcela_ate');
        $sequencia = new THidden('sequencia');
        $sequencia1 = new TEntry('sequencia1');
        $sequencia2 = new TEntry('sequencia2');
        $bem_id = new TEntry('bem_id');
        $bem = new TEntry('bem');
        $cliente_id = new TEntry('cliente_id');
        $cliente = new TEntry('cliente');
        $dtinicio = new TDate('dtinicio');
        $dtfim = new TDate('dtfim');
        $dtvencto = new TDate('dtvencto');
        $valor = new TEntry('valor');
        $vlacrescido = new TEntry('vlacrescido');
        $dtpagto = new TDate('dtpagto');
        $numrecibo = new TEntry('numrecibo');
        $vlpagar = new TEntry('vlpagar');
        $vlpago = new TEntry('vlpago');
        $opjuros = new TEntry('opjuros');
        $vljuros = new TEntry('vljuros');
        $opmulta = new TEntry('opmulta');
        $vlmulta = new TEntry('vlmulta');
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
        $opdesc = new TEntry('opdesc');
        $vldesc = new TEntry('vldesc');
        $opgas = new TEntry('opgas');
        $vlgas = new TEntry('vlgas');
        $cliente2_id = new TEntry('cliente2_id');
        $observacao = new TText('observacao');
        
        $obj = new ContratoSelectionList;
        $action = new TAction(array($obj, 'onReload'));
        $contrato_id->setAction($action);
        
        $obj = new ParcelaReceberSelectionList;
        $action = new TAction(array($obj, 'onSetContrato'));
        $parcela_de->setAction($action);
        
        // Campos Não Editáveis
        //$contrato_id->setEditable(FALSE);
        //$parcelas->setEditable(FALSE); 
        $sequencia1->setEditable(FALSE);
        $sequencia2->setEditable(FALSE);
        $bem_id->setEditable(FALSE);
        $bem->setEditable(FALSE);
        $cliente_id->setEditable(FALSE);
        $cliente->setEditable(FALSE);
        $vlpagar->setEditable(FALSE);
        $opjuros->setEditable(FALSE);
        $opmulta->setEditable(FALSE);
        $opdesc->setEditable(FALSE);
        
        // Máscaras
        $dtvencto->setMask('dd/mm/yyyy');
        $dtpagto->setMask('dd/mm/yyyy');
        
        // Formatação para Valores Monetário
        $valor->setNumericMask(2, ',', '.');
        $vlacrescido->setNumericMask(2, ',', '.');
        $vlpagar->setNumericMask(2, ',', '.');
        $vlpago->setNumericMask(2, ',', '.');
        $vljuros->setNumericMask(2, ',', '.');
        $vlmulta->setNumericMask(2, ',', '.');
        $vlseguro->setNumericMask(2, ',', '.');
        $vlcondominio->setNumericMask(2, ',', '.');
        $vlluz->setNumericMask(2, ',', '.');
        $vlagua->setNumericMask(2, ',', '.');
        $vliptu->setNumericMask(2, ',', '.');
        $vldesc->setNumericMask(2, ',', '.');
        $vlgas->setNumericMask(2, ',', '.');
        
        // Tamanho dos Campos no formulário
        $sequencia1->setSize(25);
        $sequencia2->setSize(25);
        $bem_id->setSize(50);
        $bem->setSize(277);
        $cliente_id->setSize(50);
        $cliente->setSize(277);
        $opjuros->setSize(25);
        $vljuros->setSize(172);
        $opmulta->setSize(25);
        $vlmulta->setSize(172);
        $opseguro->setSize(25);
        $vlseguro->setSize(172);
        $opcondominio->setSize(25);
        $vlcondominio->setSize(172);
        $opluz->setSize(25);
        $vlluz->setSize(172);
        $opagua->setSize(25);
        $vlagua->setSize(172);
        $opiptu->setSize(25);
        $vliptu->setSize(172);
        $opdesc->setSize(25);
        $vldesc->setSize(172);
        $opgas->setSize(25);
        $vlgas->setSize(172);
        
        // Adiciona validação aos campos
        $contrato_id->addValidation('Contrato', new TRequiredValidator);
        $parcela_de->addValidation('Parcelas', new TRequiredValidator);
        $parcela_ate->addValidation('Até', new TRequiredValidator);
        $opseguro->addValidation('Seguro', new TRequiredValidator);
        $vlseguro->addValidation('Seguro', new TRequiredValidator);
        $opcondominio->addValidation('Condomínio', new TRequiredValidator);
        $vlcondominio->addValidation('Condomínio', new TRequiredValidator);
        $opluz->addValidation('Luz', new TRequiredValidator);
        $vlluz->addValidation('Luz', new TRequiredValidator);
        $opagua->addValidation('Água', new TRequiredValidator);
        $vlagua->addValidation('Água', new TRequiredValidator);
        $opgas->addValidation('Gás', new TRequiredValidator);
        $vlgas->addValidation('Gás', new TRequiredValidator);
        $opiptu->addValidation('IPTU', new TRequiredValidator);
        $vliptu->addValidation('IPTU', new TRequiredValidator);
        
        $contrato_id->setExitAction(new TAction(array($this, 'onExitContrato')));

        // add the fields
        $this->form->addQuickField('Contrato', $contrato_id,  50 );
        //$this->form->addQuickFields('Bem', array($bem_id, $bem) );
        $this->form->addQuickFields('Locatário', array($cliente_id, $cliente) );
        $this->form->addQuickField('Parcela', $parcela_de,  50 );
        $this->form->addQuickField('Até', $parcela_ate,  50 );
        /*$this->form->addQuickFields('Sequência', array($sequencia1, $sequencia2) );
        $this->form->addQuickField('Data Início', $dtinicio,  200 );
        $this->form->addQuickField('Data Fim', $dtfim,  200 );
        $this->form->addQuickField('Data Vencto', $dtvencto,  100 );
        $this->form->addQuickField('Valor Parcela', $vlacrescido,  200 );
        $this->form->addQuickField('Valor até Vencimento', $valor,  200 );
        $this->form->addQuickField('Data Pagamento', $dtpagto,  100 );
        $this->form->addQuickField('Numrecibo', $numrecibo,  200 );
        $this->form->addQuickField('Valor a Pagar', $vlpagar,  200 );
        $this->form->addQuickFields('Juros', array($opjuros, $vljuros) );
        $this->form->addQuickFields('Multa', array($opmulta, $vlmulta) );*/
        $this->form->addQuickFields('Seguro', array($opseguro, $vlseguro) );
        $this->form->addQuickFields('Condomínio', array($opcondominio, $vlcondominio) );
        $this->form->addQuickFields('Luz', array($opluz, $vlluz) );
        $this->form->addQuickFields('Água', array($opagua, $vlagua) );
        $this->form->addQuickFields('Gás', array($opgas, $vlgas) );
        $this->form->addQuickFields('IPTU', array($opiptu, $vliptu) );
        /*$this->form->addQuickFields('Desconto', array($opdesc, $vldesc) );
        $this->form->addQuickField('Valor Pago', $vlpago,  200 );
        $this->form->addQuickField('Cliente2 Id', $cliente2_id,  200 );
        $this->form->addQuickField('Observacao', $observacao );*/
        
        /** samples
         $this->form->addQuickFields('Date', array($date1, new TLabel('to'), $date2)); // side by side fields
         $fieldX->addValidation( 'Field X', new TRequiredValidator ); // add validation
         $fieldX->setSize( 100, 40 ); // set size
         **/
         
        // create the form actions
        $this->form->addQuickAction(_t('Save'), new TAction(array($this, 'onSave')), 'fa:floppy-o');
        //$this->form->addQuickAction(_t('Back'),new TAction(array('ParcelaReceberEditForm','onEdit')),'fa:arrow-left blue');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        //$container->add(new TXMLBreadCrumb('menu2.xml', 'ParcelaReceberEditForm'));
        $container->add($this->form);
        
        parent::add($container);
    }

    /**
     * Save form data
     * @param $param Request
     */
    public function onSave( $param )
    {        
        try
        {
            TTransaction::open(TSession::getValue('banco'));
            
            $this->form->validate(); // validate form data
            
            $data = $this->form->getData(); // get form data as array
            
            $data->vlseguro = FuncoesExtras::retiraFormatacao($data->vlseguro);
            $data->vlcondominio = FuncoesExtras::retiraFormatacao($data->vlcondominio);
            $data->vlluz = FuncoesExtras::retiraFormatacao($data->vlluz);
            $data->vlagua = FuncoesExtras::retiraFormatacao($data->vlagua);
            $data->vlgas = FuncoesExtras::retiraFormatacao($data->vlgas);
            $data->vliptu = FuncoesExtras::retiraFormatacao($data->vliptu);
            
            for ($i = $param['parcela_de']; $i <= $param['parcela_ate']; $i++)
            {
                $repository = new TRepository('ParcelaReceber');
                $criteria = new TCriteria;
                $criteria->add(new TFilter('contrato_id','=',$param['contrato_id']));
                $criteria->add(new TFilter('numero','=',$i));
                $objects = $repository->load($criteria, FALSE);
                
                foreach ($objects as $object)
                {
                    $parcela = new ParcelaReceber($object->id);                    
                    $parcela->fromArray( (array) $data);
                    $parcela->store();   
                }
            }
            
            $data->vlseguro = number_format($data->vlseguro, 2, ',', '.');
            $data->vlcondominio = number_format($data->vlcondominio, 2, ',', '.');
            $data->vlluz = number_format($data->vlluz, 2, ',', '.');
            $data->vlagua = number_format($data->vlagua, 2, ',', '.');
            $data->vlgas = number_format($data->vlgas, 2, ',', '.');
            $data->vliptu = number_format($data->vliptu, 2, ',', '.');
            
            $this->form->setData($data); // fill form data            
            TTransaction::close(); // close the transaction
            
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
        
        /*$parcelas_id = $param['parcelas_id'];
        $parcelas_id = explode(',', $parcelas_id);
    
        try
        {
            TTransaction::open(TSession::getValue('banco')); // open a transaction
            
            $this->form->validate(); // validate form data
            
            $data = $this->form->getData(); // get form data as array
            
            $data->vlseguro = FuncoesExtras::retiraFormatacao($data->vlseguro);
            $data->vlcondominio = FuncoesExtras::retiraFormatacao($data->vlcondominio);
            $data->vlluz = FuncoesExtras::retiraFormatacao($data->vlluz);
            $data->vlagua = FuncoesExtras::retiraFormatacao($data->vlagua);
            $data->vlgas = FuncoesExtras::retiraFormatacao($data->vlgas);
            $data->vliptu = FuncoesExtras::retiraFormatacao($data->vliptu);
            
            foreach ($parcelas_id as $parcela_id)
            {
                $object = new ParcelaReceber();
                
                $data->id = $parcela_id;
                
                $object->fromArray( (array) $data); // load the object with data
                $object->store(); // save the object
            }
            
            $data->vlseguro = number_format($data->vlseguro, 2, ',', '.');
            $data->vlcondominio = number_format($data->vlcondominio, 2, ',', '.');
            $data->vlluz = number_format($data->vlluz, 2, ',', '.');
            $data->vlagua = number_format($data->vlagua, 2, ',', '.');
            $data->vlgas = number_format($data->vlgas, 2, ',', '.');
            $data->vliptu = number_format($data->vliptu, 2, ',', '.');
            
            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }*/
    }
    
    /**
     * Load object to form data
     * @param $param Request
     */
    public function onEdit( $param )
    {
        $contrato_id = isset($param['contrato_id']) ? $param['contrato_id'] : NULL;
        
        if ($contrato_id !== NULL)
        {
            try
            {
                TTransaction::open(TSession::getValue('banco'));
                
                $contrato = new Contrato($contrato_id);
                $bem = new Bem($contrato->bem_id);
                $cliente = new Cliente($contrato->cliente_id);
                
                TTransaction::close();
            }
            catch (Exception $e) // in case of exception
            {
                new TMessage('error', $e->getMessage()); // shows the exception error message
                TTransaction::rollback(); // undo all pending operations
            }
        }
        
        $obj = new StdClass;
        $obj->contrato_id = $contrato->id;
        $obj->bem_id = $bem->id;
        $obj->bem = $bem->descricao;
        $obj->cliente_id = $cliente->id;
        $obj->cliente = $cliente->nome;
        $obj->parcelas = $param['numero'];
        $obj->parcelas_id = $paam['numero'];
        $obj->opseguro = '-';
        $obj->vlseguro = '0,00';
        $obj->opcondominio = '-';
        $obj->vlcondominio = '0,00';
        $obj->opluz = '-';
        $obj->vlluz = '0,00';
        $obj->opagua = '-';
        $obj->vlagua = '0,00';
        $obj->opgas = '-';
        $obj->vlgas = '0,00';
        $obj->opiptu = '-';
        $obj->vliptu = '0,00';           
                                 
        TForm::sendData('form_ParcelaReceberCollection', $obj);
        
        /*if ($param)
        {
            $selected = array();
            $parcelas = array();
            $parcelas_id = array();
            $primeiro = TRUE;
            
            // get the record id's
            foreach ($param as $index => $check)
            {
                if ($check == 'on')
                {
                    $id = substr($index,5);
                    
                    try
                    {
                        TTransaction::open(TSession::getValue('banco'));                 
                        $parcela = new ParcelaReceber($id);
                        $contrato = new Contrato($parcela->contrato_id);
                        $bem = new Bem($contrato->bem_id);
                        $cliente = new Cliente($contrato->cliente_id);
                        
                        if ($primeiro)
                        {
                            $parcelas = $parcela->numero;
                            $parcelas_id = $id;
                            $primeiro = FALSE;
                        }
                        else
                        {
                            $parcelas .= ',' . $parcela->numero;
                            $parcelas_id .= ',' . $id;
                        }
                        
                        TTransaction::close();
                        
                        $selecteds[] = $id;
                    }
                    catch (Exception $e) // in case of exception
                    {
                        new TMessage('error', $e->getMessage()); // shows the exception error message
                        TTransaction::rollback(); // undo all pending operations
                    }
                    
                }
            }
            
            $obj = new StdClass;
            $obj->contrato_id = $contrato->id;
            $obj->bem_id = $bem->id;
            $obj->bem = $bem->descricao;
            $obj->cliente_id = $cliente->id;
            $obj->cliente = $cliente->nome;
            $obj->parcelas = $parcelas;
            $obj->parcelas_id = $parcelas_id;
            $obj->opseguro = '-';
            $obj->vlseguro = '0,00';
            $obj->opcondominio = '-';
            $obj->vlcondominio = '0,00';
            $obj->opluz = '-';
            $obj->vlluz = '0,00';
            $obj->opagua = '-';
            $obj->vlagua = '0,00';
            $obj->opgas = '-';
            $obj->vlgas = '0,00';
            $obj->opiptu = '-';
            $obj->vliptu = '0,00';           
                                 
            TForm::sendData('form_ParcelaReceberCollection', $obj);
        }*/
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
                $bem = new Bem($contrato->bem_id);
                $cliente = new Cliente($contrato->cliente_id);
                
                $obj = new StdClass;
                $obj->bem_id = $bem->id;
                $obj->bem = $bem->descricao;
                $obj->cliente_id = $cliente->id;
                $obj->cliente = $cliente->nome;
                $obj->opseguro = '-';
                $obj->vlseguro = '0,00';
                $obj->opcondominio = '-';
                $obj->vlcondominio = '0,00';
                $obj->opluz = '-';
                $obj->vlluz = '0,00';
                $obj->opagua = '-';
                $obj->vlagua = '0,00';
                $obj->opgas = '-';
                $obj->vlgas = '0,00';
                $obj->opiptu = '-';
                $obj->vliptu = '0,00';           
                                         
                TForm::sendData('form_ParcelaReceberCollection', $obj);
                
                TTransaction::close();
            }
            catch (Exception $e)
            {
                new TMessage('error', $e->getMessage()); // shows the exception error message
                TTransaction::rollback(); // undo all pending operations       
            }
        }
    }
}
?>