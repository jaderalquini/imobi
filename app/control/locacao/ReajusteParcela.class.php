<?php
/**
 * ReajusteParcela Form
 * @author  <your name here>
 */
class ReajusteParcela extends TPage
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
        $this->form = new TQuickForm('form_ReajusteParcela');
        $this->form->class = 'tform'; // change CSS class        
        $this->form->style = 'display: table;width:100%'; // change style
        //$this->form->setFormTitle('ParcelaPagar');
        $this->form->setFieldsByRow(2);   
        
        $id = new THidden('id');
        $contrato_id = new TSeekButton('contrato_id');
        $parcela_de = new TSeekButton('parcela_de');
        $parcela_ate = new TEntry('parcela_ate');
        $cliente_id = new TEntry('cliente_id');
        $cliente = new TEntry('cliente');
        $indice = new TEntry('indice');
        
        $obj = new ContratoSelectionList;
        $action = new TAction(array($obj, 'onReload'));
        $contrato_id->setAction($action);
        
        $obj = new ParcelaReceberSelectionList;
        $action = new TAction(array($obj, 'onSetContrato'));
        $parcela_de->setAction($action);
        
        // Campos Não Editáveis
        $cliente_id->setEditable(FALSE);
        $cliente->setEditable(FALSE);
        
        // Formatação para Valores Monetário
        $indice->setNumericMask(2, ',', '.');
        
        // Tamanho dos Campos no formulário
        $cliente_id->setSize(50);
        $cliente->setSize(277);
        
        // Adiciona validação aos campos
        $contrato_id->addValidation('Contrato', new TRequiredValidator);
        $parcela_de->addValidation('Parcelas', new TRequiredValidator);
        $parcela_ate->addValidation('Até', new TRequiredValidator);
        $indice->addValidation('Índice', new TRequiredValidator);
        
        $contrato_id->setExitAction(new TAction(array($this, 'onExitContrato')));
        
        // add the fields
        $this->form->addQuickField('Contrato', $contrato_id,  50 );
        $this->form->addQuickFields('Locatário', array($cliente_id, $cliente) );
        $this->form->addQuickField('Parcela', $parcela_de,  50 );
        $this->form->addQuickField('Até', $parcela_ate,  50 );
        $this->form->addQuickField('Índice', $indice, 100);
        
        // create the form actions
        $this->form->addQuickAction(_t('Save'), new TAction(array($this, 'onSave')), 'fa:floppy-o');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', 'ReajusteParcela'));
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
            
            $data->indice = FuncoesExtras::retiraFormatacao($data->indice);
            
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
                    $contrato = new Contrato($parcela->contrato_id);
                    
                    $parcela->vlacrescido = $parcela->vlacrescido + $parcela->vlacrescido * ($data->indice / 100);
                    $parcela->valor = $parcela->vlacrescido - ($parcela->vlacrescido * $contrato->percdesc * 0.01);
                    if ($parcela->numero <= $contrato->qtdeparcdesc) {
                        $parcela->valor = $parcela->valor - $contrato->vldesc;
                    }
                    
                    $parcela->store();   
                }
            }
            
            $data->indice = number_format($data->indice, 2, ',', '.');
            
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
                $obj->cliente_id = $cliente->id;
                $obj->cliente = $cliente->nome;      
                                         
                TForm::sendData('form_ReajusteParcela', $obj);
                
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
