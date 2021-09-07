<?php
/**
 * EmpresaForm Form
 * @author  <your name here>
 */
class EmpresaForm extends TPage
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
        $this->form = new TQuickForm('form_Empresa');
        $this->form->class = 'tform'; // change CSS class        
        $this->form->style = 'display: table;width:100%'; // change style
        //$this->form->setFormTitle('Empresas');
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
			                } else {
			                    alert("Endereço não encontrado para o cep.");
			                }
		                });
	                }
                });
            });
        ');
        parent::add($script);

        // create the form fields
        $id = new TEntry('id');
        $nome = new TEntry('nome');
        $cnpj = new TEntry('cnpj');
        $ie = new TEntry('ie');
        $nrjucesc = new TEntry('nrjucesc');
        $creci = new TEntry('creci');
        $cep = new TEntry('cep');
        $uf_id = new TDBCombo('uf_id',TSession::getValue('banco'),'UF','id','nome');
        $criteria = new TCriteria;
        $criteria->setProperty('order', 'nome');
        $municipio_id = new TDBSeekButton('municipio_id',TSession::getValue('banco'),'form_Empresa','Municipio','nome','municipio_id','municipio',$criteria);
        $municipio = new TEntry('municipio');
        $bairro = new TEntry('bairro');
        $endereco = new TEntry('endereco');
        $fone = new TEntry('fone');
        $fax = new TEntry('fax');
        $site = new TEntry('site');
        $email = new TEntry('email');
        $responsavel_nome = new TEntry('responsavel_nome');
        $responsavel_cpf = new TEntry('responsavel_cpf');
        $logo = new TFile('logo');
        $banco = new TEntry('banco');
        
        $id->setEditable(FALSE);
        $municipio->setEditable(FALSE);
        
        $municipio_id->setSize(50);
        $municipio->setSize(277);
        
        $cnpj->setMask('99.999.999/9999-99');
        $cep->setMask('99999-999');
        /*$fone->setMask('(99)9999-9999');
        $fax->setMask('(99)9999-9999');*/
        $responsavel_cpf->setMask('999.999.999-99');
        
        // Adiciona validação aos campos
        $nome->addValidation('Nome', new TRequiredValidator);
        //$cnpj->addValidation('CNPJ', new TRequiredValidator);
        $uf_id->addValidation('UF', new TRequiredValidator);
        $municipio->addValidation('Município', new TRequiredValidator);
        //$email->addValidation('Email', new TEmailValidator);
        //$logo->addValidation('Logo', new TRequiredValidator);

        // add the fields
        $this->form->addQuickField(_t('ID'), $id,  50 );
        $this->form->addQuickField('Nome', $nome,  350 );
        $this->form->addQuickField('CNPJ', $cnpj,  150 );
        $this->form->addQuickField('IE', $ie,  150 );
        $this->form->addQuickField('Jucesc', $nrjucesc,  100 );
        $this->form->addQuickField('Creci', $creci,  100 );
        $this->form->addQuickField('CEP', $cep,  100 );
        $this->form->addQuickField('UF', $uf_id,  200 );
        $this->form->addQuickFields('Município', array($municipio_id,$municipio) );
        $this->form->addQuickField('Bairro', $bairro,  350 );
        $this->form->addQuickField('Endereço', $endereco,  350 );
        $this->form->addQuickField('Fone', $fone,  100 );
        $this->form->addQuickField('Fax', $fax,  100 );
        $this->form->addQuickField('Site', $site,  350 );
        $this->form->addQuickField('Email', $email,  350 );
        $this->form->addQuickField('Responsável', $responsavel_nome,  350 );
        $this->form->addQuickField('CPF', $responsavel_cpf,  150 );
        $this->form->addQuickField('Logo', $logo,  350 );
        $this->form->addQuickField('Banco', $banco,  200 );
         
        // create the form actions
        $this->form->addQuickAction(_t('Save'), new TAction(array($this, 'onSave')), 'fa:floppy-o');
        $this->form->addQuickAction(_t('New'),  new TAction(array($this, 'onClear')), 'bs:plus-sign green');
        $this->form->addQuickAction(_t('Back to the listing'),new TAction(array('EmpresaList','onReload')),'fa:table blue');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', 'EmpresaList'));
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
            TTransaction::open('permission'); // open a transaction
            
            $this->form->validate(); // validate form data
            
            $object = new Empresa;  // create an empty object
            $data = $this->form->getData(); // get form data as array
            
            $object->fromArray( (array) $data); // load the object with data
            $object->store(); // save the object
            
            // get the generated id
            $data->id = $object->id;
            
            // have attachments
            if ($data->logo)
            {
                $target_folder = 'uploads/' . $data->id;
                $ext = substr($data->logo, -4);
                $target_file   = $target_folder . '/logo-' . $data->id . $ext;
                @mkdir($target_folder);
                rename('tmp/'.$data->logo, $target_file);
                $data->logo = $target_file;
                $object->fromArray( (array) $data);
                $object->store();
            }
	    else
	    {
	    	$data->logo = '';
	    }
            
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
                TTransaction::open('permission'); // open a transaction
                $object = new Empresa($key); // instantiates the Active Record
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
