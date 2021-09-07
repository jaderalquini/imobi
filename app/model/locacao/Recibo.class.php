<?php
/**
 * Recibo Active Record
 * @author  <your-name-here>
 */
class Recibo extends TRecord
{
    const TABLENAME = 'recibo';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('valor');
        parent::addAttribute('recebedor');
        parent::addAttribute('descricao');
        parent::addAttribute('pagador');
        parent::addAttribute('dtrecibo');
    }

    public static function CreateTable($banco)
    {
        try
        {
            TTransaction::open($banco);
            $conn = TTransaction::get();
            
            $query = "CREATE TABLE recibo (
                    	id INTEGER,
                    	valor DECIMAL (10, 2),
                    	recebedor varchar(100),
                    	descricao TEXT,
                    	pagador varchar(100),
                    	dtrecibo DATE,
                    	PRIMARY KEY (id)
                    );";
            $conn->query($query);
            
            TTransaction::close();
        }
        catch (Exception $e) // in case of exception
        {
            return NULL;    
        }
    }
}
