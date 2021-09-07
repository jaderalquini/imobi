<?php
/**
 * Parametros Active Record
 * @author  <your-name-here>
 */
class Parametros extends TRecord
{
    const TABLENAME = 'parametros';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('value');
    }
    
    /**
     * Retorna um parâmetro
     * @param $id Id do parâmetro
     */
    public static function getParametros($id)
    {
        $parametro = new Parametros($id);
        return $parametro->value;
    }
    
    /**
     * Altera uma preferência
     * @param $id  Id da preferência
     * @param $value Valor da preferência
     */
    public static function setParametro($id, $value)
    {
        $parametro = Parametros::find($id);
        if ($parametro)
        {
            $parametro->value = $value;
            $parametro->store();
        }
    }
    
    /**
     * Retorna um array com todas preferências
     */
    public static function getAllParametros()
    {
        $rep = new TRepository('Parametros');
        $objects = $rep->load(new TCriteria);
        $dataset = array();
        
        if ($objects)
        {
            foreach ($objects as $object)
            {
                $property = $object->id;
                $value    = $object->value;
                $dataset[$property] = $value;
            }
        }
        return $dataset;
    }
}