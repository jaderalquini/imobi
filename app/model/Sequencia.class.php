<?php
/**
 * Sequencia Active Record
 * @author  <your-name-here>
 */
class Sequencia extends TRecord
{
    const TABLENAME = 'sequencia';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    use SystemChangeLogTrait;    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('numrecibo');
        parent::addAttribute('numrecpro');
        parent::addAttribute('numreccli');
        parent::addAttribute('obsrecibopagar');
        parent::addAttribute('obsreciboreceber');
    }
}
