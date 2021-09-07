<?php
/**
 * Empresa Active Record
 * @author  <your-name-here>
 */
class Empresa extends TRecord
{
    const TABLENAME = 'empresa';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}    
    
    private $uf;
    
    use SystemChangeLogTrait;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
        parent::addAttribute('cnpj');
        parent::addAttribute('ie');
        parent::addAttribute('nrjucesc');
        parent::addAttribute('creci');
        parent::addAttribute('cep');
        parent::addAttribute('uf_id');
        parent::addAttribute('municipio_id');
        parent::addAttribute('municipio');
        parent::addAttribute('bairro');
        parent::addAttribute('endereco');
        parent::addAttribute('fone');
        parent::addAttribute('fax');
        parent::addAttribute('site');
        parent::addAttribute('email');
        parent::addAttribute('responsavel_nome');
        parent::addAttribute('responsavel_cpf');
        parent::addAttribute('logo');
        parent::addAttribute('banco');
        parent::addAttribute('juros');
        parent::addAttribute('multa');
    }
    
    /**
     * Method get_uf
     * Sample of usage: $empresa->uf->attribute;
     * @returns UF instance
     */
    public function get_uf()
    {        
        try
        {
            $obj = new UF($this->uf_id);
            return $obj->nome; 
        }  
        catch (Exception $e) // in case of exception
        {
            return NULL;    
        }
    }  
    
    public function onAlterTable()
    {
        try
        {
            TTransaction::open('permission');
            $conn = TTransaction::get();
            
            $query = 'ALTER TABLE empresa ADD juros DECIMAL(10,2);';
            $conn->query($query);
            
            $query = 'ALTER TABLE empresa ADD multa DECIMAL(10,2);';
            $conn->query($query);
            
            TTransaction::close();
        }
        catch (Exception $e) // in case of exception
        {
            return NULL;    
        }
    }
}