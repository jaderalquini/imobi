<?php
/**
 * ParcelaReceberForm Form
 * @author  <your name here>
 */
class ParcelaReceberEditForm extends TPage
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
        
        $contrato_id->setExitAction(new TAction(array($this, 'onExitContrato')));
        $numero->setExitAction(new TAction(array($this, 'onExitParcela')));
        
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
        $this->form->addQuickField('Parcela', $numero, 50 );
        $this->form->addQuickFields('Sequência', array($sequencia1, $sequencia2) );
        $this->form->addQuickField('Data Vencto', $dtvencto,  100 );
        $this->form->addQuickField('Valor Parcela', $vlacrescido,  200 );
        $this->form->addQuickField('Valor até Vencimento', $valor,  200 );
        $this->form->addQuickField('Data Pagamento', $dtpagto,  100 );
        //$this->form->addQuickField('Numrecibo', $numrecibo,  200 );
        //$this->form->addQuickField('Valor a Pagar', $vlpagar,  200 );
        $this->form->addQuickFields('Juros', array($opjuros, $vljuros) );
        $this->form->addQuickFields('Multa', array($opmulta, $vlmulta) );
        $this->form->addQuickFields('Seguro', array($opseguro, $vlseguro) );
        $this->form->addQuickFields('Condomínio', array($opcondominio, $vlcondominio) );
        $this->form->addQuickFields('Luz', array($opluz, $vlluz) );
        $this->form->addQuickFields('Água', array($opagua, $vlagua) );
        $this->form->addQuickFields('Gás', array($opgas, $vlgas) );
        $this->form->addQuickFields('IPTU', array($opiptu, $vliptu) );
        //$this->form->addQuickFields('Desconto', array($opdesc, $vldesc) );
        $this->form->addQuickField('Valor Pago', $vlpago,  200 );
        //$this->form->addQuickField('Cliente2 Id', $cliente2_id,  200 );
        $this->form->addQuickField('Observacao', $observacao );   
        $this->form->addQuickField('', $id );
        $this->form->addQuickField('', $sequencia);
        $this->form->addQuickField('Data Início', $dtinicio,  200 );
        $this->form->addQuickField('Data Fim', $dtfim,  200 );
        
        /** samples
         $this->form->addQuickFields('Date', array($date1, new TLabel('to'), $date2)); // side by side fields
         $fieldX->addValidation( 'Field X', new TRequiredValidator ); // add validation
         $fieldX->setSize( 100, 40 ); // set size
         **/
         
        // create the form actions
        $this->form->addQuickAction(_t('Save'), new TAction(array($this, 'onSave')), 'fa:floppy-o');
        $this->form->addQuickAction('Editar Mais Parcelas', new TAction(array('ParcelaReceberEditCollectionForm', 'onEdit')), 'fa:list blue');
        //$this->form->addQuickAction(_t('Back to the listing'),new TAction(array('ParcelaReceberList','onSetContrato')),'fa:table blue');
        
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
            
            $object->fromArray( (array) $data); // load the object with data
            $object->store(); // save the object
            
            // get the generated id
            $data->id = $object->id;
            
            $data->dtvencto = TDate::date2br($data->dtvencto);
            $data->dtpagto = TDate::date2br($data->dtpagto);
            
            $data->vlacrescido = number_format($data->vlacrescido, 2, ',', '.');
            $data->valor = number_format($data->valor, 2, ',', '.');
            $data->vljuros = number_format($data->vljuros, 2, ',', '.');
            $data->vlmulta = number_format($data->vlmulta, 2, ',', '.');
            $data->vlseguro = number_format($data->vlseguro, 2, ',', '.');
            $data->vlcondominio = number_format($data->vlcondominio, 2, ',', '.');
            $data->vlluz = number_format($data->vlluz, 2, ',', '.');
            $data->vlagua = number_format($data->vlagua, 2, ',', '.');
            $data->vlgas = number_format($data->vlgas, 2, ',', '.');
            $data->vliptu = number_format($data->vliptu, 2, ',', '.');
            
            //$this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
            
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
            
            $object->dtinicio = TDate::date2br($object->dtinicio); 
            $object->dtfim = TDate::date2br($object->dtfim); 
            $object->dtvencto = TDate::date2br($object->dtvencto); 
            $object->dtpagto = TDate::date2br($object->dtpagto);           
            if (($object->dtpagto == 0 || $object->dtpagto !== NULL) && $object->vlpago > 0)
            {
                new TMessage('info', 'Parcela já Paga'); 
            }
            else
            {
                TDate::disableField('form_ParcelaReceber', 'dtpagto');
                TEntry::disableField('form_ParcelaReceber', 'vlpago');
                TEntry::disableField('form_ParcelaReceber', 'vljuros');
                TEntry::disableField('form_ParcelaReceber', 'vlmulta');
            }
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
            $object->vlpago = number_format($object->vlpago, 2, ',', '.');
            
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
                        $obj = new StdClass;
                        $obj->id = $object->id;
                        $obj->sequencia = $object->sequencia;
                        $obj->sequencia1 = $object->numero;
                        $obj->sequencia2 = substr($object->sequencia, -2);
                        $obj->dtinicio = TDate::date2br($object->dtinicio);
                        $obj->dtfim = TDate::date2br($object->dtfim);
                        $obj->dtvencto = TDate::date2br($object->dtvencto);
                        $obj->vlacrescido = number_format($object->vlacrescido, 2, ',', '.');
                        $obj->valor = number_format($object->valor, 2, ',', '.');
                        $obj->dtpagto = TDate::date2br($object->dtpagto);
                        if (($object->dtpagto == 0 || $object->dtpagto !== NULL) && $object->vlpago > 0)
                        {
                            new TMessage('info', 'Parcela já Paga'); 
                        }
                        else
                        {
                            TDate::disableField('form_ParcelaReceber', 'dtpagto');
                            TEntry::disableField('form_ParcelaReceber', 'vlpago');
                            TEntry::disableField('form_ParcelaReceber', 'vljuros');
                            TEntry::disableField('form_ParcelaReceber', 'vlmulta');
                        }
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
                        $obj->vlpago = number_format($object->vlpago, 2, ',', '.');
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
}
?>