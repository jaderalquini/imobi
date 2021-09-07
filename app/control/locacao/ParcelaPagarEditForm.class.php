<?php
/**
 * ParcelaPagarForm Form
 * @author  <your name here>
 */
class ParcelaPagarEditForm extends TPage
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
        $cliente_id = new THidden('cliente_id');
        $cliente = new TEntry('cliente');
        $proprietario_id = new TEntry('proprietario_id');
        $proprietario = new TEntry('proprietario');
        $dtinicio = new THidden('dtinicio');
        $dtfim = new THidden('dtfim');
        $dtvencto = new TDate('dtvencto');
        $valor = new TEntry('valor');
        $dtpagto = new TDate('dtpagto');
        $numrecibo = new TEntry('numrecibo');
        $vlpago = new THidden('vlpago');
        $opcomissao = new THidden('opcomissao');
        $vlcomissao = new THidden('vlcomissao');
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
        $opdevolucao->setEditable(FALSE);
        
        // Máscaras
        $dtvencto->setMask('dd/mm/yyyy');
        $dtpagto->setMask('dd/mm/yyyy');
        
        // Formatação para Valores Monetário
        $valor->setNumericMask(2, ',', '.');
        $vloutro->setNumericMask(2, ',', '.');
        $vlseguro->setNumericMask(2, ',', '.');
        $vlcondominio->setNumericMask(2, ',', '.');
        $vlluz->setNumericMask(2, ',', '.');
        $vlagua->setNumericMask(2, ',', '.');
        $vliptu->setNumericMask(2, ',', '.');
        $vldevolucao->setNumericMask(2, ',', '.');
        $vlgas->setNumericMask(2, ',', '.');
        
        // Tamanho dos Campos no formulário
        $sequencia1->setSize(25);
        $sequencia2->setSize(50);
        $bem_id->setSize(50);
        $bem->setSize(277);
        $proprietario_id->setSize(50);
        $proprietario->setSize(277);
        $opcomissao->setSize(25);
        $vlcomissao->setSize(172);
        $opoutro->setSize(25);
        $vloutro->setSize(172);
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
        
        $contrato_id->setExitAction(new TAction(array($this, 'onExitContrato')));
        $numero->setExitAction(new TAction(array($this, 'onExitParcela')));
        
        $bem_id->tabindex = '-1';

        // add the fields        
        $this->form->addQuickField('Contrato', $contrato_id,  50 );
        $this->form->addQuickFields('Bem', array($bem_id, $bem) );
        $this->form->addQuickFields('Locador', array($proprietario_id, $proprietario) );
        $this->form->addQuickField('Parcela', $numero,  50 );
        $this->form->addQuickFields('Sequência', array($sequencia1, $sequencia2) );
        $this->form->addQuickField('Data Vencimento', $dtvencto, 100 );
        $this->form->addQuickField('Valor', $valor ); 
        $this->form->addQuickField('Data Pagamento', $dtpagto, 100 );
        /*$this->form->addQuickField('Dtinicio', $dtinicio,  200 );
        $this->form->addQuickField('Dtfim', $dtfim,  200 );
        $this->form->addQuickField('Numrecibo', $numrecibo,  200 );*/
        $this->form->addQuickFields('Seguro', array($opseguro, $vlseguro) );
        $this->form->addQuickFields('Condomínio', array($opcondominio, $vlcondominio) );
        $this->form->addQuickFields('Luz', array($opluz, $vlluz) );
        $this->form->addQuickFields('Água', array($opagua, $vlagua) );
        $this->form->addQuickFields('Gás', array($opgas, $vlgas) );
        $this->form->addQuickFields('IPTU', array($opiptu, $vliptu) );
        //$this->form->addQuickField('Numrecpro', $numrecpro,  200 );
        $this->form->addQuickFields('Devolução', array($opdevolucao, $vldevolucao) );
        $this->form->addQuickFields('Outros', array($opoutro, $vloutro) );
        $this->form->addQuickField('Observacao', $observacao,  200 );     
        $this->form->addQuickField('', $id );   
        $this->form->addQuickField('', $cliente_id );
        $this->form->addQuickField('', $sequencia);
        $this->form->addQuickField('Data Início', $dtinicio,  200 );
        $this->form->addQuickField('Data Fim', $dtfim,  200 );            
        $this->form->addQuickField('', $vlpago );
        $this->form->addQuickField('', $opcomissao );
        $this->form->addQuickField('', $vlcomissao );      
        
        /** samples
         $this->form->addQuickFields('Date', array($date1, new TLabel('to'), $date2)); // side by side fields
         $fieldX->addValidation( 'Field X', new TRequiredValidator ); // add validation
         $fieldX->setSize( 100, 40 ); // set size
         **/
         
        // create the form actions
        $this->form->addQuickAction(_t('Save'), new TAction(array($this, 'onSave')), 'fa:floppy-o');
        $this->form->addQuickAction('Editar Mais Parcelas', new TAction(array('ParcelaPagarEditCollectionForm', 'onEdit')), 'fa:list blue');
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
            $data->vlseguro = FuncoesExtras::retiraFormatacao($data->vlseguro);
            $data->vlcondominio = FuncoesExtras::retiraFormatacao($data->vlcondominio);
            $data->vlluz = FuncoesExtras::retiraFormatacao($data->vlluz);
            $data->vlagua = FuncoesExtras::retiraFormatacao($data->vlagua);
            $data->vlgas = FuncoesExtras::retiraFormatacao($data->vlgas);
            $data->vliptu = FuncoesExtras::retiraFormatacao($data->vliptu);
            $data->devolucao = FuncoesExtras::retiraFormatacao($data->devolucao);
            $data->outro = FuncoesExtras::retiraFormatacao($data->outro);
            
            $object->fromArray( (array) $data); // load the object with data
            $object->store(); // save the object
            
            // get the generated id
            $data->id = $object->id;
            
            $data->dtvencto = TDate::date2br($data->dtvencto);
            $data->dtpagto = TDate::date2br($data->dtpagto);
            
            $data->vlacrescido = number_format($object->vlacrescido, 2, ',', '.');
            $data->valor = number_format($object->valor, 2, ',', '.');
            $data->vlcomissao = number_format($object->vlcomissao, 2, ',', '.');
            $data->vlseguro = number_format($object->vlseguro, 2, ',', '.');
            $data->vlcondominio = number_format($object->vlcondominio, 2, ',', '.');
            $data->vlluz = number_format($object->vlluz, 2, ',', '.');
            $data->vliptu = number_format($object->vliptu, 2, ',', '.');
            $data->vlagua = number_format($object->vlagua, 2, ',', '.');
            $data->vlgas = number_format($object->vlgas, 2, ',', '.');
            $data->vloutro = number_format($object->vloutro, 2, ',', '.');
            $data->vldevolucao = number_format($object->vldevolucao, 2, ',', '.');
            $data->vlpago = number_format($object->vlpago, 2, ',', '.');
            
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
     * Clear form data
     * @param $param Request
     */
    public function onClear( $param )
    {
        $this->form->clear();
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
            $key = $param['key'];
            $object = new ParcelaPagar($key);
            $cliente = new Cliente($object->proprietario_id);
            
            $object->dtvencto = TDate::date2br($object->dtvencto); 
            $object->dtpagto = TDate::date2br($object->dtpagto);           
            if (($object->dtpagto == 0 || $object->dtpagto !== NULL) && $object->vlpago > 0)
            {
                new TMessage('info', 'Parcela já Paga');
            }
            $object->vlacrescido = number_format($object->vlacrescido, 2, ',', '.');
            $object->valor = number_format($object->valor, 2, ',', '.');
            $object->vlcomissao = number_format($object->vlcomissao, 2, ',', '.');
            $object->vlseguro = number_format($object->vlseguro, 2, ',', '.');
            $object->vlcondominio = number_format($object->vlcondominio, 2, ',', '.');
            $object->vlluz = number_format($object->vlluz, 2, ',', '.');
            $object->vliptu = number_format($object->vliptu, 2, ',', '.');
            $object->vlagua = number_format($object->vlagua, 2, ',', '.');
            $object->vlgas = number_format($object->vlgas, 2, ',', '.');
            $object->vloutro = number_format($object->vloutro, 2, ',', '.');
            $object->vldevolucao = number_format($object->vldevolucao, 2, ',', '.');
            $object->vlpago = number_format($object->vlpago, 2, ',', '.');
            
            $sequencia = $object->sequencia;
            $tam = strlen($sequencia);
                                      
            $obj = new StdClass;
            $obj->contrato_id = $object->contrato_id;
            $obj->proprietario = $cliente->nome;
            $obj->cliente_id = $object->cliente_id;
            $obj->sequencia1 = substr($sequencia, 0, $tam - 2);
            $obj->sequencia2 = substr($sequencia, -2);
                         
            TForm::sendData('form_ParcelaPagar', $obj);
            
            $this->form->setData($object); // fill the form with the active record data
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
                $obj->proprietario_id = $bem->proprietario_id	;
                $obj->proprietario = $proprietario->nome;
                $obj->cliente_id = $contrato->cliente_id;
                
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
                        $obj = new StdClass;
                        $obj->id = $object->id;
                        $obj->sequencia = $object->sequencia;
                        $obj->sequencia1 = $object->numero;
                        $obj->sequencia2 = substr($object->sequencia, -2);
                        $obj->dtinicio = TDate::date2br($object->dtinicio);
                        $obj->dtfim = TDate::date2br($object->dtfim);
                        $obj->dtvencto = TDate::date2br($object->dtvencto);
                        $obj->valor = number_format($object->valor, 2, ',', '.');
                        $obj->dtpagto = TDate::date2br($object->dtpagto);
                        if (($object->dtpagto == 0 || $object->dtpagto !== NULL) && $object->vlpago > 0)
                        {
                            new TMessage('info', 'Parcela já Paga'); 
                        }
                        $obj->opcomissao = $object->opcomissao;
                        $obj->vlcomissao = $object->vlcomissao;
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
                        $obj->opdevolucao = $object->opdevolucao;
                        $obj->vldevolucao = number_format($object->vldevolucao, 2, ',', '.');
                        $obj->opoutro = $object->opoutro;
                        $obj->vloutro = number_format($object->vloutro, 2, ',', '.');
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
}
?>