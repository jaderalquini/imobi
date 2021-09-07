<?php
/**
 * ContratoForm Form
 * @author  <your name here>
 */
class ContratoForm extends TPage
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
        $this->form = new TQuickForm('form_Contrato');
        $this->form->class = 'tform'; // change CSS class        
        $this->form->style = 'display: table;width:100%'; // change style
        //$this->form->setFormTitle('Contrato'); 
        $this->form->setFieldsByRow(2);       

        // create the form fields        
        $id = new TEntry('id');
        $bem_id = new TSeekButton('bem_id');
        $bem = new TEntry('bem');
        $proprietario_id = new TEntry('proprietario_id');
        $proprietario = new TEntry('proprietario'); 
        $criteria = new TCriteria;
        $criteria->add(new TFilter('id', '>', 0));
        $criteria->setProperty('order', 'nome');
        $cliente_id = new TDBSeekButton('cliente_id',TSession::getValue('banco'),'form_Contrato','Cliente','nome','cliente_id','cliente',$criteria);
        $cliente = new TEntry('cliente');
        $cliente2_id = new TDBSeekButton('cliente2_id',TSession::getValue('banco'),'form_Contrato','Cliente','nome','cliente2_id','cliente2',$criteria);
        $cliente2 = new TEntry('cliente2');
        $dtinicio = new TDate('dtinicio');
        $dtfim = new TDate('dtfim');
        $vlacrescido = new TEntry('vlacrescido');
        $percdesc = new TEntry('percdesc');
        $valor = new TEntry('valor');
        $vldesc = new TEntry('vldesc');
        $qtdeparcdesc = new TEntry('qtdeparcdesc');
        $avalista_id = new TDBSeekButton('avalista_id',TSession::getValue('banco'),'form_Contrato','Cliente','nome','avalista_id','avalista',$criteria);
        $avalista = new TEntry('avalista');
        $avalista2_id = new TDBSeekButton('avalista2_id',TSession::getValue('banco'),'form_Contrato','Cliente','nome','avalista2_id','avalista2',$criteria);
        $avalista2 = new TEntry('avalista2');
        $vlseguro = new TEntry('vlseguro');
        $qtdeparc = new TEntry('qtdeparc');
        $diavencto = new TEntry('diavencto');
        $dtcadastro = new TDate('dtcadastro');
        $tipogarantia_id = new TDBCombo('tipogarantia_id',TSession::getValue('banco'),'Tipograntia','id','descricao');
        $bemgarantia_descricao = new TEntry('bemgarantia_descricao');
        $bemgarantia_endereco = new TEntry('bemgarantia_endereco');
        $bemgarantia_matricula = new TEntry('bemgarantia_matricula');
        $bemgarantia_metragem = new TEntry('bemgarantia_metragem');
        $liquidado = new THidden('liquidado');
        $system_user_id = new THidden('system_user_id');
        
        $obj = new BemSelectionList;
        $action = new TAction(array($obj, 'onReload'));
        $bem_id->setAction($action);
        
        // Campos Não Editáveis
        $id->setEditable(FALSE);
        $proprietario_id->setEditable(FALSE);
        $proprietario->setEditable(FALSE);
        $cliente->setEditable(FALSE);
        $cliente2->setEditable(FALSE);
        $bem->setEditable(FALSE);
        $qtdeparc->setEditable(FALSE);
        $avalista->setEditable(FALSE);
        $avalista2->setEditable(FALSE);
        
        if (isset($param['key']))
        {
            $cliente_id->setEditable(FALSE);
            $cliente2_id->setEditable(FALSE);
            $bem_id->setEditable(FALSE);
            $dtinicio->setEditable(FALSE);
            $dtfim->setEditable(FALSE);
            $vlacrescido->setEditable(FALSE);
            $percdesc->setEditable(FALSE);
            $valor->setEditable(FALSE);
            $vldesc->setEditable(FALSE);
            $qtdeparcdesc->setEditable(FALSE);
            $vlseguro->setEditable(FALSE);
            $qtdeparc->setEditable(FALSE);
            $diavencto->setEditable(FALSE);
        }
        
        // Máscaras
        $dtinicio->setMask('dd/mm/yyyy');
        $dtfim->setMask('dd/mm/yyyy');
        $dtcadastro->setMask('dd/mm/yyyy');
        $dtcadastro->setMask('dd/mm/yyyy');
        
        // Formatação para Valores Monetário
        $vlacrescido->setNumericMask(2, ',', '.');
        $percdesc->setNumericMask(2, ',', '.');
        $valor->setNumericMask(2, ',', '.');
        $vldesc->setNumericMask(2, ',', '.');
        $vlseguro->setNumericMask(2, ',', '.');
        $bemgarantia_metragem->setNumericMask(2, ',', '.');
        
        // Tamanho dos Campos no formulário
        $bem_id->setSize(50);
        $bem->setSize(277); 
        $proprietario_id->setSize(50);
        $proprietario->setSize(277);
        $cliente_id->setSize(50);
        $cliente->setSize(277);        
        $cliente2_id->setSize(50);
        $cliente2->setSize(277);           
        $avalista_id->setSize(50);
        $avalista->setSize(277);        
        $avalista2_id->setSize(50);
        $avalista2->setSize(277);
        
        // Número de Caracteres permitidos dentro dos campos
        
        
        // Define actions dos campos
        $bem_id->setExitAction(new TAction(array($this, 'onExitBem')));
        $dtinicio->setExitAction(new TAction(array($this, 'onExitData')));
        $dtfim->setExitAction(new TAction(array($this, 'onExitData')));
        $percdesc->setExitAction(new TAction(array($this, 'onExitValor')));
        $tipogarantia_id->setChangeAction(new TAction(array($this, 'onChangeTipoGarantia')));
        
        // Adiciona validação aos campos
        $bem_id->addValidation('Bem', new TRequiredValidator);
        $dtinicio->addValidation('Data Início', new TRequiredValidator);
        $dtfim->addValidation('Data Fim', new TRequiredValidator);
        $cliente_id->addValidation('Localdor 1', new TRequiredValidator);
        $diavencto->addValidation('Dia Vencimento', new TRequiredValidator);
        $diavencto->addValidation('Dia Vencimento', new TMinValueValidator, array(1));
        $dtcadastro->addValidation('Data Cadastro', new TRequiredValidator);
        //$tipogarantia_id->addValidation('Tipo de Garantia', new TRequiredValidator);

        // add the fields
        $this->form->addQuickField(_t('ID'), $id, 50 );
        $this->form->addQuickFields('Bem', array($bem_id, $bem) );
        $this->form->addQuickFields('Locador', array($proprietario_id, $proprietario) );
        $this->form->addQuickFields('Locatário 1', array($cliente_id, $cliente) );
        $this->form->addQuickFields('Locatário 2', array($cliente2_id, $cliente2) );
        $this->form->addQuickField('Data Inicial', $dtinicio, 100 );
        $this->form->addQuickField('Data Fim', $dtfim, 100 );
        $this->form->addQuickField('Qtde. Parcelas', $qtdeparc, 50 );
        $this->form->addQuickField('Valor Aluguel', $vlacrescido );
        $this->form->addQuickField('% Desconto', $percdesc, 100 );
        $this->form->addQuickField('Valor Líquido', $valor );
        $this->form->addQuickField('Valor Desconto', $vldesc );
        $this->form->addQuickField('Qtde. Parc. Desc.', $qtdeparcdesc );
        $this->form->addQuickField('Valor Seguro', $vlseguro );
        $this->form->addQuickField('Dia Vencimento', $diavencto, 50 );
        $this->form->addQuickField('Data Cadastro', $dtcadastro, 100 );
        $this->form->addQuickField('Tipo de Grantia', $tipogarantia_id, 100);
        $this->form->addQuickFields('Fiador 1', array($avalista_id, $avalista) );
        $this->form->addQuickFields('Fiador 2', array($avalista2_id, $avalista2) );
        $this->form->addQuickField('Bem Garantia', $bemgarantia_descricao, 350 );
        $this->form->addQuickField('Bem Garantia (Endereço)', $bemgarantia_endereco, 350 );
        $this->form->addQuickField('Bem Garantia (Matrícula)', $bemgarantia_matricula, 100 );
        $this->form->addQuickField('Bem Garantia (Matragem)', $bemgarantia_metragem, 100 );
        $this->form->addQuickField('', $liquidado );
        $this->form->addQuickField('', $system_user_id );
        
        /** samples
         $this->form->addQuickFields('Date', array($date1, new TLabel('to'), $date2)); // side by side fields
         $fieldX->addValidation( 'Field X', new TRequiredValidator ); // add validation
         $fieldX->setSize( 100, 40 ); // set size
         **/
         
        // create the form actions
        $this->form->addQuickAction(_t('Save'), new TAction(array($this, 'onSave')), 'fa:floppy-o');
        $this->form->addQuickAction(_t('New'),  new TAction(array($this, 'onClear')), 'bs:plus-sign green');
        $this->form->addQuickAction('Imprimir Contrato', new TAction(array('ContratoList','onPrint')),'fa:print black fa-lg');
        $this->form->addQuickAction('Novo Bem', new TAction(array('BemWindowForm','onEdit')), 'fa:home blck fa-fg');
        $this->form->addQuickAction('Novo Cliente', new TAction(array('ClienteWindowForm','onEdit')), 'fa:user blck fa-fg');
        $this->form->addQuickAction(_t('Back to the listing'),new TAction(array('ContratoList','onReload')),'fa:table blue');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', 'ContratoList'));
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
            $conn = TTransaction::get();
            
            $object = new Contrato;  // create an empty object
            $data = $this->form->getData(); // get form data as array
            self::validate($data);
            
            $data->vlacrescido = FuncoesExtras::retiraFormatacao($data->vlacrescido);
            $data->percdesc = FuncoesExtras::retiraFormatacao($data->percdesc);
            $data->valor = FuncoesExtras::retiraFormatacao($data->valor);
            $data->vldesc = FuncoesExtras::retiraFormatacao($data->vldesc);
            $data->vlseguro = FuncoesExtras::retiraFormatacao($data->vlseguro);
            $data->dtinicio = TDate::date2us($data->dtinicio );
            $data->dtfim = TDate::date2us($data->dtfim );                        
            $data->dtcadastro = TDate::date2us($data->dtcadastro );
            
            if ($data->avalista_id == '')
            {
                $data->avalista_id = 0;
            }
            
            $object->fromArray( (array) $data); // load the object with data
            $object->store(); // save the object
            
            // get the generated id
            $data->id = $object->id;
            
            $data->dtinicio = TDate::date2br($object->dtinicio );
            $data->dtfim = TDate::date2br($object->dtfim );
            $data->vlacrescido = number_format($object->vlacrescido, 2, ',', '.');
            $data->percdesc = number_format($object->percdesc, 2, ',', '.');
            $data->valor = number_format($object->valor, 2, ',', '.');
            $data->vldesc = number_format($object->vldesc, 2, ',' ,'.');
            $data->vlseguro = number_format($object->vlseguro, 2, ',' ,'.');
            $data->dtcadastro = TDate::date2br($object->dtcadastro );
            
            TDBSeekButton::disableField('form_Contrato', 'bem_id');
            TDBSeekButton::disableField('form_Contrato', 'cliente_id');
            TDBSeekButton::disableField('form_Contrato', 'cliente2_id');
            TDate::disableField('form_Contrato', 'dtinicio');
            TDate::disableField('form_Contrato', 'dtfim');
            TEntry::disableField('form_Contrato', 'qtdeparc');
            TEntry::disableField('form_Contrato', 'vlacrescido');
            TEntry::disableField('form_Contrato', 'percdesc');
            TEntry::disableField('form_Contrato', 'valor');
            TEntry::disableField('form_Contrato', 'vldesc');
            TEntry::disableField('form_Contrato', 'qtdeparcdesc');
            TEntry::disableField('form_Contrato', 'vlseguro');
            TEntry::disableField('form_Contrato', 'diavencto');
            
            $this->form->setData($data); // fill form data
            
            if (!isset($param['id']) || ($param['id'] == '' || $param['id'] == NULL))
            {                
                $bem = array();
                $bem['id'] = $data->bem_id;
                $bem['contrato_id'] = $data->id;
                $bem['cliente_id'] = $data->cliente_id;
                $bem['cliente2_id'] = intval($data->cliente2_id);
                
                $objbem = new Bem();
                $objbem->fromArray( (array) $bem);
                $objbem->store();
                
                $qtdeparc = $data->qtdeparc;
                $bem = new Bem($data->bem_id);
                $diapagto = $bem->diapagto;
                $diavencto = $data->diavencto;
                $dtinicio = TDate::date2us($data->dtinicio);
                $dtinicio = date('Y-m-'.$diavencto, strtotime("+1 month", strtotime($dtinicio)));
                $dtfim = TDate::date2us($data->dtfim);
                $dtfim = date('Y-m-'.$diavencto, strtotime("+1 month", strtotime($dtfim)));
                $i = 0;
                for ($date = strtotime($dtinicio); $date < strtotime($dtfim); $date = strtotime("+1 month", $date))                                                                                                                                                                                                                                           
                {
                    $i++;
                    $parcela = new ParcelaPagar();
                    $parcela->contrato_id = $data->id;
                    $parcela->numero = $i;
                    $parcela->sequencia = $i . $qtdeparc;
                    $parcela->bem_id = $data->bem_id;
                    $parcela->cliente_id = $data->cliente_id;
                    $parcela->dtinicio = TDate::date2us($data->dtinicio);
                    $parcela->dtfim = TDate::date2us($data->dtfim);
                    $ano = date('Y', $date);
                    $mes = date('m', $date);
                    $parcela->dtvencto = $ano . '-' . $mes . '-' . $bem->diapagto;
                    $parcela->dtpagto = '0';
                    
                    $valor = FuncoesExtras::retiraFormatacao($data->valor);
                    $vldesc = FuncoesExtras::retiraFormatacao($data->vldesc);
                    $vlseguro = FuncoesExtras::retiraFormatacao($data->vlseguro);
                    $qtdeparcdesc = FuncoesExtras::retiraFormatacao($data->qtdeparcdesc);
                    $percdesc = FuncoesExtras::retiraFormatacao($data->percdesc);
                    
                    $bem = new Bem($data->bem_id);
                    
                    $percomissao = $bem->percomissao;
                    
                    $vlcomissao = $valor * $percomissao * 0.01;
                    
                    $parcela->valor = $valor;
                    $parcela->proprietario_id = $bem->proprietario_id;
                    $parcela->vlcomissao = $vlcomissao;
                    $parcela->cliente2_id = intval($data->cliente2_id);
                    $parcela->store();
                    
                    $parcela = new ParcelaReceber();
                    $parcela->contrato_id = $data->id;
                    $parcela->numero = $i;
                    $parcela->sequencia = str_pad($i, 2, '0', STR_PAD_LEFT) . $qtdeparc;
                    $parcela->bem_id = $data->bem_id;
                    $parcela->cliente_id = $data->cliente_id;
                    $parcela->dtinicio = TDate::date2us($data->dtinicio);
                    $parcela->dtfim = TDate::date2us($data->dtfim);
                    $parcela->dtvencto = date('Y-m-d', $date);
                    $parcela->dtpagto = '0';
                    
                    $vlacrescido = FuncoesExtras::retiraFormatacao($data->vlacrescido);
                    $valor = FuncoesExtras::retiraFormatacao($data->valor);
                    $vldesc = FuncoesExtras::retiraFormatacao($data->vldesc);
                    $vlseguro = FuncoesExtras::retiraFormatacao($data->vlseguro);
                    
                    if ($i <= $qtdeparcdesc)
                    {
                        $valor = $valor - $vldesc;
                    }
                    else
                    {
                        $vldesc = 0;
                    }
                    
                    $parcela->vlacrescido = $vlacrescido;
                    $parcela->valor = $valor;
                    $parcela->vldesc = $vldesc;
                    $parcela->cliente2_id = intval($data->cliente2_id);
                    $parcela->store();
                }
            }  
            
            $this->onChangeTipoGarantia(array('tipogarantia_id' => $data->tipogarantia_id));    
            
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
                $object = new Contrato($key); // instantiates the Active Record
                
                if ($object->dtinicio !== NULL) {
                    $object->dtinicio = TDate::date2br($object->dtinicio );
                }
                
                if ($object->dtfim !== NULL) {
                    $object->dtfim = TDate::date2br($object->dtfim );
                }
                
                $object->vlacrescido = number_format($object->vlacrescido, 2, ',', '.');
                $object->percdesc = number_format($object->percdesc, 2, ',', '.');
                $object->valor = number_format($object->valor, 2, ',', '.');
                $object->vldesc = number_format($object->vldesc, 2, ',' ,'.');
                $object->vlseguro = number_format($object->vlseguro, 2, ',' ,'.');
                
                if ($object->dtcadastro !== NULL) {
                    $object->dtcadastro = TDate::date2br($object->dtcadastro );
                }
                
                $bem = new Bem($object->bem_id);
                $locador = new Cliente($bem->proprietario_id);
                $locatario = new Cliente($object->cliente_id);
                try
                {
                    $locatario2 = new Cliente($object->cliente2_id);
                }
                catch (Exception $e) // in case of exception
                {
                    return NULL;
                }
                $avalista = new Cliente($object->avalista_id);
                try
                {
                    $avalista2 = new Cliente($object->avalista2_id);
                }
                catch (Exception $e) // in case of exception
                {
                    return NULL;
                }
                
                $obj = new StdClass;
                $obj->bem = $bem->descricao;
                $obj->proprietario_id = $locador->id;
                $obj->proprietario = $locador->nome;
                $obj->cliente = $locatario->nome;
                $obj->cliente2 = $locatario2->nome;
                $obj->avalista = $avalista->nome;
                $obj->avalista2 = $avalista2->nome;
                
                TForm::sendData('form_Contrato', $obj);
                
                $this->form->setData($object); // fill the form
                
                $this->onChangeTipoGarantia(array('tipogarantia_id' => $object->tipogarantia_id));
         
                TTransaction::close(); // close the transaction
            }
            else
            {
                $this->form->clear();
                
                $obj = new StdClass;
                $obj->dtcadastro = date('d/m/Y');
                $obj->liquidado = 'N';
                $obj->system_user_id = TSession::getValue('userid');
                
                TForm::sendData('form_Contrato', $obj);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    public static function onExitBem($param)
    {        
        TTransaction::open(TSession::getValue('banco'));
        
        $bem = new Bem($param['bem_id']);
        $cliente = new Cliente($bem->proprietario_id);
        $obj = new StdClass;
        $obj->proprietario_id = $cliente->id;
        $obj->proprietario = $cliente->nome;
        $obj->vlacrescido = number_format($bem->vlaluguel, 2, ',', '.');
        $obj->vldesc = number_format($bem->vldesc, 2, ',', '.');
        $obj->valor = number_format($bem->vlaluguel, 2, ',', '.');
        $obj->qtdeparcdesc = $bem->qtdemes;
        
        TForm::sendData('form_Contrato', $obj);
        
        TTransaction::close();
    }
    
    public static function onExitData($param)
    {        
        try
        {
            $dtinicio = $param['dtinicio'];
            $dtfim = $param['dtfim'];
            if ($dtinicio !== '' && $dtinicio !== NULL && $dtfim !== '' && $dtfim !== NULL)
            {
                $dtinicio = TDate::date2us($dtinicio);
                $dtfim = TDate::date2us($dtfim); 
                
                $dtinicio = new DateTime($dtinicio);
                $dtfim = new DateTime($dtfim);
                
                $dif = $dtinicio->diff($dtfim);
                $anos = $dif->format('%Y')*12;
                $meses = $dif->format('%m');
                
                $qtdeparc = $anos + $meses;
            } else 
            {
                $qtdeparc = 0;               
            }
            
            $obj = new StdClass;
            $obj->qtdeparc = $qtdeparc;
                
            TForm::sendData('form_Contrato', $obj);   
        }  
        catch (Exception $e)
        {
            
        }      
    }
    
    public static function onExitValor($param)
    {
        try
        {
            $source = array('.', ',');
            $replace = array('','.');    
            
            $vlacrescido = $param['vlacrescido'];
            $percdesc = $param['percdesc'];
            
            $vlacrescido = str_replace($source, $replace, $vlacrescido);
            $percdesc = str_replace($source, $replace, $percdesc);
            
            $obj = new StdClass;
            $valor = $vlacrescido - ($vlacrescido * $percdesc * 0.01);
            $obj->valor = number_format($valor, 2, ',', '.');
            
            TForm::sendData('form_Contrato', $obj);
        }
        catch (Exception $e)
        {
            
        }
    }
    
    public static function onChangeTipoGarantia ( $param )
    {
        $tipo = $param['tipogarantia_id'];
        
        if ($tipo == 1)
        {            
            TEntry::disableField('form_Contrato', 'matricula');
            TEntry::enableField('form_Contrato', 'avalista_id');
            TEntry::enableField('form_Contrato', 'avalista2_id');
            TEntry::enableField('form_Contrato', 'avalista2_id');
            TEntry::enableField('form_Contrato', 'bemgarantia_descricao');
            TEntry::enableField('form_Contrato', 'bemgarantia_endereco');
            TEntry::enableField('form_Contrato', 'bemgarantia_matricula');
            TEntry::enableField('form_Contrato', 'bemgarantia_metragem');
        }
        else
        {
            TEntry::disableField('form_Contrato', 'avalista_id');
            TEntry::disableField('form_Contrato', 'avalista2_id');
            TEntry::disableField('form_Contrato', 'avalista2_id');
            TEntry::disableField('form_Contrato', 'bemgarantia_descricao');
            TEntry::disableField('form_Contrato', 'bemgarantia_endereco');
            TEntry::disableField('form_Contrato', 'bemgarantia_matricula');
            TEntry::disableField('form_Contrato', 'bemgarantia_metragem');
        }
    }
    
    public function validate($data)
    {
        $this->form->validate(); // validate form data
        
        $dtinicio = TDate::date2us($data->dtinicio);
        $dtfim = TDate::date2us($data->dtfim);
        
        if (strtotime($dtinicio) > strtotime($dtfim))
        {
            throw new Exception('A data final deve ser maior que a data inicial. ');
        }
        
        $tipo = $data->tipogarantia_id;
        
        /*if ($tipo == 1)
        {
            $validator = new TRequiredValidator;
            $validator->validate('Fiador 1', $data->avalista_id);
        }*/
    }
}
?>
