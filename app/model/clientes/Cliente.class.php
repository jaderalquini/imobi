<?php
/**
 * Cliente Active Record
 * @author  <your-name-here>
 */
class Cliente extends TRecord
{
    const TABLENAME = 'cliente';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}  
    
    private $estadocivil;
    private $uf;
    private $tiporesidencia;
    
    use SystemChangeLogTrait;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
        parent::addAttribute('cpfcnpj');
        parent::addAttribute('tipo');
        parent::addAttribute('dtnasc');
        parent::addAttribute('ierg');
        parent::addAttribute('estadocivil_id');
        parent::addAttribute('municipionasc');
        parent::addAttribute('ufnasc');
        parent::addAttribute('nomepais');
        parent::addAttribute('endereco');
        parent::addAttribute('bairro');
        parent::addAttribute('cep');
        parent::addAttribute('municipio');
        parent::addAttribute('uf_id');
        parent::addAttribute('fone');
        parent::addAttribute('cel');
        parent::addAttribute('fax');
        parent::addAttribute('tipores_id');
        parent::addAttribute('tempores');
        parent::addAttribute('proximo');   
        parent::addAttribute('representante_id');
        parent::addAttribute('profissao');     
        parent::addAttribute('dtcadastro');
        parent::addAttribute('empresa_nome');
        parent::addAttribute('empresa_endereco');
        parent::addAttribute('empresa_bairro');
        parent::addAttribute('empresa_cep');
        parent::addAttribute('empresa_municipio');
        parent::addAttribute('empresa_uf');
        parent::addAttribute('empresa_fone');
        parent::addAttribute('empresa_cargo');
        parent::addAttribute('empresa_salario');
        parent::addAttribute('empresa_salarioref');
        parent::addAttribute('empresa_tempo');
        parent::addAttribute('empresa_anterior');
        parent::addAttribute('conjuge_regime');
        parent::addAttribute('conjuge_nome');
        parent::addAttribute('conjuge_dtnasc');
        parent::addAttribute('conjuge_rg');
        parent::addAttribute('conjuge_cpf');
        parent::addAttribute('conjuge_profissao');
    }
    
    /**
     * Method get_estadocivil
     * Sample of usage: $cliente->estadocivil->attribute;
     * @returns Estadocivil instance
     */
    public function get_estadocivil()
    {
        try
        {
            $obj = new Estadocivil($this->estadocivil_id);
            return $obj->descricao;   
        }  
        catch (Exception $e) // in case of exception
        {
            return NULL;    
        }        
    }
    
    /**
     * Method get_uf
     * Sample of usage: $cliente->uf->attribute;
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
     * Method get_tiporesidencia
     * Sample of usage: $cliente->tiporesidencia->attribute;
     * @returns Tiporesidencia instance
     */
    public function get_tiporesidencia()
    {
        try
        {
            $obj = new Tiporesidencia($this->tiporesidencia_id);
            return $obj->descricao;   
        }  
        catch (Exception $e) // in case of exception
        {
            return NULL;    
        }        
    }
    
    /**
     * Method get_representante
     * Sample of usage: $cliente->representante->attribute;
     * @returns Cliente instance
     */
    public function get_representante()
    {
        try
        {
            $obj = new Cliente($this->representante_id);
            return $obj->nome;   
        }  
        catch (Exception $e) // in case of exception
        {
            return NULL;    
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
        
        $sql = "INSERT INTO $table (id, nome, cpfcnpj, tipo, dtnasc, ierg, estadocivil_id, municipionasc, ufnasc, endereco, 
                bairro, cep, municipio, uf_id, fone, cel, fax, tipores_id, tempores, proximo, dtcadastro) values ";
           
        $primeiro = TRUE; 
        $i = 0;
        $j = 0;    
        $linhas = file($target_file);
        foreach ($linhas as $linha) 
        {
            if ($i == 50)
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
                $sql = "INSERT INTO $table (id, nome, cpfcnpj, tipo, dtnasc, ierg, estadocivil_id, municipionasc, ufnasc, endereco, 
                        bairro, cep, municipio, uf_id, fone, cel, fax, tipores_id, tempores, proximo, dtcadastro) values ";
            }
            
            $data = array_map('trim', explode(';', $linha));
            list($id, $nome, $cpfcnpj, $tipo, $dtnasc, $ierg, $estadocivil_id, $municipionasc, $ufnasc, $nomepais, $endereco,
                $bairro, $cep, $municipio, $uf_id, $fone, $fax, $tipores_id, $tempores, $proximo, $_20, $_21, $_22, $dtcadastro, $_24, $_25, $_26) =  $data;
            
            $i++;
            $nome = str_replace("'", "", $nome);
            $estadocivil_id = intval($estadocivil_id);
            $municipionasc = str_replace("'", "", $municipionasc);
            $nomepais = str_replace("'", "", $nomepais);
            $endereco = str_replace("'", "", $endereco);
            $municipio = str_replace("'", "", $municipio);
            $tipores_id = intval($tipores_id); 
            if ($tipo == 1)
            {
                $cpfcnpj = substr($cpfcnpj,3,3).".".substr($cpfcnpj,6,3).".".substr($cpfcnpj,9,3)."-".substr($cpfcnpj,12,2);
                $tipo = "F";
            }
            else if ($tipo == 2)
            {
                $cpfcnpj = substr($cpfcnpj,0,2).".".substr($cpfcnpj,2,3).".".substr($cpfcnpj,5,3)."/".substr($cpfcnpj,8,4)."-".substr($cpfcnpj,12,2);
                $tipo = "J";
            }
            else 
            {
                $cpfcnpj = "0";
                $tipo = "F";
            }
            $dtnasc = substr($dtnasc,4,4)."-".substr($dtnasc,2,2)."-".substr($dtnasc,0,2);
            $cep = substr($data[12],0,5)."-".substr($data[12],5,3);
            $dtcadastro = substr($dtcadastro,4,4)."-".substr($dtcadastro,2,2)."-".substr($dtcadastro,0,2);
            
            if ($primeiro)
            {
                $primeiro = FALSE;
            }
            else
            {
                $sql .= ",";        
            }
            $sql .= "($id, '$nome', '$cpfcnpj', '$tipo', '$dtnasc', '$ierg', $estadocivil_id, '$municipionasc', '$ufnasc', '$endereco', 
                    '$bairro', '$cep', '$municipio', '$uf_id', '$fone', NULL, '$fax', $tipores_id, '$tempores', '$proximo', '$dtcadastro')";
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
