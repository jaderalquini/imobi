<?php
/**
 * MovbancariaForm Form
 * @author  <your name here>
 */
class MovbancariaForm extends TPage
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
        $this->form = new TQuickForm('form_Movbancaria');
        $this->form->class = 'tform'; // change CSS class        
        $this->form->style = 'display: table;width:100%'; // change style
        //$this->form->setFormTitle('Movbancaria');
        $this->form->setFieldsByRow(2);

        // create the form fields
        $id = new THidden('id');
        $dtmov = new TDate('dtmov');
        $seq = new TEntry('seq');
        $banco_id = new TSeekButton('banco_id');
        $banco = new TEntry('banco');
        $criteria = new TCriteria;
        $criteria->setProperty('order', 'descricao');
        $contacorrente_id = new TEntry('contacorrente_id');
        $contacorrente = new TEntry('contacorrente');
        $tipo = new TRadioGroup('tipo');
        $historico = new TEntry('historico');
        $valor = new TEntry('valor');
        
        $obj = new BancoSelectionList;
        $action = new TAction(array($obj, 'onReload'));
        $banco_id->setAction($action);
        
        // Campos Não Editáveis
        $banco->setEditable(FALSE);
        $contacorrente->setEditable(FALSE);
        
        // Formatação para Valores Monetário
        $valor->setNumericMask(2, ',', '.');
        
        // Máscaras
        $dtmov->setMask('dd/mm/yyyy');
        
        // Tamanho dos Campos no formulário
        $banco_id->setSize(50);
        $banco->setSize(327);
        $contacorrente_id->setSize(100);
        $contacorrente->setSize(277);
        
        // Número de Caracteres permitidos dentro dos campos
        
        // Adiciona Items aos campos RadioGroup e ComboBox
        $tipo->addItems(array('E' => ' Entrada', 'S' => ' Saída'));
        
        // Define Layout dos campos RadioGroup e ComboBox
        $tipo->setLayout('horizontal');
        
        // Define actions dos campos
        $contacorrente_id->setExitAction(new TAction(array($this, 'onExitContacorrente')));
        
        // Adiciona validação aos campos
        $dtmov->addValidation('Data Movimento', new TDateValidator);
        $seq->addValidation('Sequência', new TRequiredValidator);
        $banco_id->addValidation('Banco', new TRequiredValidator);
        $contacorrente_id->addValidation('Conta', new TRequiredValidator);
        $tipo->addValidation('Tipo', new TRequiredValidator);
        $historico->addValidation('Histórico', new TRequiredValidator);
        $valor->addValidation('Valor', new TRequiredValidator);

        // add the fields        
        $this->form->addQuickField('Data Movimento', $dtmov,  100 );
        $this->form->addQuickField('Sequência', $seq,  50 );
        $this->form->addQuickFields('Banco', array($banco_id, $banco) );
        $this->form->addQuickFields('Conta', array($contacorrente_id, $contacorrente) );
        $this->form->addQuickField('Tipo', $tipo,  100 );
        $this->form->addQuickField('Histórico', $historico,  300 );
        $this->form->addQuickField('Valor', $valor,  200 );
        $this->form->addQuickField('', $id );
        
        /** samples
         $this->form->addQuickFields('Date', array($date1, new TLabel('to'), $date2)); // side by side fields
         $fieldX->addValidation( 'Field X', new TRequiredValidator ); // add validation
         $fieldX->setSize( 100, 40 ); // set size
         **/
         
        // create the form actions
        $this->form->addQuickAction(_t('Save'), new TAction(array($this, 'onSave')), 'fa:floppy-o');
        $this->form->addQuickAction(_t('New'),  new TAction(array($this, 'onClear')), 'bs:plus-sign green');
        $this->form->addQuickAction(_t('Back to the listing'),new TAction(array('MovbancariaList','onReload')),'fa:table blue');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', 'MovbancariaList'));
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
            
            self::validate( $param );
            
            $object = new Movbancaria;  // create an empty object
            $data = $this->form->getData(); // get form data as array
            
            $data->dtmov = TDate::date2us($data->dtmov );
            $data->valor = FuncoesExtras::retiraFormatacao($data->valor);
            
            $object->fromArray( (array) $data); // load the object with data
            $object->store(); // save the object
            
            // get the generated id
            $data->id = $object->id;
            
            $data->dtmov = TDate::date2br($data->dtmov);
            $data->valor = number_format($data->valor, 2, ',', '.');
            
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
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open(TSession::getValue('banco')); // open a transaction
                $object = new Movbancaria($key); // instantiates the Active Record
                
                if ($object->dtmov !== NULL) {
                    $object->dtmov = TDate::date2br($object->dtmov );
                }
                
                $object->valor = number_format($object->valor, 2, ',', '.');
                
                $this->form->setData($object); // fill the form
                TTransaction::close(); // close the transaction
            }
            else
            {
                $this->form->clear();
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    public static function onExitContacorrente( $param )
    {
        try
        {
            TTransaction::open(TSession::getValue('banco'));
        
            $conta = new Contacorrente($param['contacorrente_id']);
                
            $obj = new StdClass;
            $obj->contacorrente = $conta->descricao;
                    
            TForm::sendData('form_Movbancaria', $obj);
                
            TTransaction::close();   
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', 'Conta não cadastrada...');    
        }
    }
    
    public function validate( $param )
    {        
        $this->form->validate(); // validate form data
        
        try
        {
            TTransaction::open(TSession::getValue('banco'));
        
            $conta = new Contacorrente($param['contacorrente_id']);
        
            TTransaction::close();    
        }
        catch (Exception $e) // in case of exception
        {
            throw new Exception('Conta não cadastrada...');
        }
    }
}
?>