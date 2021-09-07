<?php
/**
 * ModeloContrato Active Record
 * @author  <your-name-here>
 */
class ModeloContrato extends TRecord
{
    const TABLENAME = 'modelo_contrato';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    private $tipogarantia;
    
    use SystemChangeLogTrait;
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('descricao');
        parent::addAttribute('tipocontrato_id');
        parent::addAttribute('percomissao');
        parent::addAttribute('rescom');
        parent::addAttribute('tipogarantia_id');
        parent::addAttribute('qtdeparc');
        parent::addAttribute('conteudo');
    }
    
    /**
     * Method get_tipocontrato
     * Sample of usage: $modelo_contrato->tipocontrato->attribute;
     * @returns Tipocontrato instance
     */
    public function get_tipocontrato()
    {
        try
        {
            $obj = new Tipocontrato($this->tipocontrato_id);
            return $obj->descricao;   
        }  
        catch (Exception $e) // in case of exception
        {
            return NULL;    
        }        
    }
    
    /**
     * Method get_tipogarantia
     * Sample of usage: $modelo_contrato->tipogarantia->attribute;
     * @returns Municipio instance
     */
    public function get_tipogarantia()
    {
        try
        {
            $obj = new Tipograntia($this->tipogarantia_id);
            return $obj->descricao;   
        }  
        catch (Exception $e) // in case of exception
        {
            return NULL;    
        }        
    }
}
?>