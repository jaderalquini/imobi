<?php
/**
 * ParcelaPagarForm Form
 * @author  <your name here>
 */
class ParcelaPagarPayForm extends TPage
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
        
        // creates the form
        $this->form = new TQuickForm('form_ParcelaPagar');
        $this->form->class = 'tform'; // change CSS class        
        $this->form->style = 'display: table;width:100%'; // change style
        //$this->form->setFormTitle('ParcelaPagar');
        $this->form->setFieldsByRow(2);

        // create the form fields
        $id = new THidden('id');
        $contrato_id = new TSeekButton('contrato_id');
        $numero = new TSeekButton('numero');
        $sequencia = new THidden('sequencia');
        $sequencia1 = new TEntry('sequencia1');
        $sequencia2 = new TEntry('sequencia2');
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
        $numrecpro = new THidden('numrecpro');
        $opdevolucao = new TEntry('opdevolucao');
        $vldevolucao = new TEntry('vldevolucao');
        $opgas = new TEntry('opgas');
        $vlgas = new TEntry('vlgas');
        $observacao = new TText('observacao');
        
        $obj = new ContratoSelectionList;
        $action = new TAction(array($obj, 'onReload'));
        $contrato_id->setAction($action);
        
        $obj = new ParcelaPagarSelectionList;
        $action = new TAction(array($obj, 'onSetContrato'));
        $numero->setAction($action);
        
        // Campos Não Editáveis
        //$contrato_id->setEditable(FALSE);
        //$numero->setEditable(FALSE); 
        $sequencia1->setEditable(FALSE);
        $sequencia2->setEditable(FALSE);
        $bem_id->setEditable(FALSE);
        $bem->setEditable(FALSE);
        $proprietario_id->setEditable(FALSE);
        $proprietario->setEditable(FALSE);
        $opcomissao->setEditable(FALSE);
        $opdevolucao->setEditable(FALSE);
        $observacao->setEditable(FALSE);
        
        // Máscaras
        $dtvencto->setMask('dd/mm/yyyy');
        $dtpagto->setMask('dd/mm/yyyy');
        
        // Formatação para Valores Monetário
        $valor->setNumericMask(2, ',', '.');
        $vlpago->setNumericMask(2, ',', '.');
        $vlcomissao->setNumericMask(2, ',', '.');
        $vlseguro->setNumericMask(2, ',', '.');
        $vlcondominio->setNumericMask(2, ',', '.');
        $vlluz->setNumericMask(2, ',', '.');
        $vlagua->setNumericMask(2, ',', '.');
        $vliptu->setNumericMask(2, ',', '.');
        $vldevolucao->setNumericMask(2, ',', '.');
        $vlgas->setNumericMask(2, ',', '.');
        $vloutro->setNumericMask(2, ',', '.');
        
        // Tamanho dos Campos no formulário
        $sequencia1->setSize(25);
        $sequencia2->setSize(50);
        $bem_id->setSize(50);
        $bem->setSize(277);
        $proprietario_id->setSize(50);
        $proprietario->setSize(277);
        $opcomissao->setSize(25);
        $vlcomissao->setSize(172);
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
        $opdevolucao->setSize(25);
        $vldevolucao->setSize(172);
        $opgas->setSize(25);
        $vlgas->setSize(172);
        $opoutro->setSize(25);
        $vloutro->setSize(172);
        
        // Define actions dos campos
        $contrato_id->setExitAction(new TAction(array($this, 'onExitContrato')));
        $numero->setExitAction(new TAction(array($this, 'onExitParcela')));
        $valor->setExitAction(new TAction(array($this, 'onExitValor')));
        $opcomissao->setExitAction(new TAction(array($this, 'onExitValor')));
        $vlcomissao->setExitAction(new TAction(array($this, 'onExitValor')));
        $opseguro->setExitAction(new TAction(array($this, 'onExitValor')));
        $vlseguro->setExitAction(new TAction(array($this, 'onExitValor')));
        $opcondominio->setExitAction(new TAction(array($this, 'onExitValor')));
        $vlcondominio->setExitAction(new TAction(array($this, 'onExitValor')));
        $opluz->setExitAction(new TAction(array($this, 'onExitValor')));
        $vlluz->setExitAction(new TAction(array($this, 'onExitValor')));
        $opagua->setExitAction(new TAction(array($this, 'onExitValor')));
        $vlagua->setExitAction(new TAction(array($this, 'onExitValor')));
        $opgas->setExitAction(new TAction(array($this, 'onExitValor')));
        $vlgas->setExitAction(new TAction(array($this, 'onExitValor')));
        $opiptu->setExitAction(new TAction(array($this, 'onExitValor')));
        $vliptu->setExitAction(new TAction(array($this, 'onExitValor')));
        $vldevolucao->setExitAction(new TAction(array($this, 'onExitValor')));
        $opoutro->setExitAction(new TAction(array($this, 'onExitValor')));
        $vloutro->setExitAction(new TAction(array($this, 'onExitValor')));
        
        // Adiciona validação aos campos
        $valor->addValidation('Valor Aluguel', new TRequiredValidator);
        $vlcomissao->addValidation('Comissão', new TRequiredValidator);
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

        // add the fields        
        $this->form->addQuickField('Contrato', $contrato_id,  50 );
        $this->form->addQuickFields('Bem', array($bem_id, $bem) );
        $this->form->addQuickFields('Locador', array($proprietario_id, $proprietario) );
        $this->form->addQuickField('Parcela', $numero,  50 );
        $this->form->addQuickFields('Sequência', array($sequencia1, $sequencia2) );
        /*$this->form->addQuickField('Cliente Id', $cliente_id,  200 );        
        $this->form->addQuickField('Dtinicio', $dtinicio,  200 );
        $this->form->addQuickField('Dtfim', $dtfim,  200 );*/
        $this->form->addQuickField('Data Vencimento', $dtvencto,  100 );
        $this->form->addQuickField('Data Pagamento', $dtpagto,  100 );
        $this->form->addQuickField('Valor Aluguel', $valor,  200 );        
        //$this->form->addQuickField('Numrecibo', $numrecibo,  200 );
        $this->form->addQuickFields('Comissão', array($opcomissao, $vlcomissao) );
        $this->form->addQuickFields('Seguro', array($opseguro, $vlseguro) );
        $this->form->addQuickFields('Condomínio', array($opcondominio, $vlcondominio) );
        $this->form->addQuickFields('Luz', array($opluz, $vlluz) );
        $this->form->addQuickFields('Água', array($opagua, $vlagua) );
        $this->form->addQuickFields('IPTU', array($opiptu, $vliptu) );
        $this->form->addQuickFields('Gás', array($opgas, $vlgas) );        
        $this->form->addQuickFields('Outros', array($opoutro, $vloutro) );
        $this->form->addQuickFields('Devoluções', array($opdevolucao, $vldevolucao) );
        $this->form->addQuickField('Total Líquido', $vlpago,  200 );
        $this->form->addQuickField('Observacao', $observacao,  200 );        
        $this->form->addQuickField('', $id );
        $this->form->addQuickField('', $sequencia);
        $this->form->addQuickField('', $numrecpro );
        
        /** samples
         $this->form->addQuickFields('Date', array($date1, new TLabel('to'), $date2)); // side by side fields
         $fieldX->addValidation( 'Field X', new TRequiredValidator ); // add validation
         $fieldX->setSize( 100, 40 ); // set size
         **/
         
        // create the form actions
        $this->form->addQuickAction(_t('Save'), new TAction(array($this, 'onSave')), 'fa:floppy-o');
        //$this->form->addQuickAction(_t('Back to the listing'),new TAction(array('ParcelaPagarList','onSetContrato')),'fa:table blue');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }

    /**
     * Save form data
     * @param $param Request
     */
    public function onSave( $param )
    {
        //new TMessage('info', json_encode($param));
        try
        {
            TTransaction::open(TSession::getValue('banco')); // open a transaction
            
            /**
            // Enable Debug logger for SQL operations inside the transaction
            TTransaction::setLogger(new TLoggerSTD); // standard output
            TTransaction::setLogger(new TLoggerTXT('log.txt')); // file
            **/
            
            $this->form->validate(); // validate form data
            
            $object = new ParcelaPagar;  // create an empty object
            $data = $this->form->getData(); // get form data as array
            
            $data->dtvencto = TDate::date2us($data->dtvencto);
            $data->valor = FuncoesExtras::retiraFormatacao($data->valor);
            if ($data->dtpagto == NULL)
            {
                $data->dtpagto = 0;
            }
            else
            {
                $data->dtpagto = TDate::date2us($data->dtpagto);
            }
            $data->vlcomissao = FuncoesExtras::retiraFormatacao($data->vlcomissao);
            $data->vlseguro = FuncoesExtras::retiraFormatacao($data->vlseguro);
            $data->vlcondominio = FuncoesExtras::retiraFormatacao($data->vlcondominio);
            $data->vlluz = FuncoesExtras::retiraFormatacao($data->vlluz);
            $data->vlagua = FuncoesExtras::retiraFormatacao($data->vlagua);
            $data->vlgas = FuncoesExtras::retiraFormatacao($data->vlgas);
            $data->vliptu = FuncoesExtras::retiraFormatacao($data->vliptu);
            $data->vloutro = FuncoesExtras::retiraFormatacao($data->vloutro);
            $data->vldevolucao = FuncoesExtras::retiraFormatacao($data->vldevolucao);
            $data->vlpago = FuncoesExtras::retiraFormatacao($data->vlpago);
            
            if (intval($data->numrecpro) == 0)
            {
                $objsec = new Sequencia(1);
                $numrecpro = $objsec->numrecpro + 1;
                
                $sequencia = array();
                $sequencia['id'] = 1;
                $sequencia['numrecpro'] = $numrecpro;
                
                $objsec->fromArray( (array) $sequencia );
                $objsec->store();
                
                $data->numrecpro = $numrecpro;
            }
            
            /*$dataenv = array();
            $dataenv = (array) $data;
            
            $dataenv['opcomissao'] = TSession::getValue('opcomissao');
            $dataenv['vlcomissao'] = TSession::getValue('vlcomissao');
            $dataenv['opseguro'] = TSession::getValue('opseguro');
            $dataenv['vlseguro'] = TSession::getValue('vlseguro');
            $dataenv['opcondominio'] = TSession::getValue('opcondominio');
            $dataenv['vlcondominio'] = TSession::getValue('vlcondominio');
            $dataenv['opluz'] = TSession::getValue('opluz');
            $dataenv['vlluz'] = TSession::getValue('vlluz');
            $dataenv['opagua'] = TSession::getValue('opagua');
            $dataenv['vlagua'] = TSession::getValue('vlagua');
            $dataenv['opgas'] = TSession::getValue('opgas');
            $dataenv['vlgas'] = TSession::getValue('vlgas');
            $dataenv['opiptu'] = TSession::getValue('opiptu');
            $dataenv['vliptu'] = TSession::getValue('vliptu');
            $dataenv['opoutro'] = TSession::getValue('opoutro');
            $dataenv['vloutro'] = TSession::getValue('vloutro');
            $dataenv['opdevolucao'] = TSession::getValue('opdevolucao');
            $dataenv['vldevolucao'] = TSession::getValue('vldevolucao');*/
            
            if (intval($data->vlcomissao) == 0)
            {
                $data->vlcomissao = TSession::getValue('vlcomissao');
            }
            
            if (intval($data->vlseguro) == 0)
            {
                $data->vlseguro = TSession::getValue('vlseguro');
            }
            
            if (intval($data->vlcondominio) == 0)
            {
                $data->vlcondominio = TSession::getValue('vlcondominio');
            }
            
            if (intval($data->vlluz) == 0)
            {
                $data->vlluz = TSession::getValue('vlluz');
            }
            
            if (intval($data->vlagua) == 0)
            {
                $data->vlagua = TSession::getValue('vlagua');
            }
            
            if (intval($data->vlgas) == 0)
            {
                $data->vlgas = TSession::getValue('vlgas');
            }
            
            if (intval($data->vliptu) == 0)
            {
                $data->vliptu = TSession::getValue('vliptu');
            }
            
            if (intval($data->vloutro) == 0)
            {
                $data->vloutro = TSession::getValue('vloutro');
            }
            
            if (intval($data->vldevolucao) == 0)
            {
                $data->vldevolucao = TSession::getValue('vldevolucao');
            }
            
            //new TMessage('info', json_encode($data));
            $object->fromArray( (array) $data ); // load the object with data
            $object->store(); // save the object
            
            // get the generated id
            $data->id = $object->id;
            
            $data->dtvencto = TDate::date2br($data->dtvencto);
            $data->valor = number_format($data->valor, 2, ',', '.');
            if ($data->dtpagto == NULL)
            {
                $data->dtpagto = 0;
            }
            else
            {
                $data->dtpagto = TDate::date2br($data->dtpagto);
            }
            $data->vlcomissao = number_format($data->vlcomissao, 2, ',', '.');
            $data->vlseguro = number_format($data->vlseguro, 2, ',', '.');
            $data->vlcondominio = number_format($data->vlcondominio, 2, ',', '.');
            $data->vlluz = number_format($data->vlluz, 2, ',', '.');
            $data->vlagua = number_format($data->vlagua, 2, ',', '.');
            $data->vlgas = number_format($data->vlgas, 2, ',', '.');
            $data->vliptu = number_format($data->vliptu, 2, ',', '.');
            $data->vloutro = number_format($data->vloutro, 2, ',', '.');
            $data->vldevolucao = number_format($data->vldevolucao, 2, ',', '.');
            $data->vlpago = number_format($data->vlpago, 2, ',', '.');
            
            $sequencia = new Sequencia(1);
            
            //$this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            
            $form = new TQuickForm('input_form');
            $form->style = 'padding:20px';
            
            $obsrecibopagar = new TText('obsrecibopagar');
            $obsrecibopagar->setValue($sequencia->obsrecibopagar);
            
            $form->addQuickField('Observação', $obsrecibopagar);
            
            $obsrecibopagar->setSize(400);
            
            $print = new TAction(array($this, 'setPrint'));
            //$param = (array) $data;
            $print->setParameters( $param );
            
            $form->addQuickAction('Sim', $print, 'fa:check green');
            $form->addQuickAction('Não', new TAction(array($this, 'noPrint')), 'bs:ban-circle red');
            
            // show the input dialog
            new TInputDialog('Parcela Paga. Imprimir Recibo?', $form);
            
            /*$print = new TAction(array($this, 'onPrint'));
            $param = (array) $data;
            $print->setParameters( $param );
            
            //new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
            new TQuestion('Parcela Paga. Imprimir Recibo?', $print);
            //parent::add(new TAlert('info', 'Parcela já Paga'));*/
            
            $this->form->clear();
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    /**
     * Load object to form data
     * @param $param Request
     */
    public function onEdit( $param )
    {
        try
        {
            TTransaction::open(TSession::getValue('banco')); // open a transaction
            $key = isset($param['key']) ? $param['key'] : $param['id'];  // get the parameter $key
            
            $object = new ParcelaPagar($key); // instantiates the Active Record
            
            /*TSession::setValue('opcomissao', $object->opcomissao);
            TSession::setValue('vlcomissao', $object->vlcomissao);*/
            TSession::setValue('opseguro', $object->opseguro);
            TSession::setValue('vlseguro', $object->vlseguro);
            TSession::setValue('opcondominio', $object->opcondominio);
            TSession::setValue('vlcondominio', $object->vlcondominio);
            TSession::setValue('opluz', $object->opluz);
            TSession::setValue('vlluz', $object->vlluz);
            TSession::setValue('opagua', $object->opagua);
            TSession::setValue('vlagua', $object->vlagua);
            TSession::setValue('opiptu', $object->opiptu);
            TSession::setValue('vliptu', $object->vliptu);
            TSession::setValue('opgas', $object->opgas);
            TSession::setValue('vlgas', $object->vlgas);
            TSession::setValue('opoutro', $object->opoutro);
            TSession::setValue('vloutro', $object->vloutro);
            TSession::setValue('opdevolucao', $object->opdevolucao);
            TSession::setValue('vldevolucao', $object->vldevolucao);
            
            $object->dtvencto = TDate::date2br($object->dtvencto); 
            $object->dtpagto = TDate::date2br($object->dtpagto);           
            if (($object->dtpagto == 0 || $object->dtpagto !== NULL) && $object->vlpago > 0)
            {
                new TMessage('info', 'Parcela já Paga');
                //parent::add(new TAlert('info', 'Parcela já Paga'));
                TSession::setValue('dtpagto', $object->dtpagto);
            }
            else
            {
                TSession::setValue('dtpagto', NULL);
            }
            TText::disableField('form_ParcelaPagar', 'observacao');
                
            $valor = $object->valor;
            $vlpago = $valor; 
                
            $vlpago = $vlpago - $object->vlcomissao;
                
            if ($object->opseguro == '+')
            {
                $vlpago = $vlpago + $object->vlseguro;
            }
            else
            {
                $vlpago = $vlpago - $object->vlseguro;
            }
                    
            if ($object->opcondominio == '+')
            {
                $vlpago = $vlpago + $object->vlcondominio;
            }
            else
            {
                $vlpago = $vlpago - $object->vlcondominio;
            }
                    
            if ($object->opluz == '+')
            {
                $vlpago = $vlpago + $object->vlluz;
            }
            else
            {
                $vlpago = $vlpago - $object->vlluz;
            }
                    
            if ($object->opagua == '+')
            {
                $vlpago = $vlpago + $object->vlagua;
            }
            else
            {
                $vlpago = $vlpago - $object->vlagua;
            }
                    
            if ($object->opgas == '+')
            {
                $vlpago = $vlpago + $object->vlgas;
            }
            else
            {
                $vlpago = $vlpago - $object->vlgas;
            }
                    
            if ($object->opiptu == '+')
            {
                $vlpago = $vlpago + $object->vliptu;
            }
            else
            {
                $vlpago = $vlpago - $object->vliptu;
            }
            
            if ($object->opoutro == '+')
            {
                $vlpago = $vlpago + $object->vloutro;
            }    
            else
            {
                $vlpago = $vlpago - $object->vloutro;
            }
                
            $vlpago = $vlpago + $object->vldevolucao;
            
            $object->valor = number_format($object->valor, 2, ',', '.');
            $object->vlcomissao = number_format($object->vlcomissao, 2, ',', '.');
            $object->vlseguro = number_format($object->vlseguro, 2, ',', '.');
            $object->vlcondominio = number_format($object->vlcondominio, 2, ',', '.');
            $object->vlluz = number_format($object->vlluz, 2, ',', '.');
            $object->vliptu = number_format($object->vliptu, 2, ',', '.');
            $object->vlagua = number_format($object->vlagua, 2, ',', '.');
            $object->vlgas = number_format($object->vlgas, 2, ',', '.');
            $object->vloutro = number_format($object->vloutro, 2, ',', '.');
            $object->vldevolucao = number_format($object->vldesc, 2, ',', '.');
            $object->vloutro = number_format($object->vloutro, 2, ',', '.');
            
            $sequencia = $object->sequencia;
            $tam = strlen($sequencia);
                                      
            $obj = new StdClass;
            $obj->contrato_id = $object->contrato_id;
            $obj->sequencia1 = substr($sequencia, 0, $tam - 2);
            $obj->sequencia2 = substr($sequencia, -2);
            $obj->vlpago = number_format($vlpago, 2, ',', '.');
            if (TSession::getValue('dtpagto') == NULL)
            {
                $obj->dtpagto = date('d/m/Y');
            }
                         
            TForm::sendData('form_ParcelaPagar', $obj);
            
            $this->form->setData($object); // fill the form
            TTransaction::close(); // close the transaction
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    public static function onExitContrato($param)
    {
        $contrato_id = isset($param['contrato_id']) ? $param['contrato_id'] : NULL;
        
        if ($contrato_id !== NULL && $contrato_id !== '')
        {
            try
            {
                TTransaction::open(TSession::getValue('banco'));
                
                $contrato = new Contrato($contrato_id);
                $bem = new Bem($contrato->bem_id);
                $proprietario = new Cliente($bem->proprietario_id);
                
                $obj = new StdClass;
                $obj->bem_id = $contrato->bem_id;
                $obj->bem = $bem->descricao;
                $obj->proprietario_id = $bem->proprietario_id;
                $obj->proprietario = $proprietario->nome;
                
                TForm::sendData('form_ParcelaPagar', $obj);
                
                TTransaction::close();
            }
            catch (Exception $e)
            {
                new TMessage('error', $e->getMessage()); // shows the exception error message
                TTransaction::rollback(); // undo all pending operations       
            }
        }
    }
        
    public static function onExitParcela($param)
    {
        $contrato_id = isset($param['contrato_id']) ? $param['contrato_id'] : NULL;
        $parcela = isset($param['numero']) ? $param['numero'] : NULL;
        
        if ($parcela !== NULL && $parcela !== '')
        {
            try
            {       
                TTransaction::open(TSession::getValue('banco'));
                
                $repository = new TRepository('ParcelaPagar');
                $criteria = new TCriteria;
                $criteria->add(new TFilter('contrato_id','=',$contrato_id)); 
                $criteria->add(new TFilter('numero','=',$parcela));
                $objects = $repository->load($criteria, TRUE);
                
                if ($objects)
                {
                    foreach ($objects as $object)
                    {
                        TSession::setValue('opseguro', $object->opseguro);
                        TSession::setValue('vlseguro', $object->vlseguro);
                        TSession::setValue('opcondominio', $object->opcondominio);
                        TSession::setValue('vlcondominio', $object->vlcondominio);
                        TSession::setValue('opluz', $object->opluz);
                        TSession::setValue('vlluz', $object->vlluz);
                        TSession::setValue('opagua', $object->opagua);
                        TSession::setValue('vlagua', $object->vlagua);
                        TSession::setValue('opiptu', $object->opiptu);
                        TSession::setValue('vliptu', $object->vliptu);
                        TSession::setValue('opgas', $object->opgas);
                        TSession::setValue('vlgas', $object->vlgas);
                        TSession::setValue('opoutro', $object->opoutro);
                        TSession::setValue('vloutro', $object->vloutro);
                        TSession::setValue('opdevolucao', $object->opdevolucao);
                        TSession::setValue('vldevolucao', $object->vldevolucao);
                                  
                        if (($object->dtpagto == 0 || $object->dtpagto !== NULL) && $object->vlpago > 0)
                        {
                            new TMessage('info', 'Parcela já Paga');
                            //parent::add(new TAlert('info', 'Parcela já Paga'));
                            TSession::setValue('dtpagto', $object->dtpagto);
                        }
                        else
                        {
                            TSession::setValue('dtpagto', NULL);
                        }
                        TText::disableField('form_ParcelaPagar', 'observacao');
                            
                        $valor = $object->valor;
                        $vlpago = $valor; 
                            
                        $vlpago = $vlpago - $object->vlcomissao;
                            
                        if ($object->opseguro == '+')
                        {
                            $vlpago = $vlpago + $object->vlseguro;
                        }
                        else
                        {
                            $vlpago = $vlpago - $object->vlseguro;
                        }
                                
                        if ($object->opcondominio == '+')
                        {
                            $vlpago = $vlpago + $object->vlcondominio;
                        }
                        else
                        {
                            $vlpago = $vlpago - $object->vlcondominio;
                        }
                                
                        if ($object->opluz == '+')
                        {
                            $vlpago = $vlpago + $object->vlluz;
                        }
                        else
                        {
                            $vlpago = $vlpago - $object->vlluz;
                        }
                                
                        if ($object->opagua == '+')
                        {
                            $vlpago = $vlpago + $object->vlagua;
                        }
                        else
                        {
                            $vlpago = $vlpago - $object->vlagua;
                        }
                                
                        if ($object->opgas == '+')
                        {
                            $vlpago = $vlpago + $object->vlgas;
                        }
                        else
                        {
                            $vlpago = $vlpago - $object->vlgas;
                        }
                                
                        if ($object->opiptu == '+')
                        {
                            $vlpago = $vlpago + $object->vliptu;
                        }
                        else
                        {
                            $vlpago = $vlpago - $object->vliptu;
                        }
                        
                        if ($object->opoutro == '+')
                        {
                            $vlpago = $vlpago + $object->vloutro;
                        }    
                        else
                        {
                            $vlpago = $vlpago - $object->vloutro;
                        }
                            
                        $vlpago = $vlpago + $object->vldevolucao;
                        
                        $sequencia = $object->sequencia;
                        $tam = strlen($sequencia);
                                                  
                        $obj = new StdClass;
                        $obj->id = $object->id;
                        $obj->sequencia = $object->numero;
                        $obj->sequencia1 = $object->numero;
                        $obj->sequencia2 = substr($sequencia, -2);
                        $obj->dtinicio = TDate::date2br($object->dtinicio);
                        $obj->dtfim = TDate::date2br($object->dtfim);
                        $obj->dtvencto = TDate::date2br($object->dtvencto);
                        $obj->valor = number_format($object->valor, 2, ',', '.');
                        $obj->dtpagto = TDate::date2br($object->dtpagto);
                        $obj->opcomissao = $object->opcomissao;
                        $obj->vlcomissao = number_format($object->vlcomissao, 2, ',', '.');
                        $obj->opseguro = $object->opseguro;
                        $obj->vlseguro = number_format($object->vlseguro, 2, ',', '.');
                        $obj->opcondominio = $object->opcondominio;
                        $obj->vlcondominio = number_format($object->vlcondominio, 2, ',', '.');
                        $obj->opluz = $object->opluz;
                        $obj->vlluz = number_format($object->vlluz, 2, ',', '.');
                        $obj->opiptu = $object->opiptu;
                        $obj->vliptu = number_format($object->vliptu, 2, ',', '.');
                        $obj->opagua = $object->opagua;
                        $obj->vlagua = number_format($object->vlagua, 2, ',', '.');
                        $obj->opgas = $object->opgas;
                        $obj->vlgas = number_format($object->vlgas, 2, ',', '.');
                        $obj->opoutro = $object->opoutro;
                        $obj->vloutro = number_format($object->vloutro, 2, ',', '.');
                        $obj->opdevolucao = $object->opdevolucao;
                        $obj->vldevolucao = number_format($object->vldevolucao, 2, ',', '.');
                        $obj->opseguro = $object->opseguro;
                        $obj->vlpago = number_format($vlpago, 2, ',', '.');
                        if (TSession::getValue('dtpagto') == NULL)
                        {
                            $obj->dtpagto = date('d/m/Y');
                        }
                        $obj->observacao = $object->observacao;
                                     
                        TForm::sendData('form_ParcelaPagar', $obj);
                    }
                }                
                
                TTransaction::close();
            }
            catch (Exception $e)
            {
                new TMessage('error', $e->getMessage()); // shows the exception error message
                TTransaction::rollback(); // undo all pending operations       
            }
        }
    }
    
    public static function onExitValor($param)
    {
        try
        {               
            $source = array('.', ',');
            $replace = array('','.');
            
            $valor = $param['valor'];
            $vlcomissao = $param['vlcomissao'];
            $vlseguro = $param['vlseguro'];
            $vlcondominio = $param['vlcondominio'];
            $vlluz = $param['vlluz'];
            $vlagua = $param['vlagua'];
            $vliptu = $param['vliptu'];
            $vlgas = $param['vlgas'];
            $vloutro = $param['vloutro'];
            $vldevolucao = $param['vldevolucao'];
            
            $valor = str_replace($source, $replace, $valor);
            $vlcomissao = str_replace($source, $replace, $vlcomissao);
            $vlseguro = str_replace($source, $replace, $vlseguro);
            $vlcondominio = str_replace($source, $replace, $vlcondominio);
            $vlluz = str_replace($source, $replace, $vlluz);
            $vlagua = str_replace($source, $replace, $vlagua);
            $vliptu = str_replace($source, $replace, $vliptu);
            $vlgas = str_replace($source, $replace, $vlgas);
            $vloutro = str_replace($source, $replace, $vloutro);
            $vldevolucao = str_replace($source, $replace, $vldevolucao);
                
            $vlpago = $valor - $vlcomissao;
                
            if ($param['opseguro'] == '+')
            {
                $vlpago = $vlpago + $vlseguro;
            }
            else
            {
                $vlpago = $vlpago - $vlseguro;
            }
                    
            if ($param['opcondominio'] == '+')
            {
                $vlpago = $vlpago + $vlcondominio;
            }
            else
            {
                $vlpago = $vlpago - $vlcondominio;
            }
                    
            if ($param['opluz'] == '+')
            {
                $vlpago = $vlpago + $vlluz;
            }
            else
            {
                $vlpago = $vlpago - $vlluz;
            }
                    
            if ($param['opagua'] == '+')
            {
                $vlpago = $vlpago + $vlagua;
            }
            else
            {
                $vlpago = $vlpago - $vlagua;
            }
            
            if ($param['opiptu'] == '+')
            {
                $vlpago = $vlpago + $vliptu;
            }
            else
            {
                $vlpago = $vlpago - $vliptu;
            }
                    
            if ($param['opgas'] == '+')
            {
                $vlpago = $vlpago + $vlgas;
            }
            else
            {
                $vlpago = $vlpago - $vlgas;
            }
            
            if ($param['opoutro'] == '+')
            {
                $vlpago = $vlpago + $vloutro;
            }    
            else
            {
                $vlpago = $vlpago - $vloutro;
            }
                
            $vlpago = $vlpago - $vldevolucao;
            
            $obj = new StdClass;
            $obj->vlpagar = number_format($vlpago, 2, ',', '.');
            $obj->vlpago = number_format($vlpago, 2, ',', '.');
            
            TForm::sendData('form_ParcelaPagar', $obj);            
        }
        catch (Exception $e)
        {
            
        }        
    }
    
    public function setPrint($param)
    {        
        try
        {
            TTransaction::open(TSession::getValue('banco'));
            
            $object = new Sequencia(1);
            $object->obsrecibopagar = $param['obsrecibopagar'];
            $object->store();
            
            $this->onPrint($param);
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            
        }
    }
    
    public function noPrint()
    {
        
    }
    
    public function onPrint($param)
    {
        //new TMessage('info', json_encode($param));
        try
        {            
            $designer = new TPDFDesigner;
            $designer->generate();
            $designer->SetMargins(30,30,30);
            $designer->SetAutoPageBreak(true, 30);
            
            TTransaction::open('permission');            
            $empresa = new Empresa(TSession::getValue('empresa'));
            TTransaction::close();
            
            TTransaction::open(TSession::getValue('banco'));            
            $uf = new UF($empresa->uf_id);
            TTransaction::close();
            
            $empresaInf1 = "CNPJ N°." . $empresa->cnpj . " - Registro JUCESC N°. " . $empresa->nrjucesc . " - CRECI N°. " . $empresa->creci;
            $empresaInf2 = $empresa->endereco . " - " . $empresa->bairro;
            $empresaInf3 = $empresa->municipio . " - " . $uf->nome . " - CEP " . $empresa->cep;
            
            TTransaction::open(TSession::getValue('banco'));
            $id = $param['id'];
            //$parcela = new ParcelaPagar($id);
            $contrato = new Contrato($param['contrato_id']);
            $bem = new Bem($contrato->bem_id);
            $proprietario = new Cliente($bem->proprietario_id);
            $cliente = new Cliente($contrato->cliente_id);
            $repository = new TRepository('ContacorrenteCliente');
            $criteria = new TCriteria;
            $criteria->add(new TFilter('cliente_id',  '=', $proprietario->id ));
            $objects = $repository->load($criteria, FALSE);
            if ($objects)
            {
                foreach ($objects as $object)
                {               
                    $conta = new ContacorrenteCliente($object->id);    
                }
            }            
            TTransaction::close(); 
            
            TTransaction::open(TSession::getValue('banco'));            
            $tipobem = new Tipobem($bem->tipobem_id);
            TTransaction::close();
            
            if ($param['vlpago'] <= 0)
            {
                new TMessage('info', 'Parcela não paga');
            }           
            else
            {        
                $logo = $empresa->logo;
                $designer->Image($logo, 30, 20, 70, 70);
                $designer->SetTextColor(0,0,128);
                	$designer->SetFont('Arial','B',18);
                	$designer->SetXY(110, 20);
                	$designer->Write(18, utf8_decode($empresa->nome));
                	$designer->SetFont('Arial','',14);
        		    $designer->SetTextColor(0,0,0);
        		    $designer->SetX(450);
        		    $numrecibo = number_format($param['numrecpro'], 0, ",", ".");
        		    $designer->Write(18, "Recibo N. " . $numrecibo);
        		    
        		    $designer->SetDrawColor(0,0,0);
        		    $designer->Line(375, 150, 555, 150);
        		    $designer->Line(375, 150, 375, 300);
        		    $designer->Line(375, 300, 555, 300);
        		    $designer->Line(555, 300, 555, 150);
        		    
                	$designer->SetTextColor(0,0,128);
                	$designer->SetFont('Arial','',10);
                	$designer->Ln();
                	$designer->SetX(110);
                	$designer->Write(10, utf8_decode($empresaInf1));
                $designer->Ln();
                $designer->SetX(110);
                $designer->Write(10, utf8_decode($empresaInf2));
                $designer->Ln();
                $designer->SetX(110);
                $designer->Write(10, utf8_decode($empresaInf3));
                	$designer->Ln();
                	$designer->SetX(110);
                	$designer->Write(10, "Fone/Fax: " . $empresa->fone);
                	$designer->Ln();
                	$designer->SetX(110);
                	$designer->SetTextColor(0,0,75);
                	$designer->Write(10, $empresa->site . " - E-mail:" . $empresa->email);
                	$designer->Ln();
                	$designer->Ln();
                	$designer->SetDrawColor(204,51,0);
                	$designer->Line(30, 95, 555, 95);
                	
                	$designer->SetFont('Arial','',10);
            		$designer->SetTextColor(0,0,0);
            		$designer->Write(12, utf8_decode("Contrato Número: " . number_format($param['contrato_id'], 0, ",", ".")));
            		$designer->SetX(150);
            		$dtfim = new DateTime($contrato->dtfim);
            		$designer->Write(12, "Vencimento: " . $dtfim->format('d/m/Y'));
            		$designer->SetX(275);
            		$designer->Write(12, "Parcela: " . $param['numero'] . "/" . $contrato->qtdeparc);
            		$designer->SetX(400);
            		$dtvencto = $param['dtvencto'];
            		$designer->Write(12, "Vencimento: " . $dtvencto);
            		$designer->Ln();
            		$designer->SetX(64);
            		$designer->Write(12, utf8_decode("Locatário: " . $cliente->id . " - " . $cliente->nome));
            		$designer->SetX(404);
            		$designer->Write(12, "CNPJ/CPF: " . $cliente->cpfcnpj);
            		$designer->Ln();
            		$designer->SetX(64);
            		$designer->Write(12, utf8_decode("Endereço: " . $cliente->endereco));
            		$designer->Ln();
            		$designer->SetX(85);
            		$designer->Write(12, "CEP: " . $cliente->cep);
            		$designer->SetX(175);
            		$designer->Write(12, utf8_decode("Cidade: " . $cliente->municipio));
            		$designer->SetX(422);
            		$designer->Write(12, "Estado: " . $cliente->uf_id);
            		$designer->Ln();
            		$designer->SetX(70);
            		$designer->Write(12, utf8_decode("Locador: " . $proprietario->id . " - " . $proprietario->nome));
            		$designer->Ln();
            		$designer->SetX(30);
            		$designer->Write(12, utf8_decode("Recebi(emos) de: " . $empresa->nome));
            		$valor = $param['valor'];
            		//$valor = number_format($valor, 2, ",", ".");
            		$designer->SetX(390);
            		$designer->Write(12, "Valor Aluguel"); 
            		$designer->SetX(455);
            		$designer->Write(12, "( + )");   		
            		$designer->SetX(480);
            		$designer->Write(12, "R$ " . $valor); 
            		$designer->Ln();
            		$designer->SetX(48);
            		$valorpago = $param['vlpago'];
            		$valorpago = strtoupper(FuncoesExtras::valorPorExtenso(FuncoesExtras::retiraFormatacao($valorpago)));
            		$designer->Write(12, "A Quantia de: " . utf8_decode(str_pad(substr($valorpago, 0, 40), 40, "*", STR_PAD_RIGHT)));
            		$valor = $param['vlcomissao'];
            		//$valor = number_format($valor, 2, ",", ".");
            		$designer->SetX(405);
            		$designer->Write(12, utf8_decode("Comissão")); 
            		$designer->SetX(455);
            		$designer->Write(12, "( " . $param['opcomissao'] . " )");   		
            		$designer->SetX(480);
            		$designer->Write(12, "R$ " . $valor);
            		$designer->Ln();
            		$designer->SetX(112);
            		$designer->Write(12, utf8_decode(str_pad(substr($valorpago, 40, 40), 40, "*", STR_PAD_RIGHT)));
            		$valor = $param['vlseguro'];
            		//$valor = number_format($valor, 2, ",", ".");
            		$designer->SetX(417);
            		$designer->Write(12, "Seguro"); 
            		$designer->SetX(455);
            		$designer->Write(12, "( " . $param['opseguro'] . " )");   		
            		$designer->SetX(480);
            		$designer->Write(12, "R$ " . $valor);
            		$designer->Ln();
            		$designer->SetX(112);
            		$designer->Write(12, utf8_decode(str_pad(substr($valorpago, 80, 40), 40, "*", STR_PAD_RIGHT)));
            		$valor = $param['vlcondominio'];
            		//$valor = number_format($valor, 2, ",", ".");
            		$designer->SetX(395);
            		$designer->Write(12, utf8_decode("Condomínio")); 
            		$designer->SetX(455);
            		$designer->Write(12, "( " . $param['opcondominio'] . " )");   		
            		$designer->SetX(480);
            		$designer->Write(12, "R$ " . $valor);
            		$designer->Ln();
            		$designer->SetX(112);
            		$designer->Write(12, utf8_decode(str_pad(substr($valorpago, 120, 40), 40, "*", STR_PAD_RIGHT)));
            		$valor = $param['vlluz'];
            		//$valor = number_format($valor, 2, ",", ".");
            		$designer->SetX(433);
            		$designer->Write(12, "Luz"); 
            		$designer->SetX(455);
            		$designer->Write(12, "( " . $param['opluz'] . " )");   		
            		$designer->SetX(480);
            		$designer->Write(12, "R$ " . $valor);
            		$designer->Ln();
            		$designer->SetX(45);
            		$designer->Write(12, utf8_decode("PROVENIENTE DE.: ALUGUEL DO IMOVEL CONTRATO N. " . number_format($param['contrato_id'], 0, ",", ".")));
            		$valor = $param['vlagua'];
            		//$valor = number_format($valor, 2, ",", ".");
            		$designer->SetX(428);
            		$designer->Write(12, utf8_decode("Água")); 
            		$designer->SetX(455);
            		$designer->Write(12, "( " . $param['opagua'] . " )");   		
            		$designer->SetX(480);
            		$designer->Write(12, "R$ " . $valor);
            		$designer->Ln();
            		$designer->SetX(45);
            		$periodofim = TDate::date2us($dtvencto);        		
            		$periodoini = date('Y-m-d', strtotime(date('Y-m-d', strtotime($periodofim)) . '-1 month'));
            		$designer->Write(12, utf8_decode("REFERENTE PERIODO DE: " . TDate::date2br($periodoini) . " ATE " . TDate::date2br($periodofim)));
            		$designer->SetX(408);
            		$valor = $param['vliptu'];
            		//$valor = number_format($valor, 2, ",", ".");
            		$designer->SetX(429);
            		$designer->Write(12, "IPTU"); 
            		$designer->SetX(455);
            		$designer->Write(12, "( " . $param['opiptu'] . " )");   		
            		$designer->SetX(480);
            		$designer->Write(12, "R$ " . $valor);
            		$designer->Ln();
            		$designer->SetX(45);
            		$designer->Write(12, utf8_decode("LOCALIZADO NA " . $bem->endereco));
            		$valor = $param['vlgas'];
            		//$valor = number_format($valor, 2, ",", ".");
            		$designer->SetX(433);
            		$designer->Write(12, utf8_decode("Gás")); 
            		$designer->SetX(455);
            		$designer->Write(12, "( " . $param['opgas'] . " )");   		
            		$designer->SetX(480);
            		$designer->Write(12, "R$ " . $valor);
            		setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
                date_default_timezone_set('America/Sao_Paulo');
            		$dtpagto = TDate::date2us($param['dtpagto']);
            		$dia = strftime('%d', strtotime($dtpagto));
                $mes = strftime('%B', strtotime($dtpagto));
                $ano = strftime('%Y', strtotime($dtpagto));
            		$designer->Ln();
            		$designer->SetX(65);
            		$designer->Write(12, utf8_decode('Compl.: ' . $bem->complemento));
            		$valor = $param['vloutro'];
            		//$valor = number_format($valor, 2, ",", ".");
            		$designer->SetX(422);
            		$designer->Write(12, utf8_decode("Outros")); 
            		$designer->SetX(455);
            		$designer->Write(12, "( " . $param['opoutro'] . " )");   		
            		$designer->SetX(480);
            		$designer->Write(12, "R$ " . $valor);
            		$designer->Ln();
            		$designer->SetX(68);
            		$designer->Write(12, utf8_decode('Imóvel: ' . $bem->id));
            		$designer->SetX(200);
            		$designer->Write(12, utf8_decode('Tipo de Imóvel: ' . $tipobem->descricao));
            		$valor = $param['vldevolucao'];
            		//$valor = number_format($valor, 2, ",", ".");
            		$designer->SetX(400);
            		$designer->Write(12, utf8_decode("Devoluções")); 
            		$designer->SetX(455);
            		$designer->Write(12, "( " . $param['opdevolucao'] . " )");   		
            		$designer->SetX(480);
            		$designer->Write(12, "R$ " . $valor);
            		$designer->Ln();
            		$designer->SetX(68);
            		$designer->Write(12, 'Banco:' . $conta->banco);
            		$designer->SetX(230);
            		$designer->Write(12, utf8_decode('Agência:' . $conta->agencia));
            		$valor = $param['vlpago'];
            		//$valor = number_format($valor, 2, ",", ".");
            		$designer->SetX(428);
            		$designer->Write(12, "Total"); 
            		$designer->SetX(455);
            		$designer->Write(12, "( = )");   		
            		$designer->SetX(480);
            		$designer->Write(12, "R$ " . $valor);
            		$designer->Ln();
            		$designer->SetX(70);
            		$designer->Write(12, 'Conta:' . $conta->numero);
            		$designer->SetX(240);
            		$designer->Write(12, utf8_decode('Praça:' . $conta->praca));
            		$designer->Ln();
            		$designer->SetX(48);
            		$designer->Write(12, utf8_decode('Favorecido:' . $conta->nomedep));
            		$designer->Ln();
            		$designer->SetX(45);
            		$designer->Write(12, utf8_decode("Jaraguá do Sul, {$dia} de {$mes} de {$ano}"));
            		$designer->Ln();
            		$designer->Ln();
            		$designer->SetX(45);
            		$designer->Write(12, "_________________________________________");
            		$designer->Ln();
            		$designer->Ln();
            		$designer->SetX(70);
            		$designer->Write(12, utf8_decode($proprietario->nome));
            		$designer->Ln();
            		$designer->Ln();
            		$designer->SetTextColor(204,51,0);
            		$designer->Cell(0, 10, $param['obsrecibopagar'], 0, 0, 'C');
                	
                	$logo = $empresa->logo;
                $designer->Image($logo, 30, 420, 70, 70);
                $designer->SetTextColor(0,0,128);
                	$designer->SetFont('Arial','B',18);
                	$designer->SetXY(110, 420);
                	$designer->Write(18, utf8_decode($empresa->nome));
                	$designer->SetFont('Arial','',14);
        		    $designer->SetTextColor(0,0,0);
        		    $designer->SetX(450);
        		    $numrecibo = number_format($param['numrecpro'], 0, ",", ".");
        		    $designer->Write(18, "Recibo N. " . $numrecibo);
        		    
        		    $designer->SetDrawColor(0,0,0);
        		    $designer->Line(375, 550, 555, 550);
        		    $designer->Line(375, 550, 375, 700);
        		    $designer->Line(375, 700, 555, 700);
        		    $designer->Line(555, 700, 555, 550);
        		
                	$designer->SetTextColor(0,0,128);
                	$designer->SetFont('Arial','',10);
                	$designer->Ln();
                	$designer->SetX(110);
                	$designer->Write(10, utf8_decode($empresaInf1));
                $designer->Ln();
                $designer->SetX(110);
                $designer->Write(10, utf8_decode($empresaInf2));
                $designer->Ln();
                $designer->SetX(110);
                $designer->Write(10, utf8_decode($empresaInf3));
                	$designer->Ln();
                	$designer->SetX(110);
                	$designer->Write(10, "Fone/Fax: " . $empresa->fone);
                	$designer->Ln();
                	$designer->SetX(110);
                	$designer->SetTextColor(0,0,75);
                	$designer->Write(10, $empresa->site . " - E-mail:" . $empresa->email);
                	$designer->Ln();
                	$designer->Ln();
                	$designer->SetDrawColor(204,51,0);
                	$designer->Line(30, 495, 555, 495);
                	
                	$designer->SetFont('Arial','',10);
            		$designer->SetTextColor(0,0,0);
            		$designer->Write(12, utf8_decode("Contrato Número: " . number_format($param['contrato_id'], 0, ",", ".")));
            		$designer->SetX(150);
            		$dtfim = new DateTime($contrato->dtfim);
            		$designer->Write(12, "Vencimento: " . $dtfim->format('d/m/Y'));
            		$designer->SetX(275);
            		$designer->Write(12, "Parcela: " . $param['numero'] . "/" . $contrato->qtdeparc);
            		$designer->SetX(400);
            		$dtvencto = $param['dtvencto'];
            		$designer->Write(12, "Vencimento: " . $dtvencto);
            		$designer->Ln();
            		$designer->SetX(64);
            		$designer->Write(12, utf8_decode("Locatário: " . $cliente->id . " - " . $cliente->nome));
            		$designer->SetX(404);
            		$designer->Write(12, "CNPJ/CPF: " . $cliente->cpfcnpj);
            		$designer->Ln();
            		$designer->SetX(64);
            		$designer->Write(12, utf8_decode("Endereço: " . $cliente->endereco));
            		$designer->Ln();
            		$designer->SetX(85);
            		$designer->Write(12, "CEP: " . $cliente->cep);
            		$designer->SetX(175);
            		$designer->Write(12, utf8_decode("Cidade: " . $cliente->municipio));
            		$designer->SetX(422);
            		$designer->Write(12, "Estado: " . $cliente->uf_id);
            		$designer->Ln();
            		$designer->SetX(70);
            		$designer->Write(12, utf8_decode("Locador: " . $proprietario->id . " - " . $proprietario->nome));
            		$designer->Ln();
            		$designer->SetX(30);
            		$designer->Write(12, utf8_decode("Recebi(emos) de: " . $empresa->nome));
            		$valor = $param['valor'];
            		//$valor = number_format($valor, 2, ",", ".");
            		$designer->SetX(390);
            		$designer->Write(12, "Valor Aluguel"); 
            		$designer->SetX(455);
            		$designer->Write(12, "( + )");   		
            		$designer->SetX(480);
            		$designer->Write(12, "R$ " . $valor); 
            		$designer->Ln();
            		$designer->SetX(48);
            		$designer->Write(12, "A Quantia de: " . utf8_decode(str_pad(substr($valorpago, 0, 40), 40, "*", STR_PAD_RIGHT)));
            		$valor = $param['vlcomissao'];
            		//$valor = number_format($valor, 2, ",", ".");
            		$designer->SetX(405);
            		$designer->Write(12, utf8_decode("Comissão")); 
            		$designer->SetX(455);
            		$designer->Write(12, "( " . $param['opcomissao'] . " )");   		
            		$designer->SetX(480);
            		$designer->Write(12, "R$ " . $valor);
            		$designer->Ln();
            		$designer->SetX(112);
            		$designer->Write(12, utf8_decode(str_pad(substr($valorpago, 40, 40), 40, "*", STR_PAD_RIGHT)));
            		$valor = $param['vlseguro'];
            		//$valor = number_format($valor, 2, ",", ".");
            		$designer->SetX(417);
            		$designer->Write(12, "Seguro"); 
            		$designer->SetX(455);
            		$designer->Write(12, "( " . $param['opseguro'] . " )");   		
            		$designer->SetX(480);
            		$designer->Write(12, "R$ " . $valor);
            		$designer->Ln();
            		$designer->SetX(112);
            		$designer->Write(12, utf8_decode(str_pad(substr($valorpago, 80, 40), 40, "*", STR_PAD_RIGHT)));
            		$valor = $param['vlcondominio'];
            		//$valor = number_format($valor, 2, ",", ".");
            		$designer->SetX(395);
            		$designer->Write(12, utf8_decode("Condomínio")); 
            		$designer->SetX(455);
            		$designer->Write(12, "( " . $param['opcondominio'] . " )");   		
            		$designer->SetX(480);
            		$designer->Write(12, "R$ " . $valor);
            		$designer->Ln();
            		$designer->SetX(112);
            		$designer->Write(12, str_pad(substr($valorpago, 120, 40), 40, "*", STR_PAD_RIGHT));
            		$valor = $param['vlluz'];
            		//$valor = number_format($valor, 2, ",", ".");
            		$designer->SetX(433);
            		$designer->Write(12, "Luz"); 
            		$designer->SetX(455);
            		$designer->Write(12, "( " . $param['opluz'] . " )");   		
            		$designer->SetX(480);
            		$designer->Write(12, "R$ " . $valor);
            		$designer->Ln();
            		$designer->SetX(45);
            		$designer->Write(12, utf8_decode("PROVENIENTE DE.: ALUGUEL DO IMOVEL CONTRATO N. " . number_format($param['contrato_id'], 0, ",", ".")));
            		$valor = $param['vlagua'];
            		//$valor = number_format($valor, 2, ",", ".");
            		$designer->SetX(428);
            		$designer->Write(12, utf8_decode("Água")); 
            		$designer->SetX(455);
            		$designer->Write(12, "( " . $param['opagua'] . " )");   		
            		$designer->SetX(480);
            		$designer->Write(12, "R$ " . $valor);
            		$designer->Ln();
            		$designer->SetX(45);
            		$designer->Write(12, utf8_decode("REFERENTE PERIODO DE: " . TDate::date2br($periodoini) . " ATE " . TDate::date2br($periodofim)));
            		$valor = $param['vliptu'];
            		//$valor = number_format($valor, 2, ",", ".");
            		$designer->SetX(429);
            		$designer->Write(12, "IPTU"); 
            		$designer->SetX(455);
            		$designer->Write(12, "( " . $param['opiptu'] . " )");   		
            		$designer->SetX(480);
            		$designer->Write(12, "R$ " . $valor);
            		$designer->Ln();
            		$designer->SetX(45);
            		$designer->Write(12, utf8_decode("LOCALIZADO NA " . $bem->endereco));
            		$valor = $param['vlgas'];
            		//$valor = number_format($valor, 2, ",", ".");
            		$designer->SetX(433);
            		$designer->Write(12, utf8_decode("Gás")); 
            		$designer->SetX(455);
            		$designer->Write(12, "( " . $param['opgas'] . " )");   		
            		$designer->SetX(480);
            		$designer->Write(12, "R$ " . $valor);
            		$designer->Ln();
            		$designer->SetX(45);
            		$designer->Write(12, utf8_decode('Compl.: ' . $bem->complemento));
            		$valor = $param['vloutro'];
            		//$valor = number_format($valor, 2, ",", ".");
            		$designer->SetX(422);
            		$designer->Write(12, utf8_decode("Outros")); 
            		$designer->SetX(455);
            		$designer->Write(12, "( " . $param['opoutro'] . " )");   		
            		$designer->SetX(480);
            		$designer->Write(12, "R$ " . $valor);
            		$designer->Ln();
            		$designer->SetX(68);
            		$designer->Write(12, utf8_decode('Imóvel: ' . $bem->id));
            		$designer->SetX(200);
            		$designer->Write(12, utf8_decode('Tipo de Imóvel: ' . $tipobem->descricao));
            		$valor = $param['vldevolucao'];
            		//$valor = number_format($valor, 2, ",", ".");
            		$designer->SetX(400);
            		$designer->Write(12, utf8_decode("Devoluções")); 
            		$designer->SetX(455);
            		$designer->Write(12, "( " . $param['opdevolucao'] . " )");   		
            		$designer->SetX(480);
            		$designer->Write(12, "R$ " . $valor);
            		$designer->Ln();
            		$designer->SetX(68);
            		$designer->Write(12, 'Banco:');
            		$designer->SetX(230);
            		$designer->Write(12, utf8_decode('Agência:' . $conta->agencia));
            		$valor = $param['vlpago'];
            		//$valor = number_format($valor, 2, ",", ".");
            		$designer->SetX(428);
            		$designer->Write(12, "Total"); 
            		$designer->SetX(455);
            		$designer->Write(12, "( = )");   		
            		$designer->SetX(480);
            		$designer->Write(12, "R$ " . $valor);
            		$designer->Ln();
            		$designer->SetX(70);
            		$designer->Write(12, 'Conta:' . $conta->numero);
            		$designer->SetX(240);
            		$designer->Write(12, utf8_decode('Praça:' . $conta->praca));
            		$designer->Ln();
            		$designer->SetX(48);
            		$designer->Write(12, utf8_decode('Favorecido:' . $conta->nomedep));
            		$designer->Ln();
            		$designer->SetX(45);
            		$designer->Write(12, utf8_decode("Jaraguá do Sul, {$dia} de {$mes} de {$ano}"));
            		$designer->Ln();
            		$designer->Ln();
            		$designer->SetX(45);
            		$designer->Write(12, "_________________________________________");
            		$designer->Ln();
            		$designer->SetX(70);
            		$designer->Write(12, utf8_decode($proprietario->nome));
            		$designer->Ln();
            		$designer->Ln();
            		$designer->SetTextColor(204,51,0);
            		$designer->Cell(0, 10, $param['obsrecibopagar'], 0, 0, 'C');
                
                $file = 'app/output/Recibo.pdf';
                
                if (!file_exists($file) OR is_writable($file))
                {
                    $designer->save($file);
                    parent::openFile($file);
                }
                else
                {
                    throw new Exception(_t('Permission denied') . ': ' . $file);
                }
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
        }  
        
        /*$obj = new StdClass;
        $obj->id = $param['id'];
        $obj->contrato_id = $param['contrato_id'];
        $obj->bem_id = $param['bem_id'];
        $obj->bem = $param['bem'];
        $obj->proprietario_id = $param['proprietario_id'];
        $obj->proprietario = $param['proprietario'];
        $obj->numero = $param['numero'];
        $obj->sequencia = $param['sequencia'];
        $obj->sequencia1 = $param['sequencia1'];
        $obj->sequencia2 = $param['sequencia2'];
        $obj->dtinicio = $param['dtinicio'];
        $obj->dtfim = $param['dtfim'];
        $obj->dtvencto = $param['dtvencto'];
        $obj->vlacrescido = $param['vlacrescido'];
        $obj->valor = $param['valor'];
        $obj->dtpagto = $param['dtpagto'];
        $obj->vlpagar = $param['vlpagar'];
        $obj->opcomissao = $param['opcomissao'];
        $obj->vlcomissao = $param['vlcomissao'];
        $obj->opseguro = $param['opseguro'];
        $obj->vlseguro = $param['vlseguro'];
        $obj->opcondominio = $param['opcondominio'];
        $obj->vlcondominio = $param['vlcondominio'];
        $obj->opluz = $param['opluz'];
        $obj->vlluz = $param['vlluz'];
        $obj->opagua = $param['opagua'];
        $obj->vlagua = $param['vlagua'];
        $obj->opiptu = $param['opiptu'];
        $obj->vliptu = $param['vliptu'];
        $obj->opgas = $param['opgas'];
        $obj->vlgas = $param['vlgas'];
        $obj->opoutro = $param['opoutro'];
        $obj->vloutro = $param['vloutro'];
        $obj->opdevolucao = $param['opdevolucao'];
        $obj->vldevolucao = $param['vldevolucao'];
        $obj->vlpago = $param['vlpago'];
        $obj->observacao = $param['observacao'];
        
        TForm::sendData('form_ParcelaPagar', $obj);
        
        /*if (($param['dtpagto'] == 0 || $param['dtpagto'] !== NULL) && $param['vlpago'] > 0)
        {
            parent::add(new TAlert('info', 'Parcela já Paga'));
        }*/      
    }
    
    public function onGerParcelas($param)
    {        
        TTransaction::open(TSession::getValue('banco'));
        
        $contrato_id = $param['contrato_id'];
        $contrato = new Contrato($contrato_id);
        
        $qtdeparc = $contrato->qtdeparc;
        $bem = new Bem($contrato->bem_id);
        $diapagto = $bem->diapagto;
        $dtinicio = $contrato->dtinicio;
        $dtinicio = date('Y-m-d', strtotime("+1 month", strtotime($dtinicio)));
        $dtfim = $contrato->dtfim;
        $dtfim = date('Y-m-d', strtotime("+1 month", strtotime($dtfim)));
        $i = 0;
        for ($date = strtotime($dtinicio); $date < strtotime($dtfim); $date = strtotime("+1 month", $date))
        {
            $i++;
            $parcela = array();
            $parcela['contrato_id'] = $contrato_id;
            $parcela['numero'] = $i;
            $parcela['sequencia'] = $i . $qtdeparc;
            $parcela['bem_id'] = $contrato->bem_id;
            $parcela['cliente_id'] = $contrato->cliente_id;
            $parcela['dtinicio'] = $contrato->dtinicio;
            $parcela['dtfim'] = $contrato->dtfim;
            $mes = date('m', $date);
            $ano = date('Y', $date);
            $parcela['dtvencto'] = $ano . '-'. $mes . '-' . str_pad($diapagto, 2, '0', STR_PAD_LEFT);
            $parcela['dtpagto'] = '0';
                    
            $valor = $contrato->valor;                    
            $percomissao = $bem->percomissao;                    
            $vlcomissao = $valor * $percomissao * 0.01;
                    
            $parcela['valor'] = $valor;
            $parcela['proprietario_id'] = $bem->proprietario_id;
            $parcela['vlcomissao'] = $vlcomissao;
            $parcela['cliente2_id'] = intval($contrato->cliente2_id);
                    
            $ppagr = new ParcelaPagar;
            $ppagr->fromArray( (array) $parcela);
            $ppagr->store();
        }
        
        TTransaction::close();
        
        $this->onSetContrato($param);       
    }
}
?>