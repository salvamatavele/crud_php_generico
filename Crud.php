<?php  
 
 class Crud{   
    
   // Atributo para guardar uma conexão PDO   
   private $pdo = null;   
    
   // Atributo onde será guardado o nome da tabela    
   private $tabela = null;   
    
   // Atributo estático que contém uma instância da própria classe   
   private static $crud = null;   
      
   /*   
   * Método privado construtor da classe    
   * @param $conexao = Conexão PDO configurada   
   * @param $tabela = Nome da tabela    
   */   
   private function __construct($conexao, $tabela=NULL){   
        
     if (!empty($conexao)):  
       $this->pdo = $conexao;   
     else:  
       echo "<h3>Conexão inexistente!</h3>";  
       exit();  
     endif;   
        
     if (!empty($tabela)) $this->tabela =$tabela;   
   }   
    
   /*    
   * Método público estático que retorna uma instância da classe Crud    
   * @param $conexao = Conexão PDO configurada   
   * @param $tabela = Nome da tabela   
   * @return Atributo contendo instância da classe Crud   
   */   
   public static function getInstance($conexao, $tabela=NULL){   
     
     // Verifica se existe uma instância da classe   
     if(!isset(self::$crud)):   
        try {   
          self::$crud = new Crud($conexao, $tabela);   
        } catch (Exception $e) {   
          echo "Erro " . $e->getMessage();   
        }   
     endif;   
     
     return self::$crud;   
   }   
  
   /*  
   * Método para setar o nome da tabela na propriedade $tabela  
   * @param $tabela = String contendo o nome da tabela  
   */   
   public function setTableName($tabela){  
     if(!empty($tabela)){  
       $this->tabela = $tabela;  
     }  
   }  
    
   /*   
   * Método privado para construção da instrução SQL de INSERT   
   * @param $arrayDados = Array de dados contendo colunas e valores   
   * @return String contendo instrução SQL   
   */    
   private function buildInsert($arrayDados){   
    
       // Inicializa variáveis   
       $sql = "";   
       $campos = "";   
       $valores = "";   
              
       // Loop para montar a instrução com os campos e valores   
       foreach($arrayDados as $chave => $valor):   
          $campos .= $chave . ', ';   
          $valores .= '?, ';   
       endforeach;   
              
       // Retira vírgula do final da string   
       $campos = (substr($campos, -2) == ', ') ? trim(substr($campos, , (strlen($campos) - 2))) : $campos ;    
              
       // Retira vírgula do final da string   
       $valores = (substr($valores, -2) == ', ') ? trim(substr($valores, , (strlen($valores) - 2))) : $valores ;    
              
       // Concatena todas as variáveis e finaliza a instrução   
       $sql .= "INSERT INTO {$this->tabela} (" . $campos . ")VALUES(" . $valores . ")";   
              
       // Retorna string com instrução SQL   
       return trim($sql);   
   }   
    
   /*   
   * Método privado para construção da instrução SQL de UPDATE   
   * @param $arrayDados = Array de dados contendo colunas, operadores e valores   
   * @param $arrayCondicao = Array de dados contendo colunas e valores para condição WHERE   
   * @return String contendo instrução SQL   
   */    
   private function buildUpdate($arrayDados, $arrayCondicao){   
    
       // Inicializa variáveis   
       $sql = "";   
       $valCampos = "";   
       $valCondicao = "";   
              
       // Loop para montar a instrução com os campos e valores   
       foreach($arrayDados as $chave => $valor):   
          $valCampos .= $chave . '=?, ';   
       endforeach;   
              
       // Loop para montar a condição WHERE   
       foreach($arrayCondicao as $chave => $valor):   
          $valCondicao .= $chave . '? AND ';   
       endforeach;   
              
       // Retira vírgula do final da string   
       $valCampos = (substr($valCampos, -2) == ', ') ? trim(substr($valCampos, , (strlen($valCampos) - 2))) : $valCampos ;    
              
       // Retira vírgula do final da string   
       $valCondicao = (substr($valCondicao, -4) == 'AND ') ? trim(substr($valCondicao, , (strlen($valCondicao) - 4))) : $valCondicao ;    
              
        // Concatena todas as variáveis e finaliza a instrução   
        $sql .= "UPDATE {$this->tabela} SET " . $valCampos . " WHERE " . $valCondicao;   
              
        // Retorna string com instrução SQL   
        return trim($sql);   
   }   
    
   /*   
   * Método privado para construção da instrução SQL de DELETE   
   * @param $arrayCondicao = Array de dados contendo colunas, operadores e valores para condição WHERE   
   * @return String contendo instrução SQL   
   */    
   private function buildDelete($arrayCondicao){   
    
        // Inicializa variáveis   
        $sql = "";   
        $valCampos= "";   
              
        // Loop para montar a instrução com os campos e valores   
        foreach($arrayCondicao as $chave => $valor):   
           $valCampos .= $chave . '? AND ';   
        endforeach;   
              
        // Retira a palavra AND do final da string   
        $valCampos = (substr($valCampos, -4) == 'AND ') ? trim(substr($valCampos, , (strlen($valCampos) - 4))) : $valCampos ;    
              
        // Concatena todas as variáveis e finaliza a instrução   
        $sql .= "DELETE FROM {$this->tabela} WHERE " . $valCampos;   
              
        // Retorna string com instrução SQL   
        return trim($sql);   
   }   
    
   /*   
   * Método público para inserir os dados na tabela   
   * @param $arrayDados = Array de dados contendo colunas e valores   
   * @return Retorna resultado booleano da instrução SQL   
   */   
   public function insert($arrayDados){   
      try {   
    
        // Atribui a instrução SQL construida no método   
        $sql = $this->buildInsert($arrayDados);   
    
        // Passa a instrução para o PDO   
        $stm = $this->pdo->prepare($sql);   
    
        // Loop para passar os dados como parâmetro   
        $cont = 1;   
              foreach ($arrayDados as $valor):   
                    $stm->bindValue($cont, $valor);   
                    $cont++;   
              endforeach;   
    
        // Executa a instrução SQL e captura o retorno   
        $retorno = $stm->execute();   
    
        return $retorno;   
           
      } catch (PDOException $e) {   
        echo "Erro: " . $e->getMessage();   
      }   
   }   
    
   /*   
   * Método público para atualizar os dados na tabela   
   * @param $arrayDados = Array de dados contendo colunas e valores   
   * @param $arrayCondicao = Array de dados contendo colunas e valores para condição WHERE - Exemplo array('$id='=>1)   
   * @return Retorna resultado booleano da instrução SQL   
   */   
   public function update($arrayDados, $arrayCondicao){   
      try {   
    
        // Atribui a instrução SQL construida no método   
        $sql = $this->buildUpdate($arrayDados, $arrayCondicao);   
    
        // Passa a instrução para o PDO   
        $stm = $this->pdo->prepare($sql);   
    
        // Loop para passar os dados como parâmetro   
        $cont = 1;   
        foreach ($arrayDados as $valor):   
            $stm->bindValue($cont, $valor);   
            $cont++;   
        endforeach;   
              
        // Loop para passar os dados como parâmetro cláusula WHERE   
        foreach ($arrayCondicao as $valor):   
            $stm->bindValue($cont, $valor);   
            $cont++;   
        endforeach;   
    
        // Executa a instrução SQL e captura o retorno   
        $retorno = $stm->execute();   
    
        return $retorno;   
           
      } catch (PDOException $e) {   
        echo "Erro: " . $e->getMessage();   
      }   
   }   
    
   /*   
   * Método público para excluir os dados na tabela   
   * @param $arrayCondicao = Array de dados contendo colunas e valores para condição WHERE - Exemplo array('$id='=>1)   
   * @return Retorna resultado booleano da instrução SQL   
   */   
   public function delete($arrayCondicao){   
      try {   
    
        // Atribui a instrução SQL construida no método   
        $sql = $this->buildDelete($arrayCondicao);   
    
        // Passa a instrução para o PDO   
        $stm = $this->pdo->prepare($sql);   
    
              // Loop para passar os dados como parâmetro cláusula WHERE   
              $cont = 1;   
              foreach ($arrayCondicao as $valor):   
                $stm->bindValue($cont, $valor);   
                $cont++;   
              endforeach;   
    
        // Executa a instrução SQL e captura o retorno   
        $retorno = $stm->execute();   
    
        return $retorno;   
           
      } catch (PDOException $e) {   
        echo "Erro: " . $e->getMessage();   
      }   
   }   
  
   /*  
   * Método genérico para executar instruções de consulta independente do nome da tabela passada no _construct  
   * @param $sql = Instrução SQL inteira contendo, nome das tabelas envolvidas, JOINS, WHERE, ORDER BY, GROUP BY e LIMIT  
   * @param $arrayParam = Array contendo somente os parâmetros necessários para clásusla WHERE  
   * @param $fetchAll  = Valor booleano com valor default TRUE indicando que serão retornadas várias linhas, FALSE retorna apenas a primeira linha  
   * @return Retorna array de dados da consulta em forma de objetos  
   */  
   public function getSQLGeneric($sql, $arrayParams=null, $fetchAll=TRUE){  
      try {   
    
        // Passa a instrução para o PDO   
        $stm = $this->pdo->prepare($sql);   
    
        // Verifica se existem condições para carregar os parâmetros    
        if (!empty($arrayParams)):   
    
          // Loop para passar os dados como parâmetro cláusula WHERE   
          $cont = 1;   
          foreach ($arrayParams as $valor):   
            $stm->bindValue($cont, $valor);   
            $cont++;   
          endforeach;   
        
        endif;   
    
        // Executa a instrução SQL    
        $stm->execute();   
    
        // Verifica se é necessário retornar várias linhas  
        if($fetchAll):   
          $dados = $stm->fetchAll(PDO::FETCH_OBJ);   
        else:  
          $dados = $stm->fetch(PDO::FETCH_OBJ);   
        endif;  
    
        return $dados;   
           
      } catch (PDOException $e) {   
        echo "Erro: " . $e->getMessage();   
      }   
   }   
 }
