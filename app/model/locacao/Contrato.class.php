<?php
/**
 * Contrato Active Record
 * @author  <your-name-here>
 */
class Contrato extends TRecord
{
    const TABLENAME = 'contrato';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}    
    
    private $bem;
    private $cliente;
    
    use SystemChangeLogTrait;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('bem_id');
        parent::addAttribute('cliente_id');
        parent::addAttribute('cliente2_id');
        parent::addAttribute('dtinicio');
        parent::addAttribute('dtfim');
        parent::addAttribute('vlacrescido');
        parent::addAttribute('percdesc');
        parent::addAttribute('valor');
        parent::addAttribute('vldesc');
        parent::addAttribute('qtdeparcdesc');
        parent::addAttribute('avalista_id');
        parent::addAttribute('avalista2_id');
        parent::addAttribute('vlseguro');
        parent::addAttribute('qtdeparc');
        parent::addAttribute('diavencto');
        parent::addAttribute('dtcadastro');
        parent::addAttribute('tipogarantia_id');
        parent::addAttribute('bemgarantia_descricao');
        parent::addAttribute('bemgarantia_endereco');
        parent::addAttribute('bemgarantia_matricula');
        parent::addAttribute('bemgarantia_metragem');
        parent::addAttribute('liquidado');
        parent::addAttribute('system_user_id');
    }
    
    /**
     * Method get_bem
     * Sample of usage: $contrato->bem->attribute;
     * @returns Bem instance
     */
    public function get_bem()
    {
        try
        {
            $obj = new Bem($this->bem_id);
            return $obj->descricao;   
        }  
        catch (Exception $e) // in case of exception
        {
            return NULL;    
        }        
    }
    
    /**
     * Method get_cliente
     * Sample of usage: $contrato->cliente->attribute;
     * @returns Cliente instance
     */
    public function get_cliente()
    {
        try
        {
            $obj = new Cliente($this->cliente_id);
            return $obj->nome;   
        }  
        catch (Exception $e) // in case of exception
        {
            return NULL;    
        }        
    }   
    
    /**
     * Method get_cliente2
     * Sample of usage: $contrato->cliente2->attribute;
     * @returns Cliente instance
     */
    public function get_cliente2()
    {
        try
        {
            $obj = new Cliente($this->cliente2_id);
            return $obj->nome;   
        }  
        catch (Exception $e) // in case of exception
        {
            return NULL;    
        }        
    }
    
    /**
     * Method get_avalista
     * Sample of usage: $contrato->avalista->attribute;
     * @returns Cliente instance
     */
    public function get_avalista()
    {
        try
        {
            $obj = new Cliente($this->avalista_id);
            return $obj->nome;   
        }  
        catch (Exception $e) // in case of exception
        {
            return NULL;    
        }        
    }
    
    /**
     * Method get_avalista2
     * Sample of usage: $contrato->avalista2->attribute;
     * @returns Cliente instance
     */
    public function get_avalista2()
    {
        try
        {
            $obj = new Cliente($this->avalista2_id);
            return $obj->nome;   
        }  
        catch (Exception $e) // in case of exception
        {
            return NULL;    
        }        
    }
    
    /**
     * Delete the object and its aggregates
     * @param $id object ID
     */
    public function delete($id = NULL)
    {        
        $bem = array();
        $bem['id'] = $this->bem_id;
        $bem['contrato_id'] = 0;
        $bem['cliente_id'] = 0;
        $bem['cliente2_id'] = 0;
                
        $object = new Bem();
        $object->fromArray( (array) $bem);
        $object->store();
        
        $id = isset($id) ? $id : $this->id;
        $repository = new TRepository('ParcelaPagar');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('contrato_id', '=', $id));
        $repository->delete($criteria);
                
        $id = isset($id) ? $id : $this->id;
        $repository = new TRepository('ParcelaReceber');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('contrato_id', '=', $id));
        $repository->delete($criteria);        
    
        // delete the object itself
        parent::delete($id);
    }
    
    public function onImport($data)
    {   
        $class = get_class($this);
        $table = constant("{$class}::TABLENAME");
        
        TTransaction::open(TSession::getValue('banco'));
        $conn = TTransaction::get();
        
        $sql = "delete from $table";
        
        $conn-> query($sql);
        
        $target_folder = 'tmp/CSV/';
        $target_file   = $target_folder . $class . '.csv';
        
        $sql = "INSERT INTO $table (id, cliente_id, bem_id, dtinicio, dtfim, vlacrescido, percdesc, valor, vldesc, qtdeparcdesc, 
                avalista_id, avalista2_id, vlseguro, qtdeparc, diavencto, dtcadastro, liquidado, tipogarantia_id, system_user_id, cliente2_id) values ";
           
        $primeiro = TRUE;
        $i = 0;
        $j = 0;
        $linhas = file($target_file);
        foreach ($linhas as $linha) 
        {
            if ($i == 500)
            {
                try
                {
                    $conn-> query($sql);    
                }
                catch (Exception $e) // in case of exception
                {
                    $j++;                    
                    $target_folder = 'tmp/SQL/';
                    $target_file   = $target_folder . $class . $j . '.sql';
                    $fp = fopen($target_file, "w");                    
         
                    fwrite($fp, $sql);
                    
                    new TMessage('error', $e->getMessage());    
                }
            
                $i = 0;
                $primeiro = TRUE;
                $sql = "INSERT INTO $table (id, cliente_id, bem_id, dtinicio, dtfim, vlacrescido, percdesc, valor, vldesc, qtdeparcdesc, 
                avalista_id, avalista2_id, vlseguro, qtdeparc, diavencto, dtcadastro, liquidado, tipogarantia_id, system_user_id, cliente2_id) values ";
            }
            
            $data = array_map('trim', explode(';', $linha));
            list($id, $cliente_id, $bem_id, $dtinicio, $dtfim, $vlacrescido, $percdesc, $valor, $vldesc, $qtdeparcdesc,
                $tipo_reajuste, $avalista_id, $avalista2_id, $vlseguro, $qtdeparc, $diavencto, $dtcadastro, $liquidado, 
                $system_user_id, $cliente2_id) =  $data;
            
            $i++;
            $dtinicio = substr($dtinicio,0,4)."-".substr($dtinicio,4,2)."-".substr($dtinicio,6,2);
            $dtfim = substr($dtfim,0,4)."-".substr($dtfim,4,2)."-".substr($dtfim,6,2);
            $vlacrescido = intval($vlacrescido);
            $vlacrescido = $vlacrescido / 100;
            $percdesc = intval($percdesc);
            $percdesc = $percdesc / 100;
            $valor = intval($valor);
            $valor = $valor / 100;
            $vldesc = intval($vldesc);
            $vldesc = $vldesc / 100;
            $vlseguro = intval($vlseguro);
            $vlseguro = $vlseguro / 100;
            $qtdeparc = substr($qtdeparc, -2);
            $dtcadastro = substr($dtcadastro,0,4)."-".substr($dtcadastro,4,2)."-".substr($dtcadastro,6,2);
            $cliente2_id = intval($cliente2_id);
            if ($liquidado == 0)
            {
                $liquidado = 'N';
            }
            elseif ($liquidado == 1 || $liquidado == 2)
            {
                $liquidado = 'S';
            }
            else 
            {
                $liquidado = 'O';
            }
            
            if ($primeiro)
            {
                $primeiro = FALSE;
            }
            else
            {
                $sql .= ",";        
            }
            
            $tipogarantia_id = ($avalista_id == 0 || $avalista2_id == 0) ? 2 : 1;
            $sql .= "($id, $cliente_id, $bem_id, '$dtinicio', '$dtfim', $vlacrescido, $percdesc, $valor, $vldesc, $qtdeparcdesc, 
                    $avalista_id, $avalista2_id, $vlseguro, $qtdeparc, $diavencto, '$dtcadastro', '$liquidado', $tipogarantia_id, $system_user_id, $cliente2_id)";
        }
        
        try
        {
            $conn-> query($sql);
            
            new TMessage('info', 'Arquivo importado com sucesso!');
        }
        catch (Exception $e) // in case of exception
        {
            $j++;
            $target_folder = 'tmp/SQL/';
            $target_file   = $target_folder . $class . $j . '.sql';
            $fp = fopen($target_file, "w");
 
            fwrite($fp, $sql);
            
            new TMessage('error', $e->getMessage());    
        }
        
        TTransaction::close();
    }
}