# Conversor Bananal

## Objetivos
Converter o tipo de arquivo para outro, inserindo o arquivo original no site, tipo https://convertio.co/pt/

### Stack Tecnlógico
- Backend: PHP estruturado com sessões nativas
- Banco de dados: MySQL (PDO para segurança)
- Frontend: HTML5, PHP, CSS, Tailwind CSS

#### Regras de negócio (CORE)
Manter palheta de cores amarelada e elementos gráficos de banana pelo site, semelhante a https://bananajobs.com.br/
Função de enviar o arquivo semelhante a do https://convertio.co/pt/ onde o usuário pode tanto jogar o arquivo no site, quanto enviar por um botão.
Usuário seleciona o arquivo, o sistema detecta o tipo do arquivo, o sistema faz sugestões de tipo de arquivo que ele pode fazer a conversão.
após o usuário escolher o tipo de arquivo o sistema transforma o arquivo para o tipo escolhido.

##### Regras Globais
- Use sempre PDO para conexão e queries no MySQL para evitar SQL Injections
- Mantenha o código limpo e comente apenas logicas complexas.
- Separe os arquivos de forma lógica: um arquivo para conexão com a base (bd.php) e scripts de backend isolados e views em HTML5/PHP, nunca faça o sistema como um monolito, deixe sempre separados todas as regras para facilitar os futuros upgrades.
 - Estilize as telas em Tailwind de forma responsiva priorizando o MobileFrist.
 - Retorne sempre as mensagens de erros de forma claras na interface para o usuário (TOAST)
 - sempre trate as mensagens de caixa de mensagens nativas do navegador em um MODAL
