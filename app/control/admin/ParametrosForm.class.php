<?php
/**
 * ParametrosForm
 *
 */
class ParametrosForm extends TPage
{
    protected $form; // formulário
    
    /**
     * método construtor
     * Cria a página e o formulário de cadastro
     */
    function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new TQuickForm('form_Parametros');
        $this->form->class = 'tform'; // change CSS class        
        $this->form->style = 'display: table;width:100%'; // change style
        //$this->form->setFormTitle('Empresas');
        $this->form->setFieldsByRow(2);
        
        // cria os campos do formulário
        $juros = new TEntry('juros');
        $multa   = new TEntry('multa');
        
        $juros->setNumericMask(2, ',', '.');
        $multa->setNumericMask(2, ',', '.');
        
        $this->form->addQuickField('Juros', $juros, 200, new TRequiredValidator);
        $this->form->addQuickField('Multa', $multa, 200, new TRequiredValidator);
        
        // create the form actions
        $this->form->addQuickAction(_t('Save'), new TAction(array($this, 'onSave')), 'fa:floppy-o');
        
        $container = new TVBox;
        $container->{'style'} = 'width: 100%;';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }
    
    /**
     * Carrega o formulário de preferências
     */
    function onEdit($param)
    {
        try
        {
            // open a transaction with database
            TTransaction::open('permission');
            
            $parametros = Parametros::getAllParametros();
            if ($parametros)
            {
                $this->form->setData((object) str_replace('.', ',', $parametros));
            }
            
            // close the transaction
            TTransaction::close();
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
     * Save form data
     * @param $param Request
     */
    public function onSave( $param )
    {
        try
        {
            TTransaction::open('permission'); // open a transaction
            
            // get the form data
            $data = $this->form->getData();
            $data_array = (array) $data;
            
            foreach ($data_array as $property => $value)
            {
                $object = new Parametros;
                $object->{'id'}    = str_replace(',', '.', $property);
                $object->{'value'} = str_replace(',', '.', $value);
                $object->store();
            }
            
            // fill the form with the active record data
            $this->form->setData($data);
            
            // close the transaction
            TTransaction::close();
            
            // shows the success message
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
            // reload the listing
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }
}