<?php
/**
 * Importar
 * @author  <Jáder Alexandre Alquini>
 */
class ImportarDados extends TPage
{
    private $form;
    
    /**
    * Class constructor
    * Creates the page and the registration form
    */
    function __construct()
    {        
        parent::__construct();
        parent::include_js('app/lib/include/application.js');
        
        $this->form = new TQuickForm('form_Importa');
        $this->form->class = 'tform';
        //$this->form->setFormTitle('Importar Dados');
        $this->form->style = 'width:100%';
        
        $file = new TFile('file');
        $classe = new TRadioGroup('classe');
        $classe->addItems(array('Banco'=>' Bancos',
                                'Bem'=>' Bens',
                                'Cheque'=>' Cheques',
                                'ChequeItem'=>' Cheques (Itens)',
                                'Cliente'=>' Clientes',
                                'Contacorrente'=>' Contas Correntes',
                                'ContacorrenteCliente'=>' Contas Correntes (Clientes)',
                                'Contrato'=>' Contratos de Locação',
                                'HistoricoContratos' => ' Histórico de Contratos',
                                'Movcaixa'=>' Lançamentos no Caixa',
                                'Localizacao'=>' Localizações',     
                                'Movbancaria' => ' Movimentação Bancária ',                         
                                'ParcelaPagar'=>' Parcelas à Pagar',
                                'ParcelaReceber'=>' Parcelas à Receber',
                                'Tipobem' => ' Tipos de Bem'));
                                
        //$file->addValidation('Arquivo', new TRequiredValidator);
        $classe->addValidation('Selecione a Tabela', new TRequiredValidator);
        
        //$this->form->addQuickField('Arquivo', $file, 350 );
        $this->form->addQuickField('Selecione a Tabela', $classe, 350 );
        
        $this->form->addQuickAction('Importar Dados', new TAction(array($this, 'onImportCSV')), 'fa:download');
        
        //$select->setSize('100%', 100);
        
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', 'ImportarDados'));
        $container->add($this->form);
        
        parent::add($container);
    }
    
    function onImportCSV()
    {
        $this->form->validate();
            
        $data = $this->form->getData(); // optional parameter: active record class
            
        $this->form->setData($data);
        $classe = $data->classe;
        $classe = new $classe();
                        
        $this->form->validate();
        
        $classe->onImport($data->classe);
    }
}