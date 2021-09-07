<?php
/**
 * BemForm Form
 * @author  <your name here>
 */
class BemForm extends TPage
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
        $this->form = new TQuickForm('form_Bem');
        $this->form->class = 'tform'; // change CSS class        
        $this->form->style = 'display: table;width:100%'; // change style
        //$this->form->setFormTitle('Bem');
        $this->form->setFieldsByRow(2);

        $script = new TElement('script');
        $script->type = 'text/javascript';
        $script->add('
            $(document).ready(function() {
                $("input[name=cep]").blur(function() {
                    cep = $(this).val();
                    if($.trim(cep) != ""){
		                $.getScript("http://cep.republicavirtual.com.br/web_cep.php?formato=javascript&cep="+cep, function(){
			                if(resultadoCEP["resultado"] == 1){
			                    var endereco = unescape(resultadoCEP["tipo_logradouro"])+" "+unescape(resultadoCEP["logradouro"]);
			                    var bairro = unescape(resultadoCEP["bairro"]);
			                    var cidade = unescape(resultadoCEP["cidade"]);
			                    var uf = unescape(resultadoCEP["uf"]);
			                    
			                    $("input[name=endereco]").val(endereco.toUpperCase());
			                    $("input[name=bairro]").val(bairro.toUpperCase());
			                    $("input[name=municipio]").val(cidade.toUpperCase());
			                    $("select[name=uf_id]").val(uf.toUpperCase());
			                }
		                });
	                }
                });
            });
        ');
        parent::add($script);

        // create the form fields
        $id = new TEntry('id');
        $descricao = new TEntry('descricao');
        $endereco = new TEntry('endereco');
        $complemento = new TEntry('complemento');
        $criteria = new TCriteria;
        $criteria->setProperty('order', 'nome');
        $municipio_id = new TDBSeekButton('municipio_id',TSession::getValue('banco'),'form_Bem','Municipio','nome','municipio_id','municipio',$criteria);
        $municipio = new TEntry('municipio');
        $bairro = new TEntry('bairro');
        $cep = new TEntry('cep');
        $uf_id = new TDBCombo('uf_id',TSession::getValue('banco'),'UF','id','nome'); 
        $criteria = new TCriteria;
        $criteria->setProperty('order', 'descricao'); 
        $localizacao_id = new TDBSeekButton('localizacao_id',TSession::getValue('banco'),'form_Bem','Localizacao','descricao','localizacao_id','localizacao',$criteria);
        $localizacao = new TEntry('localizacao');
        $tipobem_id = new TDBCombo('tipobem_id',TSession::getValue('banco'),'Tipobem','id','descricao');
        $criteria = new TCriteria;
        $criteria->setProperty('order', 'nome');
        $proprietario_id = new TDBSeekButton('proprietario_id',TSession::getValue('banco'),'form_Bem','Cliente','nome','proprietario_id','proprietario',$criteria);
        $proprietario = new TEntry('proprietario');
        $rescom = new TRadioGroup('rescom');
        $pagtogar = new TRadioGroup('pagtogar');
        $diapagto = new TEntry('diapagto');
        $vlaluguel = new TEntry('vlaluguel');
        $vldesc = new TEntry('vldesc');
        $qtdemes = new TEntry('qtdemes');
        $percomissao = new TEntry('percomissao');
        $urbrural = new TRadioGroup('urbrural');
        $cliente2_id = new THidden('cliente2_id');
        if (isset($param['key']))
        {
            $cliente_id = new TDBSeekButton('cliente_id',TSession::getValue('banco'),'form_Bem','Cliente','nome','cliente_id','cliente');
            $cliente = new TEntry('cliente');            
            $contrato_id = new TEntry('contrato_id');
            $reservar = new TRadioGroup('reservar');
            $obs = new TText('obs');
        }
        else
        {
            $cliente_id = new THidden('cliente_id'); 
            $cliente = new THidden('cliente');            
            $contrato_id = new THidden('contrato_id');
            $reservar = new THidden('reservar');
            $obs = new THidden('obs');                         
        }
        $area_terreno = new TEntry('area_terreno');
        $matricula = new TEntry('matricula');
        
        // Campos Não Editáveis
        $id->setEditable(FALSE);
        $localizacao->setEditable(FALSE);
        $proprietario->setEditable(FALSE);
        $cliente_id->setEditable(FALSE); 
        $cliente->setEditable(FALSE); 
        $contrato_id->setEditable(FALSE);    
        
        // Máscaras
        $cep->setMask('99999-999');
        
        // Formatação para Valores Monetário
        $vlaluguel->setNumericMask(2, ',', '.');
        $vldesc->setNumericMask(2, ',', '.');
        $percomissao->setNumericMask(2, ',' ,'.');
        $area_terreno->setNumericMask(2, ',', '.');
        
        // Tamanho dos Campos no formulário
        $localizacao_id->setSize(50);
        $localizacao->setSize(277);      
        $proprietario_id->setSize(50);
        $proprietario->setSize(277);
        $cliente_id->setSize(50);
        $cliente->setSize(277);
        
        // Número de Caracteres permitidos dentro dos campos
        
        // Adiciona Items aos campos RadioGroup e ComboBox                
        $rescom->addItems(array('R' => ' Residencial', 'C' => ' Comercial'));
        $pagtogar->addItems(array('N' => ' Não', 'S' => ' Sim'));
        if (isset($param['key']))
        {        
            $reservar->addItems(array('N' => ' Não', 'S' => ' Sim'));
        }
        $urbrural->addItems(array('U' => ' Urbano', 'R' => ' Rural'));
        
        // Define Layout dos campos RadioGroup e ComboBox
        $rescom->setLayout('horizontal');        
        $pagtogar->setLayout('horizontal');
        if (isset($param['key']))
        {     
            $reservar->setLayout('horizontal');
        }        
        $urbrural->setLayout('horizontal');
        
        // Adiciona validação aos campos
        $descricao->addValidation('Descrição', new TRequiredValidator);        
        $municipio_id->addValidation('Município', new TRequiredValidator);
        $endereco->addValidation('Endereço', new TRequiredValidator);
        $municipio->addValidation('Município', new TRequiredValidator);
        $localizacao_id->addValidation('Localização', new TRequiredValidator);
        $tipobem_id->addValidation('Tipo de Bem', new TRequiredValidator);
        $urbrural->addValidation('Urbano/Rural', new TRequiredValidator);
        $proprietario_id->addValidation('Proprietário', new TRequiredValidator);
        $percomissao->addValidation('Percentual Comissão', new TRequiredValidator);

        // add the fields
        $this->form->addQuickField(_t('ID'), $id, 50 );
        $this->form->addQuickField('Descrição', $descricao, 350 );
        $this->form->addQuickField('Código Município', $municipio_id, 50 );
        $this->form->addQuickField('CEP', $cep, 100 );
        $this->form->addQuickField('Endereço', $endereco, 350 );
        $this->form->addQuickField('Complemento', $complemento, 350 );
        $this->form->addQuickField('Bairro', $bairro, 350 );
        $this->form->addQuickField('Municipio', $municipio, 350 );
        $this->form->addQuickField('UF', $uf_id, 200 );
        $this->form->addQuickFields('Localização', array($localizacao_id, $localizacao) );
        $this->form->addQuickField('Tipo de Bem', $tipobem_id, 150 );
        $this->form->addQuickField('Urbano/Rural', $urbrural, 200 );
        $this->form->addQuickFields('Proprietário', array($proprietario_id, $proprietario) );
        $this->form->addQuickField('Residencial/Comercial', $rescom, 200 );
        $this->form->addQuickField('Pagamento Garantido', $pagtogar, 200 );
        $this->form->addQuickField('Dia Pagamento', $diapagto, 50 );
        $this->form->addQuickField('Percentual Comissão', $percomissao, 50 );
        $this->form->addQuickField('Valor Aluguel', $vlaluguel, 200 );
        $this->form->addQuickField('Valor Desconto', $vldesc, 200 );
        $this->form->addQuickField('Qtde. Meses', $qtdemes, 50 );
        $this->form->addQuickField('Área do Terreno', $area_terreno, 200);
        $this->form->addQuickField('Matrícula', $matricula, 200);
        
        if (isset($param['key']))
        {
            $this->form->addQuickField('Contrato', $contrato_id,  50 );
            $this->form->addQuickFields('Cliente', array($cliente_id, $cliente) );       
            $this->form->addQuickField('Reservar', $reservar,  200 );
            $this->form->addQuickField('Observação', $obs,  200 );
            $this->form->addQuickField('Cliente 2', $cliente2_id,  200 );
        }
        else
        {
            $this->form->addQuickField('', $contrato_id );
            $this->form->addQuickField('', $cliente_id );       
            $this->form->addQuickField('', $reservar );
            $this->form->addQuickField('', $obs );
            $this->form->addQuickField('', $cliente2_id );   
        }
        
        /** samples
         $this->form->addQuickFields('Date', array($date1, new TLabel('to'), $date2)); // side by side fields
         $fieldX->addValidation( 'Field X', new TRequiredValidator ); // add validation
         $fieldX->setSize( 100, 40 ); // set size
         **/
         
        // create the form actions
        $this->form->addQuickAction(_t('Save'), new TAction(array($this, 'onSave')), 'fa:floppy-o');
        $this->form->addQuickAction(_t('New'),  new TAction(array($this, 'onClear')), 'bs:plus-sign green');
        $this->form->addQuickAction('Imprimir Contrato', new TAction(array('BemList','onPrint')),'fa:print black fa-lg');
        $this->form->addQuickAction('Novo Cliente', new TAction(array('ClienteWindowForm','onEdit')), 'fa:user blck fa-fg');
        $this->form->addQuickAction(_t('Back to the listing'),new TAction(array('BemList','onReload')),'fa:table blue');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', 'BemList'));
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
            //TTransaction::open(TSession::getValue('banco')); // open a transaction
            
            //$this->form->validate(); // validate form data   
            
            TTransaction::open('permission');
                
            $repository = new TRepository('Empresa');
            $empresas = $repository->load();
               
            TTransaction::close(); // close the transaction
                
            foreach ($empresas as $emrpresa) {
                TTransaction::open($emrpresa->banco);         
            
                $object = new Bem;  // create an empty object
                $data = $this->form->getData(); // get form data as array
                
                self::validate($data);
                
                $data->vlaluguel = FuncoesExtras::retiraFormatacao($data->vlaluguel);
                $data->vldesc = FuncoesExtras::retiraFormatacao($data->vldesc);
                $data->percomissao = FuncoesExtras::retiraFormatacao($data->percomissao);
                $object->fromArray( (array) $data); // load the object with data
                $object->store(); // save the object
                
                // get the generated id
                $data->id = $object->id;
                
                $data->vlaluguel = number_format($data->vlaluguel, 2, ',', '.');
                $data->vldesc = number_format($data->vldesc, 2, ',', '.');
                $data->percomissao = number_format($data->percomissao, 2, ',', '.');
                
                $this->form->setData($data); // fill form data
                TTransaction::close(); // close the transaction
            }
            
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
                $object = new Bem($key); // instantiates the Active Record
                    
                $object->vlaluguel = number_format($object->vlaluguel, 2, ',', '.');
                $object->vldesc = number_format($object->vldesc, 2, ',', '.');
                $object->percomissao = number_format($object->percomissao, 2, ',', '.');
                    
                $this->form->setData($object); // fill the form
                TTransaction::close(); // close the transaction
            }
            else
            {
                $this->form->clear();
                
                $obj = new StdClass;
                $obj->contrato_id = 0;
                $obj->cliente_id = 0;
                $obj->reservar = 'N';
                $obj->obs = '';
                $obj->cliente2_id = 0;
                    
                $this->form->setData($obj);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    public function validate($data)
    {
        $this->form->validate(); // validate form data
        
        // Valida o CPF para pessoas físicas
        if($data->vldesc > 0)
        {
            $validator = new TRequiredValidator;
            $validator->validate('Qtde. Meses', $data->qtdemes);  
        }
            
        // Valida o CNPJ para pessoas jurídicas
        if($data->tipo == 'J')
        {                
            if($data->cpfcnpj != '00.000.000/0000-00')
            {                
                $validator = new TCNPJValidator;
                $validator->validate('CNPJ',$data->cpfcnpj);  
            }               
        }
    }
}
