<?php
/**
 * Municipio Active Record
 * @author  <your-name-here>
 */
class Municipio extends TRecord
{
    const TABLENAME = 'municipio';
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
        parent::addAttribute('uf_id');
    }
    
    /**
     * Method get_uf
     * Sample of usage: $municipio->uf->attribute;
     * @returns UF instance
     */
    public function get_uf()
    {
        $obj = new UF($this->uf_id);
        return $obj->nome;
    }
}