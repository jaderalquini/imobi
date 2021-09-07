<?php
/**
 * Bem Active Record
 * @author  <your-name-here>
 */
class Bem extends TRecord
{
    const TABLENAME = 'bem';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}    
    
    //private $municipio;
    private $uf;
    private $localizacao;
    private $tipobem;
    private $cliente;
    private $contrato;
    
    use SystemChangeLogTrait;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('descricao');
        parent::addAttribute('endereco');
        parent::addAttribute('complemento');
        parent::addAttribute('municipio_id');
        parent::addAttribute('municipio');
        parent::addAttribute('bairro');
        parent::addAttribute('cep');
        parent::addAttribute('uf_id');
        parent::addAttribute('localizacao_id');
        parent::addAttribute('tipobem_id');
        parent::addAttribute('proprietario_id');
        parent::addAttribute('rescom');
        parent::addAttribute('pagtogar');
        parent::addAttribute('diapagto');
        parent::addAttribute('vlaluguel');
        parent::addAttribute('vldesc');
        parent::addAttribute('qtdemes');
        parent::addAttribute('percomissao');
        parent::addAttribute('cliente_id');
        parent::addAttribute('contrato_id');
        parent::addAttribute('reservar');
        parent::addAttribute('obs');
        parent::addAttribute('urbrural');
        parent::addAttribute('cliente2_id');
        parent::addAttribute('area_terreno');
        parent::addAttribute('matricula');
    }
    
    /**
     * Method get_municipio
     * Sample of usage: $bem->municipio->attribute;
     * @returns Municipio instance
     */
    public function get_municipio()
    {
        try
        {
            $obj = new Municipio($this->municipio_id);
            return $obj->nome;   
        }  
        catch (Exception $e) // in case of exception
        {
            return NULL;    
        }        
    }
    
    /**
     * Method get_uf
     * Sample of usage: $bem->uf->attribute;
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
    
    /**
     * Method get_localizacao
     * Sample of usage: $bem->localizacao->attribute;
     * @returns Localizacao instance
     */
    public function get_localizacao()
    {
        try
        {
            TTransaction::open(TSession::getValue('banco'));
            $obj = new Localizacao($this->localizacao_id);
            TTransaction::close();
            return $obj->descricao;   
        }  
        catch (Exception $e) // in case of exception
        {
            return NULL;    
        }        
    }
    
    /**
     * Method get_tipobem
     * Sample of usage: $bem->tipobem->attribute;
     * @returns Tipobem instance
     */
    public function get_tipobem()
    {
        try
        {
            $obj = new Tipobem($this->tipobem_id);
            return $obj->descricao;   
        }  
        catch (Exception $e) // in case of exception
        {
            return NULL;    
        }        
    }
    
    /**
     * Method get_proprietario
     * Sample of usage: $bem->proprietario->attribute;
     * @returns Cliente instance
     */
    public function get_proprietario()
    {
        try
        {
            $obj = new Cliente($this->proprietario_id);
            return $obj->nome;   
        }  
        catch (Exception $e) // in case of exception
        {
            return NULL;    
        }        
    }
    
    /**
     * Method get_cliente
     * Sample of usage: $bem->cliente->attribute;
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
     * Sample of usage: $bem->cliente2->attribute;
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
     * Delete the object and its aggregates
     * @param $id object ID
     */
    public function delete($id = NULL)
    {
        // delete the related System_userSystem_user_group objects
        $id = isset($id) ? $id : $this->id;
        $repository = new TRepository('Contrato');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('bem_id', '=', $id));
        //$criteria->add(new TFilter('liquidado', '=', 'N'));
        
        if ($id == $repository->bem_id)
        {  
            $contrato = $repository->id;
            new TMessage('error', 'Bem esta locado para o contrato nº {$contrato_id}');    
        }
        else
        {            
            parent::delete($id);
        }
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
        
        $sql = "INSERT INTO $table (id, descricao, endereco, complemento, bairro, municipio, cep, uf_id, localizacao_id, tipobem_id, proprietario_id, rescom, 
                pagtogar, diapagto, vlaluguel, vldesc, qtdemes, percomissao, cliente_id, contrato_id, reservar, obs, urbrural, municipio_id, cliente2_id) values ";
           
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
                $sql = "INSERT INTO $table (id, descricao, endereco, complemento, bairro, municipio, cep, uf_id, localizacao_id, tipobem_id, proprietario_id, rescom, 
                pagtogar, diapagto, vlaluguel, vldesc, qtdemes, percomissao, cliente_id, contrato_id, reservar, obs, urbrural, municipio_id, cliente2_id) values ";
            }
            
            $data = array_map('trim', explode(';', $linha));
            list($id, $descricao1, $descricao2, $endereco, $complemento, $bairro, $municipio, $cep, $uf_id, $localizacao_id,
                $tipobem_id, $proprietario_id, $rescom, $pagtogar, $diapagto, $vlaluguel, $vldesc, $qtdemes, $percomissao,
                $cliente_id, $contrato_id, $reservar, $obs1, $obs2, $urbrural, $municipio_id, $cliente2_id) =  $data;
            
            $i++;
            $descricao = $descricao1." ".$descricao2;
            $descricao = str_replace("'", "", $descricao);
            $cep = substr($cep,0,5)."-".substr($cep,5,3);
            if ($rescom == 0)
            {
                $rescom = 'R';   
            }
            else
            {
                 $rescom = 'C';           
            }            
            if ($pagtogar == 0)
            {
                $pagtogar = 'N';
            }
            else
            {
                $pagtogar = 'S';            
            }
            $vlaluguel = $vlaluguel / 100;
            $vldesc = $vldesc / 100;
            $percomissao = $percomissao / 100;
            if ($reservar == 0)
            {
                $reservar = 'N';
            }
            else
            {
                $reservar = 'S';
            }
            $obs = $obs1." ".$obs2;
            $cliente2_id = intval($cliente2_id);
            
            if ($primeiro)
            {
                $primeiro = FALSE;
            }
            else
            {
                $sql .= ",";        
            }
            $sql .= "($id, '$descricao', '$endereco', '$complemento', '$bairro', '$municipio', '$cep', '$uf_id', $localizacao_id, $tipobem_id, $proprietario_id, '$rescom', 
                    '$pagtogar', $diapagto, $vlaluguel, $vldesc, $qtdemes, $percomissao, $cliente_id, $contrato_id, '$reservar', '$obs', '$urbrural', $municipio_id, $cliente2_id)";
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
?>
