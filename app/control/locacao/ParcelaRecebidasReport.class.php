<?php
/**
 * ParcelaRecebidasReport Report
 * @author  <your name here>
 */
class ParcelaRecebidasReport extends TPage
{
    protected $form; // form
    protected $notebook;
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();
        parent::include_js('app/lib/include/application.js');
        
        // creates the form
        $this->form = new TQuickForm('form_ParcelaRecebidasReport');
        $this->form->class = 'tform'; // change CSS class        
        $this->form->style = 'display: table;width:100%'; // change style
        //$this->form->setFormTitle('ParcelaRecebidasReport'); 

        // create the form fields
        $dtpagtode = new TDate('dtpagtode');
        $dtpagtoate = new TDate('dtpagtoate');
        $output_type = new THidden('output_type');
        
        // Máscaras
        $dtpagtode->setMask('dd/mm/yyyy');
        $dtpagtoate->setMask('dd/mm/yyyy');
        
        // Adiciona validação aos campos
        $dtpagtode->addValidation('Recebidas de', new TRequiredValidator);
        $dtpagtode->addValidation('Recebidas de', new TDateValidator);
        $dtpagtoate->addValidation('Recebidas até', new TRequiredValidator);
        $dtpagtoate->addValidation('Recebidas até', new TDateValidator);
        //$output_type->addValidation('Tipo de Saída', new TRequiredValidator);
        
        // Adiciona Items aos campos RadioGroup e ComboBox
        //$output_type->addItems(array('html'=>'HTML', 'pdf'=>'PDF', 'rtf'=>'RTF'));
        
        // Adiciona Valor Padrão aos Campos
        $dtpagtode->setValue(date('d/m/Y'));
        $dtpagtoate->setValue(date('d/m/Y'));
        $output_type->setValue('pdf');
        
        // Define Layout dos campos RadioGroup e ComboBox
        //$output_type->setLayout('horizontal');

        // add the fields
        $this->form->addQuickField('Recebidas de', $dtpagtode, 100);
        $this->form->addQuickField('Recebidas até', $dtpagtoate, 100);
        $this->form->addQuickField('Tipo de Saída', $output_type,  100);
        
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
            
            $repository = new TRepository('ParcelaReceber');
            $criteria   = new TCriteria;
            
            if ($formdata->dtpagtode)
            {
                $formdata->dtpagtode = TDate::date2us($formdata->dtpagtode);
                $criteria->add(new TFilter('dtpagto', '>=', "{$formdata->dtpagtode}"));
                $formdata->dtpagtode = TDate::date2br($formdata->dtpagtode);
            }
            
            if ($formdata->dtpagtoate)
            {
                $formdata->dtpagtoate = TDate::date2us($formdata->dtpagtoate);
                $criteria->add(new TFilter('dtpagto', '<=', "{$formdata->dtpagtoate}"));
                $formdata->dtpagtoate = TDate::date2br($formdata->dtpagtoate);
            }
            
            $criteria->setProperty('order', 'dtpagto');
            
            $objects = $repository->load($criteria, FALSE);
            $format  = $formdata->output_type;
            
            if ($objects)
            {   
                $designer = new TReport();
                $designer->setTitle('Relação de Parcelas Recebidas');
                $designer->setFiltro('PERÍODO DE: '.$formdata->dtpagtode.' ATÉ '.$formdata->dtpagtoate);
                $columns = array();
                $columns[0]['size'] = 40;
                $columns[0]['text'] = 'CODCLI';
                $columns[0]['align'] = 'R';
                $columns[1]['size'] = 120;
                $columns[1]['text'] = 'NOME';
                $columns[1]['align'] = 'L';
                $columns[2]['size'] = 50;
                $columns[2]['text'] = 'CONTRATO';
                $columns[2]['align'] = 'R';
                $columns[3]['size'] = 30;
                $columns[3]['text'] = 'PAR';
                $columns[3]['align'] = 'R';
                $columns[4]['size'] = 60;
                $columns[4]['text'] = 'DATA VCTO.';
                $columns[4]['align'] = 'R';
                $columns[5]['size'] = 70;
                $columns[5]['text'] = 'VALOR ALUGUEL';
                $columns[5]['align'] = 'R';
                $columns[6]['size'] = 60;
                $columns[6]['text'] = 'DATA PGTO.';
                $columns[6]['align'] = 'R';
                $columns[7]['size'] = 70;
                $columns[7]['text'] = 'VALOR PAGO';
                $columns[7]['align'] = 'R';
                $designer->setColumns($columns);
                $designer->generate();
                $designer->SetMargins(30,30,30);
                $designer->SetAutoPageBreak(true, 30);
            	$designer->SetFont('Arial','',8);
            	$designer->SetX(30);
            	
            	$total = 0;
            	$totalpago = 0;
            	foreach ($objects as $object)
            	{
            	    $designer->Cell(40,10,number_format($object->cliente_id,0,',','.'),0,0,'R');
            	    $designer->Cell(120,10,substr($object->cliente,0,20),0,0,'L');
            	    $designer->Cell(50,10,number_format($object->contrato_id,0,',','.'),0,0,'R');
            	    $designer->Cell(30,10,$object->numero,0,0,'R');
            	    $designer->Cell(60,10,TDate::date2br($object->dtvencto),0,0,'R');
            	    $designer->Cell(70,10,number_format($object->valor,2,',','.'),0,0,'R');
            	    $designer->Cell(60,10,TDate::date2br($object->dtpagto),0,0,'R');
            	    $designer->Cell(70,10,number_format($object->vlpago,2,',','.'),0,0,'R');
            	    $total = $total + $object->valor;
            	    $totalpago = $totalpago + $object->vlpago;
            	    $designer->Ln();
            	}
            	
            	$designer->SetFont('Arial','B',8);
            	$h = $designer->GetY();
            	$designer->Line(30, $h, 565, $h);
            	$designer->Ln();
            	$designer->Cell(300,10,'TOTAIS',0,0,'L');
            	$designer->Cell(60,10,number_format($total,2,',','.'),0,0,'R');
            	$designer->Cell(120,10,number_format($totalpago,2,',','.'),0,0,'R');
            	$designer->Line(30, $h + 25, 565, $h + 25);
            	
            	if (!file_exists("app/output/ParcelaRecebidasReport.{$format}") OR is_writable("app/output/ParcelaRecebidasReport.{$format}"))
                {
                    $designer->save("app/output/ParcelaRecebidasReport.{$format}");
                }
                else
                {
                    throw new Exception(_t('Permission denied') . ': ' . "app/output/ParcelaRecebidasReport.{$format}");
                }
                
                // open the report file
                parent::openFile("app/output/ParcelaRecebidasReport.{$format}");
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
