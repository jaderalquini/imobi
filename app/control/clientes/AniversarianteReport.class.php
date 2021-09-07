<?php
/**
 * AniversarianteReport Report
 * @author  <your name here>
 */
class AniversarianteReport extends TPage
{
    protected $form; // form
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();
        parent::include_js('app/lib/include/application.js');
        
        // creates the form
        $this->form = new TQuickForm('form_AniversarianteReport_report');
        $this->form->class = 'tform'; // change CSS class        
        $this->form->style = 'display: table;width:100%'; // change style
        //$this->form->setFormTitle('ParcelaReceber Report'); 
        
        // create the form fields
        $dtnascde = new TDate('dtnascde');
        $dtnascate = new TDate('dtnascate');
        
        // Máscaras
        $dtnascde->setMask('dd/mm/yyyy');
        $dtnascate->setMask('dd/mm/yyyy');
        
        // Adiciona validação aos campos
        $dtnascde->addValidation('Data de Nascimento de', new TRequiredValidator);
        $dtnascde->addValidation('Data de Nascimento de', new TDateValidator);
        $dtnascate->addValidation('Data de Nascimento até', new TRequiredValidator);
        $dtnascate->addValidation('Data de Nascimento até', new TDateValidator);
        
        // Adiciona Valor Padrão aos Campos
        $dtnascde->setValue(date('d/m/Y'));
        $dtnascate->setValue(date('d/m/Y'));
        
        // add the fields
        $this->form->addQuickField('Data de Nascimento de', $dtnascde, 100);
        $this->form->addQuickField('Data de Nascimento até', $dtnascate, 100);
        
        // add the action button
        $this->form->addQuickAction(_t('Generate'), new TAction(array($this, 'onGenerate')), 'fa:cog blue');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }
    
    /**
     * Generate the report
     */
    function onGenerate()
    {
        try
        {
            TTransaction::open(TSession::getValue('banco'));
            
            // get the form data into an active record
            $formdata = $this->form->getData();
            
            $repository = new TRepository('Cliente');
            $criteria   = new TCriteria;
            
            if ($formdata->dtnascde)
            {
                $formdata->dtnascde = TDate::date2us($formdata->dtnascde);
                $criteria->add(new TFilter('dtnasc', '>=', "{$formdata->dtnascde}"));
                $formdata->dtnascde = TDate::date2br($formdata->dtnascde);
            }
            
            if ($formdata->dtnascate)
            {
                $formdata->dtnascate = TDate::date2us($formdata->dtnascate);
                $criteria->add(new TFilter('dtnasc', '<=', "{$formdata->dtnascate}"));
                $formdata->dtnascate = TDate::date2br($formdata->dtnascate);
            }
            
            $criteria->setProperty('order', 'dtnasc');
            
            $objects = $repository->load($criteria, FALSE);
            $format  = 'pdf';
            
            if ($objects)
            {
                $designer = new TReport();
                $designer->setTitle('Relatório de Aniversariantes', FALSE);
                $designer->setFiltro('DATA DE NASCIMENTO DE: '.$formdata->dtnascde.' ATÉ '.$formdata->dtnascate);
                $columns = array();
                $columns[0]['size'] = 300;
                $columns[0]['text'] = 'NOME';
                $columns[0]['align'] = 'L';
                $columns[1]['size'] = 100;
                $columns[1]['text'] = 'TELEFONE';
                $columns[1]['align'] = 'L';
                $columns[2]['size'] = 100;
                $columns[2]['text'] = 'CELULAR';
                $columns[2]['align'] = 'L';
                $columns[4]['size'] = 40;
                $columns[4]['text'] = 'DATA DE NASCIMENTO';
                $columns[4]['align'] = 'R';
                $designer->setColumns($columns);
                $designer->generate();
                $designer->SetMargins(30,30,30);
                $designer->SetAutoPageBreak(true, 30);
                	$designer->SetFont('Arial','',8);
            	    $designer->SetX(30);
            	    
                	foreach ($objects as $object)
                	{
                	    $designer->Cell(300,10,$object->nome,0,0,'L');
                	    $designer->Cell(100,10,$object->fone,0,0,'L');
                	    $designer->Cell(100,10,$object->cel,0,0,'L');
                	    $designer->Cell(40,10,TDate::date2br($object->dtnasc),0,0,'R');
                	    $designer->Ln();
                	}
                	
                	if (!file_exists("app/output/AniversarianteReport.{$format}") OR is_writable("app/output/AniversarianteReport.{$format}"))
                {
                    $designer->save("app/output/AniversarianteReport.{$format}");
                }
                else
                {
                    throw new Exception(_t('Permission denied') . ': ' . "app/output/AniversarianteReport.{$format}");
                }
                
                // open the report file
                parent::openFile("app/output/AniversarianteReport.{$format}");
            }
            else
            {
                new TMessage('error', 'Nenhum registro foi encontrado');
            }
            
            // fill the form with the active record data
            $this->form->setData($formdata);
            
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
}
