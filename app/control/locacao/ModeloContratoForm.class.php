<?php
/**
 * ModeloContratoForm Form
 * @author  <your name here>
 */
class ModeloContratoForm extends TPage
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
        $this->form = new TQuickForm('form_ModeloContrato');
        $this->form->class = 'tform'; // change CSS class        
        $this->form->style = 'display: table;width:100%'; // change style
        //$this->form->setFormTitle('ModeloContrato');

        // create the form fields
        $id = new TEntry('id');
        $descricao = new TEntry('descricao');
        $tipocontrato_id = new TDBCombo('tipocontrato_id',TSession::getValue('banco'),'Tipocontrato','id','descricao');
        $percomissao = new TEntry('percomissao');
        $rescom = new TRadioGroup('rescom');
        $tipogarantia_id = new TDBCombo('tipogarantia_id',TSession::getValue('banco'),'Tipograntia','id','descricao');
        $qtdeparc = new TEntry('qtdeparc');
        $conteudo = new THtmlEditor('conteudo');
        
        //$percomissao->setNumericMask(2, ',', '.');
        
        $tipocontrato_id->setChangeAction(new TAction(array($this, 'onChangeTipoContrato')));

        // add the fields
        $this->form->addQuickField(_t('ID'), $id, 100 );
        $this->form->addQuickField('Descrição', $descricao, 400 );
        $this->form->addQuickField('Tipo de Contrato', $tipocontrato_id, 200 );
        $this->form->addQuickField('Percentual Comissão', $percomissao, 200 );
        $this->form->addQuickField('Residencial/Comercial', $rescom, 200 );
        $this->form->addQuickField('Tipo de Garantia', $tipogarantia_id, 200 );
        $this->form->addQuickField('Quantidade Parcelas', $qtdeparc, 50 );
        $this->form->addQuickField('Conteúdo', $conteudo );
        
        // Campos Não Editáveis
        $id->setEditable(FALSE);
        $conteudo->setSize(800, 400);
        
        // Adiciona Items aos campos RadioGroup e ComboBox                
        $rescom->addItems(array('R' => ' Residencial', 'C' => ' Comercial'));
        
        // Define Layout dos campos RadioGroup e ComboBox
        $rescom->setLayout('horizontal');
         
        // create the form actions
        $this->form->addQuickAction(_t('Save'), new TAction(array($this, 'onSave')), 'fa:floppy-o');
        $this->form->addQuickAction(_t('New'),  new TAction(array($this, 'onClear')), 'bs:plus-sign green');
        $this->form->addQuickAction(_t('Back to the listing'),new TAction(array('ModeloContratoList','onReload')),'fa:table blue');
        
        $html = new THtmlRenderer('app/resources/legenda.html');
        
        $html->enableSection('main');
        $html->enableTranslation();
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', 'ModeloContratoList'));
        $container->add($this->form);
        $container->add($html);
        
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
            
            $object = new ModeloContrato;  // create an empty object
            $data = $this->form->getData(); // get form data as array
            
            if ($data->percomissao == '' || $data->percomissao == NULL)
            {
                $data->percomissao = 0;
            }
            else
            {
                $data->percomissao = FuncoesExtras::retiraFormatacao($data->percomissao);
            }
            
            if ($data->rescom == '' || $data->rescom == NULL)
            {
                $data->rescom = ' ';
            }
            
            if ($data->tipogarantia_id == '' || $data->tipogarantia_id == NULL)
            {
                $data->tipogarantia_id = 0;
            }
            
            if ($data->qtdeparc == '' || $data->qtdeparc == NULL)
            {
                $data->qtdeparc = 0;
            }
            
            
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
        
        TEntry::disableField('form_ModeloContrato', 'percomissao');
        TRadioGroup::disableField('form_ModeloContrato', 'rescom');
        TDBCombo::disableField('form_ModeloContrato', 'tipogarantia_id');
        TEntry::disableField('form_ModeloContrato', 'qtdeparc');
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
                $object = new ModeloContrato($key); // instantiates the Active Record
                $tipo = $object->tipocontrato_id;
        
                if ($tipo == 1)
                {            
                    TEntry::enableField('form_ModeloContrato', 'percomissao');
                    TRadioGroup::disableField('form_ModeloContrato', 'rescom');
                    TEntry::disableField('form_ModeloContrato', 'tipogarantia_id');
                    TEntry::disableField('form_ModeloContrato', 'qtdeparc');
                }
                else
                {
                    TEntry::disableField('form_ModeloContrato', 'percomissao');
                    TRadioGroup::enableField('form_ModeloContrato', 'rescom');
                    TDBCombo::enableField('form_ModeloContrato', 'tipogarantia_id');
                    TEntry::enableField('form_ModeloContrato', 'qtdeparc');
                }
                
                $this->form->setData($object); // fill the form
                TTransaction::close(); // close the transaction
            }
            else
            {
                $this->form->clear();
                
                TEntry::disableField('form_ModeloContrato', 'percomissao');
                TRadioGroup::disableField('form_ModeloContrato', 'rescom');
                TDBCombo::disableField('form_ModeloContrato', 'tipogarantia_id');
                TEntry::disableField('form_ModeloContrato', 'qtdeparc');
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    public static function onChangeTipoContrato ( $param )
    {
        $tipo = $param['tipocontrato_id'];
        
        if ($tipo == 1)
        {            
            TEntry::enableField('form_ModeloContrato', 'percomissao');
            TRadioGroup::disableField('form_ModeloContrato', 'rescom');
            TDBCombo::disableField('form_ModeloContrato', 'tipogarantia_id');
            TEntry::disableField('form_ModeloContrato', 'qtdeparc');
        }
        else
        {
            TEntry::disableField('form_ModeloContrato', 'percomissao');
            TRadioGroup::enableField('form_ModeloContrato', 'rescom');
            TDBCombo::enableField('form_ModeloContrato', 'tipogarantia_id');
            TEntry::enableField('form_ModeloContrato', 'qtdeparc');
        }
    }
}
?>