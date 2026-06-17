# Turismo Curuçá

Site turístico para a Secretaria de Turismo de Curuçá (PA), com páginas públicas de atrativos, serviços ao visitante e painel administrativo para gestão de conteúdo.

## Requisitos

- PHP 8.1 ou superior
- MySQL/MariaDB
- Apache com `mod_rewrite`
- Extensões PHP comuns: `pdo_mysql`, `mbstring`, `fileinfo` e `gd` ou suporte equivalente para uploads

## Configuração

1. Duplique `.env.example` para `.env`.
2. Ajuste as credenciais do banco:

```env
DB_HOST=localhost
DB_NAME=sec_turismo
DB_USER=root
DB_PASSWORD=
BASE_URL=
```

3. Garanta permissão de escrita para `public/imgs/uploads`.
4. Importe/crie as tabelas usadas pelo painel antes de cadastrar notícias, locais e usuários.

## Como Rodar Localmente

Com Laragon ou Apache local, aponte o host para a raiz do projeto e acesse:

```text
/public/index.php
```

Se acessar apenas a raiz do domínio, o `.htaccess` redireciona para `public/index.php`.

## Implantação

A estrutura atual ainda possui páginas públicas em `app/Views/*.php`. Por isso, para publicar sem refatorar rotas, hospede a raiz do projeto e mantenha o `.htaccess` ativo. Ele bloqueia acesso às camadas internas (`Core`, `Controllers`, `Models`, `Utils`) e redireciona a raiz para a página inicial.

Para uma implantação mais rígida no futuro, o próximo passo recomendado é criar um roteador único em `public/index.php` e mover todo acesso público para URLs controladas por esse front controller.

## Estrutura

- `public/`: página inicial, CSS, JavaScript, imagens e documentos públicos.
- `app/Views/`: páginas públicas secundárias e telas administrativas.
- `app/Controllers/`: regras de fluxo entre views e models.
- `app/Models/`: acesso ao banco de dados.
- `app/Utils/`: helpers de ambiente, CSRF e upload.
- `app/Core/conexao.php`: conexão PDO com o banco.

## Observações

- A página inicial não derruba o site se o banco ainda não estiver configurado; nesse caso, apenas a seção de notícias fica vazia.
- Não publique `.env`, dumps SQL ou backups no servidor público.
