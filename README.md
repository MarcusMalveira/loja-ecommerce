# 🛒 TechLoja — E-commerce demonstrativo

Aplicação web **full stack** desenvolvida em PHP e MySQL, inicialmente como atividade acadêmica de CRUD e testes automatizados e posteriormente adaptada para uma **loja virtual demonstrativa publicada na nuvem**.

> **Projeto educacional:** as compras são simuladas. Não existe processamento real de pagamentos.

### 🌐 Acesse a aplicação
**[Abrir a TechLoja](https://loja-ecommerce-omega.vercel.app/)**

## Funcionalidades
- Vitrine dinâmica de produtos com cards e busca por nome;
- Cadastro, edição e exclusão de produtos;
- Controle de preços e quantidades em estoque;
- Carrinho com alteração de quantidades;
- Finalização simulada de pedidos, com atualização do estoque;
- Layout responsivo em HTML e CSS.

## Tecnologias

| Camada | Tecnologias |
| --- | --- |
| Frontend | HTML, CSS e JavaScript |
| Backend | PHP, arquitetura MVC e padrões DAO/DTO |
| Banco de dados | MySQL e PDO |
| Deploy | Vercel (runtime PHP comunitário) |
| Banco na nuvem | Aiven for MySQL |
| Versionamento | Git e GitHub |
| Desenvolvimento e testes | XAMPP, MySQL Shell, Python e Selenium |

## Como a aplicação funciona

```text
Navegador
   ↓ HTTPS
Vercel (PHP)
   ├─ Controllers → DAO / DTO → Views
   │                ↓
   └───────── MySQL na Aiven (TLS)
```

O GitHub armazena o código-fonte, e novos commits na branch `main` podem iniciar deploys automáticos na Vercel.

## Executar localmente

1. Instale o XAMPP e ative Apache e MySQL.
2. Clone este repositório dentro de `htdocs`.
3. Crie o banco local `produtos` e uma tabela `produto` com colunas `id`, `nome`, `valor`, `quantidade` e `validade`. Os arquivos SQL do projeto podem servir de referência; revise-os antes de importar.
4. Abra `http://localhost/loja-ecommerce/` no navegador.

Para o deploy, as configurações do banco ficam em variáveis de ambiente na Vercel: `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASSWORD` e `DB_SSL_CA_BASE64`. **Não adicione senhas ao repositório.**

## Aprendizados do projeto
- Versionamento e integração GitHub → Vercel;
- Deploy de PHP em ambiente serverless;
- Configuração de MySQL remoto, DNS, portas e TLS;
- Variáveis de ambiente e análise de logs de execução;
- Desenvolvimento de páginas dinâmicas, CRUD e carrinho de compras.

## Observações
- Projeto voltado a aprendizado, não a operações comerciais reais.
- O carrinho utiliza sessões PHP, cuja persistência em ambientes serverless ainda precisa de validação mais ampla.
- Os scripts Selenium presentes no repositório pertencem à versão acadêmica original e podem exigir atualização para a nova interface.
- O gerenciamento de produtos é demonstrativo e **não possui autenticação administrativa**: não utilize este sistema com dados reais ou operações financeiras sem implementar controle de acesso e segurança adicional.

---
Desenvolvido para estudo e prática de desenvolvimento web, bancos de dados e publicação de aplicações.
