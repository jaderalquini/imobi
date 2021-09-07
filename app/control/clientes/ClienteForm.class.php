<?php
/**
 * ClienteForm Form
 * @author  <your name here>
 */
class ClienteForm extends TPage
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
        $this->form = new TQuickForm('form_Cliente');
        $this->form->class = 'tform'; // change CSS class    
        $this->form->style = 'display: table;width:100%'; // change style
        //$this->form->setFormTitle('Clientes');
        //$this->form->setFieldsByRow(2);  
        
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
                
                $("input[name=empresa_cep]").blur(function() {
                    cep = $(this).val();
                    if($.trim(cep) != ""){
		                $.getScript("http://cep.republicavirtual.com.br/web_cep.php?formato=javascript&cep="+cep, function(){
			                if(resultadoCEP["resultado"] == 1){
			                    var endereco = unescape(resultadoCEP["tipo_logradouro"])+" "+unescape(resultadoCEP["logradouro"]);
			                    var bairro = unescape(resultadoCEP["bairro"]);
			                    var cidade = unescape(resultadoCEP["cidade"]);
			                    var uf = unescape(resultadoCEP["uf"]);
			                    
			                    $("input[name=empresa_endereco]").val(endereco.toUpperCase());
			                    $("input[name=empresa_bairro]").val(bairro.toUpperCase());
			                    $("input[name=empresa_municipio]").val(cidade.toUpperCase());
			                    $("select[name=empresa_uf]").val(uf.toUpperCase());
			                }
		                });
	                }
                });
                
                $("input[name=representante_cep]").blur(function() {
                    cep = $(this).val();
                    if($.trim(cep) != ""){
		                $.getScript("http://cep.republicavirtual.com.br/web_cep.php?formato=javascript&cep="+cep, function(){
			                if(resultadoCEP["resultado"] == 1){
			                    var endereco = unescape(resultadoCEP["tipo_logradouro"])+" "+unescape(resultadoCEP["logradouro"]);
			                    var bairro = unescape(resultadoCEP["bairro"]);
			                    var cidade = unescape(resultadoCEP["cidade"]);
			                    var uf = unescape(resultadoCEP["uf"]);
			                    
			                    $("input[name=representante_endereco]").val(endereco.toUpperCase());
			                    $("input[name=representante_bairro]").val(bairro.toUpperCase());
			                    $("input[name=representante_municipio]").val(cidade.toUpperCase());
			                    $("select[name=representante_uf]").val(uf.toUpperCase());
			                }
		                });
	                }
                });
                
                $(\'input[name="tipo"]\').change(function(event) {
                    tipoPessoa = $(this).val();
                    
                    if(tipoPessoa == \'F\') {
                        $(\'input[name="cpfcnpj"]\').val(\'\');
                        $(\'input[name="cpfcnpj"]\').attr({onkeypress:\'return tentry_mask(this,event,"999.999.999-99")\'});
                    }
                    
                    if(tipoPessoa == \'J\') {
                        $(\'input[name="cpfcnpj"]\').val(\'\');
                        $(\'input[name="cpfcnpj"]\').attr({onkeypress:\'return tentry_mask(this,event,"99.999.999/9999-99")\'});
                    }
                });
            });
        ');
        parent::add($script);       

        // create the form fields
        $id = new TEntry('id');
        $nome = new TEntry('nome');
        $cpfcnpj = new TEntry('cpfcnpj');
        $tipo = new TRadioGroup('tipo');
        $dtnasc = new TDate('dtnasc');
        $ierg = new TEntry('ierg');
        $estadocivil_id = new TDBCombo('estadocivil_id',TSession::getValue('banco'),'Estadocivil','id','descricao');
        $municipionasc = new TEntry('municipionasc');
        $ufnasc = new TDBCombo('ufnasc',TSession::getValue('banco'),'UF','id','nome');
        $nomepais = new TEntry('nomepais');
        $endereco = new TEntry('endereco');
        $bairro = new TEntry('bairro');
        $cep = new TEntry('cep');
        $municipio = new TEntry('municipio');
        $uf_id = new TDBCombo('uf_id',TSession::getValue('banco'),'UF','id','nome');
        $fone = new TEntry('fone');
        $cel = new TEntry('cel');
        $fax = new TEntry('fax');
        $tipores_id = new TDBCombo('tipores_id',TSession::getValue('banco'),'Tiporesidencia','id','descricao');
        $tempores = new TEntry('tempores');
        $proximo = new TEntry('proximo');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('id', '>', 0));
        $criteria->setProperty('order', 'nome'); 
        $representante_id = new TDBSeekButton('representante_id',TSession::getValue('banco'),'form_Cliente','nome','representante_id','representante',$criteria);
        $representante = new TEntry('representante');  
        $profissao = new TEntry('profissao');     
        $dtcadastro = new TDate('dtcadastro');
        $empresa_nome = new TEntry('empresa_nome');
        $empresa_endereco = new TEntry('empresa_endereco');
        $empresa_bairro = new TEntry('empresa_bairro');
        $empresa_cep = new TEntry('empresa_cep');
        $empresa_municipio = new TEntry('empresa_municipio');
        $empresa_uf = new TDBCombo('empresa_uf',TSession::getValue('banco'),'UF','id','nome');
        $empresa_fone = new TEntry('empresa_fone');
        $empresa_cargo = new TEntry('empresa_cargo');
        $empresa_salario = new TEntry('empresa_salario');
        $empresa_salarioref = new TEntry('empresa_salarioref');
        $empresa_tempo = new TEntry('empresa_tempo');
        $empresa_anterior = new TEntry('empresa_anterior');
        $conjuge_regime = new TEntry('conjuge_regime');
        $conjuge_nome = new TEntry('conjuge_nome');
        $conjuge_dtnasc = new TDate('conjuge_dtnasc');
        $conjuge_rg = new TEntry('conjuge_rg');
        $conjuge_cpf = new TEntry('conjuge_cpf');
        $conjuge_profissao = new TEntry('conjuge_profissao');
        
        // Campos Não Editáveis
        $id->setEditable(FALSE);
        $representante->setEditable(FALSE);
        
        // Máscaras
        $cpfcnpj->setMask('');
        $dtnasc->setMask('dd/mm/yyyy');
        $cep->setMask('99999-999');
        $dtcadastro->setMask('dd/mm/yyyy');
        $empresa_cep->setMask('99999-999');
        $empresa_fone->setMask('(99)9999-9999');
        $conjuge_dtnasc->setMask('dd/mm/yyyy');
        $conjuge_cpf->setMask('999.999.999-99');
        
        // Formatação para Valores Monetário
        $empresa_salario->setNumericMask(2, ',', '.');
        
        // Tamanho dos Campos no formulário
        $id->setSize(50);
        $nome->setSize('99%');
        $dtnasc->setSize(100);
        $municipionasc->setSize('50%');
        $ufnasc->setSize(200);
        $ierg->setSize(100);
        $estadocivil_id->setSize(150);
        $nomepais->setSize('99%');
        $cep->setSize(100);
        $municipio->setSize('50%');
        $uf_id->setSize(200);
        $endereco->setSize('99%');
        $bairro->setSize('99%');
        $fone->setSize(150);
        $cel->setSize(150);
        $fax->setSize(150);
        $tipores_id->setSize(150);
        $tempores->setSize(200);
        $proximo->setSize(200);   
        $representante_id->setSize(50);
        $representante->setSize(300);  
        $profissao->setSize(250);   
        $dtcadastro->setSize(100);
        $empresa_nome->setSize('99%');
        $empresa_cep->setSize(100);
        $empresa_municipio->setSize(200);
        $empresa_uf->setSize(200);
        $empresa_endereco->setSize('99%');
        $empresa_bairro->setSize('99%');        
        $empresa_fone->setSize(150);
        $empresa_cargo->setSize(250);
        $empresa_salario->SetSize(200);
        $empresa_salarioref->setSize(100);
        $empresa_tempo->setSize(200);
        $empresa_anterior->setSize('99%');
        $conjuge_regime->setSize('99%');
        $conjuge_nome->setSize('99%');
        $conjuge_dtnasc->setSize(100);
        $conjuge_rg->setSize(100);
        $conjuge_cpf->setSize(100);
        $conjuge_profissao->setSize('99%');
        
        // Número de Caracteres permitidos dentro dos campos
        
        
        // Adiciona Items aos campos RadioGroup e ComboBox
        $tipo->addItems(array('F' => ' Pessoa Física', 'J' => ' Pessoa Jurídica'));
        
        // Define Layout dos campos RadioGroup e ComboBox
        $tipo->setLayout('horizontal');
        
        // Define actions dos campos
        $tipo->setChangeAction(new TAction(array($this, 'onChangeTipo')));
        
        // Adiciona validação aos campos
        $tipo->addValidation('Tipo', new TRequiredValidator);
        //pfcnpj->addValidation('CPF/CNPJ', new TCPFValidator);
        $nome->addValidation('Nome', new TRequiredValidator);
        $municipio->addValidation('Município', new TRequiredValidator);
        $uf_id->addValidation('UF', new TRequiredValidator);
        $dtcadastro->addValidation('Data Cadastro', new TDateValidator, 'dd/mm/yyyy');
        
        $notebook = new TNotebook(500, 250);
        
        // add the notebook inside the form
        $this->form->add($notebook);
        
        // creates a table
        $table_data    = new TTable;
        $table_data->style = 'width: 100%';
        $table_representante = new TTable;
        $table_representante->style = 'width: 100%';
        $table_empresa = new TTable;
        $table_empresa->style = 'width: 100%';
        $table_conjuge = new TTable;
        $table_conjuge->style = 'width: 100%';
        
        $notebook->appendPage('Dados Principais', $table_data);
        //$notebook->appendPage('Dados do Representante Legal', $table_representante);
        $notebook->appendPage('Dados Profissionais', $table_empresa);
        $notebook->appendPage('Dados do Cônjuge', $table_conjuge);
        
        $row = $table_data->addRow();
        $cell = $row->addCell(new TLabel(_t('ID')));  
        $cell->style = 'width: 15%';      
        $cell = $row->addCell($id);        
        $cell->style = 'width: 35%';
        $cell = $row->addCell(new TLabel('Tipo'));
        $cell->style = 'width: 15%';
        $cell = $row->addCell($tipo);
        $cell->style = 'width: 35%';
        
        $table_data->addRowSet(new TLabel('CPF/CNPJ'), $cpfcnpj, new TLabel('Nome'), $nome);
        $table_data->addRowSet(new TLabel('Data Nascimento'), $dtnasc, new TLabel('Município'), $municipionasc);
        $table_data->addRowSet(new TLabel('UF'), $ufnasc, new TLabel('Insc. Estadual/Cart. Identidade'), $ierg);
        $table_data->addRowSet(new TLabel('Estado Civil'), $estadocivil_id, new TLabel('Nome Pais'), $nomepais);
        $table_data->addRowSet(new TLabel('CEP'), $cep, new TLabel('Município'), $municipio);
        $table_data->addRowSet(new TLabel('UF'), $uf_id, new TLabel('Endereço'), $endereco);
        $table_data->addRowSet(new TLabel('Bairro'), $bairro, new TLabel('Telefone'), $fone);
        $table_data->addRowSet(new TLabel('Celular'), $cel, new TLabel('Fax'), $fax);
        $table_data->addRowSet(new TLabel('Tipo de Residência'), $tipores_id, new TLabel('Tempo Residência'), $tempores);
        $table_data->addRowSet(new TLabel('Perto de'), $proximo, new TLabel('Representante Legal'), array($representante_id, $representante));
        $table_data->addRowSet(new TLabel('Profissão'), $profissao, new TLabel('Data Cadastro'), $dtcadastro);
                        
        $row = $table_empresa->addRow();
        $cell = $row->addCell(new TLabel('Nome'));
        $cell->style = 'width: 15%';
        $cell = $row->addCell($empresa_nome);
        $cell->style = 'width: 35%';
        $cell = $row->addCell(new TLabel('CEP'));
        $cell->style = 'width: 15%';
        $cell = $row->addCell($empresa_cep);
        $cell->style = 'width: 35%';
        
        $table_empresa->addRowSet(new TLabel('Município'), $empresa_municipio, new TLabel('UF'), $empresa_uf);
        $table_empresa->addRowSet(new TLabel('Endereço'), $empresa_endereco, new TLabel('Bairro'), $empresa_bairro);
        $table_empresa->addRowSet(new TLabel('Telefone'), $empresa_fone, new TLabel('Cargo'), $empresa_cargo);
        $table_empresa->addRowSet(new TLabel('Salário'), $empresa_salario, new TLabel('Salário Ref.'), $empresa_salarioref);
        $table_empresa->addRowSet(new TLabel('Empresa Anterior'), $empresa_anterior);
        
        $row = $table_conjuge->addRow();
        $cell = $row->addCell(new TLabel('Nome'));
        $cell->style = 'width: 15%';
        $cell = $row->addCell($conjuge_nome);
        $cell->style = 'width: 35%';
        $cell = $row->addCell(new TLabel('Data Nascimento'));
        $cell->style = 'width: 15%';
        $cell = $row->addCell($conjuge_dtnasc);
        $cell->style = 'width: 35%';
        
        $table_conjuge->addRowSet(new TLabel('CPF'), $conjuge_cpf, new TLabel('RG'), $conjuge_rg);
        $table_conjuge->addRowSet(new TLabel('Profissão'), $conjuge_profissao, new TLabel('Regime de Casamento'), $conjuge_regime);
         
         // create a save button
        $save_button=new TButton('save');
        $save_button->setAction(new TAction(array($this, 'onSave')), _t('Save'));
        $save_button->setImage('fa:floppy-o');
        
         // create a new button
        $new_button=new TButton('new');
        $new_button->setAction(new TAction(array($this, 'onClear')), _t('New'));
        $new_button->setImage('bs:plus-sign green');
        
         // create a back button
        $list_button=new TButton('back');
        $list_button->setAction(new TAction(array('ClienteList', 'onReload')), _t('Back to the listing'));
        $list_button->setImage('fa:table blue');
        
        $subtable = new TTable;
        $subtable-> width = '100%';
        $subtable->addRowSet( array($save_button, $new_button, $list_button), '', '')->class = 'tformaction';
        
        $this->form->setFields(array($id, $tipo, $cpfcnpj, $nome, $dtnasc, $municipionasc, $ufnasc, $ierg, $estadocivil_id, $nomepais, $cep, $municipio, $uf_id, 
                                    $endereco, $bairro, $fone, $cel, $fax, $tipores_id, $tempores, $proximo, $profissao, $dtcadastro, $empresa_nome, $representante_id, 
                                    $representante, $empresa_cep, $empresa_municipio, $empresa_uf, $empresa_endereco, $empresa_bairro, $empresa_fone, $empresa_cargo, 
                                    $empresa_salario, $empresa_salarioref, $empresa_tempo, $empresa_anterior, $conjuge_nome, $conjuge_dtnasc, $conjuge_rg, 
                                    $save_button, $new_button, $list_button));
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', 'ClienteList'));
        $container->add($this->form);
        $container->add($subtable);
        
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
            
                $object = new Cliente;  // create an empty object
                $data = $this->form->getData(); // get form data as array
                
                self::validate($data);
                            
                if ($data->estadocivil_id == NULL && $data->estadocivil_id == '')
                {
                    $data->estadocivil_id = '0';
                }
                
                if ($data->tipores_id == NULL && $data->tipores_id == '')
                {
                    $data->tipores_id = '0';
                }
                
                if ($data->dtnasc !== NULL && $data->dtnasc !== '') {
                    $data->dtnasc = TDate::date2us($data->dtnasc );
                }
                    
                if ($data->dtcadastro !== NULL && $data->dtcadastro !== '') {
                    $data->dtcadastro = TDate::date2us($data->dtcadastro );
                }
                
                if ($data->conjuge_dtnasc !== NULL && $data->conjuge_dtnasc !== '') {
                    $data->conjuge_dtnasc = TDate::date2us($data->conjuge_dtnasc );
                }
                
                $data->empresa_salario = FuncoesExtras::retiraFormatacao($data->empresa_salario);
                
                $object->fromArray( (array) $data); // load the object with data
                $object->store(); // save the object
                
                // get the generated id
                $data->id = $object->id;
                
                if ($data->dtnasc !== NULL && $data->dtnasc !== '') {
                    $data->dtnasc = TDate::date2br($data->dtnasc );
                }
                    
                if ($data->dtcadastro !== NULL && $data->dtcadastro !== '') {
                    $data->dtcadastro = TDate::date2br($data->dtcadastro );
                }
                
                if ($data->conjuge_dtnasc !== NULL && $data->conjuge_dtnasc !== '') {
                    $data->conjuge_dtnasc = TDate::date2br($data->conjuge_dtnasc );
                }
                
                $data->empresa_salario = number_format($data->empresa_salario, 2, ',', '.');
                
                $this->form->setData($data); // fill form data
                
                $this->onChangeTipo( array('tipo' => $data->tipo) );
                
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
        
        $obj = new StdClass;
        $obj->dtcadastro = date('d/m/Y');
            
        $this->form->setData($obj);
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
                    
                $object = new Cliente($key); // instantiates the Active Record
                
                if ($object->dtnasc !== NULL && $object->dtnasc !== '') {
                    $object->dtnasc = TDate::date2br($object->dtnasc );
                }
                    
                if ($object->dtcadastro !== NULL && $object->dtcadastro !=='') {
                    $object->dtcadastro = TDate::date2br($object->dtcadastro );
                }
                  
                if ($object->conjuge_dtnasc !== NULL && $object->conjuge_dtnasc !== '') {
                    $object->conjuge_dtnasc = TDate::date2br($object->conjuge_dtnasc );
                } 
                    
                $object->empresa_salario = number_format($object->empresa_salario, 2, ',', '.');
                    
                $this->form->setData($object); // fill the form
                    
                $this->onChangeTipo( array('tipo' => $object->tipo) );
                    
                TTransaction::close(); // close the transaction
            }
            else
            {
                $this->form->clear();
                
                $obj = new StdClass;
                $obj->dtcadastro = date('d/m/Y');
                    
                $this->form->setData($obj);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    public static function onChangeTipo($param)
    {        
        $tipo = $param['tipo'];
        
        if ($tipo == 'F')
        {
            TEntry::disableField('form_Cliente','representante_id');
            TDate::enableField('form_Cliente','dtnasc');
            TEntry::enableField('form_Cliente','municipionasc');
            TCombo::enableField('form_Cliente','ufnasc');
            TEntry::enableField('form_Cliente','ierg');
            TCombo::enableField('form_Cliente','estadocivil_id');
            TCombo::enableField('form_Cliente','nomepais');
            TEntry::enableField('form_Cliente','cel');
            TCombo::enableField('form_Cliente','tipores_id');
            TEntry::enableField('form_Cliente','tempores');
            TEntry::enableField('form_Cliente','proximo');
            TEntry::enableField('form_Cliente','empresa_nome');
            TEntry::enableField('form_Cliente','empresa_endereco');
            TEntry::enableField('form_Cliente','empresa_bairro');
            TEntry::enableField('form_Cliente','empresa_cep');
            TEntry::enableField('form_Cliente','empresa_municipio');
            TCombo::enableField('form_Cliente','empresa_uf');
            TEntry::enableField('form_Cliente','empresa_fone');
            TEntry::enableField('form_Cliente','empresa_cargo');
            TEntry::enableField('form_Cliente','empresa_salario');
            TEntry::enableField('form_Cliente','empresa_salarioref');
            TEntry::enableField('form_Cliente','empresa_tempo');
            TEntry::enableField('form_Cliente','empresa_anterior');
            TEntry::enableField('form_Cliente','conjuge_regime');
            TEntry::enableField('form_Cliente','conjuge_nome');
            TDate::enableField('form_Cliente','conjuge_dtnasc');
            TEntry::enableField('form_Cliente','conjuge_rg');
            TEntry::enableField('form_Cliente','conjuge_cpf');
            TEntry::enableField('form_Cliente','conjuge_profissao');
        }
        
        if ($tipo == 'J')
        {
            TEntry::enableField('form_Cliente','representante_id');
            TDate::disableField('form_Cliente','dtnasc');
            TEntry::disableField('form_Cliente','municipionasc');
            TCombo::disableField('form_Cliente','ufnasc');
            TEntry::disableField('form_Cliente','ierg');
            TCombo::disableField('form_Cliente','estadocivil_id');
            TCombo::disableField('form_Cliente','nomepais');
            TEntry::disableField('form_Cliente','cel');
            TCombo::disableField('form_Cliente','tipores_id');
            TEntry::disableField('form_Cliente','tempores');
            TEntry::disableField('form_Cliente','proximo');
            TEntry::disableField('form_Cliente','empresa_nome');
            TEntry::disableField('form_Cliente','empresa_endereco');
            TEntry::disableField('form_Cliente','empresa_bairro');
            TEntry::disableField('form_Cliente','empresa_cep');
            TEntry::disableField('form_Cliente','empresa_municipio');
            TCombo::disableField('form_Cliente','empresa_uf');
            TEntry::disableField('form_Cliente','empresa_fone');
            TEntry::disableField('form_Cliente','empresa_cargo');
            TEntry::disableField('form_Cliente','empresa_salario');
            TEntry::disableField('form_Cliente','empresa_salarioref');
            TEntry::disableField('form_Cliente','empresa_tempo');
            TEntry::disableField('form_Cliente','empresa_anterior');
            TEntry::disableField('form_Cliente','conjuge_regime');
            TEntry::disableField('form_Cliente','conjuge_nome');
            TDate::disableField('form_Cliente','conjuge_dtnasc');
            TEntry::disableField('form_Cliente','conjuge_rg');
            TEntry::disableField('form_Cliente','conjuge_cpf');
            TEntry::disableField('form_Cliente','conjuge_profissao');
        }
    }
    
    public function validate($data)
    {        
        $this->form->validate(); // validate form data
        
        // Valida o CPF para pessoas físicas
        if($data->tipo == 'F')
        {                
            if($data->cpfcnpj != '000.000.000-00')
            {
                $validator = new TCPFValidator;
                $validator->validate('CPF',$data->cpfcnpj);
            }
            
            $validator = new TDateValidator;
            $validator->validate('Data Nascimento', $data->dtnasc, 'dd/mm/yyyy');
                
            $validator = new TRequiredValidator;
            $validator->validate('Estado Civil', $data->estadocivil_id);
            $validator->validate('Tipo Residência', $data->tipores_id);  
        }
            
        // Valida o CNPJ para pessoas jurídicas
        if($data->tipo == 'J')
        {                
            if($data->cpfcnpj != '00.000.000/0000-00')
            {                
                $validator = new TCNPJValidator;
                $validator->validate('CNPJ',$data->cpfcnpj);
                /*$validator = new TRequiredValidator; 
                $validator->validate('Representante Legal', $data->representante_id); */
            }               
        }
    }
}
?>
