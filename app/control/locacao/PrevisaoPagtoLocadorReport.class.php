<?php
/**
 * PrevisaoPagtoLocadorReport Report
 * @author  <your name here>
 */
class PrevisaoPagtoLocadorReport extends TPage
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
        $this->form = new TQuickForm('form_ParcelaPagar_report');
        $this->form->class = 'tform'; // change CSS class        
        $this->form->style = 'display: table;width:100%'; // change style
        //$this->form->setFormTitle('ParcelaPagar Report'); 

        // create the form fields
        $mes = new TEntry('mes');
        $ano = new TEntry('ano');
        $listar_pagas = new TRadioGroup('listar_pagas');
        $output_type = new THidden('output_type');
        
        // Tamanho dos Campos no formulário
        $mes->setSize(25);
        $ano->setSize(50);
        
        // Número de Caracteres permitidos dentro dos campos
        $mes->setMaxLength(2);
        $ano->setMaxLength(4);
        
        // Adiciona validação aos campos
        $output_type->addValidation('Tipo de Saída', new TRequiredValidator);
        
        // Adiciona Items aos campos RadioGroup e ComboBox
        $listar_pagas->addItems(array('S' => 'Sim', 'N' => 'Não'));
        //$output_type->addItems(array('html'=>'HTML', 'pdf'=>'PDF', 'rtf'=>'RTF'));
        
        // Adiciona Valor Padrão aos Campos
        $mes->setValue(date('m'));
        $ano->setValue(date('Y'));
        $listar_pagas->setValue('N');
        $output_type->setValue('pdf');
        
        // Define Layout dos campos RadioGroup e ComboBox
        $listar_pagas->setLayout('horizontal');
        //$output_type->setLayout('horizontal');

        // add the fields
        $this->form->addQuickFields('Mês/Ano Referência', array($mes, $ano));
        $this->form->addQuickField('Listar Pagas?', $listar_pagas, 100);
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
            
            // get the form data into an active record
            $formdata = $this->form->getData();
            
            $repository = new TRepository('ParcelaPagar');
            $criteria   = new TCriteria;
            
            if ($formdata->mes && $formdata->ano)
            {
                $criteria->add(new TFilter('dtvencto', '>=', "{$formdata->ano}-{$formdata->mes}-01"));
                $criteria->add(new TFilter('dtvencto', '<=', "{$formdata->ano}-{$formdata->mes}-31"));
            }
            if ($formdata->listar_pagas == 'N')
            {
                $criteria->add(new TFilter('vlpago', '<>', "0"));
            }
            $criteria->setProperty('order', 'dtvencto');
           
            $objects = $repository->load($criteria, FALSE);
            $format  = $formdata->output_type;
            
            if ($objects)
            {   
                $designer = new TReport();
                $designer->setTitle('Previsão de Pagamentos ao Locador');
                $designer->setFiltro('REFERENTE: '.$formdata->mes.'/'.$formdata->ano);
                $columns = array();
                $columns[0]['size'] = 40;
                $columns[0]['text'] = 'CODPRO';
                $columns[0]['align'] = 'R';
                $columns[1]['size'] = 120;
                $columns[1]['text'] = 'NOME';
                $columns[1]['align'] = 'L';
                $columns[2]['size'] = 40;
                $columns[2]['text'] = 'CONTRATO';
                $columns[2]['align'] = 'R';
                $columns[3]['size'] = 40;
                $columns[3]['text'] = 'IMÓVEL';
                $columns[3]['align'] = 'R';
                $columns[4]['size'] = 20;
                $columns[4]['text'] = 'DD';
                $columns[4]['align'] = 'R';
                $columns[5]['size'] = 60;
                $columns[5]['text'] = 'VL.ALUGUEL';
                $columns[5]['align'] = 'R';
                $columns[6]['size'] = 60;
                $columns[6]['text'] = 'VL.ADICIONAL';
                $columns[6]['align'] = 'R';
                $columns[7]['size'] = 60;
                $columns[7]['text'] = 'COMISSÃO';
                $columns[7]['align'] = 'R';
                $columns[8]['size'] = 40;
                $columns[8]['text'] = 'CONTA';
                $columns[8]['align'] = 'R';
                $columns[9]['size'] = 20;
                $columns[9]['text'] = 'DG';
                $columns[9]['align'] = 'R';
                $columns[10]['size'] = 40;
                $columns[10]['text'] = 'BANCO';
                $columns[10]['align'] = 'L';
                $designer->setColumns($columns);
                $designer->generate();
                $designer->SetMargins(30,30,30);
                $designer->SetAutoPageBreak(true, 30);
                $designer->SetFont('Arial','',8);
                $designer->SetX(30);
                	
                $i = 0;
                $totdia = 0;
                $totadicdia = 0;
                $totcomisdia = 0;
                $total = 0;
                $totaladicional = 0;
                $totalcomissao = 0;
                $primeiro = TRUE;
                $dia = '';
                foreach ($objects as $object)
                {
                    $i++;
                    if ($primeiro)
                    {
                        $primeiro = FALSE;
                        $dia = $object->dtvencto;
                    }
                    else
                    {
                        if ($dia != $object->dtvencto)
                        {
                            $designer->Cell(40,10,'',0,0,'L');
                            $designer->Cell(220,10,'Total do Dia',0,0,'L');
                            $designer->Cell(60,10,number_format($totdia,2,',','.'),0,0,'R');
                            $designer->Cell(60,10,number_format($totadicdia,2,',','.'),0,0,'R');
                            $designer->Cell(60,10,number_format($totcomisdia,2,',','.'),0,0,'R');
                            $designer->Ln();
                            $designer->Cell(40,10,'',0,0,'L');
                            $designer->Cell(220,10,utf8_decode('Líquido (Aluguel + Valor Adicional - Comissão)'),0,0,'L');
                            $designer->Cell(60,10,number_format($totdia + $totadicdia - $totcomisdia,2,',','.'),0,0,'R');
                            $designer->Ln();
                            $designer->Ln();
                            $totdia = 0;
                            $totcomisdia = 0;
                            $totadicdia = 0;
                            $dia = $object->dtvencto;
                        }
                    }
                    $designer->Cell(40,10,number_format($object->cliente_id,0,',','.'),0,0,'R');
                    $designer->Cell(120,10,substr($object->cliente,0,25),0,0,'L');
                    $designer->Cell(40,10,number_format($object->contrato_id,0,',','.'),0,0,'R');
                    $designer->Cell(40,10,number_format($object->bem_id,0,',','.'),0,0,'R');
                    $designer->Cell(20,10,substr($object->dtvencto,8,2),0,0,'R');
                    $designer->Cell(60,10,number_format($object->valor,2,',','.'),0,0,'R');
                    $totdia = $totdia + $object->valor;
                    $total = $total + $object->valor;
                    $vladicional = 0;
                    if ($object->opoutro == '+')
                    {
                        $vladicional = $vladicional + $object->vloutro;
                    }
                    if ($object->opseguro == '+')
                    {
                        $vladicional = $vladicional + $object->vlseguro;
                    }
                    if ($object->opcondominio == '+')
                    {
                	        $vladicional = $vladicional + $object->vlcondominio;
                	}
                	if ($object->opluz == '+')
                	{
                	    $vladicional = $vladicional + $object->vlluz;
                	}
                	if ($object->opagua == '+')
                	{
                	    $vladicional = $vladicional + $object->vlagua;
                	}
                	if ($object->opiptu == '+')
                	{
                	    $vladicional = $vladicional + $object->vliptu;
                	}
                	if ($object->opgas == '+')
                	{
                	    $vladicional = $vladicional + $object->vlgas;
                	}
                	$vladicional = $vladicional + $object->vldevolucao;
                	$designer->Cell(60,10,number_format($vladicional,2,',','.'),0,0,'R');
                	$totadicdia = $totadicdia + $vladicional;
                	$totaladicional = $totaladicional + $vladicional;
                	$designer->Cell(60,10,number_format($object->vlcomissao,2,',','.'),0,0,'R');
                	$totcomisdia = $totcomisdia + $object->vlcomissao;
                	$totalcomissao = $totalcomissao + $object->vlcomissao;
                	$repository = new TRepository('ContacorrenteCliente');
                    $criteria   = new TCriteria;
                    $criteria->add(new TFilter('cliente_id','=',$object->cliente_id));
                    $contas = $repository->load($criteria, FALSE);
                    foreach ($contas as $conta)
                    {
                        $designer->Cell(40,10,$conta->numero,0,0,'R');
                        $designer->Cell(20,10,$conta->digito,0,0,'R');
                        $designer->Cell(40,10,$conta->banco,0,0,'L');
                    }
            	    $designer->Ln();
            	}
            	
            	$designer->Cell(40,10,'',0,0,'L');
            	$designer->Cell(220,10,'Total do Dia',0,0,'L');
            	$designer->Cell(60,10,number_format($totdia,2,',','.'),0,0,'R');
            	$designer->Cell(120,10,number_format($totcomisdia,2,',','.'),0,0,'R');
            	$designer->Ln();
            	$designer->Cell(40,10,'',0,0,'L');
            	$designer->Cell(220,10,utf8_decode('Líquido (Aluguel + Valor Adicional - Comissão)'),0,0,'L');
            	$designer->Cell(60,10,number_format($totdia - $totcomisdia,2,',','.'),0,0,'R');
            	$designer->Ln();
            	$designer->Ln();
            	
            	$designer->SetFont('Arial','B',8);
            	$h = $designer->GetY();
            	$totalgeral = $total + $totaladicional - $totalcomissao;
            	$designer->Line(30, $h, 565, $h);
            	$designer->Ln();
            	$designer->Cell(260,10,$i.' TOTAIS',0,0,'L');
            	$designer->Cell(60,10,number_format($total,2,',','.'),0,0,'R');
            	$designer->Cell(60,10,number_format($totaladicional,2,',','.'),0,0,'R');
            	$designer->Cell(60,10,number_format($totalcomissao,2,',','.'),0,0,'R');
            	$designer->Ln();
            	$designer->Line(30, $h + 25, 565, $h + 25);
            	$designer->Ln();
            	$designer->Cell(260,10,utf8_decode('TOTAL LÍQUIDO (Aluguel + Valor Adicional - Comissão)'),0,0,'L');
            	$designer->Cell(60,10,number_format($totalgeral,2,',','.'),0,0,'R');
            	$designer->Line(30, $h + 45, 565, $h + 45);
                
                if (!file_exists("app/output/PrevisaoPagtoLocador.{$format}") OR is_writable("app/output/PrevisaoPagtoLocador.{$format}"))
                {
                    $designer->save("app/output/PrevisaoPagtoLocador.{$format}");
                }
                else
                {
                    throw new Exception(_t('Permission denied') . ': ' . "app/output/PrevisaoPagtoLocador.{$format}");
                }
                
                // open the report file
                parent::openFile("app/output/PrevisaoPagtoLocador.{$format}");
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
?>