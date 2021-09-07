<?php
/**
 * Tipograntia Active Record
 * @author  <your-name-here>
 */
class Tipograntia extends TRecord
{
    const TABLENAME = 'tipograntia';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}  
    
    use SystemChangeLogTrait;  
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('descricao');
    }
}
?>