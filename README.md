FrontZend

O FrontZend é um sistema de gerencimento de conteúdo desenvolvido com foco na
usabilidade, procurando tornar o trabalho, tanto dos desenvolvedores quanto
dos usuários, mais fácil e intuitivo e totalmente flexível, aumentando a
produtividade e tornando a tarefa de criar e manter sites mais objetiva e
prazerosa.

***

INSTALAÇÃO

Para começar a utilizar o FrontZend, salve ele no seu servidor, faça uma cópia
do arquivo .htaccess-dist, presente na pasta public, e renomeie a cópia para
.htaccess, edite este arquivo para alterar a constante SITE_URL para a url de
seu site, e retire o comentário (;). Você pode alterar o caminho para área
administrativa do sistema editando a constante ADMIN_ROUTE também. O padrão é 
fz-admin, o que significa que você poderá acessar sua área administrativa 
acessando http://url-exemplo.com/fz-admin. Você pode alterá-la para acessar 
http://url-exemplo.com/admin, ou http://url-exemplo.com/gerencia, por exemplo.
Evite usar cpanel para não dar conflito com o painel de controle do servidor.

Depois faça uma cópia do arquivo application/configs/local-dist.ini, renomeie
para local.ini e altere as configurações de banco de dados e timezone.
Neste arquivo é possível fazer quaisquer alterações adicionais, utilizando a 
mesma lógica do Zend Framework. 

Importe o arquivo frontzend.sql presente na pasta docs/ para o o seu banco de 
dados. 

É necessário acrescentar as seguintes bibliotecas a pasta library para que o 
CMS funcione corretamente: 
- Zend Framework 1.12 => http://www.zendframework.com/downloads/latest#ZF1
- BootstrapZF => https://github.com/jaimeneto/BootstrapZF

Acesse a área administrativa do seu site pela url padrão 
http://url-exemplo.com/fz-admin ou pela que você definiu no .htaccess

Para seu primeiro acesso, utilize login admin e senha 123456. Altere seus 
dados após o primeiro acesso. Siga o menú Acesso > Usuários, e clique no ícone 
de editar ao lado do nome do usuário admin. Insira uma senha forte. Lembre-se 
que este usuário tem acesso a todas as configurações do sistema.

***

UTILIZANDO

O primeiro passo é criar os tipos de conteúdo que deseja utilizar, e definir
os campos personalizados para cada tipo. Depois você pode criar as páginas
de layout para cada conteúdo ou tipo de conteúdo. Agora é só inserir conteúdo
e ver como é fácil utilizá-lo.

***

MAIS INFORMAÇÕES, DÚVIDAS, CRÍTICAS, SUGESTÕES em http://frontzend.jaimeneto.com
