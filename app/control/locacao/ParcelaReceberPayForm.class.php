<?php
/**
 * ParcelaReceberForm Form
 * @author  <your name here>
 */
class ParcelaReceberPayForm extends TPage
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
        $this->form = new TQuickForm('form_ParcelaReceber');
        $this->form->class = 'tform'; // change CSS class        
        $this->form->style = 'display: table;width:100%'; // change style
        //$this->form->setFormTitle('ParcelaReceber');
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
        $cliente = new TEntry('cliente');
        $dtinicio = new THidden('dtinicio');
        $dtfim = new THidden('dtfim');
        $dtvencto = new TDate('dtvencto');
        $valor = new TEntry('valor');
        $vlacrescido = new TEntry('vlacrescido');
        $dtpagto = new TDate('dtpagto');
        $numrecibo = new THidden('numrecibo');
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
        $numero->setAction($action);
        
        // Campos Não Editáveis
        //$contrato_id->setEditable(FALSE);
        //$numero->setEditable(FALSE); 
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
        $observacao->setEditable(FALSE);
        
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
        $sequencia2->setSize(50);
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
        
        // Define actions dos campos
        $contrato_id->setExitAction(new TAction(array($this, 'onExitContrato')));
        $numero->setExitAction(new TAction(array($this, 'onExitParcela')));
        $vlacrescido->setExitAction(new TAction(array($this, 'onExitValor')));
        $valor->setExitAction(new TAction(array($this, 'onExitValor')));
        $opjuros->setExitAction(new TAction(array($this, 'onExitValor')));
        $vljuros->setExitAction(new TAction(array($this, 'onExitValor')));
        $opmulta->setExitAction(new TAction(array($this, 'onExitValor')));
        $vlmulta->setExitAction(new TAction(array($this, 'onExitValor')));
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
        $vldesc->setExitAction(new TAction(array($this, 'onExitValor')));
        
        // Adiciona validação aos campos
        $vlacrescido->addValidation('Valor Parcela', new TRequiredValidator);
        $valor->addValidation('Valor até Vencimento', new TRequiredValidator);
        $vljuros->addValidation('Juros', new TRequiredValidator);
        $vlmulta->addValidation('Juros', new TRequiredValidator);
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
        $this->form->addQuickFields('Locatário', array($cliente_id, $cliente) );
        $this->form->addQuickField('Parcela', $numero,  50 );
        $this->form->addQuickFields('Sequência', array($sequencia1, $sequencia2) );
        /*$this->form->addQuickField('Data Início', $dtinicio,  200 );
        $this->form->addQuickField('Data Fim', $dtfim,  200 );*/
        $this->form->addQuickField('Data Vencimento', $dtvencto,  100 );
        $this->form->addQuickField('Valor Parcela', $vlacrescido,  200 );
        $this->form->addQuickField('Valor até Vencimento', $valor,  200 );
        $this->form->addQuickField('Data Pagamento', $dtpagto,  100 );
        $this->form->addQuickField('Valor a Pagar', $vlpagar,  200 );
        $this->form->addQuickFields('Juros', array($opjuros, $vljuros) );
        $this->form->addQuickFields('Multa', array($opmulta, $vlmulta) );
        $this->form->addQuickFields('Seguro', array($opseguro, $vlseguro) );
        $this->form->addQuickFields('Condomínio', array($opcondominio, $vlcondominio) );
        $this->form->addQuickFields('Luz', array($opluz, $vlluz) );
        $this->form->addQuickFields('Água', array($opagua, $vlagua) );
        $this->form->addQuickFields('Gás', array($opgas, $vlgas) );
        $this->form->addQuickFields('IPTU', array($opiptu, $vliptu) );
        $this->form->addQuickFields('Desconto', array($opdesc, $vldesc) );
        $this->form->addQuickField('Valor Pago', $vlpago,  200 );
        //$this->form->addQuickField('Cliente2 Id', $cliente2_id,  200 );
        $this->form->addQuickField('Observacao', $observacao );   
        $this->form->addQuickField('', $sequencia);
        $this->form->addQuickField('', $id );
        $this->form->addQuickField('', $numrecibo );
        $this->form->addQuickField('', $dtinicio );
        $this->form->addQuickField('', $dtfim );
        
        /** samples
         $this->form->addQuickFields('Date', array($date1, new TLabel('to'), $date2)); // side by side fields
         $fieldX->addValidation( 'Field X', new TRequiredValidator ); // add validation
         $fieldX->setSize( 100, 40 ); // set size
         **/
         
        // create the form actions
        $this->form->addQuickAction(_t('Save'), new TAction(array($this, 'onSave')), 'fa:floppy-o');
        //$this->form->addQuickAction('Imprimir Recibo',new TAction(array($this,'onPrint')),'fa:table blue');
        
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
        try
        {
            TTransaction::open(TSession::getValue('banco')); // open a transaction
            
            $this->form->validate(); // validate form data
            
            $object = new ParcelaReceber;  // create an empty object
            $data = $this->form->getData(); // get form data as array
            
            $data->dtvencto = TDate::date2us($data->dtvencto);
            $data->vlacrescido = FuncoesExtras::retiraFormatacao($data->vlacrescido);
            $data->valor = FuncoesExtras::retiraFormatacao($data->valor);
            if ($data->dtpagto == NULL)
            {
                $data->dtpagto = 0;
            }
            else
            {
                $data->dtpagto = TDate::date2us($data->dtpagto);
            }
            $data->vljuros = FuncoesExtras::retiraFormatacao($data->vljuros);
            $data->vlmulta = FuncoesExtras::retiraFormatacao($data->vlmulta);
            $data->vlseguro = FuncoesExtras::retiraFormatacao($data->vlseguro);
            $data->vlcondominio = FuncoesExtras::retiraFormatacao($data->vlcondominio);
            $data->vlluz = FuncoesExtras::retiraFormatacao($data->vlluz);
            $data->vlagua = FuncoesExtras::retiraFormatacao($data->vlagua);
            $data->vlgas = FuncoesExtras::retiraFormatacao($data->vlgas);
            $data->vliptu = FuncoesExtras::retiraFormatacao($data->vliptu);
            $data->vlpago = FuncoesExtras::retiraFormatacao($data->vlpago);
            
            
            if (intval($data->numrecibo) == 0)
            {
                $objsec = new Sequencia(1);
                $numreccli = $objsec->numreccli + 1;
                
                $sequencia = array();
                $sequencia['id'] = 1;
                $sequencia['numreccli'] = $numreccli;
                
                $objsec->fromArray( (array) $sequencia );
                $objsec->store();
                
                $data->numrecibo = $numreccli;
            }
            
            /*$dataenv = array();
            $dataenv = (array) $data;
            
            $dataenv['opjuros'] = TSession::getValue('opjuros');
            $dataenv['vljuros'] = TSession::getValue('vljuros');
            $dataenv['opmulta'] = TSession::getValue('opmulta');
            $dataenv['vlmulta'] = TSession::getValue('vlmulta');
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
            $dataenv['opdesc'] = TSession::getValue('opdesc');
            $dataenv['vldesc'] = TSession::getValue('vldesc');*/
            
            if (intval($data->vljuros)==0)
            {
                $data->vljuros = TSession::getValue('vljuros');
            }
            
            if (intval($data->vlmulta)==0)
            {
                $data->vlmulta = TSession::getValue('vlmulta');
            }
            
            if (intval($data->vlseguro)==0)
            {
                $data->vlseguro = TSession::getValue('vlseguro');
            }
            
            if (intval($data->vljuros)==0)
            {
                $data->vljuros = TSession::getValue('vljuros');
            }
            
            if (intval($data->vlcondominio)==0)
            {
                $data->vlcondominio = TSession::getValue('vlcondominio');
            }
            
            if (intval($data->vlluz)==0)
            {
                $data->vlluz = TSession::getValue('vlluz');
            }
            
            if (intval($data->vlagua)==0)
            {
                $data->vlagua = TSession::getValue('vlagua');
            }
            
            if (intval($data->vlgas)==0)
            {
                $data->vlgas = TSession::getValue('vlgas');
            }
            
            if (intval($data->vliptu)==0)
            {
                $data->vliptu = TSession::getValue('vliptu');
            }
            
            if (intval($data->vldesc)==0)
            {
                $data->vldesc = TSession::getValue('vldesc');
            }
            
            $object->fromArray( (array) $data); // load the object with data
            $object->store(); // save the object
            
            $repository = new TRepository('ParcelaReceber');
            $criteria = new TCriteria;
            $criteria->add(new TFilter('contrato_id', '=', TSession::getValue('contrato_id')));
            
            $objects = $repository->load($criteria, FALSE);
            if ($objects)
            {
                $liquidado = TRUE;
                foreach ($objects as $object)
                {
                    if ($object->vlpago == 0)
                    {
                        $liquidado = FALSE;
                    }
                }
                
                if ($liquidado == TRUE)
                {
                    $contrato = new Contrato($object->contrato_id);
                    $bem = new Bem($contrato->bem_id);
                    
                    $array = array();
                    $array['liquidado'] = 'S';
                    $contrato->fromArray((array)$array);
                    $contrato->store();
                    
                    $array = array();
                    $array['cliente_id'] = 0;
                    $array['contrato_id'] = 0;
                    $array['cliente2_id'] = 0;
                    $bem->fromArray((array)$array);
                    $bem->store();
                }
            }
            
            $data->dtvencto = TDate::date2br($data->dtvencto);
            $data->vlacrescido = number_format($data->vlacrescido, 2, ',', '.');
            $data->valor = number_format($data->valor, 2, ',', '.');
            if ($data->dtpagto == NULL)
            {
                $data->dtpagto = 0;
            }
            else
            {
                $data->dtpagto = TDate::date2br($data->dtpagto);
            }
            $data->vljuros = number_format($data->vljuros, 2, ',', '.');
            $data->vlmulta = number_format($data->vlmulta, 2, ',', '.');
            $data->vlseguro = number_format($data->vlseguro, 2, ',', '.');
            $data->vlcondominio = number_format($data->vlcondominio, 2, ',', '.');
            $data->vlluz = number_format($data->vlluz, 2, ',', '.');
            $data->vlagua = number_format($data->vlagua, 2, ',', '.');
            $data->vlgas = number_format($data->vlgas, 2, ',', '.');
            $data->vliptu = number_format($data->vliptu, 2, ',', '.');
            $data->vlpago = number_format($data->vlpago, 2, ',', '.');
            
            $sequencia = new Sequencia(1);
            
            //$this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            
            $form = new TQuickForm('input_form');
            $form->style = 'padding:20px';
            
            $obsreciboreceber = new TText('obsreciboreceber');
            $obsreciboreceber->setValue($sequencia->obsreciboreceber);
            
            $form->addQuickField('Observação', $obsreciboreceber);
            
            $obsreciboreceber->setSize(400);
            
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
            
            $object = new ParcelaReceber($key); // instantiates the Active Record
            
            TSession::setValue('opjuros', $object->opjuros);
            TSession::setValue('vljuros', $object->vljuros);
            TSession::setValue('opmulta', $object->opmulta);
            TSession::setValue('vlmulta', $object->vlmulta);
            TSession::setValue('opseguro', $object->opseguro);
            TSession::setValue('vlseguro', $object->vlseguro);
            TSession::setValue('opcondominio', $object->opcondominio);
            TSession::setValue('vlcondominio', $object->vlcondominio);
            TSession::setValue('opluz', $object->opluz);
            TSession::setValue('vlluz', $object->vlluz);
            TSession::setValue('opagua', $object->opagua);
            TSession::setValue('vlagua', $object->vlagua);
            TSession::setValue('opgas', $object->opgas);
            TSession::setValue('vlgas', $object->vlgas);
            TSession::setValue('opiptu', $object->opiptu);
            TSession::setValue('vliptu', $object->vliptu);
            TSession::setValue('opdesc', $object->opdesc);
            TSession::setValue('vldesc', $object->vldesc);
            
            if ($object->dtpagto !== '0')
            {
                if (strtotime($object->dtpagto) <= strtotime($object->dtvencto))
                {
                    $valor = $object->valor; 
                }
                else
                {
                    $valor = $object->vlacrescido;
                }
            }
            else
            {            
                if (strtotime(date('Y-m-d')) <= strtotime($object->dtvencto))
                {
                    $valor = $object->valor;  
                }
                else
                {
                    $valor = $object->vlacrescido;
                }
            }
            
            $object->dtinicio = TDate::date2br($object->dtinicio);
            $object->dtfim = TDate::date2br($object->dtfim);
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
                
            $vlpagar = $valor;
            $valor = $valor + $object->vljuros;
            $valor = $valor + $object->vlmulta;
                
            if ($object->opseguro == '+')
            {
                $valor = $valor + $object->vlseguro;
            }
            else
            {
                $valor = $valor - $object->vlseguro;
            }
                
            if ($object->opcondominio == '+')
            {
                $valor = $valor + $object->vlcondominio;
            }
            else
            {
                $valor = $valor - $object->vlcondominio;
            }
                
            if ($object->opluz == '+')
            {
                $valor = $valor + $object->vlluz;
            }
            else
            {
                $valor = $valor - $object->vlluz;
            }
                
            if ($object->opagua == '+')
            {
                $valor = $valor + $object->vlagua;
            }
            else
            {
                $valor = $valor - $object->vlagua;
            }
                
            if ($object->opgas == '+')
            {
                $valor = $valor + $object->vlgas;
            }
            else
            {
                $valor = $valor - $object->vlgas;
            }
                
            if ($object->opiptu == '+')
            {
                $valor = $valor + $object->vliptu;
            }
            else
            {
                $valor = $valor - $object->vliptu;
            }
                
            $valor = $valor - $object->vldesc;
            
            $object->vlacrescido = number_format($object->vlacrescido, 2, ',', '.');
            $object->valor = number_format($object->valor, 2, ',', '.');
            $object->vljuros = number_format($object->vljuros, 2, ',', '.');
            $object->vlmulta = number_format($object->vlmulta, 2, ',', '.');
            $object->vlseguro = number_format($object->vlseguro, 2, ',', '.');
            $object->vlcondominio = number_format($object->vlcondominio, 2, ',', '.');
            $object->vlluz = number_format($object->vlluz, 2, ',', '.');
            $object->vliptu = number_format($object->vliptu, 2, ',', '.');
            $object->vlagua = number_format($object->vlagua, 2, ',', '.');
            $object->vlgas = number_format($object->vlgas, 2, ',', '.');
            $object->vloutro = number_format($object->vloutro, 2, ',', '.');
            $object->vldesc = number_format($object->vldesc, 2, ',', '.');
            //$object->vlpago = number_format($object->vlpago, 2, ',', '.');
            
            $sequencia = $object->sequencia;
            $tam = strlen($sequencia);
                                      
            $obj = new StdClass;
            $obj->contrato_id = $object->contrato_id;
            $obj->sequencia1 = substr($sequencia, 0, $tam - 2);
            $obj->sequencia2 = substr($sequencia, -2);
            $obj->vlpagar = number_format($vlpagar, 2, ',', '.');
            $obj->vlpago = number_format($valor, 2, ',', '.');
            if (TSession::getValue('dtpagto') == NULL)
            {
                $obj->dtpagto = date('d/m/Y');
            }
                         
            TForm::sendData('form_ParcelaReceber', $obj);
            
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
                $cliente = new Cliente($contrato->cliente_id);
                
                $obj = new StdClass;
                $obj->bem_id = $contrato->bem_id;
                $obj->bem = $bem->descricao;
                $obj->cliente_id = $contrato->cliente_id;
                $obj->cliente = $cliente->nome;
                
                TForm::sendData('form_ParcelaReceber', $obj);
                
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
                
                $repository = new TRepository('ParcelaReceber');
                $criteria = new TCriteria;
                $criteria->add(new TFilter('contrato_id','=',$contrato_id)); 
                $criteria->add(new TFilter('numero','=',$parcela));
                $objects = $repository->load($criteria, TRUE);
                
                if ($objects)
                {
                    foreach ($objects as $object)
                    {                        
                        TSession::setValue('opjuros', $object->opjuros);                        
                        TSession::setValue('opmulta', $object->opmulta);                        
                        TSession::setValue('opseguro', $object->opseguro);
                        TSession::setValue('vlseguro', $object->vlseguro);
                        TSession::setValue('opcondominio', $object->opcondominio);
                        TSession::setValue('vlcondominio', $object->vlcondominio);
                        TSession::setValue('opluz', $object->opluz);
                        TSession::setValue('vlluz', $object->vlluz);
                        TSession::setValue('opagua', $object->opagua);
                        TSession::setValue('vlagua', $object->vlagua);
                        TSession::setValue('opgas', $object->opgas);
                        TSession::setValue('vlgas', $object->vlgas);
                        TSession::setValue('opiptu', $object->opiptu);
                        TSession::setValue('vliptu', $object->vliptu);
                        TSession::setValue('opdesc', $object->opdesc);
                        TSession::setValue('vldesc', $object->vldesc);
                        
                        if ($object->dtpagto !== '0')
                        {
                            if (strtotime($object->dtpagto) <= strtotime($object->dtvencto))
                            {
                                $valor = $object->valor; 
                            }
                            else
                            {
                                $valor = $object->vlacrescido;
                            }
                        }
                        else
                        {            
                            if (strtotime(date('Y-m-d')) <= strtotime($object->dtvencto))
                            {
                                $valor = $object->valor;  
                            }
                            else
                            {
                                $valor = $object->vlacrescido;
                                
                                TTransaction::close();
                                TTransaction::open('permission');
                                $multa = Parametros::getParametro('multa');
                                $juros = Parametros::getParametro('juros');
                                TTransaction::close();
                                TTransaction::open(TSession::getValue('banco'));
                                
                                $object->vljuros = FuncoesExtras::calculaJuros($object->dtvencto, $valor, $juros);
                                $object->vlmulta = FuncoesExtras::calculaMulta($object->dtvencto, $valor, $multa);
                            }
                        }
                                  
                        if (($object->dtpagto == 0 || $object->dtpagto !== NULL) && $object->vlpago > 0)
                        {
                            new TMessage('info', 'Parcela já Paga');
                            TSession::setValue('dtpagto', $object->dtpagto);
                        }
                        else
                        {
                            TSession::setValue('dtpagto', NULL);
                        }                                                
                        
                        TSession::setValue('vljuros', $object->vljuros);
                        TSession::setValue('vlmulta', $object->vlmulta);
                            
                        $vlpagar = $valor;
                        $valor = $valor + $object->vljuros;
                        $valor = $valor + $object->vlmulta;
                            
                        if ($object->opseguro == '+')
                        {
                            $valor = $valor + $object->vlseguro;
                        }
                        else
                        {
                            $valor = $valor - $object->vlseguro;
                        }
                            
                        if ($object->opcondominio == '+')
                        {
                            $valor = $valor + $object->vlcondominio;
                        }
                        else
                        {
                            $valor = $valor - $object->vlcondominio;
                        }
                            
                        if ($object->opluz == '+')
                        {
                            $valor = $valor + $object->vlluz;
                        }
                        else
                        {
                            $valor = $valor - $object->vlluz;
                        }
                            
                        if ($object->opagua == '+')
                        {
                            $valor = $valor + $object->vlagua;
                        }
                        else
                        {
                            $valor = $valor - $object->vlagua;
                        }
                            
                        if ($object->opgas == '+')
                        {
                            $valor = $valor + $object->vlgas;
                        }
                        else
                        {
                            $valor = $valor - $object->vlgas;
                        }
                            
                        if ($object->opiptu == '+')
                        {
                            $valor = $valor + $object->vliptu;
                        }
                        else
                        {
                            $valor = $valor - $object->vliptu;
                        }
                            
                        $valor = $valor - $object->vldesc;
                        
                        $obj = new StdClass;
                        $obj->id = $object->id;
                        $obj->sequencia = $object->numero;
                        $obj->sequencia1 = $object->numero;
                        $obj->sequencia2 = substr($object->sequencia, -2);
                        $obj->dtinicio = TDate::date2br($object->dtinicio);
                        $obj->dtfim = TDate::date2br($object->dtfim);
                        $obj->dtvencto = TDate::date2br($object->dtvencto);
                        $obj->vlacrescido = number_format($object->vlacrescido, 2, ',', '.');
                        $obj->valor = number_format($object->valor, 2, ',', '.');
                        $obj->dtpagto = TDate::date2br($object->dtpagto);
                        $obj->opjuros = $object->opjuros;
                        $obj->vljuros = number_format($object->vljuros, 2, ',', '.');
                        $obj->opmulta = $object->opmulta;
                        $obj->vlmulta = number_format($object->vlmulta, 2, ',', '.');
                        $obj->opseguro = $object->opseguro;
                        $obj->vlseguro = number_format($object->vlseguro, 2, ',', '.');
                        $obj->opcondominio = $object->opcondominio;
                        $obj->vlcondominio = number_format($object->vlcondominio, 2, ',', '.');
                        $obj->opluz = $object->opluz;
                        $obj->vlluz = number_format($object->vlluz, 2, ',', '.');
                        $obj->opagua = $object->opagua;
                        $obj->vlagua = number_format($object->vlagua, 2, ',', '.');
                        $obj->opgas = $object->opgas;
                        $obj->vlgas = number_format($object->vlgas, 2, ',', '.');
                        $obj->opiptu = $object->opiptu;
                        $obj->vliptu = number_format($object->vliptu, 2, ',', '.');
                        $obj->opdesc = '+';
                        $obj->vldesc = number_format($object->vldesc, 2, ',', '.');
                        $obj->vlpagar = number_format($vlpagar, 2, ',', '.');
                        $obj->vlpago = number_format($valor, 2, ',', '.');
                        if (TSession::getValue('dtpagto') == NULL)
                        {
                            $obj->dtpagto = date('d/m/Y');
                        }
                        $obj->observacao = $object->observacao;
                        
                        TForm::sendData('form_ParcelaReceber', $obj);
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
            
            $vlacrescido = $param['vlacrescido'];
            $valor = $param['valor'];
            $vljuros = $param['vljuros'];
            $vlmulta = $param['vlmulta'];
            $vlseguro = $param['vlseguro'];
            $vlcondominio = $param['vlcondominio'];
            $vlluz = $param['vlluz'];
            $vlagua = $param['vlagua'];
            $vlgas = $param['vlgas'];
            $vliptu = $param['vliptu'];
            $vldesc = $param['vldesc'];
            
            if ($param['dtpagto'] !== '0')
            {
                if (strtotime($param['dtpagto']) <= strtotime($param['dtvencto']))
                {
                    $valor = $param['valor']; 
                }
                else
                {
                    $valor = $param['vlacrescido'];
                }
            }
            else
            {            
                if (strtotime(date('Y-m-d')) <= strtotime($param['dtvencto']))
                {
                    $valor = $param['valor'];  
                }
                else
                {
                    $valor = $param['vlacrescido'];
                }
            }
            
            $vlacrescido = str_replace($source, $replace, $vlacrescido);
            $valor = str_replace($source, $replace, $valor);
            $vljuros = str_replace($source, $replace, $vljuros);
            $vlmulta = str_replace($source, $replace, $vlmulta);
            $vlseguro = str_replace($source, $replace, $vlseguro);
            $vlcondominio = str_replace($source, $replace, $vlcondominio);
            $vlluz = str_replace($source, $replace, $vlluz);
            $vlagua = str_replace($source, $replace, $vlagua);
            $vlgas = str_replace($source, $replace, $vlgas);
            $vliptu = str_replace($source, $replace, $vliptu);
            $vldesc = str_replace($source, $replace, $vldesc);
                        
            $valor = $valor + $vljuros + $vlmulta;
            
            if ($param['opseguro'] == '+')
            {
                $valor = $valor + $vlseguro;
            }
            else
            {
                $valor = $valor - $vlseguro;
            }
            
            if ($param['opcondominio'] == '+')
            {
                $valor = $valor + $vlcondominio;
            }
            else
            {
                $valor = $valor - $vlcondominio;
            }
            
            if ($param['opluz'] == '+')
            {
                $valor = $valor + $vlluz;
            }
            else
            {
                $valor = $valor - $vlluz;
            }
            
            if ($param['opagua'] == '+')
            {
                $valor = $valor + $vlagua;
            }
            else
            {
                $valor = $valor - $vlagua;
            }
            
            if ($param['opgas'] == '+')
            {
                $valor = $valor + $vlgas;
            }
            else
            {
                $valor = $valor - $vlgas;
            }
            
            if ($param['opiptu'] == '+')
            {
                $valor = $valor + $vliptu;
            }
            else
            {
                $valor = $valor - $vliptu;
            }
            
            $valor = $valor - $vldesc;
            
            $obj = new StdClass;
            //$obj->vlpagar = number_format($vlpagar, 2, ',', '.');
            $obj->vlpago = number_format($valor, 2, ',', '.');
            
            TForm::sendData('form_ParcelaReceber', $obj);            
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
            $object->obsreciboreceber = $param['obsreciboreceber'];
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
            //$parcela = new ParcelaReceber($id);
            $contrato = new Contrato($param['contrato_id']);
            $bem = new Bem($contrato->bem_id);
            $proprietario = new Cliente($bem->proprietario_id);
            $cliente = new Cliente($contrato->cliente_id);
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
        		    $numrecibo = number_format($param['numrecibo'], 0, ",", ".");
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
            		$designer->Write(12, utf8_decode("Recebi(emos) de: " . $cliente->nome));
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
            		$designer->Write(12, utf8_decode(str_pad(substr($valorpago, 40, 40), 40, "*", STR_PAD_RIGHT)));
            		$valor = $param['vlmulta'];
            		//$valor = number_format($valor, 2, ",", ".");
            		$designer->SetX(425);
            		$designer->Write(12, "Multa"); 
            		$designer->SetX(455);
            		$designer->Write(12, "( " . $param['opmulta'] . " )");   		
            		$designer->SetX(480);
            		$designer->Write(12, "R$ " . $valor);
            		$designer->Ln();
            		$designer->SetX(112);
            		$designer->Write(12, utf8_decode(str_pad(substr($valorpago, 80, 40), 40, "*", STR_PAD_RIGHT)));
            		$valor = $param['vljuros'];
            		//$valor = number_format($valor, 2, ",", ".");
            		$designer->SetX(425);
            		$designer->Write(12, utf8_decode("Juros")); 
            		$designer->SetX(455);
            		$designer->Write(12, "( " . $param['opjuros'] . " )");   		
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
            		$designer->SetX(42);
            		$designer->Write(12, utf8_decode("Código do Bem: " . $bem->id));
            		$designer->SetX(408);
            		$valor = $param['vliptu'];
            		//$valor = number_format($valor, 2, ",", ".");
            		$designer->SetX(430);
            		$designer->Write(12, "IPTU"); 
            		$designer->SetX(455);
            		$designer->Write(12, "( " . $param['opiptu'] . " )");   		
            		$designer->SetX(480);
            		$designer->Write(12, "R$ " . $valor);
            		$designer->Ln();
            		$designer->SetX(42);
            		$designer->Write(12, utf8_decode("Tipo do Imóvel: " . $tipobem->descricao));
            		$valor = $param['vlseguro'];
            		//$valor = number_format($valor, 2, ",", ".");
            		$designer->SetX(420);
            		$designer->Write(12, utf8_decode("Seguro")); 
            		$designer->SetX(455);
            		$designer->Write(12, "( " . $param['opseguro'] . " )");   		
            		$designer->SetX(480);
            		$designer->Write(12, "R$ " . $valor);
            		setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
                    date_default_timezone_set('America/Sao_Paulo');
            		$dtpagto = TDate::date2us($param['dtpagto']);
            		$dia = strftime('%d', strtotime($dtpagto));
                    $mes = strftime('%B', strtotime($dtpagto));
                    $ano = strftime('%Y', strtotime($dtpagto));
            		$designer->Ln();
            		$designer->SetX(40);
            		$designer->Write(12, utf8_decode("Jaraguá do Sul, {$dia} de {$mes} de {$ano}"));
            		$valor = $param['vlgas'];
            		//$valor = number_format($valor, 2, ",", ".");
            		$designer->SetX(433);
            		$designer->Write(12, utf8_decode("Gás")); 
            		$designer->SetX(455);
            		$designer->Write(12, "( " . $param['opgas'] . " )");   		
            		$designer->SetX(480);
            		$designer->Write(12, "R$ " . $valor);
            		$designer->Ln();
            		$valor = $param['vldesc'];
            		//$valor = number_format($valor, 2, ",", ".");
            		$designer->SetX(410);
            		$designer->Write(12, utf8_decode("Desconto")); 
            		$designer->SetX(455);
            		$designer->Write(12, "( " . $param['opdesc'] . " )");   		
            		$designer->SetX(480);
            		$designer->Write(12, "R$ " . $valor);
            		$designer->Ln();
            		$designer->SetX(40);
            		$designer->Write(12, "_________________________________________");
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
            		$designer->Write(12, utf8_decode($empresa->nome));
            		$designer->Ln();
            		$designer->Ln();
            		$designer->SetTextColor(204,51,0);
            		$designer->Cell(0, 10, $param['obsreciboreceber'], 0, 0, 'C');
                	
                	$logo = $empresa->logo;
                    $designer->Image($logo, 30, 420, 70, 70);
                    $designer->SetTextColor(0,0,128);
                	$designer->SetFont('Arial','B',18);
                	$designer->SetXY(110, 420);
                	$designer->Write(18, utf8_decode($empresa->nome));
                	$designer->SetFont('Arial','',14);
        		    $designer->SetTextColor(0,0,0);
        		    $designer->SetX(450);
        		    $numrecibo = number_format($param['numrecibo'], 0, ",", ".");
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
            		$designer->Write(12, utf8_decode("Recebi(emos) de: " . $cliente->nome));
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
            		$designer->Write(12, utf8_decode(str_pad(substr($valorpago, 40, 40), 40, "*", STR_PAD_RIGHT)));
            		$valor = $param['vlmulta'];
            		//$valor = number_format($valor, 2, ",", ".");
            		$designer->SetX(425);
            		$designer->Write(12, "Multa"); 
            		$designer->SetX(455);
            		$designer->Write(12, "( " . $param['opmulta'] . " )");   		
            		$designer->SetX(480);
            		$designer->Write(12, "R$ " . $valor);
            		$designer->Ln();
            		$designer->SetX(112);
            		$designer->Write(12, utf8_decode(str_pad(substr($valorpago, 80, 40), 40, "*", STR_PAD_RIGHT)));
            		$valor = $param['vljuros'];
            		//$valor = number_format($valor, 2, ",", ".");
            		$designer->SetX(425);
            		$designer->Write(12, utf8_decode("Juros")); 
            		$designer->SetX(455);
            		$designer->Write(12, "( " . $param['opjuros'] . " )");   		
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
            		$designer->SetX(42);
            		$designer->Write(12, utf8_decode("Código do Bem: " . $bem->id));
            		$designer->SetX(408);
            		$designer->SetX(408);
            		$valor = $param['vliptu'];
            		//$valor = number_format($valor, 2, ",", ".");
            		$designer->SetX(430);
            		$designer->Write(12, "IPTU"); 
            		$designer->SetX(455);
            		$designer->Write(12, "( " . $param['opiptu'] . " )");   		
            		$designer->SetX(480);
            		$designer->Write(12, "R$ " . $valor);
            		$designer->Ln();
            		$designer->SetX(42);
            		$designer->Write(12, utf8_decode("Tipo do Imóvel: " . $tipobem->descricao));
            		$valor = $param['vlseguro'];
            		//$valor = number_format($valor, 2, ",", ".");
            		$designer->SetX(420);
            		$designer->Write(12, utf8_decode("Seguro")); 
            		$designer->SetX(455);
            		$designer->Write(12, "( " . $param['opseguro'] . " )");   		
            		$designer->SetX(480);
            		$designer->Write(12, "R$ " . $valor);
            		$designer->Ln();
            		$designer->SetX(40);
            		$designer->Write(12, utf8_decode("Jaraguá do Sul, {$dia} de {$mes} de {$ano}"));
            		$valor = $param['vlgas'];
            		//$valor = number_format($valor, 2, ",", ".");
            		$designer->SetX(433);
            		$designer->Write(12, utf8_decode("Gás")); 
            		$designer->SetX(455);
            		$designer->Write(12, "( " . $param['opgas'] . " )");   		
            		$designer->SetX(480);
            		$designer->Write(12, "R$ " . $valor);
            		$designer->Ln();
            		$valor = $param['vldesc'];
            		//$valor = number_format($valor, 2, ",", ".");
            		$designer->SetX(410);
            		$designer->Write(12, utf8_decode("Desconto")); 
            		$designer->SetX(455);
            		$designer->Write(12, "( " . $param['opdesc'] . " )");   		
            		$designer->SetX(480);
            		$designer->Write(12, "R$ " . $valor);
            		$designer->Ln();
            		$designer->SetX(40);
            		$designer->Write(12, "_________________________________________");
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
            		$designer->Write(12, utf8_decode($empresa->nome));
            		$designer->Ln();
            		$designer->Ln();
            		$designer->SetTextColor(204,51,0);
            		$designer->Cell(0, 10, $param['obsreciboreceber'], 0, 0, 'C');
                
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
        $obj->cliente_id = $param['cliente_id'];
        $obj->cliente = $param['cliente'];
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
        $obj->opjuros = $param['opjuros'];
        $obj->vljuros = $param['vljuros'];
        $obj->opmulta = $param['opmulta'];
        $obj->vlmulta = $param['vlmulta'];
        $obj->opseguro = $param['opseguro'];
        $obj->vlseguro = $param['vlseguro'];
        $obj->opcondominio = $param['opcondominio'];
        $obj->vlcondominio = $param['vlcondominio'];
        $obj->opluz = $param['opluz'];
        $obj->vlluz = $param['vlluz'];
        $obj->opagua = $param['opagua'];
        $obj->vlagua = $param['vlagua'];
        $obj->opgas = $param['opgas'];
        $obj->vlgas = $param['vlgas'];
        $obj->opiptu = $param['opiptu'];
        $obj->vliptu = $param['vliptu'];
        $obj->opdesc = $param['opdesc'];
        $obj->vldesc = $param['vldesc'];
        $obj->vlpago = $param['vlpago'];
        $obj->observacao = $param['observacao'];
        
        TForm::sendData('form_ParcelaReceber', $obj); 
        
        /*if (($param['dtpagto'] == 0 || $param['dtpagto'] !== NULL) && $param['vlpago'] > 0)
        {
            parent::add(new TAlert('info', 'Parcela já Paga'));
        }*/
    }
}
?>