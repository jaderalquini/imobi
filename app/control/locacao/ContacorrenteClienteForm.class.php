<?php
/**
 * ContacorrenteClienteForm Form
 * @author  <your name here>
 */
class ContacorrenteClienteForm extends TPage
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
        $this->form = new TQuickForm('form_ContacorrenteCliente');
        $this->form->class = 'tform'; // change CSS class        
        $this->form->style = 'display: table;width:100%'; // change style
        //$this->form->setFormTitle('Contaa Correntes (Clientes)');
        $this->form->setFieldsByRow(2);        

        // create the form fields
        $id = new TEntry('id');
        $criteria = new TCriteria;
        $criteria->setProperty('order', 'nome');    
        $cliente_id = new TDBSeekButton('cliente_id',TSession::getValue('banco'),'form_ContacorrenteCliente','Cliente','nome','cliente_id','cliente',$criteria);
        $cliente = new TEntry('cliente');
        $banco = new TEntry('banco');
        $praca = new TEntry('praca');
        $numero = new TEntry('numero');
        $digito = new TEntry('digito');
        $agencia = new TEntry('agencia');
        $digitoagencia = new TEntry('digitoagencia');
        $nomedep = new TEntry('nomedep');
        
        // Campos Não Editáveis
        $cliente->setEditable(FALSE);
        
        // Tamanho dos Campos no formulário
        $cliente_id->setSize(50);
        $cliente->setSize(277);
        
        // Número de Caracteres permitidos dentro dos campos
        
        
        // Adiciona validação aos campos
        $cliente_id->addValidation('Cliente', new TRequiredValidator);
        $banco->addValidation('Banco', new TRequiredValidator);
        $numero->addValidation('N° Conta', new TRequiredValidator);
        $digito->addValidation('Dígito', new TRequiredValidator);
        $agencia->addValidation('Agência', new TRequiredValidator);
        $digitoagencia->addValidation('Dígito Agência', new TRequiredValidator);

        // add the fields
        //$this->form->addQuickField(_t('ID'), $id,  50 );
        $this->form->addQuickFields('Cliente', array($cliente_id, $cliente) );
        $this->form->addQuickField('Banco', $banco,  350 );
        $this->form->addQuickField('Praça', $praca,  350 );
        $this->form->addQuickField('N° Conta', $numero,  100 );
        $this->form->addQuickField('Dígito', $digito,  50 );
        $this->form->addQuickField('Agência', $agencia,  100 );
        $this->form->addQuickField('Dígito Agência', $digitoagencia,  50 );
        $this->form->addQuickField('Nome Depositante', $nomedep,  350 );

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
        $this->form->addQuickAction(_t('Back to the listing'),new TAction(array('ContacorrenteClienteList','onReload')),'fa:table blue');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', 'ContacorrenteClienteList'));
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
            
            $object = new ContacorrenteCliente;  // create an empty object
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
                $object = new ContacorrenteCliente($key); // instantiates the Active Record
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