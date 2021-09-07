<?php
/**
 * BancoForm Form
 * @author  <your name here>
 */
class BancoForm extends TPage
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
        $this->form = new TQuickForm('form_Banco');
        $this->form->class = 'tform'; // change CSS class        
        $this->form->style = 'display: table;width:100%'; // change style
        //$this->form->setFormTitle('Banco');
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
			                    var cidade = unescape(resultadoCEP["cidade"]);
			                    var uf = unescape(resultadoCEP["uf"]);
			                    
			                    $("input[name=endereco]").val(endereco.toUpperCase());
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
        $id = new THidden('id');
        $codigo = new TEntry('codigo');
        $agencia = new TEntry('agencia');
        $digito = new TEntry('digito');
        $nome = new TEntry('nome');
        $endereco = new TEntry('endereco');
        $cep = new TEntry('cep');
        $municipio = new TEntry('municipio');
        $uf_id = new TDBCombo('uf_id',TSession::getValue('banco'),'UF','id','nome');
        $cnpj = new TEntry('cnpj');
        
        // Máscaras
        $cep->setMask('99999-999');
        $cnpj->setMask('99.999.999/9999-9');
        
        // Adiciona validação aos campos
        $codigo->addValidation('Código', new TRequiredValidator);
        $agencia->addValidation('Agência', new TRequiredValidator);
        $nome->addValidation('Nome', new TRequiredValidator);

        // add the fields        
        $this->form->addQuickField('Código', $codigo,  100 );
        $this->form->addQuickField('Agência', $agencia,  100 );
        $this->form->addQuickField('Dígito', $digito,  50 );
        $this->form->addQuickField('Nome', $nome,  300 );
        $this->form->addQuickField('Endereço', $endereco,  300 );
        $this->form->addQuickField('CEP', $cep,  100 );
        $this->form->addQuickField('Município', $municipio,  300 );
        $this->form->addQuickField('UF', $uf_id,  200 );
        $this->form->addQuickField('CNPJ', $cnpj,  100 );
        $this->form->addQuickField('', $id,  200 );

        /*if (!empty($id))
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
        $this->form->addQuickAction(_t('Back to the listing'),new TAction(array('BancoList','onReload')),'fa:table blue');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', 'BancoList'));
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
            
            // Enable Debug logger for SQL operations inside the transaction
            TTransaction::setLogger(new TLoggerSTD); // standard output
            TTransaction::setLogger(new TLoggerTXT('log.txt')); // file
            
            $this->form->validate(); // validate form data
            
            $object = new Banco;  // create an empty object
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
                $object = new Banco($key); // instantiates the Active Record
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