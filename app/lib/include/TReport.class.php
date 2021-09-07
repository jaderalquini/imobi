<?php
class TReport extends TPDFDesigner
{
    var $title = '';
    var $filtro = '';
    var $columns = array();
    
    function setTitle($param)
    {
        global $title;
        $title = $param;
    }
    
    function setFiltro($param)
    {
        global $filtro;
        $filtro = $param;
    }
    
    function setColumns($param)
    {
        global $columns;
        $columns = $param;
    }
    
    function Header()
    {
        global $title;
        global $filtro;
        global $columns;
        
        TTransaction::open('permission');            
        $empresa = new Empresa(TSession::getValue('empresa'));
        TTransaction::close();
        
        $this->SetXY(30,30);
        $this->SetFont('Arial','B',8);
        $this->Cell(200,10,utf8_decode('AlquiniWEB | Sistema Imobiliário'),0,0,'L');
        $this->Cell(200,10,date('d/m/Y H:i'),0,0,'C');
        $this->Cell(150,10,utf8_decode('Página ' . $this->PageNo() . ' de {nb}'),0,0,'R');
        $this->Ln();
        $this->SetX(30);
        $this->Cell(200,10,utf8_decode($empresa->nome),0,0,'L');
        $this->Cell(200,10,utf8_decode($title),0,0,'C');
        $this->Ln();
        $h = $this->GetY();
        $this->Line(30, $h + 5, 565, $h + 5);
        $this->Ln();
        $this->SetX(30);
        $this->Cell(0,10,utf8_decode($filtro),0,0,'L');
        $this->Line(30, $h + 25, 565, $h + 25);
        $this->Ln();
        $this->Ln();
        $this->SetX(30);
        foreach ($columns as $column)
        {
            $this->Cell($column['size'],10,utf8_decode($column['text']),0,0,$column['align']);
        }
        $this->Line(30, $h + 45, 565, $h + 45);
        $this->Ln();
        $this->Ln();
    }
}