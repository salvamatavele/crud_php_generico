 require_once "Crud.php"; 
 
 // Consumindo métodos do CRUD genérico 
 
 // Atribui uma conexão PDO   
 $pdo = Conexao::getInstance();  
 
 // Atribui uma instância da classe Crud, passando como parâmetro a conexão PDO e o nome da tabela  
 $crud = Crud::getInstance($pdo, 'Nome_Tabela');//se a tabele for de usuarios por exemplo poderia subistuir com o nome usuarios no Nome_tabela
 
 // Inserir os dados do usuário 
 $arrayUser = array('nome' => 'o nome', 'email' => 'email@gmail.com', 'senha' => base64_encode('senha'), 'privilegio' => 'Admin');  
 $retorno   = $crud->insert($arrayUser);  
 
 // Editar os dados do usuario com id 1 
 $arrayUser = array('nome' => 'o nome updated', 'email' => 'email@gmail.com', 'senha' => base64_encode('senha'), 'privilegio' => 'Admin');   
 $arrayCond = array('id=' => 1);  
 $retorno   = $crud->update($arrayUser, $arrayCond);  
 
 // Excluir o registro do usuário com id 1 
 $arrayCond = array('id=' => 1);  
 $retorno   = $crud->delete($arrayCond);  
 
 // Consulta os dados do usuário com id 1 e privilegio A 
 $sql        = "SELECT nome, email, privilegio FROM TAB_USUARIO WHERE id = ? AND privilegio = ?";  
 $arrayParam = array(1, 'A');  
 $dados      = $crud->getSQLGeneric($sql, $arrayParam, FALSE); 

// Consulta os dados dos usuários
 $sql        = "SELECT * FROM TAB_USUARIO;   
 $dados      = $crud->getSQLGeneric($sql); 
