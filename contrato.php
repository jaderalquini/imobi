<?php
require_once 'init.php';
require_once('app/lib/TCPDF/tcpdf.php');
require_once('app/model/locacao/Contrato.class.php');

new TSession;

class ContratoPDF extends TCPDF 
{
	public function Header() 
    {        
        TTransaction::open('permission');            
        $empresa = new Empresa(TSession::getValue('empresa'));
        TTransaction::close();
        
        TTransaction::open(TSession::getValue('banco'));
        $uf = new UF($empresa->uf_id);
        TTransaction::close();

        $empresaInf1 = "CNPJ N°." . $empresa->cnpj . " - Registro JUCESC N°. " . $empresa->nrjucesc . " - CRECI N°. " . $empresa->creci;
        $empresaInf2 = $empresa->endereco . " - " . $empresa->bairro;
        $empresaInf3 = $empresa->municipio . " - " . $uf->nome . " - CEP " . $empresa->cep;
        
        $image_file = $empresa->logo;
        $this->Image($image_file, 15, 15, 25, 25, 'JPG', '', 'T', FALSE, 300, '', FALSE, FALSE, 0, FALSE, FALSE, FALSE);
        $this->SetTextColor(0,0,100);
        $this->SetFont('Helvetica','B',18);
        $this->SetX(42);
        $this->Write(0, $empresa->nome, '', 0, 'L', TRUE, 0, FALSE, FALSE, 0);
        $this->SetFont('Helvetica','',11);
        $this->SetX(42);
        $this->Write(0, $empresaInf1, '', 0, 'L', TRUE, 0, FALSE, FALSE, 0);
        $this->SetX(42);
        $this->Write(0, $empresaInf2, '', 0, 'L', TRUE, 0, FALSE, FALSE, 0);
        $this->SetX(42);
        $this->Write(0, $empresaInf3, '', 0, 'L', TRUE, 0, FALSE, FALSE, 0);
        $this->SetX(42);
        $this->SetFont('Helvetica','B',11);
        $this->Write(0, $empresa->site . " - E-mail:" . $empresa->email, '', 0, 'L', TRUE, 0, FALSE, FALSE, 0);        
        $this->SetDrawColor(200,50,0);
        $this->SetXY(15, 45);
        $this->Cell(($this->w - $this->original_lMargin - $this->original_rMargin), 0, '', 'T', 0, 'C');
	}

	public function Footer() {
		$this->SetFont('Helvetica', 'B', 12);
        $this->SetY(-PDF_MARGIN_BOTTOM);
        $NumPagina = 'Página '.$this->getAliasNumPage().' de '.$this->getAliasNbPages();
        $this->Cell(($this->w - $this->original_lMargin - $this->original_rMargin), 0, $NumPagina, 'T', 0, 'C');
	}
}

$pdf = new ContratoPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, TRUE, 'UTF-8', FALSE);

$id = $_REQUEST['id'];
$tipo = $_REQUEST['tipo'];

TTransaction::open('permission');            
$empresa = new Empresa(TSession::getValue('empresa'));
TTransaction::close();

TTransaction::open(TSession::getValue('banco'));
$uf = new UF($empresa->uf_id);

if ($tipo == 1)
{
    $empresaInfo = '<b>NOME:</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $empresa->nome . '.<br>';
    $empresaInfo .= '<b>END.:</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $empresa->endereco . '.<br>';
    $empresaInfo .= '<b>CEP/CID/UF:</b>&nbsp;' . $empresa->cep . ' - ' . $empresa->municipio . ' - ' . $empresa->uf_id . ' FONE: ' . $empresa->fone . '<br>';
    $empresaInfo .= 'Registro na JUCESC Nº ' . $empresanrjucesc . ' - Registro no CRECI Nº ' . $empresa->creci;
    
    $empresaAssinatura = '__________________________________<br>' . $empresa->nome . '<br>ADMINISTRADORA';    
    
    $bem =  new Bem($id);
    $bemInfo = '<b>Descrição:</b> ' . $bem->descricao . ', LOCALIZADO NA ' . $bem->endereco . ' - ' . $bem->complemento . ', BAIRRO ' . $bem->bairro . ' - ' .$bem->municipio . ' - ' . $bem->uf_id . '<br>';
    
    $locador = new Cliente($bem->proprietario_id);
    $locadorInfo = '<b>NOME:</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $locador->nome . '<br>';
    $locadorInfo .= '<b>END.:</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $locador->endereco . '<br>';
    $locadorInfo .= '<b>CEP/CID/UF:</b>&nbsp;' . $locador->cep . ' - BAIRRO ' . $locador->bairro . ' - ' . $locador->municipio . ' - ' . $locador->uf_id . '<br>';
    $locadorInfo .= '<b>CPF /CNPJ:</b>&nbsp;&nbsp;&nbsp;' . $locador->cpfcnpj . '<br>';
    
    $locadorAssinatura = '__________________________________<br>' . $locador->nome . '<br>PROPRIETÁRIO(A)';
    
    $id = number_format($id, 0, ',', '.');
    $pdf->SetTitle('CONTRATO DE ADMINISTRAÇÃOO DE IMÓVEIS - CB ' . $id);
}


if ($tipo == 2)
{
    $empresaRepresentante = $empresa->nomeresp;
    $empresaInfo = $empresa->nome . ', pessoa jurídica de direito privado, inscrita no CNPJ sob o n.º ' .
                  $empresa->cnpj . ', com sede à ' . $empresa->endereco . ', bairro ' . $empresa->bairro . 
                  ', na cidade de ' . $empresa->municipio . ', estado de ' . $empresa->uf;
    $empresaAssinatura = '__________________________________<br>ADMINISTRADORA<br>' . $empresa->nome . '<br>'.$empresa->representante_nome;
    
    
    $contrato = new Contrato($id); 
     
    $bem = new Bem($contrato->bem_id); 
    $bemInfo = $bem->descricao . ', localizado na ' .$bem->endereco . ' - ' . $bem->complemento . ', bairro ' . $bem->bairro . ', na cidade de ' . 
               $bem->municipio . ', ' . $bem->uf;
                                     
    $locador = new Cliente($bem->proprietario_id);
    $locadorInfo = $locador->nome;
    if ($locador->tipo == 'F')
    {
        $locadorInfo .= ', brasileiro(a), ' . $locador->estadocivil . ', ' . $locador->profissao . ', ' . 
                        'portador(a) da cédula de identidade RG n.º ' . $locador->ierg . ', inscrito(a) no CPF/MF sob o n.º ' . 
                        $locador->cpfcnpj . ', residente e domiciliado(a) à ' . $locador->endereco . ', bairro ' . $locador->bairro .
                        ', na cidade de ' . $locador->municipio . ' estado de ' . $locador->uf . ', CEP ' . $locador->cep;
    }
    else
    {
        $locadorInfo .= ', pessoa jurídica de direito privado, inscrita no CNPJ sob o n.º ' . $locador->cpfcnpj . 
                        ', com sede à ' . $locador->endereco . ', bairro ' . $locador->bairro . ', na cidade de ' .
                        $locador->municipio . ', estado de ' . $locador->uf . ', CEP ' . $locador->cep;
    }
    /*$locadorInfo = $locador->nome . ', brasileiro(a), ' . $locador->estadocivil . ', ' . $locador->profissao . ', ' .
                    'portador(a) da cédula de identidade RG n.º ' . $locador->ierg . ', SSP/SC, inscrito(a) no CPF/MF sob o n.º ' .
                    $locador->cpfcnpj . ', residente e domiciliado(a) à ' . $locador->endereco . ', bairro ' . $locador->bairro . 
                    ', na cidade de ' . $locador->municipio .  ', estado de ' . $locador->uf . ', CEP: ' . $locador->cep;*/                  
    
    $locadorAssinatura = '__________________________________<br>' . $locador->nome . '<br>PROPRIETÁRIO(A)';
    
    $locatario = new Cliente($contrato->cliente_id);
    $locatarioInfo = $locatario->nome;
    if ($locatario->tipo == 'F')
    {
        $locatarioInfo .= ', brasileiro(a), ' . $locatario->empresa_cargo . ', portador(a) da cédula de identidade RG n.º ' .
                          $locatario->ierg . ', SSP/SC, inscrito(a) no CPF/MF sob o n.º ' . $locatario->cpfcnpj . ', ' . 
                          $locatario->estadocivil;
        if ($locatario->estadocivil_id == 1)
        {
            $locatarioInfo .= ' em regime de ' . $locatario->conjuge_regime . ' com ' . $locatario->conjuge_nome . ', brasileiro(a), ' . 
                              $locatario->conjuge_profissao . ', portador(a) da cédula de identidade RG n.º ' . $locatario->conjuge_rg .
                              ', SSP/SC, inscrito(a) no CPF/MF sob o n.º ' . $locatario->conjuge_cpf . ', ambos residentes e domiciliado(a) à ';
        }
        else
        {
            $locatarioInfo .= 'residente e domiciliado(a) à '; 
        } 
                          
        $locatarioInfo .= $locatario->endereco . ', bairro ' . $locatario->bairro . ', na cidade de ' . $locatario->municipio .
                          ', estado de ' . $locatario->uf . ', CEP: ' . $locatario->cep; 
    }
    else
    {
        $representante = new Cliente($locatario->representante_id);
        $locatarioInfo .= ', pessoa jurídica de direito privado, inscrita no CNPJ sob o n.º ' . $locatario->cpfcnpj . 
                          ', com sede à ' . $locatario->endereco . ', bairro ' . $locatario->bairro . ', na cidade de ' .
                          $locatario->municipio . ', estado de ' . $locatario->uf . ', neste ato representada por seu(sua) ' . 
                          'representante legal, Sr(a). ' . $representante->nome . ', brasileiro(a), ' . $representante->profissao .
                          ', ' . $representante->estadocivil . ', portador(a) da cédula de identidade RG n.º ' . $representante->ierg .
                          ', SSP/SC, inscrito(a) no CPF/MF sob o n.º ' . $representante->cpfcnpj . ', residente e domiciliado(a) à ' .
                          $representante->endereco . ', bairro ' . $representante->bairro . ', na cidade de ' . $representante->municipio .
                          ', estado de ' . $representante->uf . ', CEP: ' . $representante->cep;    
    } 
    $locatarioAssinatura = '__________________________________<br>LOCATÁRIO(A)<br>' . $locatario->nome . '<br>' . $representante->nome;
    
    if ($contrato->cliente2_id > 0) {
        $locatario = new Cliente($contrato->cliente2_id);
        $locatarioInfo .= ' e ' . $locatario->nome;
        if ($locatario->tipo == 'F')
        {
            $locatarioInfo .= ', brasileiro(a), ' . $locatario->empresa_cargo . ', portador(a) da cédula de identidade RG n.º ' .
                              $locatario->ierg . ', SSP/SC, inscrito(a) no CPF/MF sob o n.º ' . $locatario->cpfcnpj . ', ' . 
                              $locatario->estadocivil;
            if ($locatario->estadocivil_id == 1)
            {
                $locatarioInfo .= ' em regime de ' . $locatario->conjuge_regime . ' com ' . $locatario->conjuge_nome . ', brasileiro(a), ' . 
                                  $locatario->conjuge_profissao . ', portador(a) da cédula de identidade RG n.º ' . $locatario->conjuge_rg .
                                  ', SSP/SC, inscrito(a) no CPF/MF sob o n.º ' . $locatario->conjuge_cpf . ', ambos residentes e domiciliado(a) à ';
            }
            else
            {
                $locatarioInfo .= 'residente e domiciliado(a) à '; 
            } 
                              
            $locatarioInfo .= $locatario->endereco . ', bairro ' . $locatario->bairro . ', na cidade de ' . $locatario->municipio .
                              ', estado de ' . $locatario->uf . ', CEP: ' . $locatario->cep; 
        }
        else
        {
            $representante = new Cliente($locatario->representante_id);
            $locatarioInfo .= ', pessoa jurídica de direito privado, inscrita no CNPJ sob o n.º ' . $locatario->cpfcnpj . 
                              ', com sede à ' . $locatario->endereco . ', bairro ' . $locatario->bairro . ', na cidade de ' .
                              $locatario->municipio . ', estado de ' . $locatario->uf . ', neste ato representada por seu(sua) ' . 
                              'representante legal, Sr(a). ' . $representante->nome . ', brasileiro(a), ' . $representante->profissao .
                              ', ' . $representante->estadocivil . ', portador(a) da cédula de identidade RG n.º ' . $representante->ierg .
                              ', SSP/SC, inscrito(a) no CPF/MF sob o n.º ' . $representante->cpfcnpj . ', residente e domiciliado(a) à ' .
                              $representante->endereco . ', bairro ' . $representante->bairro . ', na cidade de ' . $representante->municipio .
                              ', estado de ' . $representante->uf . ', CEP: ' . $representante->cep;    
        } 
        $locatarioAssinatura .= '<br><br>__________________________________<br>LOCATÁRIO(A)<br>' . $locatario->nome . '<br>' . $representante->nome;
    }
      		
    if ($contrato->avalista_id != 0)
    {
        $fiador = new Cliente($contrato->avalista_id);
        $fiadorInfo = $fiador->nome . ', brasileiro(a), ' . $fiador->profissao . ', portador(a) da cédula de identidade RG n.º ' . 
                      $fiador->ierg . ', SSP/SC, inscrito(a) no CPF/MF sob o n.º ' . $fiador->cpfcnpj . ', ' . $fiador->estadocivil;
        
        if ($fiador->estadocivil_id == 1)
        {
            $fiadorInfo .= 'em regime de ' . $fiador->conjuge_regime . ' com ' . $fiador->conjuge_nome . ', brasileiro(a), ' .
                           $fiador->conjuge_profissao . ', portador(a) da cédula de identidade RG n.º ' . $fiador->conjuge_rg .
                           ', SSP/SC, inscrito(a) no CPF/MF sob o n.º ' . $fiador->conjuge_cpf . ', ambos residentes e domiciliado(a) à ';
            $conjugeAssinatura = '__________________________________<br>CÔNJUGE DO FIADOR<br>' . $fiador->conjuge_nome;
        } 
        else
        {
            $fiadorInfo .= 'residente e domiciliado(a) à ';
            $conjugeAssinatura = '';
        }
        
        $fiadorInfo .= $fiador->endereco . ', bairro ' . $fiador->bairro . ', na cidade de ' . $fiador->municipio .
                       ', estado de ' . $fiador->uf . ', CEP: ' . $fiador->cep;
        $fiadorAssinatura = '__________________________________<br>FIADOR<br>' . $fiador->nome;
    }
        		
    if ($contrato->avalista2_id != 0)
    {
        $fiador2 = new Cliente($contrato->avalista2_id);
        $fiador2Info = $fiador2->nome . ', brasileiro(a), ' . $fiador2->profissao . ', portador(a) da cédula de identidade RG n.º ' . 
                      $fiador2->ierg . ', SSP/SC, inscrito(a) no CPF/MF sob o n.º ' . $fiador2->cpfcnpj . ', ' . $fiador2->estadocivil;
        
        if ($fiador2->estadocivil_id == 1)
        {
            $fiador2Info .= 'em regime de ' . $fiador2->conjuge_regime . ' com ' . $fiador2->conjuge_nome . ', brasileiro(a), ' .
                           $fiador2->conjuge_profissao . ', portador(a) da cédula de identidade RG n.º ' . $fiador2->conjuge_rg .
                           ', SSP/SC, inscrito(a) no CPF/MF sob o n.º ' . $fiador2->conjuge_cpf . ', ambos residentes e domiciliado(a) à ';
            $conjuge2Assinatura = '__________________________________<br>CÔNJUGE DO FIADOR<br>' . $fiador2->conjuge_nome;
        } 
        else
        {
            $fiador2Info .= 'residente e domiciliado(a) à ';
            $conjuge2Assinatura = '';
        }
        
        $fiador2Info .= $fiador2->endereco . ', bairro ' . $fiador2->bairro . ', na cidade de ' . $fiador2->municipio .
                       ', estado de ' . $fiador2->uf . ', CEP: ' . $fiador2->cep;
        $fiador2Assinatura = '__________________________________<br>FIADOR<br>' . $fiador2->nome;
    }
    
    if ($contrato->tipogarantia_id == 1)
    {
        $bemgarantiaInfo = $contrato->bemgarantia_descricao . ' situado à ' . $bemgarantia->endereco . ', contendo área total de ' . 
                           $contrato->bemgarantia_metragem . 'm² (' . FuncoesExtras::valorPorExtenso($contrato->bemgarantia_metragem) . '), ' .
                           'matriculado sob o n.º ' . $contrato->bemgarantia_matricula;
    }
    
    $id = number_format($id, 0, ',', '.');
}

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor($empresa->nome);
$pdf->SetTitle('CONTRATO DE LOCAÇÃO Nº ' . $id);

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 039', PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, 50/*PDF_MARGIN_TOP*/, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

$pdf->AddPage();

$modelo = new ModeloContrato($_REQUEST['modelo']);
$html = $modelo->conteudo;

$html = str_replace('CONTRATO_NUMERO', $id, $html);
$html = str_replace('LOCADOR_INFO', $locadorInfo, $html);
$html = str_replace('EMPRESA_INFO', $empresaInfo, $html);
$html = str_replace('LOCATARIO_INFO', $locatarioInfo, $html);
$html = str_replace('FIADOR_INFO', $fiadorInfo, $html);
$html = str_replace('FIADOR2_INFO', $fiador2Info, $html);
$html = str_replace('BEM_INFO', $bemInfo, $html);

setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');

if ($tipo == 1)
{
    $valor = $bem->vlaluguel;
    $valor = 'R$ ' . number_format($valor, 2, ',', '.') . ' (' . strtoupper(FuncoesExtras::valorPorExtenso(number_format($valor, 2, ',', ''), 1)) . ' )';
    $html = str_replace('CONTRATO_VALOR', $valor, $html);
    $dia = strftime('%d', strtotime(date('Y-m-d')));
    $mes = strftime('%B', strtotime(date('Y-m-d')));
    $ano = strftime('%Y', strtotime(date('Y-m-d')));
}

if ($tipo == 2)
{
    $qtdeparc = $contrato->qtdeparc;
    $qtdeparc = $qtdeparc . ' (' . FuncoesExtras::valorPorExtenso(number_format($qtdeparc, 2, ',', '.'), FALSE) . ' meses )';
    $html = str_replace('CONTRATO_QTDEPARC', $qtdeparc, $html);
    $html = str_replace('CONTRATO_DTINICIO', TDate::date2br($contrato->dtinicio), $html);
    $html = str_replace('CONTRATO_DTFIM', TDate::date2br($contrato->dtfim), $html);
    $valor = $contrato->valor;
    $CONTRATO_VALOR = number_format($valor, 2, ',', '.') . ' ( ' . FuncoesExtras::valorPorExtenso(number_format($valor, 2, ',', ''), 1) . ' )';
    if ($contrato->vldesc > 0)
    {
        $CONTRATO_VALOR .= ', sendo que nos ' . $contrato->qtdeparcdesc . ' ( ' . FuncoesExtras::valorPorExtenso($contrato->qtdeparcdesc, FALSE) . ' ) ' . 
                  ' primeiros meses será concedido um desconto no aluguel de  R$ ' . number_format($contrato->vldesc, 2, ',', '.') . 
                  ' ( ' . FuncoesExtras::valorPorExtenso(number_format($contrato->vldesc, 2, ',', '.')) . ' ),  '; 
    }
    $html = str_replace('CONTRATO_VALOR', $CONTRATO_VALOR, $html);
    $html = str_replace('CONTRATO_DIAVENCTO', $contrato->diavencto, $html);
    if ($contrato->tipogarantia_id == 1)
    {    
        $html = str_replace('FIADOR_ASSINATURA', $fiadorAssinatura, $html);
        $html = str_replace('FIADOR2_ASSINATURA', $fiador2Assinatura, $html);
        $html = str_replace('CONJUGE_ASSINATURA', $conjugeAssinatura, $html);
        $html = str_replace('CONJUGE2_ASSINATURA', $conjuge2Assinatura, $html);
        $html = str_replace('BEMGARANTIA_INFO', $bemgarantiaInfo, $html);
    }
    
    if ($contrato->tipogarantia_id == 2)
    {
        $valor = $valor * 3;
        $CONTRATO_VLCAUCAO = number_format($valor, 2, ',', '.') . ' ( ' . FuncoesExtras::valorPorExtenso(number_format($valor, 2, ',', ''), 1) . ' )';
        $html = str_replace('CONTRATO_VLCAUCAO', $CONTRATO_VLCAUCAO, $html);
    }
    $dia = strftime('%d', strtotime($contrato->dtcadastro));
    $mes = strftime('%B', strtotime($contrato->dtcadastro));
    $ano = strftime('%Y', strtotime($contrato->dtcadastro));
}

$html = str_replace('CONTRATO_DATA', $dia . ' de ' . ucfirst($mes) . ' de ' . $ano, $html);
$html = str_replace('EMPRESA_ASSINATURA', $empresaAssinatura, $html);
$html = str_replace('LOCADOR_ASSINATURA', $locadorAssinatura, $html);
$html = str_replace('LOCATARIO_ASSINATURA', $locatarioAssinatura, $html);

$pdf->writeHTML($html, TRUE, 0, TRUE, TRUE);

TTransaction::close();

if ($tipo == 1)
{
    $pdf->Output('Contrato de Locação Nº ' . $id . '.pdf', 'I');
}

if ($tipo == 2)
{
    $pdf->Output('Contrato de Administração Nº ' . $id . '.pdf', 'I');
}
?>