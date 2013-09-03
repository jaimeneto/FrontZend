********************************************************************************
                                    FrontZend
********************************************************************************
O FrontZend é um sistema de gerencimento de conteúdo desenvolvido com foco na
usabilidade, procurando tornar o trabalho, tanto dos desenvolvedores quanto
dos usuários, mais fácil e intuitivo e totalmente flexível, aumentando a
produtividade e tornando a tarefa de criar e manter sites mais objetiva e
prazerosa.


* INSTALAÇÃO

Para começar a utilizar o FrontZend, salve ele no seu servidor, faça uma cópia
do arquivo .htaccess-dist, presente na pasta public, e renomeie a cópia para
.htaccess, edite este arquivo para alterar a constante SITE_URL para a url em
questão, e retire o comentário (;). Você pode alterar o caminho para área
administrativa do sistema editando a constante ADMIN_ROUTE também.

Depois faça uma cópia do arquivo application/configs/local-dist.ini, renomeie
para local.ini e altere as configurações de banco de dados e timezone.

É preciso ter uma cópia do Zend Framework 1.12+ na pasta library para que o
CMS funcione. Baixe em http://www.zendframework.com/downloads/latest#ZF1


* UTILIZANDO

O primeiro passo é criar os tipos de conteúdo que deseja utilizar, e definir
os campos personalizados para cada tipo. Depois você pode criar as páginas
de layout para cada conteúdo ou tipo de conteúdo. Agora é só inserir conteúdo
e ver como é fácil utilizá-lo


* DÚVIDAS, CRÍTICAS, SUGESTÕES

http://frontzend.jaimeneto.com
