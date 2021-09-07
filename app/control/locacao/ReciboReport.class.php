<?php
/**
 * ReciboReport Report
 * @author  <your name here>
 */
class ReciboReport extends TPage
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
        $this->form = new TQuickForm('form_ReciboReport');
        $this->form->class = 'tform'; // change CSS class        
        $this->form->style = 'display: table;width:100%'; // change style
        //$this->form->setFormTitle('ReciboReport');
        
        // create the form fields
        $dtini = new TDate('dtini');
        $dtfim = new TDate('dtfim');
        
        // Máscaras
        $dtini->setMask('dd/mm/yyyy');
        $dtfim->setMask('dd/mm/yyyy');
        
        // Adiciona validação aos campos
        $dtini->addValidation('Recebidas de', new TRequiredValidator);
        $dtini->addValidation('Recebidas de', new TDateValidator);
        $dtfim->addValidation('Recebidas até', new TRequiredValidator);
        $dtfim->addValidation('Recebidas até', new TDateValidator);
        
        // Adiciona Valor Padrão aos Campos
        $dtini->setValue(date('d/m/Y'));
        $dtfim->setValue(date('d/m/Y'));
        
        // add the fields
        $this->form->addQuickField('Data de', $dtini, 100);
        $this->form->addQuickField('Data até', $dtfim, 100);
        
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
            $conn = TTransaction::get();
            
            // get the form data into an active record
            $formdata = $this->form->getData();
            
            $repository = new TRepository('Recibo');
            $criteria   = new TCriteria;
            
            if ($formdata->dtini)
            {
                $formdata->dtini = TDate::date2us($formdata->dtini);
                $criteria->add(new TFilter('dtrecibo', '>=', "{$formdata->dtini}"));
                $formdata->dtini = TDate::date2br($formdata->dtini);
            }
            
            if ($formdata->dtfim)
            {
                $formdata->dtfim = TDate::date2us($formdata->dtfim);
                $criteria->add(new TFilter('dtrecibo', '<=', "{$formdata->dtfim}"));
                $formdata->dtfim = TDate::date2br($formdata->dtfim);
            }
            
            $criteria->setProperty('order', 'dtrecibo');
            
            $objects = $repository->load($criteria, FALSE);
            $format  = 'pdf';
            
            if ($objects)
            {   
                $designer = new TReport();
                $designer->setTitle('Relatório de Emissão de Recibos');
                $designer->setFiltro('PERÍODO DE: '.$formdata->dtini.' ATÉ '.$formdata->dtfim);
                $columns = array();
                $columns[0]['size'] = 40;
                $columns[0]['text'] = 'NÚMERO';
                $columns[0]['align'] = 'R';
                $columns[1]['size'] = 70;
                $columns[1]['text'] = 'VALOR';
                $columns[1]['align'] = 'L';
                $columns[2]['size'] = 120;
                $columns[2]['text'] = 'RECEBEDOR';
                $columns[2]['align'] = 'L';
                $columns[3]['size'] = 120;
                $columns[3]['text'] = 'DECRIÇÃO';
                $columns[3]['align'] = 'L';
                $columns[4]['size'] = 120;
                $columns[4]['text'] = 'PAGADOR';
                $columns[4]['align'] = 'L';
                $columns[5]['size'] = 70;
                $columns[5]['text'] = 'DATA';
                $columns[5]['align'] = 'L';
                $designer->setColumns($columns);
                $designer->generate();
                $designer->SetMargins(30,30,30);
                $designer->SetAutoPageBreak(true, 30);
                	$designer->SetFont('Arial','',8);
                	$designer->SetX(30);
                	
                	$total = 0;
                	foreach ($objects as $object)
                	{
                	    $designer->Cell(40,10,number_format($object->id,0,',','.'),0,0,'R');
                	    $designer->Cell(70,10,number_format($object->valor,2,',','.'),0,0,'L');
                	    $designer->Cell(120,10,$object->recebedor,0,0,'L');
                	    $designer->Cell(120,10,$object->descricao,0,0,'L');
                	    $designer->Cell(120,10,$object->pagador,0,0,'L');
                	    $designer->Cell(70,10,TDate::date2br($object->dtrecibo),0,0,'L');
                	    $total = $total + $object->valor;
                	    $designer->Ln();
                }
                	
                	$designer->SetFont('Arial','B',8);
                	$h = $designer->GetY();
                	$designer->Line(30, $h, 565, $h);
                	$designer->Ln();
                	$designer->Cell(400,10,'TOTAL',0,0,'L');
                	$designer->Cell(70,10,number_format($total,2,',','.'),0,0,'R');
                	$designer->Line(30, $h + 25, 565, $h + 25);
                	
                	if (!file_exists("app/output/ReciboReport.{$format}") OR is_writable("app/output/ReciboReport.{$format}"))
                {
                    $designer->save("app/output/ReciboReport.{$format}");
                }
                else
                {
                    throw new Exception(_t('Permission denied') . ': ' . "app/output/ReciboReport.{$format}");
                }
                
                // open the report file
                parent::openFile("app/output/ReciboReport.{$format}");
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
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            
            // undo all pending operations
            TTransaction::rollback();
        }
    }
}
