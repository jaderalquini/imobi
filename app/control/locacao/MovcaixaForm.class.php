<?php
/**
 * MovcaixaForm Form
 * @author  <your name here>
 */
class MovcaixaForm extends TPage
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
        $this->form = new TQuickForm('form_Movcaixa');
        $this->form->class = 'tform'; // change CSS class        
        $this->form->style = 'display: table;width:100%'; // change style
        //$this->form->setFormTitle('Movimentação de  Caixa');
        $this->form->setFieldsByRow(2);        

        // create the form fields
        $id = new TEntry('id');
        $dtmov = new TDate('dtmov');
        $seq = new TEntry('seq');
        $tipo = new TRadioGroup('tipo');
        $historico = new TEntry('historico');
        $valor = new TEntry('valor');
        
        // Máscaras
        $dtmov->setMask('dd/mm/yyyy');
        
        // Adiciona Items aos campos RadioGroup e ComboBox
        $tipo->addItems(array('E' => ' Entrada', 'S' => ' Saída'));
        
        // Define Layout dos campos RadioGroup e ComboBox
        $tipo->setLayout('horizontal');
        
        // Adiciona validação aos campos
        $dtmov->addValidation('Data', new TRequiredValidator);
        $seq->addValidation('Sequência', new TRequiredValidator);
        $tipo->addValidation('Tipo', new TRequiredValidator);
        $historico->addValidation('Histórico', new TRequiredValidator);
        $valor->addValidation('Valor', new TRequiredValidator);

        // add the fields
        //$this->form->addQuickField('ID', $id,  50 );
        $this->form->addQuickField('Data', $dtmov,  100 );
        $this->form->addQuickField('Sequência', $seq,  50 );
        $this->form->addQuickField('Tipo', $tipo,  100 );
        $this->form->addQuickField('Histórico', $historico,  400 );
        $this->form->addQuickField('Valor', $valor,  100 );


        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        /** samples
         $this->form->addQuickFields('Date', array($date1, new TLabel('to'), $date2)); // side by side fields
         $fieldX->addValidation( 'Field X', new TRequiredValidator ); // add validation
         $fieldX->setSize( 100, 40 ); // set size
         **/
         
        // create the form actions
        $this->form->addQuickAction(_t('Save'), new TAction(array($this, 'onSave')), 'fa:floppy-o');
        $this->form->addQuickAction(_t('New'),  new TAction(array($this, 'onClear')), 'bs:plus-sign green');
        $this->form->addQuickAction(_t('Back to the listing'),new TAction(array('MovcaixaList','onReload')),'fa:table blue');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', 'MovcaixaList'));
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
            
            $object = new Movcaixa;  // create an empty object
            $data = $this->form->getData(); // get form data as array            
            $object->fromArray( (array) $data); // load the object with data
            $object->store(); // save the object
            
            // get the generated id
            $data->id = $object->id;
            
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
                $object = new Movcaixa($key); // instantiates the Active Record
                
                if ($object->dtmov !== NULL) {
                    $date = new DateTime($object->dtmov);
                    $object->dtmov = $date->format('d/m/Y');
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
}
?>